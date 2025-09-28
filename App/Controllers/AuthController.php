<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../Models/BaseModel.php';
require_once __DIR__ . '/../Models/UserModel.php';
class AuthController extends BaseController
{
    public function Info()
    {
        global $title;
        $title = "Thông Tin Người Dùng | Blossy";

        if (!isset($_SESSION['user'])) {
            header("Location: " . BASE_URL . "index.php?controller=auth&action=login");
            exit();
        }

        $user = $_SESSION['user'];
        $data = ['user' => $user];
        $this->loadView('User.Account', $data);
    }

    public function login()
    {
        global $title;
        $title = "Đăng Nhập | Blossy";
        $this->loadView('User.Login');
    }

    public function register()
    {
        global $title;
        $title = "Đăng Ký | Blossy";
        $this->loadView('User.Register');
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        header("Location: " . BASE_URL . "index.php");
        exit();
    }

    public function handleLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $db = new BaseModel();

            // 1. Tìm user theo email
            $stmt = $db->conn->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // 2. Kiểm tra mật khẩu (nếu đã hash thì dùng password_verify)
           // if ($user && password_verify($password, $user['password'])) {
            if ($user && $password === $user['password']) {
                $_SESSION['user'] = [
                    'name' => $user['first_name'] . ' ' . $user['last_name'],
                    'email' => $user['email'],
                    'role_id' => $user['role_id'],
                    'user_id' => $user['id'],
                    'first_name' => $user['first_name'],
                    'last_name'  => $user['last_name'],
                    'phone'      => $user['phone'],
                    'address'    => $user['address'],
                    'gender'     => $user['gender']
                ];
                header("Location: " . BASE_URL . "index.php");
                exit();
            } else {
                $error = "❌ Email hoặc mật khẩu không đúng!";
                $this->loadView('User.Login', ['error' => $error]);
            }
        }
    }
    public function handleRegister()
{
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $first_name = trim($_POST['first_name']);
        $last_name  = trim($_POST['last_name']);
        $email      = trim($_POST['email']);
        $password   = trim($_POST['password']);
        $confirm    = trim($_POST['confirm_password']);
        $phone      = trim($_POST['phone']);
        $address    = trim($_POST['address']);
        $gender     = trim($_POST['gender']);

        if ($password !== $confirm) {
            $this->loadView('User.Register', ['error' => '❌ Mật khẩu không khớp!']);
            return;
        }

        $userModel = new UserModel();

        if ($userModel->emailExists($email)) {
            $this->loadView('User.Register', ['error' => '❌ Email đã tồn tại!']);
            return;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $success = $userModel->createUser([
            'email'         => $email,
            'password'      => $password,      
            'password_hash' => $hashedPassword, 
            'first_name'    => $first_name,
            'last_name'     => $last_name,
            'phone'         => $phone,
            'address'       => $address,
            'gender'        => $gender
        ]);

        if ($success) {
            echo "<script>alert('✅ Đăng ký thành công!'); window.location.href='" . BASE_URL . "index.php?controller=auth&action=login';</script>";
        } else {
            $this->loadView('User.Register', ['error' => '❌ Có lỗi xảy ra!']);
        }
    }
}

}
