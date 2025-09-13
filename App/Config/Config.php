<?php
// File: App/Config/Config.php
declare(strict_types=1);

namespace App\Config;

final class Config
{
    public const DEV_INSECURE_SSL = true; // sadece localde
    // DB bilgileri (gerekirse değiştir)
    public const DB_HOST = '127.0.0.1';
    public const DB_PORT = 3306;
    public const DB_NAME = 'general'; // veya 'exegeo'
    public const DB_USER = 'root';
    public const DB_PASS = 'root';

    public static function dsn(): string
    {
        return sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            self::DB_HOST, self::DB_PORT, self::DB_NAME
        );
    }

   
    // Nominatim için kibar User-Agent (politik gereklilik: e-posta ya da site)
    public const APP_CONTACT = 'exegeo/1.0 (+you@example.com)';
}
