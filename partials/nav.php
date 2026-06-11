<?php
/**
 * Navigation bar partial for the library management pages.
 * The variable $active should be set to the current page slug.
 */
?>
<header class="position-absolute top-0 start-0 end-0 p-3 zindex-3">
    <div class="container">
        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
            <div>
                <span class="badge bg-warning text-dark">Bibliothèque Sénégal</span>
                <p class="mb-0 text-white-50 small">Gestion universitaire moderne</p>
            </div>
            <nav class="nav gap-2">
                <a href="dashboard.php" class="nav-link px-3 py-2 rounded-3 <?= ($active === 'dashboard') ? 'bg-white text-dark' : 'text-white-75' ?>">Tableau de bord</a>
                <a href="books.php" class="nav-link px-3 py-2 rounded-3 <?= ($active === 'books') ? 'bg-white text-dark' : 'text-white-75' ?>">Livres</a>
                <a href="students.php" class="nav-link px-3 py-2 rounded-3 <?= ($active === 'students') ? 'bg-white text-dark' : 'text-white-75' ?>">Étudiants</a>
                <a href="borrowings.php" class="nav-link px-3 py-2 rounded-3 <?= ($active === 'borrowings') ? 'bg-white text-dark' : 'text-white-75' ?>">Emprunts</a>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Déconnexion</a>
            </nav>
        </div>
    </div>
</header>
