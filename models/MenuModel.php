<?php

class MenuModel
{
    private PDO $pdo;

    private const SLOTS = ['Pagi', 'Siang', 'Malam'];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // =====================================================
    // READ (dipakai halaman menu pelanggan sekarang)
    // =====================================================

    /**
     * Menu satu tanggal, urut Pagi -> Siang -> Malam.
     * $date format 'Y-m-d', contoh '2026-10-19'.
     */
    public function getMenuByDate(string $date): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT dm.slot, m.menu_id, m.menu_name, m.description, m.image,
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
             ORDER BY FIELD(dm.slot, 'Pagi', 'Siang', 'Malam')"
        );
        $stmt->execute(['date' => $date]);

        return array_map([$this, 'castRow'], $stmt->fetchAll());
    }

    /**
     * Menu rentang tanggal, dikelompokkan per tanggal:
     * ['2026-10-19' => [ {slot, menu_name, ...}, ... ], ...]
     * Tanggal tanpa jadwal tidak muncul di hasil.
     */
    public function getMenuByDateRange(string $startDate, string $endDate): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT dm.`date` AS menu_date, dm.slot, m.menu_id, m.menu_name,
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
             ORDER BY dm.`date`, FIELD(dm.slot, 'Pagi', 'Siang', 'Malam')"
        );
        $stmt->execute(['start' => $startDate, 'end' => $endDate]);

        $grouped = [];
        foreach ($stmt->fetchAll() as $row) {
            $date = $row['menu_date'];
            unset($row['menu_date']);
            $grouped[$date][] = $this->castRow($row);
        }

        return $grouped;
    }

    // =====================================================
    // CRUD katalog menu (disiapkan untuk halaman admin)
    // =====================================================

    public function getAllMenus(): array
    {
        return $this->pdo
            ->query("SELECT menu_id, menu_name, description, image FROM menus ORDER BY menu_name")
            ->fetchAll();
    }

    public function getMenuById(int $menuId): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT menu_id, menu_name, description, image FROM menus WHERE menu_id = :id"
        );
        $stmt->execute(['id' => $menuId]);

        return $stmt->fetch() ?: null;
    }

    /** Mengembalikan menu_id yang baru dibuat. */
    public function createMenu(string $name, ?string $description, ?string $image): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO menus (menu_name, description, image)
             VALUES (:name, :description, :image)"
        );
        $stmt->execute([
            'name'        => $name,
            'description' => $description,
            'image'       => $image,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function updateMenu(int $menuId, string $name, ?string $description, ?string $image): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE menus
             SET menu_name = :name, description = :description, image = :image
             WHERE menu_id = :id"
        );

        return $stmt->execute([
            'id'          => $menuId,
            'name'        => $name,
            'description' => $description,
            'image'       => $image,
        ]);
    }

    /** Menghapus menu juga menghapus jadwal dan ulasannya (ON DELETE CASCADE). */
    public function deleteMenu(int $menuId): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM menus WHERE menu_id = :id");

        return $stmt->execute(['id' => $menuId]);
    }

    // =====================================================
    // CRUD jadwal harian (disiapkan untuk halaman admin)
    // =====================================================

    /**
     * Mengisi atau mengganti menu pada tanggal + slot tertentu.
     * Memakai batasan unik (date, slot) dari schema.sql.
     */
    public function setDailyMenu(int $menuId, string $date, string $slot): bool
    {
        $this->assertSlot($slot);

        $stmt = $this->pdo->prepare(
            "INSERT INTO daily_menus (menu_id, `date`, slot)
             VALUES (:menu_id, :date, :slot)
             ON DUPLICATE KEY UPDATE menu_id = VALUES(menu_id)"
        );

        return $stmt->execute([
            'menu_id' => $menuId,
            'date'    => $date,
            'slot'    => $slot,
        ]);
    }

    public function deleteDailyMenu(string $date, string $slot): bool
    {
        $this->assertSlot($slot);

        $stmt = $this->pdo->prepare(
            "DELETE FROM daily_menus WHERE `date` = :date AND slot = :slot"
        );

        return $stmt->execute(['date' => $date, 'slot' => $slot]);
    }

    // =====================================================
    // Helper internal
    // =====================================================

    private function assertSlot(string $slot): void
    {
        if (!in_array($slot, self::SLOTS, true)) {
            throw new InvalidArgumentException("Slot tidak valid: {$slot}");
        }
    }

    /** Ubah rating dan reviews dari string ke angka supaya JSON bersih. */
    private function castRow(array $row): array
    {
        $row['rating']  = (float) $row['rating'];
        $row['reviews'] = (int) $row['reviews'];

        return $row;
    }
}