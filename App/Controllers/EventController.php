<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Event;

class EventController extends Controller
{
    public function index(): void
    {
        // if (!isset($_SESSION['user_id'])) {
        //     $this->redirect('/auth/login');
        //     return;
        // }

        $events = Event::all();
        // $this->render('events/index', [
        //     'title' => 'قائمة الفعاليات',
        //     'events' => $events
        // ]);
        http_response_code(200);
            echo json_encode([
        'status' => 'success',
        'events' => $events
    ]);
    }

    public function indexApi(): void
    {
        $events = Event::all();
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'events' => $events
        ]);
    }

    public function getAllEvents(): void
    {
        $events = Event::all();
        $this->json(['events' => $events]);
    }

    public function apiCreate(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        $event = new Event();
        $event->name = $data['name'] ?? '';
        $event->description = $data['description'] ?? '';
        $event->event_time = $data['event_time'] ?? '';
        $event->location = $data['location'] ?? '';
        $event->required_Skills = isset($data['required_Skills']) ? json_encode($data['required_Skills']) : '';

       
        if (empty($event->name) || empty($event->location) || empty($event->event_time)) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'الحقول الأساسية مطلوبة'
            ]);
            exit;
        }

        try {
            if ($event->save()) {
                http_response_code(201);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'تم إضافة الفعالية بنجاح',
                    'event' => [
                        'id' => $event->id,
                        'name' => $event->name,
                        'location' => $event->location
                    ]
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'فشل في إضافة الفعالية'
                ]);
            }
        } catch (\PDOException $e) {
            http_response_code(409);
            echo json_encode([
                'status' => 'error',
                'message' => 'حدث خطأ في الإضافة',
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }

public function apiUpdate($id): void
{
    $id = (int)$id;

    header('Content-Type: application/json; charset=utf-8');

    if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    $event = Event::find($id);
    if (!$event) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'الفعالية غير موجودة']);
        exit;
    }

    $event->name = $data['name'] ?? $event->name;
    $event->description = $data['description'] ?? $event->description;
    $event->event_time = $data['event_time'] ?? $event->event_time;
    $event->location = $data['location'] ?? $event->location;
    $event->required_Skills = isset($data['required_Skills']) ? json_encode($data['required_Skills']) : $event->required_Skills;

    try {
        if ($event->save()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'تم تحديث بيانات الفعالية بنجاح'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'فشل في تحديث البيانات'
            ]);
        }
    } catch (\PDOException $e) {
        http_response_code(409);
        echo json_encode([
            'status' => 'error',
            'message' => 'خطأ في تحديث البيانات',
            'error' => $e->getMessage()
        ]);
    }
    exit;
}

public function apiDelete($id): void
{
    $id = (int)$id;

    header('Content-Type: application/json; charset=utf-8');

    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
        exit;
    }

    try {
        if (Event::delete($id)) {
            echo json_encode([
                'status' => 'success',
                'message' => 'تم حذف الفعالية بنجاح'
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'الفعالية غير موجودة'
            ]);
        }
    } catch (\PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'فشل في حذف الفعالية',
            'error' => $e->getMessage()
        ]);
    }
    exit;
}
    public function show($id): void
   {
    $id = (int)$id;
    $event = Event::find($id);
    
    header('Content-Type: application/json; charset=utf-8');
    
    if ($event) {
        echo json_encode([
            'status' => 'success',
            'event' => [
                'id' => $event->id,
                'name' => $event->name,
                'description' => $event->description,
                'event_time' => $event->event_time,
                'location' => $event->location,
                'required_Skills' => json_decode($event->required_Skills, true)
            ]
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'الفعالية غير موجودة'
        ]);
    }
    exit;
}
}