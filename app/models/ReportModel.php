<?php
require_once __DIR__ . '/../config/database.php';

function validateReportDateRange(?string $startDate, ?string $endDate): ?string {
    if ($startDate === null || $endDate === null || $startDate === '' || $endDate === '') {
        return 'Tanggal mulai dan tanggal akhir wajib diisi.';
    }

    $startObj = DateTimeImmutable::createFromFormat('Y-m-d', $startDate);
    $endObj = DateTimeImmutable::createFromFormat('Y-m-d', $endDate);

    if (!$startObj || $startObj->format('Y-m-d') !== $startDate || !$endObj || $endObj->format('Y-m-d') !== $endDate) {
        return 'Format rentang tanggal tidak valid.';
    }

    if ($endDate < $startDate) {
        return 'Rentang tanggal tidak valid: Tanggal akhir tidak boleh lebih awal dari tanggal mulai.';
    }

    return null;
}

function resolveReportPeriod(string $preset, ?string $customStart = null, ?string $customEnd = null): array {
    $today = new DateTimeImmutable('today');
    $defaultEnd = $today->format('Y-m-d');
    $defaultStart = $today->modify('-83 days')->format('Y-m-d');

    if ($preset === '4w') {
        return [
            'preset' => '4w',
            'label' => '4 Minggu Terakhir',
            'start_date' => $today->modify('-27 days')->format('Y-m-d'),
            'end_date' => $defaultEnd,
            'error' => null,
        ];
    }

    if ($preset === 'month') {
        return [
            'preset' => 'month',
            'label' => 'Bulan Ini',
            'start_date' => $today->format('Y-m-01'),
            'end_date' => $today->format('Y-m-t'),
            'error' => null,
        ];
    }

    if ($preset === 'all') {
        return [
            'preset' => 'all',
            'label' => 'Semua Periode',
            'start_date' => '2020-01-01',
            'end_date' => $today->modify('+365 days')->format('Y-m-d'),
            'error' => null,
        ];
    }

    if ($preset === 'custom') {
        $err = validateReportDateRange($customStart, $customEnd);
        if ($err !== null) {
            return [
                'preset' => '12w',
                'label' => '12 Minggu Terakhir',
                'start_date' => $defaultStart,
                'end_date' => $defaultEnd,
                'error' => $err,
            ];
        }
        return [
            'preset' => 'custom',
            'label' => 'Periode Kustom',
            'start_date' => (string) $customStart,
            'end_date' => (string) $customEnd,
            'error' => null,
        ];
    }

    return [
        'preset' => '12w',
        'label' => '12 Minggu Terakhir',
        'start_date' => $defaultStart,
        'end_date' => $defaultEnd,
        'error' => null,
    ];
}

