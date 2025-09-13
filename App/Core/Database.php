<?php
// File: App/Core/Database.php
declare(strict_types=1);

namespace App\Core;

use App\Config\Config; // <-- Önemli: doğru namespace burası
use PDO;
use PDOException;

final class Database
{
    private static ?PDO $conn = null;

    public static function getConnection(): PDO
    {
        if (self::$conn !== null) {
            return self::$conn;
        }

        try {
            self::$conn = new PDO(
                Config::dsn(),
                Config::DB_USER,
                Config::DB_PASS,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'
                ]
            );
        } catch (PDOException $e) {
            http_response_code(500);
            echo 'DB connection error: ' . $e->getMessage();
            exit;
        }

        return self::$conn;
    }
}
