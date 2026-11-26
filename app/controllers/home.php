<?php
require_once __DIR__ . '/../models/PackageModel.php';

$features = [
    [
        'icon' => './assets/svg/bowl.svg',
        'title' => 'Menu Variatif',
        'desc' => 'Setiap hari ada pilihan menu Lezat dan bergizi, dari masakan rumahan hingga favorit anak muda',
    ],
    [
        'icon' => './assets/svg/security.svg',
        'title' => 'Bahan Berkualitas',
        'desc' => 'Menggunakan bahan segar dan diolah dengan standar kebersihan yang baik.',
    ],
    [
        'icon' => './assets/svg/phone.svg',
        'title' => 'Mudah Digunakan',
        'desc' => 'Pesan, bayar, dan pantau pesanan semua dari satu aplikasi. Praktis dan cepat.',
    ],
    [
        'icon' => './assets/svg/clock.svg',
        'title' => 'On-Time',
        'desc' => 'Pesanan selalu tepat waktu, sesuai jadwal',
    ],
];

$steps = [
    [
        'no' => '1',
        'icon' => './assets/svg/docs.svg',
        'title' => 'Pilih Paket Langganan',
        'desc' => 'Jelajahi pilihan paket katering kami dan temukan durasi yang paling pas untuk kebutuhanmu.',
    ],
    [
        'no' => '2',
        'icon' => './assets/svg/calendar.svg',
        'title' => 'Atur Jadwal',
        'desc' => 'Tentukan tanggal dan waktu pemesanan',
    ],
    [
        'no' => '3',
        'icon' => './assets/svg/card.svg',
        'title' => 'Lakukan Pembayaran',
        'desc' => 'Bayar dengan metode yang tersedia',
    ],
    [
        'no' => '4',
        'icon' => './assets/svg/package.svg',
        'title' => 'Pesanan Diproses',
        'desc' => 'Kami masak dengan penuh cinta dan mengirim tepat waktu',
    ],
];

$dbPackages = getPopularPackages(4);
if (!empty($dbPackages)) {
    $packages = [];
    foreach ($dbPackages as $pkg) {
        $formattedDays = ($pkg['applicable_days'] === 'Senin-Minggu') ? 'Senin - Minggu' : 'Senin - Jumat';
        $img = !empty($pkg['image']) ? './assets/img/' . $pkg['image'] : './assets/img/nasi-lemak.webp';
        $packages[] = [
            'id' => (int) $pkg['package_id'],
            'img' => $img,
            'title' => $pkg['package_name'],
            'duration_days' => $pkg['duration_days'] . ' hari',
            'applicable_days' => $formattedDays,
            'description' => $pkg['description'] ?? '',
            'price' => number_format((float) $pkg['price'], 0, ',', '.'),
        ];
    }
} else {
    $packages = array_fill(0, 4, [
        'img' => './assets/img/nasi-lemak.webp',
        'title' => 'Paket Mingguan',
        'duration_days' => '5 hari',
        'applicable_days' => 'Senin - Jumat',
        'description' => 'Makan siang lengkap tiap hari kerja dengan menu berganti, cocok untuk mahasiswa dan pekerja',
        'price' => '125.000',
    ]);
}
$reviews = [];
try {
    $pdo = getDBConnection();
    $stmtRev = $pdo->query(
        "SELECT r.rating, r.comment, u.name
         FROM reviews r
         JOIN orders o ON o.order_id = r.order_id
         JOIN users u ON u.user_id = o.user_id
         WHERE r.comment IS NOT NULL AND TRIM(r.comment) != ''
         ORDER BY r.created_at DESC, r.review_id DESC
         LIMIT 4"
    );
    foreach ($stmtRev->fetchAll() as $revRow) {
        $reviews[] = [
            'name' => $revRow['name'],
            'initial_name' => getInitials($revRow['name']),
            'review' => $revRow['comment'],
            'rating' => (string) $revRow['rating'],
        ];
    }
} catch (Throwable $e) {
    $reviews = [];
}

if (empty($reviews)) {
    $reviews = array_map(static fn (float $rating): array => [
        'name' => 'Luna Maya Lestari',
        'initial_name' => 'LM',
        'review' => 'Rasanya seperti masakan rumah sendiri. Harganya juga terjangkau. Recommended!!!!',
        'rating' => (string) $rating,
    ], [4.5, 4.0, 3.2, 3.0]);
}

render('customer/home', [
    'title' => 'Beranda - Mealmate',
    'page' => 'home',
    'features' => $features,
    'steps' => $steps,
    'packages' => $packages,
    'reviews' => $reviews,
]);
