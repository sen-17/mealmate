<?php

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

    $packages = [
        [
            'img' => './assets/img/nasi-lemak.webp',
            'title' => 'Paket Mingguan',
            'duration_days' => '5 hari',
            'applicable_days' => 'Senin - Jumat',
            'description' => 'Makan siang lengkap tiap hari kerja dengan menu berganti, cocok untuk mahasiswa dan pekerja',
            'price' => '125.000'
        ],

        [
            'img' => './assets/img/nasi-lemak.webp',
            'title' => 'Paket Mingguan',
            'duration_days' => '5 hari',
            'applicable_days' => 'Senin - Jumat',
            'description' => 'Makan siang lengkap tiap hari kerja dengan menu berganti, cocok untuk mahasiswa dan pekerja',
            'price' => '125.000'
        ],

        [
            'img' => './assets/img/nasi-lemak.webp',
            'title' => 'Paket Mingguan',
            'duration_days' => '5 hari',
            'applicable_days' => 'Senin - Jumat',
            'description' => 'Makan siang lengkap tiap hari kerja dengan menu berganti, cocok untuk mahasiswa dan pekerja',
            'price' => '125.000'
        ],

        [
            'img' => './assets/img/nasi-lemak.webp',
            'title' => 'Paket Mingguan',
            'duration_days' => '5 hari',
            'applicable_days' => 'Senin - Jumat',
            'description' => 'Makan siang lengkap tiap hari kerja dengan menu berganti, cocok untuk mahasiswa dan pekerja',
            'price' => '125.000'
        ],
    ];

    $reviews = [
        [
            'name' => 'Luna Maya Lestari',
            'initial_name' => 'LM',
            'review' => 'Rasanya seperti masakan rumah sendiri. Harganya juga terjangkau. Recommended!!!!',
            'rating' => '4.5',
        ],

        [
            'name' => 'Luna Maya Lestari',
            'initial_name' => 'LM',
            'review' => 'Rasanya seperti masakan rumah sendiri. Harganya juga terjangkau. Recommended!!!!',
            'rating' => '4',
        ],

        [
            'name' => 'Luna Maya Lestari',
            'initial_name' => 'LM',
            'review' => 'Rasanya seperti masakan rumah sendiri. Harganya juga terjangkau. Recommended!!!!',
            'rating' => '3.2',
        ],

        [
            'name' => 'Luna Maya Lestari',
            'initial_name' => 'LM',
            'review' => 'Rasanya seperti masakan rumah sendiri. Harganya juga terjangkau. Recommended!!!!',
            'rating' => '3',
        ],
    ];

    render("customer/home", [
        'title' => 'Beranda - Mealmate',
        'page' => 'home',
        'features' => $features,
        'steps' => $steps,
        'packages' => $packages,
        'reviews' => $reviews,
    ]);
