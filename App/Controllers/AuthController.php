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
        $userId = $user['user_id'];

        $userModel = new UserModel();
        $addresses = $userModel->getAddresses($userId); 
        $orders = $userModel->getUserOrders($userId); 
        $this->loadView('User.Account', [
            'user' => $user,
            'addresses' => $addresses,
            'orders' => $orders 
        ]);

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
    public function addNewCard()
    {
        global $title;
        $title = "Thêm Phương Thức Thanh Toán | Blossy";

        if (!isset($_SESSION['user'])) {
            header("Location: " . BASE_URL . "index.php?controller=auth&action=login");
            exit();
        }

        $this->loadView('Payment.Payment');
    }

    public function handleAddNewCard()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['user'])) {
            $card_holder = trim($_POST['card_holder']);
            $card_number = trim($_POST['card_number']);
            $expiry_date = trim($_POST['expiry_date']);
            $cvv         = trim($_POST['cvv']);
            $card_brand  = trim($_POST['card_brand']);
            $userId      = $_SESSION['user']['user_id'];

            if (empty($card_holder) || empty($card_number) || empty($expiry_date) || empty($cvv)) {
                $this->loadView('Payment.Payment', ['error' => '❌ Vui lòng nhập đầy đủ thông tin thẻ!']);
                return;
            }

            $userModel = new UserModel();
            $success = $userModel->addUserCard($userId, $card_holder, $card_number, $expiry_date, $card_brand, $card_number);

            if ($success) {
                echo "<script>alert('✅ Thêm thẻ thành công!'); window.location.href='" . BASE_URL . "index.php?controller=auth&action=Info';</script>";
            } else {
                $this->loadView('Payment.Payment', ['error' => '❌ Lưu thẻ thất bại, vui lòng thử lại!']);
            }
        }
    }

    // Hiển thị danh sách địa chỉ
    public function Address()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: " . BASE_URL . "index.php?controller=auth&action=login");
            exit();
        }

        $userId = $_SESSION['user']['user_id'];
        $userModel = new UserModel();

        // ✅ Kiểm tra có user thật không
        $user = $userModel->getUserById($userId);
        if (!$user) {
            echo "<script>alert('❌ Không tìm thấy thông tin người dùng!'); 
                window.location.href='" . BASE_URL . "index.php?controller=auth&action=login';</script>";
            exit();
        }

        // ✅ Lấy danh sách địa chỉ theo user_id
        $addresses = $userModel->getAddresses($userId);

        // ✅ Truyền dữ liệu sang view
        $this->loadView('User.Address', [
            'user' => $user,
            'addresses' => $addresses
        ]);
    }


    // Thêm hoặc sửa địa chỉ
    public function HandleSaveAddress()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (!isset($_SESSION['user'])) exit();

            $userId = $_SESSION['user']['user_id'];
            $address = trim($_POST['address']);
            $id = $_POST['id'] ?? '';

            $userModel = new UserModel();

            if ($id) {
                $userModel->updateAddress($id, $userId, $address);
                $msg = '✅ Cập nhật địa chỉ thành công!';
            } else {
                $userModel->addAddress($userId, $address);
                $msg = '✅ Thêm địa chỉ mới thành công!';
            }

            echo "<script>alert('$msg'); window.location.href='" . BASE_URL . "index.php?controller=auth&action=Info';</script>";
            exit;
        }
    }

    // Xóa địa chỉ
    public function HandleDeleteAddress()
    {
        if (isset($_GET['id']) && isset($_SESSION['user'])) {
            $userId = $_SESSION['user']['user_id'];
            $id = (int)$_GET['id'];

            $userModel = new UserModel();
            $userModel->deleteAddress($id, $userId);

            echo "<script>alert('🗑️ Xóa địa chỉ thành công!'); window.location.href='" . BASE_URL . "index.php?controller=auth&action=Info';</script>";
            exit;
        }
    }


    public function HandleChangePassword(){
        if (isset($_POST['current_password'], $_POST['new_password'], $_POST['confirm_password']) && isset($_SESSION['user'])) {
            $userId = $_SESSION['user']['user_id'];
            $currentPassword = trim($_POST['current_password']);
            $newPassword     = trim($_POST['new_password']);
            $confirmPassword = trim($_POST['confirm_password']);

            if ($newPassword !== $confirmPassword) {
                $this->loadView('User.Account', [
                    'user' => $_SESSION['user'],
                    'error' => '❌ Mật khẩu mới không khớp!'
                ]);
                return;
            }

            $userModel = new UserModel();
            $user = $userModel->getUserById($userId);

            // Kiểm tra mật khẩu hiện tại
            if ($user && $currentPassword === $user['password']) {
                // Cập nhật mật khẩu mới
                $success = $userModel->changePassword($userId, $newPassword);
                if ($success) {
                    echo "<script>alert('✅ Đổi mật khẩu thành công!'); window.location.href='" . BASE_URL . "index.php?controller=auth&action=Info';</script>";
                    exit;
                } else {
                    $this->loadView('User.Account', [
                        'user' => $_SESSION['user'],
                        'error' => '❌ Có lỗi xảy ra khi đổi mật khẩu!'
                    ]);
                }
            } else {
                $this->loadView('User.Account', [
                    'user' => $_SESSION['user'],
                    'error' => '❌ Mật khẩu hiện tại không đúng!'
                ]);
            }
        }
    }
}
