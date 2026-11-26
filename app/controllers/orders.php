<?php
require_once __DIR__ . '/../models/OrderModel.php';

requireRole('Pelanggan');

$userId = (int) $_SESSION['user_id'];

$rawSearch = $_GET['q'] ?? '';
$search = is_string($rawSearch) ? trim($rawSearch) : '';
if (mb_strlen($search) > 100) {
    $search = mb_substr($search, 0, 100);
}

$perPage = 4;
$totalOrders = countCustomerOrders($userId, $search);
$totalPages = max(1, (int) ceil($totalOrders / $perPage));
$rawPage = filter_var($_GET['p'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 2147483647]]);
$currentPageNum = max(1, min($totalPages, $rawPage !== false ? $rawPage : 1));
$offset = ($currentPageNum - 1) * $perPage;
$orders = getCustomerOrders($userId, $search, $perPage, $offset);

$statusClassMap = [
    'Belum Dibayar' => 'order-status-unpaid',
    'Menunggu Verifikasi' => 'order-status-pending',
    'Aktif' => 'order-status-active',
    'Selesai' => 'order-status-completed',
    'Ditolak' => 'order-status-rejected',
    'Dibatalkan' => 'order-status-cancelled',
];
$imgDir = __DIR__ . '/../../public/assets/img/';

foreach ($orders as &$ord) {
    $start = new DateTimeImmutable($ord['start_date']);
    $end = new DateTimeImmutable($ord['end_date']);

    $ord['order_code'] = sprintf('#ORD-%03d', (int) $ord['order_id']);
    $ord['image_url'] = (!empty($ord['package_image']) && is_file($imgDir . $ord['package_image']))
        ? './assets/img/' . $ord['package_image']
        : null;
    $ord['status_class'] = $statusClassMap[$ord['status']] ?? 'order-status-unpaid';
    $ord['period_text'] = formatDateIndo($ord['start_date'], true) . ' - ' . formatDateIndo($ord['end_date'], true);
    $ord['calendar_days'] = $start->diff($end)->days + 1;
    $ord['detail_url'] = 'index.php?page=order-detail&id=' . (int) $ord['order_id'];
}
unset($ord);

$pageUrl = static fn (int $p): string =>
    'index.php?page=orders&q=' . urlencode($search) . '&p=' . $p;

$pagination = [
    'show' => $totalPages > 1,
    'has_prev' => $currentPageNum > 1,
    'has_next' => $currentPageNum < $totalPages,
    'prev_url' => $pageUrl(max(1, $currentPageNum - 1)),
    'next_url' => $pageUrl(min($totalPages, $currentPageNum + 1)),
    'pages' => [],
];
for ($i = max(1, $currentPageNum - 2); $i <= min($totalPages, $currentPageNum + 2); $i++) {
    $pagination['pages'][] = [
        'number' => $i,
        'url' => $pageUrl($i),
        'active' => $i === $currentPageNum,
    ];
}

$startRow = $totalOrders > 0 ? $offset + 1 : 0;
$endRow = min($offset + $perPage, $totalOrders);
$countText = $totalOrders <= $perPage
    ? "Menampilkan {$totalOrders} dari {$totalOrders} Pesanan"
    : "Menampilkan {$startRow}-{$endRow} dari {$totalOrders} Pesanan";

render('customer/orders', [
    'title' => 'Riwayat Pesanan - MealMate',
    'page' => 'orders',
    'hideFooter' => true,
    'orders' => $orders,
    'search' => $search,
    'countText' => $countText,
    'pagination' => $pagination,
]);