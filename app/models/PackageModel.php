<?php
require_once __DIR__ . '/../config/database.php';

function getAllPackages(): array {
    $pdo = getDBConnection();
    return $pdo->query(
        "SELECT package_id, package_name, description, price, image, duration_days, applicable_days
         FROM packages
         ORDER BY price ASC, package_id ASC"
    )->fetchAll();
}

function getPopularPackages(int $limit = 4): array {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare(
        "SELECT p.package_id, p.package_name, p.description, p.price, p.image, p.duration_days, p.applicable_days,
                COUNT(o.order_id) AS total_orders
         FROM packages p
         LEFT JOIN orders o ON o.package_id = p.package_id
             AND o.status IN ('Menunggu Verifikasi', 'Aktif', 'Selesai')
         GROUP BY p.package_id, p.package_name, p.description, p.price, p.image, p.duration_days, p.applicable_days
         ORDER BY total_orders DESC, p.price ASC, p.package_id ASC
         LIMIT :limit"
    );
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getPackageById(int $packageId): ?array {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare(
        "SELECT package_id, package_name, description, price, image, duration_days, applicable_days
         FROM packages
         WHERE package_id = :id"
    );
    $stmt->execute(['id' => $packageId]);
    return $stmt->fetch() ?: null;
}

function isPackageNameExists(string $name, int $excludeId = 0): bool {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare(
        "SELECT COUNT(*)
         FROM packages
         WHERE LOWER(TRIM(package_name)) = LOWER(TRIM(:name))
           AND package_id != :exclude_id"
    );
    $stmt->execute([
        'name' => $name,
        'exclude_id' => $excludeId,
    ]);
    return (int) $stmt->fetchColumn() > 0;
}

function isValidApplicableDays(string $days): bool {
    return in_array($days, ['Senin-Jumat', 'Senin-Minggu'], true);
}

function buildPackageSearchFilter(string $search): array {
    if (trim($search) === '') {
        return ['', []];
    }
    $like = '%' . trim($search) . '%';
    return [
        "WHERE package_name LIKE :q1 OR description LIKE :q2 OR applicable_days LIKE :q3",
        ['q1' => $like, 'q2' => $like, 'q3' => $like],
    ];
}

function searchPackages(string $search = '', int $limit = 10, int $offset = 0): array {
    $pdo = getDBConnection();
    [$where, $params] = buildPackageSearchFilter($search);
    $stmt = $pdo->prepare(
        "SELECT package_id, package_name, description, price, image, duration_days, applicable_days
         FROM packages {$where}
         ORDER BY package_id DESC
         LIMIT :limit OFFSET :offset"
    );
    foreach ($params as $key => $val) {
        $stmt->bindValue(":{$key}", $val, PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function countPackages(string $search = ''): int {
    $pdo = getDBConnection();
    [$where, $params] = buildPackageSearchFilter($search);
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM packages {$where}");
    $stmt->execute($params);
    return (int) $stmt->fetchColumn();
}

function createPackage(
    string $name,
    ?string $description,
    int $price,
    int $durationDays,
    string $applicableDays,
    ?string $image = null
): int {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare(
        "INSERT INTO packages (package_name, description, price, duration_days, applicable_days, image)
         VALUES (:name, :description, :price, :duration_days, :applicable_days, :image)"
    );
    $stmt->execute([
        'name' => trim($name),
        'description' => $description !== null ? trim($description) : null,
        'price' => $price,
        'duration_days' => $durationDays,
        'applicable_days' => $applicableDays,
        'image' => $image ?: 'nasi-lemak.webp',
    ]);
    return (int) $pdo->lastInsertId();
}

function updatePackage(
    int $packageId,
    string $name,
    ?string $description,
    int $price,
    int $durationDays,
    string $applicableDays,
    ?string $image = null
): bool {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare(
        "UPDATE packages
         SET package_name = :name,
             description = :description,
             price = :price,
             duration_days = :duration_days,
             applicable_days = :applicable_days,
             image = :image
         WHERE package_id = :id"
    );
    return $stmt->execute([
        'id' => $packageId,
        'name' => trim($name),
        'description' => $description !== null ? trim($description) : null,
        'price' => $price,
        'duration_days' => $durationDays,
        'applicable_days' => $applicableDays,
        'image' => $image,
    ]);
}

function isPackageInActiveOrders(int $packageId): bool {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare(
        "SELECT COUNT(*)
         FROM orders
         WHERE package_id = :id
           AND status IN ('Menunggu Verifikasi', 'Aktif')"
    );
    $stmt->execute(['id' => $packageId]);
    return (int) $stmt->fetchColumn() > 0;
}

function deletePackage(int $packageId): bool {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("DELETE FROM packages WHERE package_id = :id");
    return $stmt->execute(['id' => $packageId]);
}
