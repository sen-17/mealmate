<?php
$currentPage = $_GET['page'] ?? 'home';
$flash = $flash ?? getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'MealMate') ?></title>
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/bootstrap-icons.min.css">
    <link rel="stylesheet" href="./assets/css/style.css"> 
    <link rel="stylesheet" href="./assets/css/footer.css">
    <link rel="stylesheet" href="./assets/css/navbar.css">
    <?php if ($currentPage === 'home'): ?>
        <link rel="stylesheet" href="./assets/css/home.css">
    <?php elseif ($currentPage === 'menu'): ?>
        <link rel="stylesheet" href="./assets/css/menu.css">
    <?php elseif ($currentPage === 'about'): ?>
        <link rel="stylesheet" href="./assets/css/about.css">
    <?php elseif ($currentPage === 'faq'): ?>
        <link rel="stylesheet" href="./assets/css/faq.css">
    <?php elseif (in_array($currentPage, ['checkout', 'order-detail'], true)): ?>
        <link rel="stylesheet" href="./assets/css/checkout.css">
    <?php elseif ($currentPage === 'orders'): ?>
        <link rel="stylesheet" href="./assets/css/orders.css">
    <?php elseif ($currentPage === 'review'): ?>
        <link rel="stylesheet" href="./assets/css/review.css">
    <?php elseif ($currentPage === 'profile'): ?>
        <link rel="stylesheet" href="./assets/css/profile.css">
    <?php elseif ($currentPage === 'payment'): ?>
        <link rel="stylesheet" href="./assets/css/payment.css">
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@400..700&family=Kaushan+Script&family=Poppins:wght@400;500;600;700&family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <?php include __DIR__ . '/navbar.php'; ?>

    <?php if (!empty($flash)): ?>
        <div class="flash-toast" id="flash-toast">
            <?= renderFlash($flash, 'shadow-sm mb-0') ?>
        </div>
    <?php endif; ?>
