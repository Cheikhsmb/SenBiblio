<?php
require_once __DIR__ . '/config.php';

$alert = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $alert = ['type' => 'warning', 'text' => 'Merci de remplir tous les champs.'];
    } else {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT id, name, password_hash FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header('Location: dashboard.php');
            exit;
        }

        $alert = ['type' => 'danger', 'text' => 'Email ou mot de passe invalide.'];
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion | <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/p5.js/1.6.0/p5.min.js"></script>
</head>
<body class="login-bg">
    <div id="p5-login-canvas"></div>
    <main class="login-page d-flex align-items-center justify-content-center min-vh-100">
        <div class="login-card shadow-lg rounded-4 glass-card border border-white border-opacity-10">
            <div class="card-body p-4">
                <div class="mb-4 text-center">
                    <h1 class="h3 text-white fw-bold">Bibliothèque Numérique</h1>
                    <p class="text-white-50 mb-0">Espace administration - Université du Sénégal</p>
                </div>

                <?php if ($alert): ?>
                    <div class="alert alert-<?= htmlspecialchars($alert['type']) ?> alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($alert['text']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="email" class="form-label text-white">Adresse email</label>
                        <input type="email" name="email" id="email" class="form-control form-control-lg" placeholder="admin@senlibrary.edu" required>
                        <div class="invalid-feedback">Veuillez saisir un email valide.</div>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label text-white">Mot de passe</label>
                        <input type="password" name="password" id="password" class="form-control form-control-lg" placeholder="Votre mot de passe" required>
                        <div class="invalid-feedback">Le mot de passe est requis.</div>
                    </div>
                    <button type="submit" class="btn btn-warning btn-lg w-100 shadow-sm">Se connecter</button>
                </form>
                <p class="mt-4 text-center text-white-50 small">Connexion d&apos;administrateur | Email: admin@senlibrary.edu | Mot de passe: admin123</p>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/p5-login.js"></script>
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
