<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\App;
use PDO;

class Event {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = App::db();
    }

    // جلب كل الفعاليات مع إمكانية الفلترة
    public function all(string $location = '', string $availability = '', string $search = ''): array {
        $query = "SELECT * FROM events WHERE 1=1";
        $params = [];

        if ($location) {
            $query .= " AND location LIKE ?";
            $params[] = "%$location%";
        }
        if ($availability) {
            $query .= " AND availability LIKE ?";
            $params[] = "%$availability%";
        }
        if ($search) {
            $query .= " AND (title LIKE ? OR description LIKE ? OR required_skills LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // تحويل JSON إلى مصفوفة
        foreach ($events as &$ev) {
            $ev['required_skills'] = json_decode($ev['required_skills'], true) ?: [];
        }

        return $events;
    }

    // جلب فعالية واحدة حسب ID
    public function find(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([$id]);
        $ev = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$ev) return null;

        $ev['required_skills'] = json_decode($ev['required_skills'], true) ?: [];
        return $ev;
    }

    // إضافة فعالية جديدة
    public function create(array $data): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO events (title, description, availability, location, required_skills) VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $data['title'],
            $data['description'],
            $data['availability'],
            $data['location'],
            json_encode($data['required_skills'])
        ]);
    }

    // تعديل فعالية
    public function update(int $id, array $data): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE events SET title=?, description=?, availability=?, location=?, required_skills=? WHERE id=?"
        );
        return $stmt->execute([
            $data['title'],
            $data['description'],
            $data['availability'],
            $data['location'],
            json_encode($data['required_skills']),
            $id
        ]);
    }

    // حذف فعالية
    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM events WHERE id=?");
        return $stmt->execute([$id]);
    }
}
