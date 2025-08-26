<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\App;
use PDO;

class Event
{
    public ?int $id = null;
    public string $name;
    public string $description;
    public string $event_time;
    public string $location;
    public string $required_Skills;

    public static function all(): array
    {
        $stmt = App::db()->query('SELECT * FROM events ORDER BY id DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find(int $id): ?self
    {
        $stmt = App::db()->prepare('SELECT * FROM events WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
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
            "INSERT INTO events (name, description, event_time, location, required_Skills) 
             VALUES (:name, :description, :event_time, :location, :required_Skills)"
        );

        return $stmt->execute([
            ':name' => $this->name,
            ':description' => $this->description,
            ':event_time' => $this->event_time,
            ':location' => $this->location,
            ':required_Skills' => $this->required_Skills
        ]);
    }

    private function update(): bool
    {
        $stmt = App::db()->prepare(
            "UPDATE events SET 
                name = :name, 
                description = :description, 
                event_time = :event_time, 
                location = :location, 
                required_Skills = :required_Skills 
             WHERE id = :id"
        );

        return $stmt->execute([
            ':id' => $this->id,
            ':name' => $this->name,
            ':description' => $this->description,
            ':event_time' => $this->event_time,
            ':location' => $this->location,
            ':required_Skills' => $this->required_Skills
        ]);
    }

    public static function delete(int $id): bool
    {
        $stmt = App::db()->prepare('DELETE FROM events WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    private static function map(array $row): self
    {
        $e = new self();
        $e->id = (int)$row['id'];
        $e->name = $row['name'];
        $e->description = $row['description'];
        $e->event_time = $row['event_time'];
        $e->location = $row['location'];
        $e->required_Skills = $row['required_Skills'];
        return $e;
    }
}