function getDashboardSummary(string $startDate, string $endDate): array {
    $pdo = getDBConnection();

    $stmt = $pdo->prepare(
        "SELECT
            COUNT(*) AS total_orders,
            COALESCE(SUM(CASE WHEN status IN ('Aktif', 'Selesai') THEN total_price ELSE 0 END), 0) AS total_revenue,
            COALESCE(SUM(CASE WHEN status = 'Aktif' THEN 1 ELSE 0 END), 0) AS active_subscriptions,
            COALESCE(SUM(CASE WHEN status = 'Menunggu Verifikasi' THEN 1 ELSE 0 END), 0) AS pending_orders,
            COALESCE(SUM(CASE WHEN status IN ('Aktif', 'Selesai') THEN 1 ELSE 0 END), 0) AS successful_orders
         FROM orders
         WHERE DATE(created_at) BETWEEN :start_date AND :end_date"
    );
    $stmt->execute([
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]);
    $metrics = $stmt->fetch() ?: [
        'total_orders' => 0,
        'total_revenue' => 0,
        'active_subscriptions' => 0,
        'pending_orders' => 0,
        'successful_orders' => 0,
    ];

    $stmtGlobal = $pdo->query(
        "SELECT
            COALESCE(SUM(CASE WHEN status = 'Aktif' THEN 1 ELSE 0 END), 0) AS current_active_subscriptions,
            COALESCE(SUM(CASE WHEN status = 'Menunggu Verifikasi' THEN 1 ELSE 0 END), 0) AS current_pending_orders,
            COALESCE(SUM(CASE WHEN status = 'Aktif' AND DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 ELSE 0 END), 0) AS new_active_this_week
         FROM orders"
    );
    $globalCounts = $stmtGlobal->fetch() ?: [
        'current_active_subscriptions' => 0,
        'current_pending_orders' => 0,
        'new_active_this_week' => 0,
    ];

    $stmtBest = $pdo->prepare(
        "SELECT p.package_name, COUNT(o.order_id) AS total_sold
         FROM orders o
         JOIN packages p ON p.package_id = o.package_id
         WHERE DATE(o.created_at) BETWEEN :start_date AND :end_date
           AND o.status IN ('Aktif', 'Selesai')
         GROUP BY p.package_id, p.package_name
         ORDER BY total_sold DESC, p.package_id ASC
         LIMIT 1"
    );
    $stmtBest->execute([
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]);
    $bestPkg = $stmtBest->fetch();

    return [
        'total_orders' => (int) $metrics['total_orders'],
        'total_revenue' => (int) $metrics['total_revenue'],
        'active_subscriptions' => (int) $metrics['active_subscriptions'],
        'pending_orders' => (int) $metrics['pending_orders'],
        'successful_orders' => (int) $metrics['successful_orders'],
        'current_active_subscriptions' => (int) $globalCounts['current_active_subscriptions'],
        'current_pending_orders' => (int) $globalCounts['current_pending_orders'],
        'new_active_this_week' => (int) $globalCounts['new_active_this_week'],
        'best_package' => $bestPkg ? (string) $bestPkg['package_name'] : 'Belum ada data',
        'best_package_sold' => $bestPkg ? (int) $bestPkg['total_sold'] : 0,
    ];
}

function getWeeklySalesTrend(string $startDate, string $endDate): array {
    $pdo = getDBConnection();

    $stmt = $pdo->prepare(
        "SELECT
            DATE(DATE_SUB(DATE(created_at), INTERVAL WEEKDAY(created_at) DAY)) AS week_start,
            COALESCE(SUM(CASE WHEN status IN ('Aktif', 'Selesai') THEN total_price ELSE 0 END), 0) AS revenue,
            COUNT(*) AS order_count
         FROM orders
         WHERE DATE(created_at) BETWEEN :start_date AND :end_date
         GROUP BY week_start
         ORDER BY week_start ASC"
    );
    $stmt->execute([
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]);
    $rows = $stmt->fetchAll();

    $byWeek = [];
    foreach ($rows as $row) {
        $byWeek[$row['week_start']] = [
            'revenue' => (int) $row['revenue'],
            'order_count' => (int) $row['order_count'],
        ];
    }

    $startObj = new DateTimeImmutable($startDate);
    $endObj = new DateTimeImmutable($endDate);
    $diffDays = (int) $startObj->diff($endObj)->format('%a');

    if ($diffDays > 120 && !empty($rows)) {
        $firstRowWeek = new DateTimeImmutable($rows[0]['week_start']);
        $lastRowWeek = new DateTimeImmutable($rows[count($rows) - 1]['week_start']);
        $startMonday = $firstRowWeek->modify('-14 days');
        $endMonday = $lastRowWeek;
    } else {
        $effectiveEnd = $endObj > new DateTimeImmutable('today') ? new DateTimeImmutable('today') : $endObj;
        $endMonday = $effectiveEnd->modify('-' . ((int) $effectiveEnd->format('N') - 1) . ' days');
        $bucketCount = ($diffDays <= 31) ? 4 : 6;
        $startMonday = $endMonday->modify('-' . (($bucketCount - 1) * 7) . ' days');
    }

    $monthsShort = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
        5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
        9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
    ];

    $points = [];
    $cursor = $startMonday;
    $guard = 0;
    while ($cursor <= $endMonday && $guard < 16) {
        $key = $cursor->format('Y-m-d');
        $day = (int) $cursor->format('j');
        $month = $monthsShort[(int) $cursor->format('n')] ?? '';
        $points[] = [
            'week_start' => $key,
            'label' => "{$day} {$month}",
            'revenue' => $byWeek[$key]['revenue'] ?? 0,
            'order_count' => $byWeek[$key]['order_count'] ?? 0,
        ];
        $cursor = $cursor->modify('+7 days');
        $guard++;
    }

    return $points;
}

