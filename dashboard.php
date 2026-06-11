<?php
require_once __DIR__ . '/config.php';
redirectIfNotAuthenticated();

$pdo = getPDO();

$totalBooks = $pdo->query('SELECT SUM(copies_total) AS total_books FROM books')->fetchColumn();
$totalStudents = $pdo->query('SELECT COUNT(*) FROM students')->fetchColumn();
$totalBorrowed = $pdo->query('SELECT COUNT(*) FROM borrowings WHERE returned = 0')->fetchColumn();

$recentActivityStmt = $pdo->prepare(
    'SELECT s.name AS student_name, s.student_number, bk.title, b.borrow_date, b.due_date, b.returned
     FROM borrowings b
     JOIN books bk ON b.book_id = bk.id
     JOIN students s ON b.student_id = s.id
     ORDER BY b.borrow_date DESC
     LIMIT 6'
);
$recentActivityStmt->execute();
$recentActivities = $recentActivityStmt->fetchAll();

$search = trim($_GET['search'] ?? '');
$searchCondition = '';
$params = [];
if ($search !== '') {
    $searchCondition = 'WHERE title LIKE ? OR author LIKE ? OR isbn LIKE ?';
    $pattern = '%' . $search . '%';
    $params = [$pattern, $pattern, $pattern];
}
$booksStmt = $pdo->prepare(
    'SELECT bk.id, bk.isbn, bk.title, bk.author, c.name AS category, bk.copies_available, bk.copies_total
     FROM books bk
     JOIN categories c ON bk.category_id = c.id
     ' . $searchCondition . '
     ORDER BY bk.title ASC
     LIMIT 10'
);
$booksStmt->execute($params);
$books = $booksStmt->fetchAll();

$flash = flashMessage();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord | <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="dashboard-bg">
    <?php $active = 'dashboard'; include __DIR__ . '/partials/nav.php'; ?>
    <div id="p5-dashboard-canvas"></div>
    <main class="container py-5 mt-5">
        <div class="row align-items-end mb-4">
            <div class="col-lg-8">
                <h1 class="display-6 text-white fw-bold">Tableau de bord</h1>
                <p class="text-white-50 mb-0">Bienvenue sur le centre de gestion de la bibliothèque inspirée du Sénégal.</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="index.php" class="btn btn-warning shadow-sm">Retour à l&apos;accueil</a>
            </div>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($flash['text']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="dashboard-card p-4 rounded-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="badge bg-white text-dark">Livres</span>
                        <span class="text-primary fs-4">📚</span>
                    </div>
                    <h2 class="display-5 fw-bold"><?= number_format($totalBooks) ?></h2>
                    <p class="text-white-50 mb-0">Total de livres disponibles dans la collection.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card p-4 rounded-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="badge bg-white text-dark">Étudiants</span>
                        <span class="text-warning fs-4">🎓</span>
                    </div>
                    <h2 class="display-5 fw-bold"><?= number_format($totalStudents) ?></h2>
                    <p class="text-white-50 mb-0">Étudiants enregistrés via le système de prêt.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card p-4 rounded-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="badge bg-white text-dark">Emprunts</span>
                        <span class="text-success fs-4">📖</span>
                    </div>
                    <h2 class="display-5 fw-bold"><?= number_format($totalBorrowed) ?></h2>
                    <p class="text-white-50 mb-0">Livres actuellement empruntés par les étudiants.</p>
                </div>
            </div>
        </div>

        <div class="row gy-4">
            <div class="col-xl-6">
                <div class="dashboard-card p-4 rounded-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="h5 text-white mb-0">Rechercher des livres</h3>
                        <span class="text-white-50">Catalogue</span>
                    </div>
                    <form class="mb-4" method="GET" action="dashboard.php">
                        <div class="input-group shadow-sm">
                            <input type="text" name="search" class="form-control form-control-lg" placeholder="Titre, auteur ou ISBN" value="<?= htmlspecialchars($search) ?>">
                            <button class="btn btn-warning btn-lg" type="submit">Rechercher</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-borderless text-white align-middle mb-0">
                            <thead>
                                <tr class="text-white-50 small text-uppercase letter-spacing-1">
                                    <th>Titre</th>
                                    <th>Auteur</th>
                                    <th>Catégorie</th>
                                    <th class="text-end">Disponible</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($books)): ?>
                                    <tr><td colspan="4" class="text-center text-white-50 py-4">Aucun livre trouvé.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($books as $book): ?>
                                        <tr class="border-top border-white border-opacity-10">
                                            <td><?= htmlspecialchars($book['title']) ?></td>
                                            <td><?= htmlspecialchars($book['author']) ?></td>
                                            <td><?= htmlspecialchars($book['category']) ?></td>
                                            <td class="text-end text-warning fw-semibold"><?= htmlspecialchars($book['copies_available']) ?>/<?= htmlspecialchars($book['copies_total']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="dashboard-card p-4 rounded-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="h5 text-white mb-0">Activité récente</h3>
                        <span class="text-white-50">Prêts</span>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentActivities as $activity): ?>
                            <div class="list-group-item bg-transparent border-top border-white border-opacity-10 text-white py-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <strong><?= htmlspecialchars($activity['student_name']) ?></strong>
                                    <span class="badge bg-<?= $activity['returned'] ? 'success' : 'warning' ?> text-dark"><?= $activity['returned'] ? 'Retourné' : 'Actif' ?></span>
                                </div>
                                <p class="mb-1 text-white-50 small">"<?= htmlspecialchars($activity['title']) ?>" par <?= htmlspecialchars($activity['student_number']) ?></p>
                                <small class="text-white-50">Emprunté le <?= htmlspecialchars($activity['borrow_date']) ?> • Échéance <?= htmlspecialchars($activity['due_date']) ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/p5.js/1.6.0/p5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/dashboard.js"></script>
</body>
</html>
