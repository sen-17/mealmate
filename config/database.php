<?php

function getDBConfig(): array {
    $credFile = __DIR__ . '/credentials.php';

    if (!file_exists($credFile)) {
        $credFile = __DIR__ . '/credential.example.php';
    }

    return require $credFile;
}

function getDBConnection():PDO {
    static $pdo = null;

    if ($pdo === null){
        $config = getDBConfig();

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['dbname'],
            $config['charset'],
        );

        $pdo = new PDO($dsn, $config['user'], $config['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    return $pdo;
}