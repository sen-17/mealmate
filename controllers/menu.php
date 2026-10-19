<?php

    date_default_timezone_set('Asia/Jakarta');

    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../models/MenuModel.php';

    $menuModel = new MenuModel(getDBConnection());

    // mapping ke dalam bahasa indo
    $hariIndo  = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
    $bulanIndo = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    // tgl hari ini
    $hari  = $hariIndo[date('l')];
    $tgl   = date('d');
    $bulan = $bulanIndo[(int)date('n')];
    $tahun = date('Y');
    $tanggalHariIni = "{$hari}, {$tgl} {$bulan} {$tahun}";

     // menu hari ini (dari database)
    $dailyMenus = $menuModel->getMenuByDate(date('Y-m-d'));

    // logika untuk ngambil tanggal dalam 1 minggu
    $weeklyMenus = [];
    $startOfWeek = new DateTime('monday this week'); // dapat tanggal hari senin-minggu
    $endOfWeek   = (clone $startOfWeek)->modify('+6 days');

    // ambil jadwal 1 minggu sekaligus (1 query), dikelompokkan per tanggal
    $menusByDate = $menuModel->getMenuByDateRange(
        $startOfWeek->format('Y-m-d'),
        $endOfWeek->format('Y-m-d')
    );

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
            'menus'   => $menusByDate[$dateCurrent->format('Y-m-d')] ?? []
        ];
    }

    // render ke view
    render("customer/menu", [
        'title'          => 'Menu',
        'tanggalHariIni' => $tanggalHariIni,
        'dailyMenus'     => $dailyMenus,
        'weeklyMenus'    => $weeklyMenus
    ]);