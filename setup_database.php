<?php 
    if (php_sapi_name() !== 'cli') {
        http_response_code(403);
        
        die("Akses ditolak! Setup database hanya boleh dijalankan melalui Command Line/Terminal.");
    }

    require_once __DIR__ . '/config/database.php';

    $config = getDBConfig();

    try {
        $dsn = sprintf('mysql:host=%s; port=%s; charset=%s', $config['host'], $config['port'], $config['charset']);
        $pdo = new PDO($dsn, $config['user'], $config['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        $dbName = $config['dbname'];
        $pdo -> exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo -> exec ("USE `{$dbName}`");

        echo "Database `{$dbName}` berhasil disiapkan. \n";

        $sqlFilePath = __DIR__ . '/schema.sql';

        if (file_exists($sqlFilePath)) {
            $sql = file_get_contents($sqlFilePath);
            $pdo -> exec($sql);

            echo "Seluruh tabel berhasil dibuat dari file schema.sql \n";
        } else {
            die("Gagal: File schema.sql tidak ditemukan di " . $sqlFilePath . "\n");
        }

        echo "Setup database selesai \n";

    } catch (PDOException $e) {
        http_response_code(500);
        die("Gagal melakukan setup database: " . $e -> getMessage() . "\n");
    }
?>