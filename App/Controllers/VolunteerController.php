<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Volunteer;

class VolunteerController extends Controller
{
    public function index(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/auth/login');
            return;
        }

        $volunteers = Volunteer::all();
        $this->render('volunteers/index', [
            'title' => 'قائمة المتطوعين',
            'volunteers' => $volunteers
        ]);
    }

    public function getAllVolunteers(): void
    {
        $volunteers = Volunteer::all();
        $this->json(['volunteers' => $volunteers]);
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
        
        $volunteer = new Volunteer();
        $volunteer->name = $data['name'] ?? '';
        $volunteer->age = (int)($data['age'] ?? 0);
        $volunteer->location = $data['location'] ?? '';
        $volunteer->availability = $data['availability'] ?? '';
        $volunteer->email = $data['email'] ?? '';
        $volunteer->skills = $data['skills'] ?? '';

        // التحقق من الحقول المطلوبة
        if (empty($volunteer->name) || empty($volunteer->email) || empty($volunteer->location)) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'الحقول الأساسية مطلوبة'
            ]);
            exit;
        }

        try {
            if ($volunteer->save()) {
                http_response_code(201);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'تم إضافة المتطوع بنجاح',
                    'volunteer' => [
                        'id' => $volunteer->id,
                        'name' => $volunteer->name,
                        'email' => $volunteer->email
                    ]
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'فشل في إضافة المتطوع'
                ]);
            }
        } catch (\PDOException $e) {
            http_response_code(409);
            echo json_encode([
                'status' => 'error',
                'message' => 'البريد الإلكتروني مستخدم مسبقًا',
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }

    public function apiUpdate(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $id = (int)($data['id'] ?? 0);

        $volunteer = Volunteer::find($id);
        if (!$volunteer) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'المتطوع غير موجود']);
            exit;
        }

        $volunteer->name = $data['name'] ?? $volunteer->name;
        $volunteer->age = (int)($data['age'] ?? $volunteer->age);
        $volunteer->location = $data['location'] ?? $volunteer->location;
        $volunteer->availability = $data['availability'] ?? $volunteer->availability;
        $volunteer->email = $data['email'] ?? $volunteer->email;
        $volunteer->skills = $data['skills'] ?? $volunteer->skills;

        try {
            if ($volunteer->save()) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'تم تحديث بيانات المتطوع بنجاح'
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

    public function apiDelete(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $id = (int)($data['id'] ?? 0);

        try {
            if (Volunteer::delete($id)) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'تم حذف المتطوع بنجاح'
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'المتطوع غير موجود'
                ]);
            }
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'فشل في حذف المتطوع',
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }
}