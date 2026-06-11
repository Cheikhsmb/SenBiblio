<?php
require_once __DIR__ . '/config.php';
redirectIfNotAuthenticated();
$pdo = getPDO();

$alert = flashMessage();
$studentId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$action = $_GET['action'] ?? '';
$student = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $studentNumber = trim($_POST['student_number'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $program = trim($_POST['program'] ?? '');
    $academicYear = trim($_POST['academic_year'] ?? '');
    $editId = isset($_POST['student_id']) ? (int)$_POST['student_id'] : null;

    if ($name === '' || $studentNumber === '' || $email === '' || $program === '' || $academicYear === '') {
        setFlash('Tous les champs doivent être remplis.', 'warning');
        header('Location: students.php' . ($editId ? '?action=edit&id=' . $editId : ''));
        exit;
    }

    $duplicateStmt = $pdo->prepare('SELECT id FROM students WHERE student_number = ? AND id != ?');
    $duplicateStmt->execute([$studentNumber, $editId ?: 0]);
    if ($duplicateStmt->fetch()) {
        setFlash('Ce numéro étudiant existe déjà.', 'danger');
        header('Location: students.php' . ($editId ? '?action=edit&id=' . $editId : ''));
        exit;
    }

    if ($editId) {
        $stmt = $pdo->prepare('UPDATE students SET name = ?, student_number = ?, email = ?, program = ?, academic_year = ? WHERE id = ?');
        $stmt->execute([$name, $studentNumber, $email, $program, $academicYear, $editId]);
        setFlash('Étudiant mis à jour avec succès.', 'success');
    } else {
        $stmt = $pdo->prepare('INSERT INTO students (name, student_number, email, program, academic_year) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$name, $studentNumber, $email, $program, $academicYear]);
        setFlash('Étudiant ajouté à la base.', 'success');
    }
    header('Location: students.php');
    exit;
}

if ($action === 'delete' && $studentId) {
    try {
        $stmt = $pdo->prepare('DELETE FROM students WHERE id = ?');
        $stmt->execute([$studentId]);
        setFlash('Étudiant supprimé.', 'success');
    } catch (PDOException $e) {
        setFlash('Impossible de supprimer cet étudiant. Vérifiez les emprunts associés.', 'danger');
    }
    header('Location: students.php');
    exit;
}

if ($action === 'edit' && $studentId) {
    $stmt = $pdo->prepare('SELECT * FROM students WHERE id = ?');
    $stmt->execute([$studentId]);
    $student = $stmt->fetch();
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
$students = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Étudiants | <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="dashboard-bg">
    <?php $active = 'students'; include __DIR__ . '/partials/nav.php'; ?>
    <main class="container py-5 mt-5">
        <div class="row g-4">
            <div class="col-xl-7">
                <div class="dashboard-card p-4 rounded-4">
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between mb-4 gap-3">
                        <div>
                            <h2 class="h4 text-white mb-1">Gestion des étudiants</h2>
                            <p class="text-white-50 mb-0">Ajoutez des profils étudiants et retrouvez-les rapidement.</p>
                        </div>
                        <form class="w-100 w-md-auto" method="GET" action="students.php">
                            <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden">
                                <input type="text" name="search" class="form-control border-0" placeholder="Recherche étudiant" value="<?= htmlspecialchars($search) ?>">
                                <button class="btn btn-warning" type="submit">Rechercher</button>
                            </div>
                        </form>
                    </div>

                    <?php if ($alert): ?>
                        <div class="alert alert-<?= htmlspecialchars($alert['type']) ?> alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($alert['text']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-borderless align-middle text-white mb-0">
                            <thead>
                                <tr class="text-white-50 small text-uppercase">
                                    <th>Nom</th>
                                    <th>Numéro</th>
                                    <th>Programme</th>
                                    <th>Email</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($students)): ?>
                                    <tr><td colspan="5" class="text-center text-white-50 py-4">Aucun étudiant enregistré.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($students as $row): ?>
                                        <tr class="border-top border-white border-opacity-10">
                                            <td><?= htmlspecialchars($row['name']) ?></td>
                                            <td><?= htmlspecialchars($row['student_number']) ?></td>
                                            <td><?= htmlspecialchars($row['program']) ?></td>
                                            <td><?= htmlspecialchars($row['email']) ?></td>
                                            <td class="text-end">
                                                <a href="students.php?action=edit&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-light me-2">Modifier</a>
                                                <a href="students.php?action=delete&id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cet étudiant ?');">Supprimer</a>
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
                    <h3 class="h5 text-white mb-3"><?= $student ? 'Modifier l’étudiant' : 'Ajouter un étudiant' ?></h3>
                    <form method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="student_id" value="<?= $student ? $student['id'] : '' ?>">
                        <div class="mb-3">
                            <label class="form-label text-white">Nom complet</label>
                            <input type="text" name="name" class="form-control form-control-lg" value="<?= htmlspecialchars($student['name'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Numéro étudiant</label>
                            <input type="text" name="student_number" class="form-control form-control-lg" value="<?= htmlspecialchars($student['student_number'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Email</label>
                            <input type="email" name="email" class="form-control form-control-lg" value="<?= htmlspecialchars($student['email'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Programme</label>
                            <input type="text" name="program" class="form-control form-control-lg" value="<?= htmlspecialchars($student['program'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Année d’étude</label>
                            <input type="text" name="academic_year" class="form-control form-control-lg" value="<?= htmlspecialchars($student['academic_year'] ?? '') ?>" required>
                        </div>
                        <button class="btn btn-warning btn-lg w-100"><?= $student ? 'Enregistrer' : 'Ajouter' ?></button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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
