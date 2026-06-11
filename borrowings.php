<?php
require_once __DIR__ . '/config.php';
redirectIfNotAuthenticated();
$pdo = getPDO();

$alert = flashMessage();
$action = $_GET['action'] ?? '';
$borrowingId = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = (int)($_POST['student_id'] ?? 0);
    $bookId = (int)($_POST['book_id'] ?? 0);
    $borrowDate = $_POST['borrow_date'] ?? '';
    $dueDate = $_POST['due_date'] ?? '';

    if ($studentId <= 0 || $bookId <= 0 || $borrowDate === '' || $dueDate === '') {
        setFlash('Tous les champs doivent être remplis pour un emprunt.', 'warning');
        header('Location: borrowings.php');
        exit;
    }

    $availableStmt = $pdo->prepare('SELECT copies_available FROM books WHERE id = ?');
    $availableStmt->execute([$bookId]);
    $bookAvailable = $availableStmt->fetchColumn();

    if ($bookAvailable === false || $bookAvailable < 1) {
        setFlash('Ce livre n’est pas disponible actuellement.', 'danger');
        header('Location: borrowings.php');
        exit;
    }

    $stmt = $pdo->prepare('INSERT INTO borrowings (student_id, book_id, borrow_date, due_date) VALUES (?, ?, ?, ?)');
    $stmt->execute([$studentId, $bookId, $borrowDate, $dueDate]);
    $pdo->prepare('UPDATE books SET copies_available = copies_available - 1 WHERE id = ?')->execute([$bookId]);
    setFlash('Emprunt enregistré avec succès.', 'success');
    header('Location: borrowings.php');
    exit;
}

if ($action === 'return' && $borrowingId) {
    $stmt = $pdo->prepare('SELECT book_id, returned FROM borrowings WHERE id = ?');
    $stmt->execute([$borrowingId]);
    $borrowing = $stmt->fetch();

    if (!$borrowing) {
        setFlash('Emprunt introuvable.', 'danger');
    } elseif ($borrowing['returned']) {
        setFlash('Ce livre a déjà été retourné.', 'warning');
    } else {
        $pdo->prepare('UPDATE borrowings SET returned = 1, returned_date = CURDATE() WHERE id = ?')->execute([$borrowingId]);
        $pdo->prepare('UPDATE books SET copies_available = copies_available + 1 WHERE id = ?')->execute([$borrowing['book_id']]);
        setFlash('Retour enregistré. Merci.', 'success');
    }
    header('Location: borrowings.php');
    exit;
}

$search = trim($_GET['search'] ?? '');
$where = '';
$params = [];
if ($search !== '') {
    $where = 'WHERE s.name LIKE ? OR s.student_number LIKE ? OR bk.title LIKE ?';
    $pattern = '%' . $search . '%';
    $params = [$pattern, $pattern, $pattern];
}

$borrowingsStmt = $pdo->prepare(
    'SELECT br.*, bk.title AS book_title, s.name AS student_name, s.student_number
     FROM borrowings br
     JOIN books bk ON br.book_id = bk.id
     JOIN students s ON br.student_id = s.id
     ' . $where . '
     ORDER BY br.borrow_date DESC'
);
$borrowingsStmt->execute($params);
$borrowings = $borrowingsStmt->fetchAll();

$students = $pdo->query('SELECT id, name, student_number FROM students ORDER BY name')->fetchAll();
$books = $pdo->query('SELECT id, title, copies_available FROM books ORDER BY title')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emprunts | <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="dashboard-bg">
    <?php $active = 'borrowings'; include __DIR__ . '/partials/nav.php'; ?>
    <main class="container py-5 mt-5">
        <div class="row g-4">
            <div class="col-xl-7">
                <div class="dashboard-card p-4 rounded-4">
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between mb-4 gap-3">
                        <div>
                            <h2 class="h4 text-white mb-1">Système de prêt</h2>
                            <p class="text-white-50 mb-0">Enregistrer les emprunts et suivre les retours.</p>
                        </div>
                        <form class="w-100 w-md-auto" method="GET" action="borrowings.php">
                            <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden">
                                <input type="text" name="search" class="form-control border-0" placeholder="Rechercher un emprunt" value="<?= htmlspecialchars($search) ?>">
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
                                    <th>Étudiant</th>
                                    <th>Livre</th>
                                    <th>Emprunt</th>
                                    <th>Échéance</th>
                                    <th>Statut</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($borrowings)): ?>
                                    <tr><td colspan="6" class="text-center text-white-50 py-4">Aucun emprunt trouvé.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($borrowings as $row): ?>
                                        <tr class="border-top border-white border-opacity-10">
                                            <td><?= htmlspecialchars($row['student_name']) ?> <span class="text-white-50 d-block small"><?= htmlspecialchars($row['student_number']) ?></span></td>
                                            <td><?= htmlspecialchars($row['book_title']) ?></td>
                                            <td><?= htmlspecialchars($row['borrow_date']) ?></td>
                                            <td><?= htmlspecialchars($row['due_date']) ?></td>
                                            <td>
                                                <?php if ($row['returned']): ?>
                                                    <span class="badge bg-success text-dark">Retourné</span>
                                                <?php elseif (strtotime($row['due_date']) < time()): ?>
                                                    <span class="badge bg-danger text-dark">En retard</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark">Actif</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <?php if (!$row['returned']): ?>
                                                    <a href="borrowings.php?action=return&id=<?= $row['id'] ?>" class="btn btn-sm btn-success" onclick="return confirm('Marquer comme retourné ?');">Retour</a>
                                                <?php else: ?>
                                                    <span class="text-white-50 small">-</span>
                                                <?php endif; ?>
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
                    <h3 class="h5 text-white mb-3">Nouvel emprunt</h3>
                    <form method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label class="form-label text-white">Étudiant</label>
                            <select name="student_id" class="form-select form-select-lg" required>
                                <option value="">Sélectionner un étudiant</option>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?= $student['id'] ?>"><?= htmlspecialchars($student['name'] . ' (' . $student['student_number'] . ')') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Livre</label>
                            <select name="book_id" class="form-select form-select-lg" required>
                                <option value="">Sélectionner un livre disponible</option>
                                <?php foreach ($books as $book): ?>
                                    <option value="<?= $book['id'] ?>" <?= $book['copies_available'] < 1 ? 'disabled' : '' ?>><?= htmlspecialchars($book['title']) ?> <?= $book['copies_available'] < 1 ? '(indisponible)' : '(' . $book['copies_available'] . ' dispo)' ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-white">Date emprunt</label>
                                <input type="date" name="borrow_date" class="form-control form-control-lg" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white">Date de retour</label>
                                <input type="date" name="due_date" class="form-control form-control-lg" value="<?= date('Y-m-d', strtotime('+14 days')) ?>" required>
                            </div>
                        </div>
                        <button class="btn btn-warning btn-lg w-100">Enregistrer l’emprunt</button>
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
