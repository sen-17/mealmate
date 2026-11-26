<?php
require_once __DIR__ . '/../config/database.php';

requireRole('Admin');
session_write_close();

header('Content-Type: application/json; charset=utf-8');

$q = trim($_GET['q'] ?? '');
if ($q === '') {
    echo json_encode(['groups' => []]);
    exit;
}

$pdo = getDBConnection();
$like = '%' . $q . '%';
$groups = [];

$stmtMenu = $pdo->prepare(
    "SELECT menu_id, menu_name, description
     FROM menus
     WHERE menu_name LIKE :q1 OR description LIKE :q2
     ORDER BY menu_id DESC LIMIT 4"
);
$stmtMenu->execute(['q1' => $like, 'q2' => $like]);
$menus = $stmtMenu->fetchAll();
if (!empty($menus)) {
    $items = [];
    foreach ($menus as $m) {
        $items[] = [
            'title' => $m['menu_name'],
            'subtitle' => mb_strimwidth($m['description'] ?? '', 0, 60, '...'),
            'url' => 'index.php?page=admin-menus&q=' . urlencode($m['menu_name']),
            'icon' => 'bi-fork-knife',
        ];
    }
    $groups[] = ['label' => 'Menu', 'items' => $items];
}

$stmtPkg = $pdo->prepare(
    "SELECT package_id, package_name, duration_days, price
     FROM packages
     WHERE package_name LIKE :q1 OR description LIKE :q2
     ORDER BY package_id DESC LIMIT 3"
);
$stmtPkg->execute(['q1' => $like, 'q2' => $like]);
$packages = $stmtPkg->fetchAll();
if (!empty($packages)) {
    $items = [];
    foreach ($packages as $p) {
        $items[] = [
            'title' => $p['package_name'],
            'subtitle' => $p['duration_days'] . ' Hari • ' . formatRupiah((int) $p['price']),
            'url' => 'index.php?page=admin-packages&q=' . urlencode($p['package_name']),
            'icon' => 'bi-box-seam',
        ];
    }
    $groups[] = ['label' => 'Paket Langganan', 'items' => $items];
}

$cleanOrderQ = ltrim($q, '#ORDordMm-0');
$validOrderId = ($cleanOrderQ !== '' && ctype_digit($cleanOrderQ))
    ? filter_var($cleanOrderQ, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 2147483647]])
    : false;
$exactOrderId = $validOrderId !== false ? $validOrderId : -1;
$orderIdLike = '%' . ($cleanOrderQ !== '' ? $cleanOrderQ : $q) . '%';
$stmtOrd = $pdo->prepare(
    "SELECT o.order_id, u.name AS customer_name, p.package_name, o.status
     FROM orders o
     JOIN users u ON u.user_id = o.user_id
     JOIN packages p ON p.package_id = o.package_id
     WHERE CAST(o.order_id AS CHAR) LIKE :q1
        OR u.name LIKE :q2
        OR p.package_name LIKE :q3
     ORDER BY (o.order_id = :exact_id) DESC, o.order_id DESC LIMIT 4"
);
$stmtOrd->execute(['q1' => $orderIdLike, 'q2' => $like, 'q3' => $like, 'exact_id' => $exactOrderId]);
$orders = $stmtOrd->fetchAll();
if (!empty($orders)) {
    $items = [];
    foreach ($orders as $o) {
        $items[] = [
            'title' => sprintf('#ORD-%03d - %s', (int) $o['order_id'], $o['customer_name']),
            'subtitle' => $o['package_name'] . ' • ' . $o['status'],
            'url' => 'index.php?page=admin-order-detail&id=' . (int) $o['order_id'],
            'icon' => 'bi-credit-card',
        ];
    }
    $groups[] = ['label' => 'Pesanan', 'items' => $items];
}

$stmtCust = $pdo->prepare(
    "SELECT user_id, name, email, phone_number
     FROM users
     WHERE role = 'Pelanggan'
       AND (name LIKE :q1 OR email LIKE :q2 OR phone_number LIKE :q3)
     ORDER BY user_id DESC LIMIT 3"
);
$stmtCust->execute(['q1' => $like, 'q2' => $like, 'q3' => $like]);
$customers = $stmtCust->fetchAll();
if (!empty($customers)) {
    $items = [];
    foreach ($customers as $c) {
        $items[] = [
            'title' => $c['name'],
            'subtitle' => $c['email'] . ' • ' . ($c['phone_number'] ?? '-'),
            'url' => 'index.php?page=admin-customers&q=' . urlencode($c['name']),
            'icon' => 'bi-person',
        ];
    }
    $groups[] = ['label' => 'Pelanggan', 'items' => $items];
}

echo json_encode(['groups' => $groups]);
exit;
