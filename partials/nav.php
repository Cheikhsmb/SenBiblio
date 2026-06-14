<?php
/**
 * Navigation bar partial for the library management pages.
 * The variable $active should be set to the current page slug.
 */
?>
<header class="navbar-wrapper py-3">
    <div class="container">
        <nav class="navbar navbar-expand-lg glass-navbar rounded-4 px-4 py-2 shadow-lg">
            <a class="navbar-brand d-flex align-items-center text-white gap-2" href="dashboard.php">
                <span class="brand-icon"><i class="fa-solid fa-book-open-reader text-warning"></i></span>
                <div>
                    <h5 class="mb-0 fw-bold font-heading text-white" style="font-size: 1.1rem; letter-spacing: 0.5px;"><?= APP_NAME ?></h5>
                    <p class="mb-0 text-white-50 small" style="font-size: 0.72rem;">Gestion de bibliothèque</p>
                </div>
            </a>
            
            <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"><i class="fa-solid fa-bars text-white fs-5"></i></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-1 mt-3 mt-lg-0">
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link custom-nav-link <?= ($active === 'dashboard') ? 'active-link' : '' ?>">
                            <i class="fa-solid fa-chart-pie me-2"></i>Tableau de bord
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="books.php" class="nav-link custom-nav-link <?= ($active === 'books') ? 'active-link' : '' ?>">
                            <i class="fa-solid fa-book me-2"></i>Livres
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="students.php" class="nav-link custom-nav-link <?= ($active === 'students') ? 'active-link' : '' ?>">
                            <i class="fa-solid fa-user-group me-2"></i>Membres
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="borrowings.php" class="nav-link custom-nav-link <?= ($active === 'borrowings') ? 'active-link' : '' ?>">
                            <i class="fa-solid fa-clock-rotate-left me-2"></i>Emprunts
                        </a>
                    </li>
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <a href="logout.php" class="btn btn-outline-warning btn-sm rounded-3 px-3 py-2 logout-btn d-flex align-items-center gap-2">
                            <i class="fa-solid fa-right-from-bracket"></i>Déconnexion
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>
