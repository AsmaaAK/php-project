<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Core\Database;
use App\Core\App;

class AuthController extends Controller
{
    public function showLoginForm(): void
    {
        // // session_start();
        // $error = $_SESSION['error'] ?? null;
        // unset($_SESSION['error']);
        // $this->render('auth/login', ['title' => 'تسجيل الدخول', 'error' => $error]);
        require __DIR__ . '/../Views/auth/login.php';
    }

    public function login(): void
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = User::findByEmail($email);

        if ($user && password_verify($password, $user->password)) {
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_name'] = $user->name;
            $this->redirect('/volunteer-managment/public/users');
        } else {
            $_SESSION['error'] = "البريد الإلكتروني أو كلمة المرور غير صحيحة";
            $this->redirect('/volunteer-managment/public/auth/login');
        }
    }


    public function showRegisterForm(): void
    {
        require __DIR__ . '/../Views/auth/register.php';
    }

    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);

            $stmt = App::db()->prepare("INSERT INTO users (name,email,password) VALUES (:name,:email,:password)");
            $stmt->execute([':name' => $name, ':email' => $email, ':password' => $password]);

            $this->redirect('/volunteer-managment/public/auth/login');
        } else {
            $this->render('auth/register');
        }
    }

    public function logout(): void
    {
        // session_start();
        $_SESSION = [];
        session_destroy();
        $this->redirect('/volunteer-managment/public/auth/login');
    }



public function apiLogin(): void
{
    // Handle CORS (if needed)
    header("Content-Type: application/json; charset=utf-8");
    
    // Only accept POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['status'=>'error','message'=>'Method not allowed']);
        return;
    }

    // Get JSON input
    $data = json_decode(file_get_contents('php://input'), true);
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    $user = User::findByEmail($email);

    if ($user && password_verify($password, $user->password)) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_name'] = $user->name;

        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'message' => 'تم تسجيل الدخول بنجاح',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]
        ]);
                exit; // ⚠️ Important: stop script execution after sending JSON

    } else {
        http_response_code(401);
        echo json_encode([
            'status' => 'error',
            'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'
        ]);
                exit; // ⚠️ Important: stop script execution after sending JSON

    }
}



public function apiRegister(): void
{
    // قراءة البيانات من JSON
    $data = json_decode(file_get_contents('php://input'), true);
    $name = $data['name'] ?? '';
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    header('Content-Type: application/json; charset=utf-8');

    // التحقق من أن الحقول غير فارغة
    if (empty($name) || empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'جميع الحقول مطلوبة'
        ]);
        exit; // <-- استخدم exit بدلاً من return
    }

    // تشفير كلمة المرور
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        // إدخال المستخدم في قاعدة البيانات
        $stmt = App::db()->prepare(
            "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)"
        );
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $hashedPassword
        ]);

        http_response_code(201); // تم إنشاء المستخدم
        echo json_encode([
            'status' => 'success',
            'message' => 'تم تسجيل المستخدم بنجاح',
            'user' => [
                'name' => $name,
                'email' => $email
            ]
        ]);
        exit; // <-- تأكد من إنهاء السكريبت بعد الإرسال

    } catch (\PDOException $e) {
        // في حال البريد موجود مسبقًا أو أي خطأ في DB
        http_response_code(409);
        echo json_encode([
            'status' => 'error',
            'message' => 'البريد الإلكتروني مستخدم مسبقًا أو حدث خطأ',
            'error' => $e->getMessage()
        ]);
        exit; // <-- نهاية السكريبت
    }
}


}
