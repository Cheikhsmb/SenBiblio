<?php
/**
 * Page de connexion (Login)
 * Gère l'authentification des administrateurs et membres du personnel
 */

require_once __DIR__ . '/config.php';

// Si déjà connecté, rediriger vers le tableau de bord
if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';

    // Vérification du jeton CSRF
    if (!verifyCsrf($csrfToken)) {
        $error = 'Jeton de sécurité invalide. Veuillez réessayer.';
    } elseif ($email === '' || $password === '') {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            setFlash('Connexion réussie. Bienvenue ' . htmlspecialchars($user['name']) . ' !', 'success');
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Identifiants incorrects.';
        }
    }
}

$csrfToken = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion | <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<?php
$flash = flashMessage();
$flashJson = $flash ? json_encode($flash) : 'null';
?>
<body class="login-bg" data-flash="<?= htmlspecialchars($flashJson) ?>">
    <div id="p5-login-canvas"></div>
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>
    
    <div class="d-flex align-items-center justify-content-center min-vh-100 login-page">
        <div class="login-card glass-card p-5 w-100 shadow-lg">
            <div class="text-center mb-4">
                <span class="fs-1">📚</span>
                <h1 class="h3 text-white fw-bold mt-2"><?= APP_NAME ?></h1>
                <p class="text-white-50">Système de gestion de bibliothèque</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
            <?php endif; ?>

            <form method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                
                <div class="mb-3">
                    <label class="form-label text-white">Adresse Email</label>
                    <input type="email" name="email" class="form-control form-control-lg" placeholder="admin@library.local" required>
                    <div class="invalid-feedback">Veuillez entrer une adresse email valide.</div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label text-white">Mot de passe</label>
                    <input type="password" name="password" class="form-control form-control-lg" placeholder="••••••••" required>
                    <div class="invalid-feedback">Veuillez entrer votre mot de passe.</div>
                </div>
                
                <button type="submit" class="btn btn-warning btn-lg w-100 text-dark fw-semibold">Se connecter</button>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/p5.js/1.6.0/p5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/p5-login.js"></script>
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
