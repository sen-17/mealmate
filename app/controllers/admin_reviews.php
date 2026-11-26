<?php
require_once __DIR__ . '/../models/ReviewModel.php';

requireRole('Admin');

// filter & sort
$rawSearch = $_GET['q'] ?? '';
$search = is_string($rawSearch) ? trim($rawSearch) : '';
if (mb_strlen($search) > 100) {
    $search = mb_substr($search, 0, 100);
}

$rawRating = filter_var($_GET['rating'] ?? 0, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 5]]);
$rating = $rawRating !== false ? $rawRating : 0;

$sortMap = getAdminReviewSortMap();
$rawSort = $_GET['sort'] ?? 'date';
$sort = (is_string($rawSort) && isset($sortMap[$rawSort])) ? $rawSort : 'date';

$defaultDir = in_array($sort, ['date', 'rating'], true) ? 'desc' : 'asc';
$rawDir = $_GET['dir'] ?? $defaultDir;
$dir = (is_string($rawDir) && in_array(strtolower($rawDir), ['asc', 'desc'], true)) ? strtolower($rawDir) : $defaultDir;

// paginasi
$perPage = 10;
$totalReviews = countAdminReviews($search, $rating);
$totalPages = max(1, (int) ceil($totalReviews / $perPage));
$rawPage = filter_var($_GET['p'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 2147483647]]);
$currentPageNum = max(1, min($totalPages, $rawPage !== false ? $rawPage : 1));
$offset = ($currentPageNum - 1) * $perPage;
$reviews = getAdminReviews($search, $rating, $sort, $dir, $perPage, $offset);

$buildUrl = static function (array $override = []) use ($search, $rating, $sort, $dir): string {
    $params = array_merge([
        'page' => 'admin-reviews',
        'q' => $search,
        'rating' => $rating,
        'sort' => $sort,
        'dir' => $dir,
    ], $override);
    if ($params['q'] === '') {
        unset($params['q']);
    }
    if ((int) $params['rating'] === 0) {
        unset($params['rating']);
    }
    return 'index.php?' . http_build_query($params);
};

// header kolom
$columnLabels = [
    'date' => 'Tanggal',
    'customer' => 'Nama Pelanggan',
    'menu' => 'Menu',
    'rating' => 'Rating',
    'comment' => 'Komentar',
];
$columns = [];
foreach ($columnLabels as $key => $label) {
    $isActive = ($sort === $key);
    $firstDir = in_array($key, ['date', 'rating'], true) ? 'desc' : 'asc';
    $nextDir = $isActive ? ($dir === 'asc' ? 'desc' : 'asc') : $firstDir;

    $columns[$key] = [
        'label' => $label,
        'url' => $buildUrl(['sort' => $key, 'dir' => $nextDir, 'p' => 1]),
        'active' => $isActive,
        'icon' => $isActive ? ($dir === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down') : 'bi-arrow-down-up',
        'aria_sort' => $isActive ? ($dir === 'asc' ? 'ascending' : 'descending') : 'none',
    ];
}

// dropdown (mobile)
$sortChoices = [
    ['label' => 'Terbaru', 'sort' => 'date', 'dir' => 'desc'],
    ['label' => 'Terlama', 'sort' => 'date', 'dir' => 'asc'],
    ['label' => 'Rating tertinggi', 'sort' => 'rating', 'dir' => 'desc'],
    ['label' => 'Rating terendah', 'sort' => 'rating', 'dir' => 'asc'],
    ['label' => 'Nama A-Z', 'sort' => 'customer', 'dir' => 'asc'],
    ['label' => 'Menu A-Z', 'sort' => 'menu', 'dir' => 'asc'],
];
$sortOptions = array_map(static fn (array $c): array => [
    'label' => $c['label'],
    'url' => $buildUrl(['sort' => $c['sort'], 'dir' => $c['dir'], 'p' => 1]),
    'selected' => ($sort === $c['sort'] && $dir === $c['dir']),
], $sortChoices);

// dropdown filter rating
$ratingOptions = [['value' => 0, 'label' => 'Semua Rating', 'selected' => $rating === 0]];
for ($i = 5; $i >= 1; $i--) {
    $ratingOptions[] = ['value' => $i, 'label' => $i . ' Bintang', 'selected' => $rating === $i];
}

// baris tabel
foreach ($reviews as &$row) {
    $ts = strtotime((string) $row['created_at']);
    $comment = trim((string) ($row['comment'] ?? ''));

    $row['date_text'] = $ts ? formatDateIndo(date('Y-m-d', $ts), true) : '-';
    $row['time_text'] = $ts ? date('H:i', $ts) : '';
    $row['initials'] = getInitials($row['customer_name']);
    $row['avatar_class'] = 'admin-avatar-c' . ((int) $row['user_id'] % 3);
    $row['order_code'] = sprintf('#ORD-%03d', (int) $row['order_id']);
    $row['stars_html'] = renderStars((float) $row['rating']);
    $row['rating_label'] = (int) $row['rating'] . ' dari 5';
    $row['has_comment'] = $comment !== '';
    $row['comment_text'] = $comment;
}
unset($row);

// paginasi
$pageUrl = static fn (int $p): string => $buildUrl(['p' => $p]);
$pagination = [
    'show' => $totalPages > 1,
    'has_prev' => $currentPageNum > 1,
    'has_next' => $currentPageNum < $totalPages,
    'prev_url' => $pageUrl(max(1, $currentPageNum - 1)),
    'next_url' => $pageUrl(min($totalPages, $currentPageNum + 1)),
    'pages' => [],
];
for ($i = max(1, $currentPageNum - 2); $i <= min($totalPages, $currentPageNum + 2); $i++) {
    $pagination['pages'][] = ['number' => $i, 'url' => $pageUrl($i), 'active' => $i === $currentPageNum];
}

$startRow = $totalReviews > 0 ? $offset + 1 : 0;
$endRow = min($offset + $perPage, $totalReviews);
$countText = $totalReviews <= $perPage
    ? "Menampilkan {$totalReviews} dari {$totalReviews} ulasan"
    : "Menampilkan {$startRow}-{$endRow} dari {$totalReviews} ulasan";

render('admin/reviews', [
    'title' => 'Ulasan Pelanggan - MealMate',
    'reviews' => $reviews,
    'search' => $search,
    'sort' => $sort,
    'dir' => $dir,
    'isFiltered' => ($search !== '' || $rating > 0),
    'resetUrl' => 'index.php?page=admin-reviews',
    'columns' => $columns,
    'sortOptions' => $sortOptions,
    'ratingOptions' => $ratingOptions,
    'countText' => $countText,
    'pagination' => $pagination,
], 'admin');