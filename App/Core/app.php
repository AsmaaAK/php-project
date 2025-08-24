<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

class App
{
    private static ?PDO $db = null;

    public static function db(): PDO
    {
        if (self::$db === null) {
            $host = 'localhost';
            $dbname = 'volunteer_portal';
            $user = 'root';
            $pass = '';

            try {
                self::$db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
                self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }
        return self::$db;
    }
}
