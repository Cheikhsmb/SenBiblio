<?php
require_once __DIR__ . '/config.php';
redirectIfNotAuthenticated();
$pdo = getPDO();

$flash = flashMessage();
$flashJson = $flash ? json_encode($flash) : 'null';

$memberCategories = ['Adhérent', 'Étudiant', 'Senior', 'Enfant', 'Autre'];

$memberId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$action = $_GET['action'] ?? '';
$member = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!verifyCsrf($csrfToken)) {
        setFlash('Jeton de sécurité invalide. Veuillez réessayer.', 'danger');
        header('Location: students.php');
        exit;
    }

    $postAction = $_POST['action'] ?? '';

    if ($postAction === 'delete') {
        $deleteId = isset($_POST['student_id']) ? (int)$_POST['student_id'] : null;
        if ($deleteId) {
            try {
                $stmt = $pdo->prepare('DELETE FROM students WHERE id = ?');
                $stmt->execute([$deleteId]);
                setFlash('Membre supprimé.', 'success');
            } catch (PDOException $e) {
                setFlash('Impossible de supprimer ce membre. Vérifiez les emprunts associés.', 'danger');
            }
        }
        header('Location: students.php');
        exit;
    }

    $name = trim($_POST['name'] ?? '');
    $memberNumber = trim($_POST['student_number'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $category = trim($_POST['program'] ?? '');
    $membershipType = trim($_POST['academic_year'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $editId = isset($_POST['student_id']) ? (int)$_POST['student_id'] : null;

    if ($name === '' || $memberNumber === '' || $email === '' || $category === '' || $membershipType === '') {
        setFlash('Tous les champs obligatoires doivent être remplis.', 'warning');
        header('Location: students.php' . ($editId ? '?action=edit&id=' . $editId : ''));
        exit;
    }

    $duplicateStmt = $pdo->prepare('SELECT id FROM students WHERE student_number = ? AND id != ?');
    $duplicateStmt->execute([$memberNumber, $editId ?: 0]);
    if ($duplicateStmt->fetch()) {
        setFlash('Ce numéro membre existe déjà.', 'danger');
        header('Location: students.php' . ($editId ? '?action=edit&id=' . $editId : ''));
        exit;
    }

    if ($editId) {
        $stmt = $pdo->prepare('UPDATE students SET name = ?, student_number = ?, email = ?, program = ?, academic_year = ?, phone = ?, address = ? WHERE id = ?');
        $stmt->execute([$name, $memberNumber, $email, $category, $membershipType, $phone, $address, $editId]);
        setFlash('Membre mis à jour avec succès.', 'success');
    } else {
        $stmt = $pdo->prepare('INSERT INTO students (name, student_number, email, program, academic_year, phone, address) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $memberNumber, $email, $category, $membershipType, $phone, $address]);
        setFlash('Membre ajouté.', 'success');
    }
    header('Location: students.php');
    exit;
}

if ($action === 'edit' && $memberId) {
    $stmt = $pdo->prepare('SELECT * FROM students WHERE id = ?');
    $stmt->execute([$memberId]);
    $member = $stmt->fetch();
}

$search = trim($_GET['search'] ?? '');
$query = 'SELECT * FROM students';
$params = [];
if ($search !== '') {
    $query .= ' WHERE name LIKE ? OR student_number LIKE ? OR email LIKE ? OR program LIKE ?';
    $pattern = '%' . $search . '%';
    $params = [$pattern, $pattern, $pattern, $pattern];
}
$query .= ' ORDER BY name ASC';
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$members = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membres | <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="dashboard-bg" data-flash="<?= htmlspecialchars($flashJson) ?>">
    <?php $active = 'students'; include __DIR__ . '/partials/nav.php'; ?>
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>
    <main class="container py-5 mt-5">
        <div class="row g-4">
            <div class="col-xl-7">
                <div class="dashboard-card p-4 rounded-4">
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between mb-4 gap-3">
                        <div>
                            <h2 class="h4 text-white mb-1">Gestion des membres</h2>
                            <p class="text-white-50 mb-0">Ajoutez des profils membres et retrouvez-les rapidement.</p>
                        </div>
                        <form class="w-100 w-md-auto" method="GET" action="students.php">
                            <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden">
                                <input type="text" name="search" class="form-control border-0" placeholder="Recherche membre" value="<?= htmlspecialchars($search) ?>">
                                <button class="btn btn-warning" type="submit">Rechercher</button>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-borderless table-striped table-hover align-middle text-white mb-0">
                            <thead>
                                <tr class="text-white-50 small text-uppercase">
                                    <th>Nom</th>
                                    <th>N° Membre</th>
                                    <th>Catégorie</th>
                                    <th>Type abonnement</th>
                                    <th>Email</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($members)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center gap-2">
                                                <i class="fa-solid fa-user-group text-white-50" style="font-size: 3rem;"></i>
                                                <span class="text-white-50">Aucun membre enregistré</span>
                                                <small class="text-white-50">Cliquez sur "Ajouter" pour créer le premier membre</small>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($members as $row): ?>
                                        <tr class="border-top border-white border-opacity-10">
                                            <td><?= htmlspecialchars($row['name']) ?></td>
                                            <td><?= htmlspecialchars($row['student_number']) ?></td>
                                            <td><span class="badge bg-secondary"><?= htmlspecialchars($row['program']) ?></span></td>
                                            <td><?= htmlspecialchars($row['academic_year']) ?></td>
                                            <td><?= htmlspecialchars($row['email']) ?></td>
                                            <td class="text-end">
                                                <a href="students.php?action=edit&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-light me-2">Modifier</a>
                                                <form method="POST" action="students.php" style="display: inline;" onsubmit="return confirm('Supprimer ce membre ?');">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrfToken()) ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="student_id" value="<?= $row['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-xl-5">
                <div class="dashboard-card p-4 rounded-4">
                    <h3 class="h5 text-white mb-3"><?= $member ? 'Modifier le membre' : 'Ajouter un membre' ?></h3>
                    <form method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrfToken()) ?>">
                        <input type="hidden" name="student_id" value="<?= $member ? $member['id'] : '' ?>">
                        <div class="mb-3">
                            <label class="form-label text-white">Nom complet</label>
                            <input type="text" name="name" class="form-control form-control-lg" value="<?= htmlspecialchars($member['name'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">N° Membre</label>
                            <input type="text" name="student_number" class="form-control form-control-lg" value="<?= htmlspecialchars($member['student_number'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Email</label>
                            <input type="email" name="email" class="form-control form-control-lg" value="<?= htmlspecialchars($member['email'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Catégorie</label>
                            <select name="program" class="form-select form-select-lg" required>
                                <option value="">Sélectionner une catégorie</option>
                                <?php foreach ($memberCategories as $cat): ?>
                                    <option value="<?= $cat ?>" <?= isset($member['program']) && $member['program'] === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Type d'abonnement</label>
                            <input type="text" name="academic_year" class="form-control form-control-lg" value="<?= htmlspecialchars($member['academic_year'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Téléphone <span class="text-white-50 small">(optionnel)</span></label>
                            <input type="tel" name="phone" class="form-control form-control-lg" value="<?= htmlspecialchars($member['phone'] ?? '') ?>" placeholder="+33 6 00 00 00 00">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Adresse <span class="text-white-50 small">(optionnel)</span></label>
                            <textarea name="address" rows="2" class="form-control form-control-lg" placeholder="Adresse complète"><?= htmlspecialchars($member['address'] ?? '') ?></textarea>
                        </div>
                        <button class="btn btn-warning btn-lg w-100"><?= $member ? 'Enregistrer' : 'Ajouter' ?></button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/toast.js"></script>
    <script>
        (() => {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                });
            });
        })();
    </script>
</body>
</html>
