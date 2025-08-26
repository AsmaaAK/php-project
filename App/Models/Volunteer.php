<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\App;
use PDO;

class Volunteer
{
    public int $id;
    public string $name;
    public int $age;
    public string $location;
    public string $availability;
    public string $email;
    public string $skills;

    public static function all(): array
    {
        $stmt = App::db()->query('SELECT * FROM volunteers ORDER BY id DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find(int $id): ?self
    {
        $stmt = App::db()->prepare('SELECT * FROM volunteers WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? self::map($row) : null;
    }

    public static function findByEmail(string $email): ?self
    {
        $stmt = App::db()->prepare('SELECT * FROM volunteers WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? self::map($row) : null;
    }

    public function save(): bool
    {
        if (isset($this->id)) {
            return $this->update();
        } else {
            return $this->create();
        }
    }

    private function create(): bool
    {
        $stmt = App::db()->prepare(
            "INSERT INTO volunteers (name, age, location, availability, email, skills) 
             VALUES (:name, :age, :location, :availability, :email, :skills)"
        );

        return $stmt->execute([
            ':name' => $this->name,
            ':age' => $this->age,
            ':location' => $this->location,
            ':availability' => $this->availability,
            ':email' => $this->email,
            ':skills' => $this->skills
        ]);
    }

    private function update(): bool
    {
        $stmt = App::db()->prepare(
            "UPDATE volunteers SET 
                name = :name, 
                age = :age, 
                location = :location, 
                availability = :availability, 
                email = :email, 
                skills = :skills 
             WHERE id = :id"
        );

        return $stmt->execute([
            ':id' => $this->id,
            ':name' => $this->name,
            ':age' => $this->age,
            ':location' => $this->location,
            ':availability' => $this->availability,
            ':email' => $this->email,
            ':skills' => $this->skills
        ]);
    }

    public static function delete(int $id): bool
    {
        $stmt = App::db()->prepare('DELETE FROM volunteers WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    private static function map(array $row): self
    {
        $v = new self();
        $v->id = (int)$row['id'];
        $v->name = $row['name'];
        $v->age = (int)$row['age'];
        $v->location = $row['location'];
        $v->availability = $row['availability'];
        $v->email = $row['email'];
        $v->skills = $row['skills'];
        return $v;
    }
}