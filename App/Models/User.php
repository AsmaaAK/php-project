<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Controller;
use App\Core\App;
use PDO;

class User
{
    public int $id;
    public string $name;
    public string $email;
    public string $password;

    public static function findByEmail(string $email): ?self
    {
    
        $stmt = App::db()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? self::map($row) : null;
    }

    public static function all(): array
    {
        $stmt = App::db()->query('SELECT id, name, email FROM users ORDER BY id DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private static function map(array $row): self
    {
        $u = new self();
        $u->id       = (int)$row['id'];
        $u->name     = $row['name'];
        $u->email    = $row['email'];
        $u->password = $row['password'];
        return $u;
    }
}
