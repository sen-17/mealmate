<?php
require_once __DIR__ . '/../models/ReportModel.php';
require_once __DIR__ . '/../models/PackageModel.php';

requireRole('Admin');

$preset = trim((string) ($_GET['period'] ?? '12w'));
$customStart = isset($_GET['start_date']) ? trim((string) $_GET['start_date']) : null;
$customEnd = isset($_GET['end_date']) ? trim((string) $_GET['end_date']) : null;
$statusFilter = trim((string) ($_GET['status'] ?? ''));
$packageIdFilter = (int) ($_GET['package_id'] ?? 0);
$searchQuery = trim((string) ($_GET['q'] ?? ''));
$exportFormat = strtolower(trim((string) ($_GET['export'] ?? '')));

$sortMap = getReportTransactionSortMap();
$rawSort = $_GET['sort'] ?? 'date';
$sort = (is_string($rawSort) && isset($sortMap[$rawSort])) ? $rawSort : 'date';

$defaultDir = in_array($sort, ['date', 'order_id', 'price'], true) ? 'desc' : 'asc';
$rawDir = $_GET['dir'] ?? $defaultDir;
$dir = (is_string($rawDir) && in_array(strtolower($rawDir), ['asc', 'desc'], true)) ? strtolower($rawDir) : $defaultDir;

$periodInfo = resolveReportPeriod($preset, $customStart, $customEnd);

if ($periodInfo['error'] !== null) {
    setFlash('danger', $periodInfo['error']);
    redirect('index.php?page=admin-reports');
}

$startDate = $periodInfo['start_date'];
$endDate = $periodInfo['end_date'];

if (in_array($exportFormat, ['csv', 'excel'], true)) {
    $allMatchingTransactions = getReportTransactions(
        $startDate,
        $endDate,
        $statusFilter,
        $packageIdFilter,
        $searchQuery,
        null,
        null,
        $sort,
        $dir
    );

    if (empty($allMatchingTransactions)) {
        setFlash('danger', 'Tidak ada data penjualan pada periode yang dipilih untuk diekspor.');
        $backQuery = http_build_query([
            'page' => 'admin-reports',
            'period' => $periodInfo['preset'],
            'start_date' => $periodInfo['preset'] === 'custom' ? $startDate : null,
            'end_date' => $periodInfo['preset'] === 'custom' ? $endDate : null,
            'status' => $statusFilter !== '' ? $statusFilter : null,
            'package_id' => $packageIdFilter > 0 ? $packageIdFilter : null,
            'q' => $searchQuery !== '' ? $searchQuery : null,
            'sort' => $sort,
            'dir' => $dir,
        ]);
        redirect('index.php?' . $backQuery);
    }

    $dateSlug = $startDate . '_sd_' . $endDate;
    if ($exportFormat === 'excel') {
        streamTransactionsXlsx($allMatchingTransactions, "laporan_penjualan_mealmate_{$dateSlug}.xlsx");
    }
    streamTransactionsCsv($allMatchingTransactions, "laporan_penjualan_mealmate_{$dateSlug}.csv");
}

$kpi = getReportKpiSummary($startDate, $endDate, $statusFilter, $packageIdFilter, $searchQuery);

$perPage = 10;
$totalTransactions = $kpi['total_transactions'];
$totalPages = max(1, (int) ceil($totalTransactions / $perPage));
$currentPageNum = max(1, min($totalPages, (int) ($_GET['p'] ?? 1)));
$offset = ($currentPageNum - 1) * $perPage;

$transactions = getReportTransactions(
    $startDate,
    $endDate,
    $statusFilter,
    $packageIdFilter,
    $searchQuery,
    $perPage,
    $offset,
    $sort,
    $dir
);

$buildReportUrl = static function (array $override = []) use ($periodInfo, $startDate, $endDate, $statusFilter, $packageIdFilter, $searchQuery, $sort, $dir): string {
    $params = array_merge([
        'page' => 'admin-reports',
        'period' => $periodInfo['preset'],
        'start_date' => $periodInfo['preset'] === 'custom' ? $startDate : null,
        'end_date' => $periodInfo['preset'] === 'custom' ? $endDate : null,
        'status' => $statusFilter !== '' ? $statusFilter : null,
        'package_id' => $packageIdFilter > 0 ? $packageIdFilter : null,
        'q' => $searchQuery !== '' ? $searchQuery : null,
        'sort' => $sort,
        'dir' => $dir,
    ], $override);
    return 'index.php?' . http_build_query($params);
};

$columnDefs = [
    'order_id' => ['label' => 'Order ID', 'width' => '120px'],
    'customer' => ['label' => 'Nama Pelanggan', 'width' => ''],
    'date' => ['label' => 'Tanggal', 'width' => '150px'],
    'package' => ['label' => 'Paket', 'width' => ''],
    'price' => ['label' => 'Total Harga', 'width' => '160px'],
    'status' => ['label' => 'Status', 'width' => '180px'],
];

$columns = [];
foreach ($columnDefs as $key => $meta) {
    $isActive = ($sort === $key);
    $firstDir = in_array($key, ['date', 'order_id', 'price'], true) ? 'desc' : 'asc';
    $nextDir = $isActive ? ($dir === 'asc' ? 'desc' : 'asc') : $firstDir;

    $columns[$key] = [
        'label' => $meta['label'],
        'width' => $meta['width'],
        'url' => $buildReportUrl(['sort' => $key, 'dir' => $nextDir, 'p' => 1]),
        'active' => $isActive,
        'icon' => $isActive ? ($dir === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down') : 'bi-arrow-down-up',
        'aria_sort' => $isActive ? ($dir === 'asc' ? 'ascending' : 'descending') : 'none',
    ];
}

$sortChoices = [
    ['label' => 'Tanggal Terbaru', 'sort' => 'date', 'dir' => 'desc'],
    ['label' => 'Tanggal Terlama', 'sort' => 'date', 'dir' => 'asc'],
    ['label' => 'Total Harga Tertinggi', 'sort' => 'price', 'dir' => 'desc'],
    ['label' => 'Total Harga Terendah', 'sort' => 'price', 'dir' => 'asc'],
    ['label' => 'Nama Pelanggan A-Z', 'sort' => 'customer', 'dir' => 'asc'],
    ['label' => 'Paket A-Z', 'sort' => 'package', 'dir' => 'asc'],
    ['label' => 'Status A-Z', 'sort' => 'status', 'dir' => 'asc'],
];

$sortOptions = array_map(static fn (array $c): array => [
    'label' => $c['label'],
    'url' => $buildReportUrl(['sort' => $c['sort'], 'dir' => $c['dir'], 'p' => 1]),
    'selected' => ($sort === $c['sort'] && $dir === $c['dir']),
], $sortChoices);

$allPackages = getAllPackages();

render('admin/reports', [
    'title' => 'Laporan Transaksi - MealMate',
    'periodInfo' => $periodInfo,
    'statusFilter' => $statusFilter,
    'packageIdFilter' => $packageIdFilter,
    'searchQuery' => $searchQuery,
    'sort' => $sort,
    'dir' => $dir,
    'columns' => $columns,
    'sortOptions' => $sortOptions,
    'kpi' => $kpi,
    'transactions' => $transactions,
    'allPackages' => $allPackages,
    'totalTransactions' => $totalTransactions,
    'totalPages' => $totalPages,
    'currentPageNum' => $currentPageNum,
    'perPage' => $perPage,
], 'admin');
