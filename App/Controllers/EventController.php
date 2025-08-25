<?php
declare(strict_types=1);
namespace App\Controllers; 
use App\Core\Controller;
use App\Core\App;
use PDO;

class EventController extends Controller {

    private $pdo;

    public function __construct() {
        $this->pdo = App::db();
        header('Content-Type: application/json');
    }

    // GET /events?location=&availability=&search=
    public function index() {
        $location = $_GET['location'] ?? '';
        $availability = $_GET['availability'] ?? '';
        $search = $_GET['search'] ?? '';

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

        // تحويل عمود required_skills من JSON إلى Array
        foreach ($events as &$ev) {
            $ev['required_skills'] = json_decode($ev['required_skills'], true) ?: [];
        }

        echo json_encode($events);
    }

    // GET /events/{id}
    public function show(int $id) {
        $stmt = $this->pdo->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([$id]);
        $ev = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$ev) {
            http_response_code(404);
            echo json_encode(['error' => 'الفعالية غير موجودة']);
            return;
        }
        $ev['required_skills'] = json_decode($ev['required_skills'], true) ?: [];
        echo json_encode($ev);
    }

    // POST /events
    public function store() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'بيانات غير صالحة']);
            return;
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO events (title, description, availability, location, required_skills) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['title'],
            $data['description'],
            $data['availability'],
            $data['location'],
            json_encode($data['required_skills'])
        ]);

        echo json_encode(['message' => 'تم إضافة الفعالية بنجاح']);
    }

    // PUT /events/{id}
    public function update(int $id) {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'بيانات غير صالحة']);
            return;
        }

        $stmt = $this->pdo->prepare(
            "UPDATE events SET title=?, description=?, availability=?, location=?, required_skills=? WHERE id=?"
        );
        $stmt->execute([
            $data['title'],
            $data['description'],
            $data['availability'],
            $data['location'],
            json_encode($data['required_skills']),
            $id
        ]);

        echo json_encode(['message' => 'تم تعديل الفعالية بنجاح']);
    }

    // DELETE /events/{id}
    public function delete(int $id) {
        $stmt = $this->pdo->prepare("DELETE FROM events WHERE id=?");
        $stmt->execute([$id]);
        echo json_encode(['message' => 'تم حذف الفعالية بنجاح']);
    }
}
