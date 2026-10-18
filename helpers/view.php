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

    function renderStars(float $rating, int $max = 5): string {
        $html = '';

        for ($i = 1; $i <= $max; $i++) {
            if ($rating >= $i) {
                $class = 'bi-star-fill';
            } elseif ($rating >= $i - 0.5) {
                $class = 'bi-star-half';
            } else {
                $class = 'bi-star';
            }
            $html .= '<i class="bi ' . $class . '"></i>';
        }
        
        return $html;
    }
