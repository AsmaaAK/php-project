<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/app.php';

class VolunteerController {

    private $pdo;

    public function __construct() {
        $this->pdo = App::pdo();
        header('Content-Type: application/json');
    }

    // GET /volunteers?location=&availability=&search=
    public function index() {
        $location = $_GET['location'] ?? '';
        $availability = $_GET['availability'] ?? '';
        $search = $_GET['search'] ?? '';

        $query = "SELECT * FROM volunteers WHERE 1=1";
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
            $query .= " AND (name LIKE ? OR email LIKE ? OR skills LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        $volunteers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // تحويل عمود skills من JSON إلى Array
        foreach ($volunteers as &$vol) {
            $vol['skills'] = json_decode($vol['skills'], true) ?: [];
        }

        echo json_encode($volunteers);
    }

    // GET /volunteers/{id}
    public function show(int $id) {
        $stmt = $this->pdo->prepare("SELECT * FROM volunteers WHERE id = ?");
        $stmt->execute([$id]);
        $vol = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$vol) {
            http_response_code(404);
            echo json_encode(['error' => 'المتطوع غير موجود']);
            return;
        }
        $vol['skills'] = json_decode($vol['skills'], true) ?: [];
        echo json_encode($vol);
    }

    // POST /volunteers
    public function store() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'بيانات غير صالحة']);
            return;
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO volunteers (name, age, location, availability, email, skills) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['name'],
            $data['age'],
            $data['location'],
            $data['availability'],
            $data['email'],
            json_encode($data['skills'])
        ]);

        echo json_encode(['message' => 'تم إضافة المتطوع بنجاح']);
    }

    // PUT /volunteers/{id}
    public function update(int $id) {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'بيانات غير صالحة']);
            return;
        }

        $stmt = $this->pdo->prepare(
            "UPDATE volunteers SET name=?, age=?, location=?, availability=?, email=?, skills=? WHERE id=?"
        );
        $stmt->execute([
            $data['name'],
            $data['age'],
            $data['location'],
            $data['availability'],
            $data['email'],
            json_encode($data['skills']),
            $id
        ]);

        echo json_encode(['message' => 'تم تعديل المتطوع بنجاح']);
    }

    // DELETE /volunteers/{id}
    public function delete(int $id) {
        $stmt = $this->pdo->prepare("DELETE FROM volunteers WHERE id=?");
        $stmt->execute([$id]);
        echo json_encode(['message' => 'تم حذف المتطوع بنجاح']);
    }
}
