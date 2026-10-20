<?php
// Memanggil koneksi database
require_once __DIR__ . '/../config/database.php';

$pdo = getDBConnection();
// =====================================================
// READ (dipakai halaman menu pelanggan sekarang)
// =====================================================

/**
 * Menu satu tanggal, urut Pagi -> Siang -> Malam.
 * $date format 'Y-m-d', contoh '2026-10-19'.
 */
function getMenuByDate(string $date): array
{
    global $pdo;

    $query = "SELECT dm.slot, m.menu_id, m.menu_name, m.description, m.image,
                     COALESCE(rv.rating, 0)  AS rating,
                     COALESCE(rv.reviews, 0) AS reviews
              FROM daily_menus dm
              JOIN menus m ON m.menu_id = dm.menu_id
              LEFT JOIN (
                  SELECT menu_id,
                         ROUND(AVG(rating) * 2) / 2 AS rating,
                         COUNT(*)                   AS reviews
                  FROM reviews
                  GROUP BY menu_id
              ) rv ON rv.menu_id = m.menu_id
              WHERE dm.`date` = :date
              ORDER BY FIELD(dm.slot, 'Pagi', 'Siang', 'Malam')";
              
    $stmt = $pdo->prepare($query);
    $stmt->execute(['date' =>$date]);

    // Format tipe data rating dan review
    $results = [];
    foreach ($stmt->fetchAll() as$row) {
        $row['rating']  = (float)$row['rating'];
        $row['reviews'] = (int)$row['reviews'];
        $results[] =$row;
    }

    return $results;
}

/**
 * Menu rentang tanggal, dikelompokkan per tanggal.
 */
function getMenuByDateRange(string $startDate, string$endDate): array
{
    global $pdo;

    $query = "SELECT dm.`date` AS menu_date, dm.slot, m.menu_id, m.menu_name,
                     m.description, m.image,
                     COALESCE(rv.rating, 0)  AS rating,
                     COALESCE(rv.reviews, 0) AS reviews
              FROM daily_menus dm
              JOIN menus m ON m.menu_id = dm.menu_id
              LEFT JOIN (
                  SELECT menu_id,
                         ROUND(AVG(rating) * 2) / 2 AS rating,
                         COUNT(*)                   AS reviews
                  FROM reviews
                  GROUP BY menu_id
              ) rv ON rv.menu_id = m.menu_id
              WHERE dm.`date` BETWEEN :start AND :end
              ORDER BY dm.`date`, FIELD(dm.slot, 'Pagi', 'Siang', 'Malam')";
              
    $stmt =$pdo->prepare($query);$stmt->execute(['start' => $startDate, 'end' =>$endDate]);

    $grouped = [];
    foreach ($stmt->fetchAll() as$row) {
        $date =$row['menu_date'];
        unset($row['menu_date']);$row['rating']  = (float) $row['rating'];$row['reviews'] = (int) $row['reviews'];$grouped[$date][] =$row;
    }

    return $grouped;
}

// =====================================================
// CRUD katalog menu (disiapkan untuk halaman admin)
// =====================================================

function getAllMenus(): array
{
    global $pdo;
    
    $query = "SELECT menu_id, menu_name, description, image FROM menus ORDER BY menu_name";
    return $pdo->query($query)->fetchAll();
}

function getMenuById(int $menuId): ?array
{
    global $pdo;
    
    $query = "SELECT menu_id, menu_name, description, image FROM menus WHERE menu_id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' =>$menuId]);

    $result =$stmt->fetch();
    return $result ? $result : null;
}

/** Mengembalikan menu_id yang baru dibuat. */
function createMenu(string $name, ?string $description, ?string $image): int
{
    global $pdo;
    
    $query = "INSERT INTO menus (menu_name, description, image) VALUES (:name, :description, :image)";
    $stmt =$pdo->prepare($query);$stmt->execute([
        'name'        => $name,
        'description' => $description,
        'image'       => $image,
    ]);

    return (int) $pdo->lastInsertId();
}

function updateMenu(int $menuId, string$name, ?string $description, ?string $image): bool
{
    global $pdo;
    
    $query = "UPDATE menus SET menu_name = :name, description = :description, image = :image WHERE menu_id = :id";
    $stmt = $pdo->prepare($query);

    return $stmt->execute([
        'id'          => $menuId,
        'name'        => $name,
        'description' => $description,
        'image'       => $image,
    ]);
}

/** Menghapus menu juga menghapus jadwal dan ulasannya (ON DELETE CASCADE). */
function deleteMenu(int $menuId): bool
{
    global $pdo;
    
    $query = "DELETE FROM menus WHERE menu_id = :id";
    $stmt = $pdo->prepare($query);

    return $stmt->execute(['id' =>$menuId]);
}

// =====================================================
// CRUD jadwal harian (disiapkan untuk halaman admin)
// =====================================================

function isValidSlot(string $slot): bool 
{
    $validSlots = ['Pagi', 'Siang', 'Malam'];
    return in_array($slot,$validSlots, true);
}

/**
 * Mengisi atau mengganti menu pada tanggal + slot tertentu.
 */
function setDailyMenu(int $menuId, string $date, string$slot): bool
{
    global $pdo;
    
    if (!isValidSlot($slot)) {
        die("Error: Slot tidak valid.");
    }

    $query = "INSERT INTO daily_menus (menu_id, `date`, slot)
              VALUES (:menu_id, :date, :slot)
              ON DUPLICATE KEY UPDATE menu_id = VALUES(menu_id)";
              
    $stmt = $pdo->prepare($query);

    return $stmt->execute([
        'menu_id' => $menuId,
        'date'    => $date,
        'slot'    => $slot,
    ]);
}

function deleteDailyMenu(string $date, string$slot): bool
{
    global $pdo;
    
    if (!isValidSlot($slot)) {
        die("Error: Slot tidak valid.");
    }

    $query = "DELETE FROM daily_menus WHERE `date` = :date AND slot = :slot";
    $stmt = $pdo->prepare($query);

    return $stmt->execute(['date' => $date, 'slot' =>$slot]);
}
?>