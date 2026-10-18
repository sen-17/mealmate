<nav class="navbar navbar-expand-lg container pt-4">
    <!-- logo -->
    <div class="logo d-flex gap-2 align-items-center">
        <img src="./assets/img/logo.png" alt="logo" width="56px" height="56px" loading="lazy">
        <h2>Mealmate</h2>
    </div>

    <!-- hamburger -->
    <button class="navbar-toggler border-0 shadow-none" type="button"
    data-bs-toggle="offcanvas" data-bs-target="#navMenu"
    aria-controls="navMenu" aria-label="Toggle navigation">
        <i class="bi bi-list"></i>
    </button>

    <!-- menu (offcanvas di mobile dan tab, navbar biasa di desktop) -->
    <div class="offcanvas offcanvas-end offcanvas-lg" tabindex="-1" id="navMenu" aria-labelledby="navMenuLabel">
        <!-- header offcanvas (hanya tampil di mobile and tab) -->
        <div class="offcanvas-header">
            <div class="logo d-flex gap-2 align-items-center" id="navMenuLabel">
                <img src="./assets/img/logo.png" alt="logo" width="40px" height="40px" loading="lazy">
                <h2 class="mb-0 fs-4">Mealmate</h2>
            </div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" data-bs-target="#navMenu" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body d-flex flex-column flex-lg-row align-items-lg-center w-100">
            <ul class="navbar-nav mx-lg-auto gap-3 gap-lg-5 nav-link">
                <li class="nav-item">
                    <a href="" class="text-decoration-none fw-semibold <?= ($page == 'home') ? 'active' : 'non-active'; ?>">Beranda</a>
                </li>
                <li class="nav-item">
                    <a href="" class="text-decoration-none fw-semibold <?= ($page == 'menu') ? 'active' : 'non-active'; ?>">Menu</a>
                </li>
                <li class="nav-item">
                    <a href="" class="text-decoration-none fw-semibold <?= ($page == 'about') ? 'active' : 'non-active'; ?>">About</a>
                </li>
                <li class="nav-item">
                    <a href="" class="text-decoration-none fw-semibold <?= ($page == 'faq') ? 'active' : 'non-active'; ?>">FAQ</a>
                </li>
            </ul>

            <div class="navbar-button d-flex flex-column flex-lg-row gap-3 gap-lg-4 align-items-start align-items-lg-center mt-4 mt-lg-0">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/profile" class="btn">Profil</a>
                    <a href="/logout" class="btn">Keluar</a>
                <?php else: ?>
                    <a href="" class="login-nav"><i class="bi bi-person d-none d-lg-inline-block"></i> Masuk</a>
                    <a href="" class="order-nav btn">Mulai Pesan</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>