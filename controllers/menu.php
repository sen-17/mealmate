<?php

    date_default_timezone_set('Asia/Jakarta');

    // mapping ke dalam bahasa indo
    $hariIndo  = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
    $bulanIndo = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    // tgl hari ini
    $hari  = $hariIndo[date('l')];
    $tgl   = date('d');
    $bulan = $bulanIndo[(int)date('n')];
    $tahun = date('Y');
    $tanggalHariIni = "{$hari}, {$tgl} {$bulan} {$tahun}";

    // master list 1 minggu
    $rawMenus = [
        'Senin' => [
            ['slot' => 'Pagi', 'nama' => 'Nasi Uduk Spesial', 'gambar' => 'nasi-lemak.webp', 'rating' => 5, 'reviews' => 30],
            ['slot' => 'Siang', 'nama' => 'Ayam Bakar Madu', 'gambar' => 'nasi-lemak.webp', 'rating' => 4, 'reviews' => 15],
            ['slot' => 'Malam', 'nama' => 'Tumis Buncis Sapi', 'gambar' => 'nasi-lemak.webp', 'rating' => 4.5, 'reviews' => 8]
        ],
        'Selasa' => [
            ['slot' => 'Pagi', 'nama' => 'Lontong Sayur', 'gambar' => 'nasi-lemak.webp', 'rating' => 5, 'reviews' => 20],
            ['slot' => 'Siang', 'nama' => 'Ikan Goreng Kuning', 'gambar' => 'nasi-lemak.webp', 'rating' => 4, 'reviews' => 11],
            ['slot' => 'Malam', 'nama' => 'Sup Ayam Bening', 'gambar' => 'nasi-lemak.webp', 'rating' => 4.5, 'reviews' => 9]
        ],
        'Rabu' => [
            ['slot' => 'Pagi', 'nama' => 'Nasi Kuning Komplit', 'gambar' => 'nasi-lemak.webp', 'rating' => 5, 'reviews' => 25],
            ['slot' => 'Siang', 'nama' => 'Rendang Sapi', 'gambar' => 'nasi-lemak.webp', 'rating' => 5, 'reviews' => 42],
            ['slot' => 'Malam', 'nama' => 'Capcay Seafood', 'gambar' => 'nasi-lemak.webp', 'rating' => 4, 'reviews' => 14]
        ],
        'Kamis' => [
            ['slot' => 'Pagi', 'nama' => 'Bubur Ayam', 'gambar' => 'nasi-lemak.webp', 'rating' => 4.5, 'reviews' => 19],
            ['slot' => 'Siang', 'nama' => 'Ayam Geprek', 'gambar' => 'nasi-lemak.webp', 'rating' => 5, 'reviews' => 35],
            ['slot' => 'Malam', 'nama' => 'Soto Ayam', 'gambar' => 'nasi-lemak.webp', 'rating' => 4, 'reviews' => 16]
        ],
        'Jumat' => [
            ['slot' => 'Pagi', 'nama' => 'Nasi Goreng Kampung', 'gambar' => 'nasi-lemak.webp', 'rating' => 5, 'reviews' => 28],
            ['slot' => 'Siang', 'nama' => 'Ikan Bakar Jimbaran', 'gambar' => 'nasi-lemak.webp', 'rating' => 4.5, 'reviews' => 22],
            ['slot' => 'Malam', 'nama' => 'Fu Yung Hai', 'gambar' => 'nasi-lemak.webp', 'rating' => 4, 'reviews' => 10]
        ],
        'Sabtu' => [
            ['slot' => 'Pagi', 'nama' => 'Roti Bakar Telur', 'gambar' => 'nasi-lemak.webp', 'rating' => 4.5, 'reviews' => 14],
            ['slot' => 'Siang', 'nama' => 'Mie Goreng Spesial', 'gambar' => 'nasi-lemak.webp', 'rating' => 5, 'reviews' => 31],
            ['slot' => 'Malam', 'nama' => 'Sate Ayam', 'gambar' => 'nasi-lemak.webp', 'rating' => 5, 'reviews' => 27]
        ],
        'Minggu' => [
            ['slot' => 'Pagi', 'nama' => 'Pancake Nutella', 'gambar' => 'nasi-lemak.webp', 'rating' => 5, 'reviews' => 18],
            ['slot' => 'Siang', 'nama' => 'Nasi Liwet', 'gambar' => 'nasi-lemak.webp', 'rating' => 4.5, 'reviews' => 21],
            ['slot' => 'Malam', 'nama' => 'Bistik Sapi', 'gambar' => 'nasi-lemak.webp', 'rating' => 5, 'reviews' => 15]
        ]
    ];

    // menu hari ini
    $hariIni = $hariIndo[date('l')];
    $dailyMenus = $rawMenus[$hariIni] ?? [];

    // logika untuk ngambil tanggal dalam 1 minggu
    $weeklyMenus = [];
    $startOfWeek = new DateTime('monday this week'); // dapat tanggal hari senin-minggu

    $listHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    foreach ($listHari as $index => $namaHari) {
        $dateCurrent = clone $startOfWeek;
        $dateCurrent->modify("+{$index} days");

        $tglAngka  = $dateCurrent->format('j');
        $blnAngka  = (int)$dateCurrent->format('n');
        $tahunAngka = $dateCurrent->format('Y');

        $tglFormatted = "{$namaHari}, {$tglAngka} {$bulanIndo[$blnAngka]} {$tahunAngka}";

        $weeklyMenus[$namaHari] = [
            'tanggal' => $tglFormatted,
            'menus'   => $rawMenus[$namaHari] ?? []
        ];
    }

    // render ke view
    render("customer/menu", [
        'title'          => 'Menu',
        'tanggalHariIni' => $tanggalHariIni,
        'dailyMenus'     => $dailyMenus,
        'weeklyMenus'    => $weeklyMenus
    ]);