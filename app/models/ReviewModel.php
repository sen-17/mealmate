<?php
require_once __DIR__ . '/../config/database.php';

const REVIEW_COMMENT_MAX_LENGTH = 500;

function getReviewableMenus(array $order): array {
    $pdo = getDBConnection();
    $weekdayFilter = ($order['applicable_days'] === 'Senin-Jumat')
        ? 'AND WEEKDAY(dm.`date`) <= 4'
        : '';

    $stmt = $pdo->prepare(
        "SELECT m.menu_id, m.menu_name, m.description, m.image,
                MIN(dm.`date`) AS first_date,
                r.review_id, r.rating, r.comment
         FROM daily_menus dm
         JOIN menus m ON m.menu_id = dm.menu_id
         LEFT JOIN reviews r ON r.order_id = :order_id AND r.menu_id = m.menu_id
         WHERE dm.slot = :slot
           AND dm.`date` BETWEEN :start_date AND :end_date
           {$weekdayFilter}
         GROUP BY m.menu_id, m.menu_name, m.description, m.image,
                  r.review_id, r.rating, r.comment
         ORDER BY first_date ASC, m.menu_id ASC"
    );
    $stmt->execute([
        'order_id' => (int) $order['order_id'],
        'slot' => $order['slot'],
        'start_date' => $order['start_date'],
        'end_date' => $order['end_date'],
    ]);

    $rows = $stmt->fetchAll();
    if (!empty($rows)) {
        return $rows;
    }

    $limit = max(1, min(7, (int) ($order['duration_days'] ?? 5)));
    $fallbackStmt = $pdo->prepare(
        "SELECT m.menu_id, m.menu_name, m.description, m.image,
                MIN(dm.`date`) AS first_date,
                r.review_id, r.rating, r.comment
         FROM daily_menus dm
         JOIN menus m ON m.menu_id = dm.menu_id
         LEFT JOIN reviews r ON r.order_id = :order_id AND r.menu_id = m.menu_id
         WHERE dm.slot = :slot
         GROUP BY m.menu_id, m.menu_name, m.description, m.image,
                  r.review_id, r.rating, r.comment
         ORDER BY first_date DESC, m.menu_id ASC
         LIMIT {$limit}"
    );
    $fallbackStmt->execute([
        'order_id' => (int) $order['order_id'],
        'slot' => $order['slot'],
    ]);

    $fallbackRows = $fallbackStmt->fetchAll();
    if (!empty($fallbackRows)) {
        return $fallbackRows;
    }

    $catalogStmt = $pdo->prepare(
        "SELECT m.menu_id, m.menu_name, m.description, m.image,
                :start_date AS first_date,
                r.review_id, r.rating, r.comment
         FROM menus m
         LEFT JOIN reviews r ON r.order_id = :order_id AND r.menu_id = m.menu_id
         ORDER BY m.menu_id ASC
         LIMIT {$limit}"
    );
    $catalogStmt->execute([
        'order_id' => (int) $order['order_id'],
        'start_date' => $order['start_date'],
    ]);

    return $catalogStmt->fetchAll();
}

function hasPendingReviews(array $order): bool {
    foreach (getReviewableMenus($order) as $menu) {
        if ($menu['review_id'] === null) {
            return true;
        }
    }
    return false;
}

function createReviews(int $orderId, array $items): array {
    $pdo = getDBConnection();

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare(
            "INSERT INTO reviews (order_id, menu_id, rating, comment)
             VALUES (:order_id, :menu_id, :rating, :comment)"
        );
        foreach ($items as $menuId => $item) {
            $stmt->execute([
                'order_id' => $orderId,
                'menu_id' => (int) $menuId,
                'rating' => (int) $item['rating'],
                'comment' => ($item['comment'] !== null && $item['comment'] !== '') ? $item['comment'] : null,
            ]);
        }
        $pdo->commit();
        return ['ok' => true];
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        if ($e->getCode() === '23000') {
            return ['ok' => false, 'message' => 'Sebagian menu sudah pernah Anda ulas. Silakan muat ulang halaman.'];
        }
        error_log($e->getMessage());
        return ['ok' => false, 'message' => 'Ulasan gagal disimpan. Silakan coba lagi.'];
    }
}

function getAdminReviewSortMap(): array {
    return [
        'date' => 'r.created_at',
        'customer' => 'u.name',
        'menu' => 'm.menu_name',
        'rating' => 'r.rating',
        'comment' => 'r.comment',
    ];
}

function buildAdminReviewFilter(string $search = '', int $rating = 0): array {
    $conditions = [];
    $params = [];

    if ($rating >= 1 && $rating <= 5) {
        $conditions[] = 'r.rating = :rating';
        $params['rating'] = $rating;
    }

    $q = trim($search);
    if ($q !== '') {
        $like = '%' . $q . '%';
        $conditions[] = '(u.name LIKE :q_name OR m.menu_name LIKE :q_menu OR r.comment LIKE :q_comment)';
        $params['q_name'] = $like;
        $params['q_menu'] = $like;
        $params['q_comment'] = $like;
    }

    if (empty($conditions)) {
        return ['', []];
    }

    return ['WHERE ' . implode(' AND ', $conditions), $params];
}

function countAdminReviews(string $search = '', int $rating = 0): int {
    $pdo = getDBConnection();
    [$where, $params] = buildAdminReviewFilter($search, $rating);
    $stmt = $pdo->prepare(
        "SELECT COUNT(*)
         FROM reviews r
         JOIN orders o ON o.order_id = r.order_id
         JOIN users u ON u.user_id = o.user_id
         JOIN menus m ON m.menu_id = r.menu_id
         {$where}"
    );
    foreach ($params as $key => $val) {
        $stmt->bindValue(":{$key}", $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $stmt->execute();
    return (int) $stmt->fetchColumn();
}

function getAdminReviews(
    string $search = '',
    int $rating = 0,
    string $sort = 'date',
    string $dir = 'desc',
    int $limit = 10,
    int $offset = 0
): array {
    $pdo = getDBConnection();
    [$where, $params] = buildAdminReviewFilter($search, $rating);

    $sortMap = getAdminReviewSortMap();
    $orderColumn = $sortMap[$sort] ?? $sortMap['date'];
    $orderDir = (strtolower($dir) === 'asc') ? 'ASC' : 'DESC';

    $stmt = $pdo->prepare(
        "SELECT r.review_id, r.order_id, r.rating, r.comment, r.created_at,
                u.user_id, u.name AS customer_name,
                m.menu_name
         FROM reviews r
         JOIN orders o ON o.order_id = r.order_id
         JOIN users u ON u.user_id = o.user_id
         JOIN menus m ON m.menu_id = r.menu_id
         {$where}
         ORDER BY {$orderColumn} {$orderDir}, r.review_id DESC
         LIMIT :limit OFFSET :offset"
    );
    foreach ($params as $key => $val) {
        $stmt->bindValue(":{$key}", $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}