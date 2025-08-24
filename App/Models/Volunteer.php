<?php
declare(strict_types=1);

namespace App\Models;

use PDO;
use PDOException;

class Volunteer
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // ==========================
    // جلب كل المتطوعين
    // ==========================
    public function all(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM volunteers ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ==========================
    // جلب متطوع واحد
    // ==========================
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM volunteers WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $volunteer = $stmt->fetch(PDO::FETCH_ASSOC);
        return $volunteer ?: null;
    }

    // ==========================
    // إنشاء متطوع جديد
    // ==========================
    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO volunteers (name, age, location, availability, email, skills)
            VALUES (:name, :age, :location, :availability, :email, :skills)
        ");
        $stmt->execute([
            ':name' => $data['name'],
            ':age' => $data['age'],
            ':location' => $data['location'],
            ':availability' => $data['availability'],
            ':email' => $data['email'],
            ':skills' => implode(',', $data['skills']), // نخزن المهارات كمجموعة نصية
        ]);

        return (int)$this->db->lastInsertId();
    }

    // ==========================
    // تحديث متطوع
    // ==========================
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE volunteers SET
                name = :name,
                age = :age,
                location = :location,
                availability = :availability,
                email = :email,
                skills = :skills
            WHERE id = :id
        ");
        return $stmt->execute([
            ':name' => $data['name'],
            ':age' => $data['age'],
            ':location' => $data['location'],
            ':availability' => $data['availability'],
            ':email' => $data['email'],
            ':skills' => implode(',', $data['skills']),
            ':id' => $id,
        ]);
    }

    // ==========================
    // حذف متطوع
    // ==========================
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM volunteers WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // ==========================
    // بحث وفلاتر
    // ==========================
    public function search(?string $location = null, ?string $availability = null): array
    {
        $query = "SELECT * FROM volunteers WHERE 1=1";
        $params = [];

        if ($location) {
            $query .= " AND location LIKE :location";
            $params[':location'] = "%$location%";
        }

        if ($availability) {
            $query .= " AND availability LIKE :availability";
            $params[':availability'] = "%$availability%";
        }

        $query .= " ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