function getLatestReviewsSummary(int $limit = 4): array {
    $pdo = getDBConnection();

    $stats = $pdo->query(
        "SELECT COUNT(*) AS total_reviews, COALESCE(ROUND(AVG(rating), 1), 0) AS avg_rating
         FROM reviews"
    )->fetch() ?: ['total_reviews' => 0, 'avg_rating' => 0];

    $stmt = $pdo->prepare(
        "SELECT r.review_id, r.rating, r.comment, r.created_at,
                u.name AS customer_name,
                p.package_name,
                m.menu_name
         FROM reviews r
         JOIN orders o ON o.order_id = r.order_id
         JOIN users u ON u.user_id = o.user_id
         JOIN packages p ON p.package_id = o.package_id
         JOIN menus m ON m.menu_id = r.menu_id
         ORDER BY r.created_at DESC, r.review_id DESC
         LIMIT :limit"
    );
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return [
        'total_reviews' => (int) $stats['total_reviews'],
        'avg_rating' => (float) $stats['avg_rating'],
        'items' => $stmt->fetchAll(),
    ];
}

function getPendingVerificationOrders(int $limit = 5): array {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare(
        "SELECT o.order_id, o.start_date, o.slot, o.total_price, o.status, o.created_at,
                u.name AS customer_name,
                p.package_name,
                pay.payment_id, pay.payment_proof
         FROM orders o
         JOIN users u ON u.user_id = o.user_id
         JOIN packages p ON p.package_id = o.package_id
         LEFT JOIN payments pay ON pay.payment_id = (
             SELECT p2.payment_id FROM payments p2
             WHERE p2.order_id = o.order_id
             ORDER BY p2.payment_id DESC LIMIT 1
         )
         WHERE o.status = 'Menunggu Verifikasi'
         ORDER BY o.created_at ASC, o.order_id ASC
         LIMIT :limit"
    );
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function buildReportTransactionFilter(
    string $startDate,
    string $endDate,
    string $status = '',
    int $packageId = 0,
    string $search = ''
): array {
    $conditions = ["DATE(o.created_at) BETWEEN :start_date AND :end_date"];
    $params = [
        'start_date' => $startDate,
        'end_date' => $endDate,
    ];

    $validStatuses = ['Belum Dibayar', 'Menunggu Verifikasi', 'Aktif', 'Selesai', 'Dibatalkan', 'Ditolak'];
    if ($status === 'Berhasil') {
        $conditions[] = "o.status IN ('Aktif', 'Selesai')";
    } elseif ($status !== '' && in_array($status, $validStatuses, true)) {
        $conditions[] = "o.status = :status";
        $params['status'] = $status;
    }

    if ($packageId > 0) {
        $conditions[] = "o.package_id = :package_id";
        $params['package_id'] = $packageId;
    }

    $q = trim($search);
    if ($q !== '') {
        $cleanId = ltrim($q, '#ORDordMm-0');
        $idLike = '%' . ($cleanId !== '' ? $cleanId : $q) . '%';
        $textLike = '%' . $q . '%';
        $conditions[] = "(CAST(o.order_id AS CHAR) LIKE :q_id OR u.name LIKE :q_name OR p.package_name LIKE :q_pkg)";
        $params['q_id'] = $idLike;
        $params['q_name'] = $textLike;
        $params['q_pkg'] = $textLike;
    }

    return ['WHERE ' . implode(' AND ', $conditions), $params];
}

function getReportKpiSummary(
    string $startDate,
    string $endDate,
    string $status = '',
    int $packageId = 0,
    string $search = ''
): array {
    $pdo = getDBConnection();
    [$where, $params] = buildReportTransactionFilter($startDate, $endDate, $status, $packageId, $search);

    $stmt = $pdo->prepare(
        "SELECT
            COUNT(*) AS total_transactions,
            COALESCE(SUM(CASE WHEN o.status IN ('Aktif', 'Selesai') THEN 1 ELSE 0 END), 0) AS successful_count,
            COALESCE(SUM(CASE WHEN o.status IN ('Aktif', 'Selesai') THEN o.total_price ELSE 0 END), 0) AS total_revenue
         FROM orders o
         JOIN users u ON u.user_id = o.user_id
         JOIN packages p ON p.package_id = o.package_id
         {$where}"
    );
    foreach ($params as $k => $v) {
        $stmt->bindValue(":{$k}", $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $stmt->execute();
    $row = $stmt->fetch() ?: ['total_transactions' => 0, 'successful_count' => 0, 'total_revenue' => 0];

    $total = (int) $row['total_transactions'];
    $success = (int) $row['successful_count'];
    $rate = $total > 0 ? round(($success / $total) * 100, 1) : 0.0;

    return [
        'total_transactions' => $total,
        'successful_count' => $success,
        'success_rate' => $rate,
        'total_revenue' => (int) $row['total_revenue'],
    ];
}

function getReportTransactionSortMap(): array {
    return [
        'order_id' => 'o.order_id',
        'customer' => 'u.name',
        'date' => 'o.created_at',
        'package' => 'p.package_name',
        'price' => 'o.total_price',
        'status' => 'o.status',
    ];
}

function getReportTransactions(
    string $startDate,
    string $endDate,
    string $status = '',
    int $packageId = 0,
    string $search = '',
    ?int $limit = 10,
    ?int $offset = 0,
    string $sort = 'date',
    string $dir = 'desc'
): array {
    $pdo = getDBConnection();
    [$where, $params] = buildReportTransactionFilter($startDate, $endDate, $status, $packageId, $search);

    $sortMap = getReportTransactionSortMap();
    $sortCol = $sortMap[$sort] ?? 'o.created_at';
    $sortDir = strtolower($dir) === 'asc' ? 'ASC' : 'DESC';
    $orderBy = "{$sortCol} {$sortDir}, o.order_id DESC";

    $sql = "SELECT o.order_id, o.start_date, o.slot, o.total_price, o.status, o.created_at,
                   u.name AS customer_name, u.email AS customer_email, u.phone_number AS customer_phone,
                   p.package_name, p.duration_days, p.applicable_days,
                   pay.status AS payment_status
            FROM orders o
            JOIN users u ON u.user_id = o.user_id
            JOIN packages p ON p.package_id = o.package_id
            LEFT JOIN payments pay ON pay.payment_id = (
                SELECT p2.payment_id FROM payments p2
                WHERE p2.order_id = o.order_id
                ORDER BY p2.payment_id DESC LIMIT 1
            )
            {$where}
            ORDER BY {$orderBy}";

    if ($limit !== null && $offset !== null) {
        $sql .= " LIMIT :limit OFFSET :offset";
    }

    $stmt = $pdo->prepare($sql);
    foreach ($params as $k => $v) {
        $stmt->bindValue(":{$k}", $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    if ($limit !== null && $offset !== null) {
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    }
    $stmt->execute();
    return $stmt->fetchAll();
}

function streamTransactionsCsv(array $transactions, string $filename): never {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF");

    fputcsv($out, [
        'Order ID',
        'Nama Pelanggan',
        'Email Pelanggan',
        'No. Telepon',
        'Tanggal Transaksi',
        'Paket Langganan',
        'Tanggal Mulai',
        'Slot Antar',
        'Total Harga (Rp)',
        'Status Pesanan',
        'Status Pembayaran',
    ]);

    $totalValidRevenue = 0;
    foreach ($transactions as $row) {
        $orderCode = '#ORD-' . str_pad((string) $row['order_id'], 3, '0', STR_PAD_LEFT);
        $price = (int) $row['total_price'];
        if (in_array($row['status'], ['Aktif', 'Selesai'], true)) {
            $totalValidRevenue += $price;
        }

        fputcsv($out, [
            $orderCode,
            $row['customer_name'],
            $row['customer_email'],
            $row['customer_phone'],
            date('Y-m-d H:i', strtotime((string) $row['created_at'])),
            $row['package_name'],
            $row['start_date'],
            $row['slot'],
            $price,
            $row['status'],
            $row['payment_status'] ?? 'Belum Ada',
        ]);
    }

    fputcsv($out, []);
    fputcsv($out, ['Total Transaksi', count($transactions), '', '', '', '', '', 'Total Pendapatan Berhasil (Rp)', $totalValidRevenue, '', '']);
    fclose($out);
    exit;
}

function buildXlsxColumnLetter(int $colIndex): string {
    $letter = '';
    while ($colIndex >= 0) {
        $letter = chr($colIndex % 26 + 65) . $letter;
        $colIndex = intdiv($colIndex, 26) - 1;
    }
    return $letter;
}

function streamTransactionsXlsx(array $transactions, string $filename): never {
    $headers = [
        'Order ID',
        'Nama Pelanggan',
        'Email Pelanggan',
        'No. Telepon',
        'Tanggal Transaksi',
        'Paket Langganan',
        'Tanggal Mulai',
        'Slot Antar',
        'Total Harga (Rp)',
        'Status Pesanan',
        'Status Pembayaran',
    ];

    $rows = [$headers];
    $totalValidRevenue = 0;

    foreach ($transactions as $row) {
        $orderCode = '#ORD-' . str_pad((string) $row['order_id'], 3, '0', STR_PAD_LEFT);
        $price = (int) $row['total_price'];
        if (in_array($row['status'], ['Aktif', 'Selesai'], true)) {
            $totalValidRevenue += $price;
        }

        $rows[] = [
            $orderCode,
            (string) $row['customer_name'],
            (string) $row['customer_email'],
            (string) $row['customer_phone'],
            date('Y-m-d H:i', strtotime((string) $row['created_at'])),
            (string) $row['package_name'],
            (string) $row['start_date'],
            (string) $row['slot'],
            $price,
            (string) $row['status'],
            (string) ($row['payment_status'] ?? 'Belum Ada'),
        ];
    }

    $rows[] = ['', '', '', '', '', '', '', '', '', '', ''];
    $rows[] = ['Total Transaksi', count($transactions), '', '', '', '', '', 'Total Pendapatan Berhasil (Rp)', $totalValidRevenue, '', ''];

    $sheetRowsXml = '';
    foreach ($rows as $rIdx => $rowCells) {
        $rowNum = $rIdx + 1;
        $cellsXml = '';
        foreach ($rowCells as $cIdx => $cellVal) {
            $colLetter = buildXlsxColumnLetter($cIdx);
            $cellRef = $colLetter . $rowNum;
            $styleAttr = ($rowNum === 1) ? ' s="1"' : '';

            if (is_int($cellVal) || is_float($cellVal)) {
                $cellsXml .= '<c r="' . $cellRef . '" t="n"' . $styleAttr . '><v>' . $cellVal . '</v></c>';
            } else {
                $escaped = htmlspecialchars((string) $cellVal, ENT_XML1 | ENT_QUOTES, 'UTF-8');
                $cellsXml .= '<c r="' . $cellRef . '" t="inlineStr"' . $styleAttr . '><is><t xml:space="preserve">' . $escaped . '</t></is></c>';
            }
        }
        $sheetRowsXml .= '<row r="' . $rowNum . '">' . $cellsXml . '</row>';
    }

    $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
        . '<sheetData>' . $sheetRowsXml . '</sheetData>'
        . '</worksheet>';

    $workbookXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
        . '<sheets><sheet name="Laporan Transaksi" sheetId="1" r:id="rId1"/></sheets>'
        . '</workbook>';

    $stylesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
        . '<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><name val="Calibri"/></font></fonts>'
        . '<fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill></fills>'
        . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
        . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
        . '<cellXfs count="2"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/><xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/></cellXfs>'
        . '</styleSheet>';

    $contentTypesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
        . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
        . '<Default Extension="xml" ContentType="application/xml"/>'
        . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
        . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
        . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
        . '</Types>';

    $rootRelsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
        . '</Relationships>';

    $wbRelsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
        . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
        . '</Relationships>';

    $tmpFile = tempnam(sys_get_temp_dir(), 'mm_xlsx_');
    $zip = new ZipArchive();
    $zip->open($tmpFile, ZipArchive::OVERWRITE);
    $zip->addFromString('[Content_Types].xml', $contentTypesXml);
    $zip->addFromString('_rels/.rels', $rootRelsXml);
    $zip->addFromString('xl/workbook.xml', $workbookXml);
    $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRelsXml);
    $zip->addFromString('xl/styles.xml', $stylesXml);
    $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
    $zip->close();

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($tmpFile));
    header('Pragma: no-cache');
    header('Expires: 0');

    readfile($tmpFile);
    @unlink($tmpFile);
    exit;
}
