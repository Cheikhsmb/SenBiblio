<?php
require_once __DIR__ . '/config.php';
redirectIfNotAuthenticated();
$pdo = getPDO();

$alert = flashMessage();
$bookId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$action = $_GET['action'] ?? '';

$categories = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
$book = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isbn = trim($_POST['isbn'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $year = trim($_POST['year'] ?? '');
    $copiesTotal = (int)($_POST['copies_total'] ?? 0);
    $summary = trim($_POST['summary'] ?? '');
    $editId = isset($_POST['book_id']) ? (int)$_POST['book_id'] : null;

    if ($isbn === '' || $title === '' || $author === '' || $categoryId <= 0 || $year === '' || $copiesTotal <= 0) {
        setFlash('Merci de renseigner tous les champs obligatoires.', 'warning');
        header('Location: books.php' . ($editId ? '?action=edit&id=' . $editId : ''));
        exit;
    }

    $duplicateStmt = $pdo->prepare('SELECT id FROM books WHERE isbn = ?' . ($editId ? ' AND id != ?' : ''));
    $params = $editId ? [$isbn, $editId] : [$isbn];
    $duplicateStmt->execute($params);

    if ($duplicateStmt->fetch()) {
        setFlash('Un livre avec ce ISBN existe déjà.', 'danger');
        header('Location: books.php' . ($editId ? '?action=edit&id=' . $editId : ''));
        exit;
    }

    if ($editId) {
        $current = $pdo->prepare('SELECT copies_total, copies_available FROM books WHERE id = ?');
        $current->execute([$editId]);
        $existing = $current->fetch();
        $available = $existing ? max(0, min($existing['copies_available'] + ($copiesTotal - $existing['copies_total']), $copiesTotal)) : $copiesTotal;

        $stmt = $pdo->prepare('UPDATE books SET isbn = ?, title = ?, author = ?, category_id = ?, year = ?, copies_total = ?, copies_available = ?, summary = ? WHERE id = ?');
        $stmt->execute([$isbn, $title, $author, $categoryId, $year, $copiesTotal, $available, $summary, $editId]);
        setFlash('Livre mis à jour avec succès.', 'success');
    } else {
        $stmt = $pdo->prepare('INSERT INTO books (isbn, title, author, category_id, year, copies_total, copies_available, summary) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$isbn, $title, $author, $categoryId, $year, $copiesTotal, $copiesTotal, $summary]);
        setFlash('Livre ajouté à la collection.', 'success');
    }

    header('Location: books.php');
    exit;
}

if ($action === 'delete' && $bookId) {
    try {
        $stmt = $pdo->prepare('DELETE FROM books WHERE id = ?');
        $stmt->execute([$bookId]);
        setFlash('Livre supprimé avec succès.', 'success');
    } catch (PDOException $e) {
        setFlash('Impossible de supprimer le livre. Vérifiez s’il est utilisé par un emprunt.', 'danger');
    }
    header('Location: books.php');
    exit;
}

if ($action === 'edit' && $bookId) {
    $stmt = $pdo->prepare('SELECT * FROM books WHERE id = ?');
    $stmt->execute([$bookId]);
    $book = $stmt->fetch();
}

$search = trim($_GET['search'] ?? '');
$query = 'SELECT bk.*, c.name AS category FROM books bk JOIN categories c ON bk.category_id = c.id';
$params = [];
if ($search !== '') {
    $query .= ' WHERE bk.title LIKE ? OR bk.author LIKE ? OR bk.isbn LIKE ?';
    $pattern = '%' . $search . '%';
    $params = [$pattern, $pattern, $pattern];
}
$query .= ' ORDER BY bk.title ASC';
$books = $pdo->prepare($query);
$books->execute($params);
$books = $books->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livres | <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="dashboard-bg">
    <?php $active = 'books'; include __DIR__ . '/partials/nav.php'; ?>
    <main class="container py-5 mt-5">
        <div class="row g-4">
            <div class="col-xl-7">
                <div class="dashboard-card p-4 rounded-4">
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between mb-4 gap-3">
                        <div>
                            <h2 class="h4 text-white mb-1">Gestion des livres</h2>
                            <p class="text-white-50 mb-0">Ajouter, modifier ou supprimer des titres de la bibliothèque.</p>
                        </div>
                        <form class="w-100 w-md-auto" method="GET" action="books.php">
                            <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden">
                                <input type="text" name="search" class="form-control border-0" placeholder="Rechercher un livre" value="<?= htmlspecialchars($search) ?>">
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
                                    <th>Titre</th>
                                    <th>Auteur</th>
                                    <th>Catégorie</th>
                                    <th class="text-center">Dispo</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($books)): ?>
                                    <tr><td colspan="5" class="text-center text-white-50 py-4">Aucun livre trouvé.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($books as $row): ?>
                                        <tr class="border-top border-white border-opacity-10">
                                            <td><?= htmlspecialchars($row['title']) ?></td>
                                            <td><?= htmlspecialchars($row['author']) ?></td>
                                            <td><?= htmlspecialchars($row['category']) ?></td>
                                            <td class="text-center text-warning fw-semibold"><?= htmlspecialchars($row['copies_available']) ?>/<?= htmlspecialchars($row['copies_total']) ?></td>
                                            <td class="text-end">
                                                <a href="books.php?action=edit&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-light me-2">Modifier</a>
                                                <a href="books.php?action=delete&id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce livre ?');">Supprimer</a>
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
                    <h3 class="h5 text-white mb-3"><?= $book ? 'Modifier le livre' : 'Ajouter un nouveau livre' ?></h3>
                    <form method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="book_id" value="<?= $book ? $book['id'] : '' ?>">
                        <div class="mb-3">
                            <label class="form-label text-white">ISBN</label>
                            <input type="text" name="isbn" class="form-control form-control-lg" value="<?= htmlspecialchars($book['isbn'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Titre</label>
                            <input type="text" name="title" class="form-control form-control-lg" value="<?= htmlspecialchars($book['title'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Auteur</label>
                            <input type="text" name="author" class="form-control form-control-lg" value="<?= htmlspecialchars($book['author'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Catégorie</label>
                            <select name="category_id" class="form-select form-select-lg" required>
                                <option value="">Sélectionner une catégorie</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= isset($book['category_id']) && $book['category_id'] == $category['id'] ? 'selected' : '' ?>><?= htmlspecialchars($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-white">Année</label>
                                <input type="number" name="year" class="form-control form-control-lg" value="<?= htmlspecialchars($book['year'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white">Copies totales</label>
                                <input type="number" name="copies_total" class="form-control form-control-lg" value="<?= htmlspecialchars($book['copies_total'] ?? 5) ?>" min="1" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Résumé</label>
                            <textarea name="summary" rows="4" class="form-control form-control-lg"><?= htmlspecialchars($book['summary'] ?? '') ?></textarea>
                        </div>
                        <button class="btn btn-warning btn-lg w-100"><?= $book ? 'Enregistrer les modifications' : 'Ajouter le livre' ?></button>
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
