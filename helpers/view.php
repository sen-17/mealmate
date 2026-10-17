<?php
    function render(string $view, array $data = []): void {
        $viewFile = __DIR__ . '/../views/' . $view . '.php';

        if (!file_exists($viewFile)) {
            http_response_code(500);
            die("View '{$view}' tidak ditemukan");
        };

        extract($data);

        require_once __DIR__ . '/../views/layouts/header.php';
        require $viewFile;
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
