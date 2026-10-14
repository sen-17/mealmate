<?php
$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'home':
        if (file_exists('./controllers/home.php')) {
            require_once './controllers/home.php';
        } else {
            require_once './views/layouts/header.php';
            echo "<main class='container my-5 text-center'>
                    <h2 class='text-muted'9>Selamat Datang di MealMate</h2>
                    <p class='text-muted'>Router aktif. Buat file <code>controllers/home.php</code> untuk mengontrol halaman ini.</p>
                  </main>";
            require_once './views/layouts/footer.php';
        }
        break;

    // Contoh rute berikutnya:
    // case 'menu':
    //     require_once './controllers/menu.php';
    //     break;

    default:
        http_response_code(404);
        require_once './views/layouts/header.php';
        echo "<main class='container my-5 text-center'>
                <div class='py-5'>
                    <h1 class='display-1 fw-bold text-danger'>404</h1>
                    <h3 class='fw-semibold'>Halaman Tidak Ditemukan</h3>
                    <p class='text-muted'>Halaman '{$page}' belum dibuat atau tidak valid.</p>
                    <a href='index.php' class='btn btn-success mt-3'>Kembali ke Beranda</a>
                </div>
              </main>";
        require_once './views/layouts/footer.php';
        break;
}