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
        $userId = $user['user_id'];

        $userModel = new UserModel();
        $address = $userModel->getAddress($userId);
 

        // Pagination order
        $limit = 10;
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $totalOrders = $userModel->countUserOrders($userId);
        $totalPages = ceil($totalOrders / $limit);

        $orders = $userModel->getUserOrdersPaginated($userId, $limit, $offset);

        $this->loadView('User.Account', [
            'user' => $user,
            'addresses' => $address,
            'orders' => $orders,
            'page' => $page,
            'totalPages' => $totalPages
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
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            $db = new BaseModel();
            $stmt = $db->conn->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // ⚠️ Kiểm tra tồn tại tài khoản
            if (!$user) {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => '❌ Email không tồn tại trong hệ thống!'
                ];
                header("Location: index.php?controller=auth&action=login");
                exit;
            }

            // ⚠️ Kiểm tra trạng thái tài khoản bị khóa
            if (!empty($user['is_blocked']) && (int)$user['is_blocked'] === 1) {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => '🚫 Tài khoản của bạn đã bị khóa! Vui lòng liên hệ Admin.'
                ];
                header("Location: index.php?controller=auth&action=login");
                exit;
            }

            // ✅ Kiểm tra mật khẩu
            if ($user && $password === $user['password']) {
                $_SESSION['user'] = [
                    'name'       => trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')),
                    'email'      => $user['email'],
                    'role_id'    => $user['role_id'],
                    'user_id'    => $user['id'],
                    'first_name' => $user['first_name'],
                    'last_name'  => $user['last_name'],
                    'phone'      => $user['phone'],
                    'address'    => $user['address'],
                    'gender'     => $user['gender'],
                    'level'      => $user['level'],
                ];

                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => 'Đăng nhập thành công!'
                ];

                // Admin → vào dashboard
                if ((int)$user['role_id'] === 3) {
                    header("Location: index.php?controller=admin&action=dashboard");
                } else {
                    header("Location: index.php");
                }
                exit;
            }

            // ❌ Sai mật khẩu
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Mật khẩu không chính xác!'
            ];
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
    }
public function verifyOTP()
{
    global $title;
    $title = "Xác thực OTP | Blossy";

    // Nếu chưa có dữ liệu OTP tạm, quay về đăng ký
    if (!isset($_SESSION['otp'], $_SESSION['pending_user'])) {
        $_SESSION['toast'] = [
            'type' => 'warning',
            'message' => 'Vui lòng đăng ký trước khi xác thực OTP!'
        ];
        header("Location: index.php?controller=auth&action=register");
        exit;
    }

    // Hiển thị view nhập OTP
    $this->loadView('User.VerifyOTP');
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

            // Kiểm tra mật khẩu khớp
            if ($password !== $confirm) {
                $_SESSION['toast'] = ['type' => 'error', 'message' => '❌ Mật khẩu không khớp!'];
                header("Location: index.php?controller=auth&action=register");
                exit;
            }

            $userModel = new UserModel();

            // Kiểm tra email đã tồn tại
            if ($userModel->emailExists($email)) {
                $_SESSION['toast'] = ['type' => 'error', 'message' => '⚠️ Email đã tồn tại!'];
                header("Location: index.php?controller=auth&action=register");
                exit;
            }

            // Tạo OTP ngẫu nhiên
            $otp = rand(100000, 999999);

            // Lưu dữ liệu tạm vào session (chưa insert vào DB)
            $_SESSION['pending_user'] = [
                'first_name'    => $first_name,
                'last_name'     => $last_name,
                'email'         => $email,
                'password'      => $password,
                'phone'         => $phone,
                'address'       => $address,
                'gender'        => $gender
            ];

            $_SESSION['otp'] = [
                'code'    => $otp,
                'expires' => time() + 300 // 5 phút
            ];

            // Gửi email OTP
            require_once __DIR__ . '/../Includes/Mailer.php';
            $sent = sendOTP($email, $otp);

            if ($sent) {
                $_SESSION['toast'] = ['type' => 'success', 'message' => '📩 Mã OTP đã được gửi tới email của bạn!'];
                header("Location: index.php?controller=auth&action=verifyOTP");
                exit;
            } else {
                $_SESSION['toast'] = ['type' => 'error', 'message' => '❌ Gửi OTP thất bại, vui lòng thử lại sau!'];
                header("Location: index.php?controller=auth&action=register");
                exit;
            }
        }
    }
    public function handleVerifyOTP()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $otpInput = trim($_POST['otp']);

            if (!isset($_SESSION['otp'], $_SESSION['pending_user'])) {
                $_SESSION['toast'] = ['type' => 'error', 'message' => 'OTP đã hết hạn.'];
                header("Location: index.php?controller=auth&action=register");
                exit;
            }

            $otpData = $_SESSION['otp'];

            if (time() > $otpData['expires']) {
                unset($_SESSION['otp'], $_SESSION['pending_user']);
                $_SESSION['toast'] = ['type' => 'error', 'message' => '⏰ Mã OTP đã hết hạn!'];
                header("Location: index.php?controller=auth&action=register");
                exit;
            }

            if ($otpInput == $otpData['code']) {
                // ✅ OTP đúng → tạo tài khoản chính thức
                $userData = $_SESSION['pending_user'];
                unset($_SESSION['otp'], $_SESSION['pending_user']);

                $userModel = new UserModel();
                $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);

                $success = $userModel->createUser([
                    'email'         => $userData['email'],
                    'password'      => $userData['password'],
                    'password_hash' => $hashedPassword,
                    'first_name'    => $userData['first_name'],
                    'last_name'     => $userData['last_name'],
                    'phone'         => $userData['phone'],
                    'address'       => $userData['address'],
                    'gender'        => $userData['gender']
                ]);

                if ($success) {
                    $_SESSION['toast'] = ['type' => 'success', 'message' => '🎉 Xác thực thành công! Bạn có thể đăng nhập.'];
                    header("Location: index.php?controller=auth&action=login");
                } else {
                    $_SESSION['toast'] = ['type' => 'error', 'message' => '❌ Lỗi khi tạo tài khoản!'];
                    header("Location: index.php?controller=auth&action=register");
                }
            } else {
                $_SESSION['toast'] = ['type' => 'error', 'message' => '⚠️ Mã OTP không chính xác!'];
                header("Location: index.php?controller=auth&action=verifyOTP");
            }
        }
    }

    public function handleUpdateInfo()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user'])) {
            $userId     = $_SESSION['user']['user_id'];
            $first_name = trim($_POST['first_name'] ?? '');
            $last_name  = trim($_POST['last_name'] ?? '');
            $phone      = trim($_POST['phone'] ?? '');
            $gender     = trim($_POST['gender'] ?? '');

            // ✅ Kiểm tra dữ liệu nhập
            if (empty($first_name) || empty($last_name) || empty($phone)) {
                $_SESSION['toast'] = [
                    'type' => 'warning',
                    'message' => 'Vui lòng nhập đầy đủ thông tin!'
                ];
                header("Location: index.php?controller=auth&action=Info");
                exit;
            }

            // ✅ Cập nhật thông tin vào database
            $userModel = new UserModel();
            $updated = $userModel->updateUserInfo($userId, [
                'first_name' => $first_name,
                'last_name'  => $last_name,
                'phone'      => $phone,
                'gender'     => $gender
            ]);

            if ($updated) {
                // ✅ Cập nhật lại session
                $_SESSION['user']['first_name'] = $first_name;
                $_SESSION['user']['last_name']  = $last_name;
                $_SESSION['user']['phone']      = $phone;
                $_SESSION['user']['gender']     = $gender;
                $_SESSION['user']['name']       = $first_name . ' ' . $last_name;

                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => 'Cập nhật thông tin thành công!'
                ];
            } else {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Có lỗi xảy ra khi cập nhật!'
                ];
            }

            header("Location: index.php?controller=auth&action=Info");
            exit;
        } else {
            // Nếu chưa đăng nhập
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Bạn cần đăng nhập để thực hiện thao tác này!'
            ];
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
    }

    // QUÊN MẬT KHẨU / KHÔI PHỤC MẬT KHẨU
    public function forgotPassword()
    {
        global $title;
        $title = "Quên mật khẩu | Blossy";
        $this->loadView('User.ForgotPassword_OTP');
    }

    public function handleForgotPassword()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $action = $_POST['action'] ?? '';
            $userModel = new UserModel();

            /* 📨 Gửi OTP qua email */
            if ($action === 'send_otp') {
                $email = trim($_POST['email'] ?? '');

                if (!$userModel->emailExists($email)) {
                    $_SESSION['toast'] = ['type' => 'error', 'message' => '❌ Email không tồn tại!'];
                    header("Location: index.php?controller=auth&action=forgotPassword");
                    exit;
                }

                $otp = rand(100000, 999999);
                $_SESSION['reset_otp'] = [
                    'email' => $email,
                    'code' => $otp,
                    'expires' => time() + 300 // 5 phút
                ];

                require_once __DIR__ . '/../Includes/Mailer.php';
                $sent = sendOTP($email, $otp);

                if ($sent) {
                    $_SESSION['toast'] = ['type' => 'success', 'message' => '📩 Đã gửi mã OTP đến email của bạn!'];
                } else {
                    $_SESSION['toast'] = ['type' => 'error', 'message' => '❌ Gửi OTP thất bại, vui lòng thử lại!'];
                }

                header("Location: index.php?controller=auth&action=forgotPassword");
                exit;
            }

            /* 🔐 Xác thực OTP và đổi mật khẩu */
            if ($action === 'reset_password') {
                $otpInput = trim($_POST['otp'] ?? '');
                $password = trim($_POST['password'] ?? '');
                $confirm  = trim($_POST['confirm_password'] ?? '');
                $otpData  = $_SESSION['reset_otp'] ?? null;

                if (!$otpData) {
                    $_SESSION['toast'] = ['type' => 'error', 'message' => '⚠️ Vui lòng gửi mã OTP trước!'];
                    header("Location: index.php?controller=auth&action=forgotPassword");
                    exit;
                }

                if (time() > $otpData['expires']) {
                    unset($_SESSION['reset_otp']);
                    $_SESSION['toast'] = ['type' => 'error', 'message' => '⏰ Mã OTP đã hết hạn!'];
                    header("Location: index.php?controller=auth&action=forgotPassword");
                    exit;
                }

                if ($otpInput != $otpData['code']) {
                    $_SESSION['toast'] = ['type' => 'error', 'message' => '❌ Mã OTP không chính xác!'];
                    header("Location: index.php?controller=auth&action=forgotPassword");
                    exit;
                }

                if ($password !== $confirm) {
                    $_SESSION['toast'] = ['type' => 'error', 'message' => 'Mật khẩu xác nhận không khớp!'];
                    header("Location: index.php?controller=auth&action=forgotPassword");
                    exit;
                }

                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $updated = $userModel->updatePasswordByEmail($otpData['email'], $password, $hashed);
                unset($_SESSION['reset_otp']);

                if ($updated) {
                    $_SESSION['toast'] = ['type' => 'success', 'message' => '🎉 Cập nhật mật khẩu thành công!'];
                    header("Location: index.php?controller=auth&action=login");
                } else {
                    $_SESSION['toast'] = ['type' => 'error', 'message' => '❌ Lỗi khi đổi mật khẩu!'];
                    header("Location: index.php?controller=auth&action=forgotPassword");
                }
                exit;
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

            // ⚠️ Kiểm tra dữ liệu thiếu
            if (empty($card_holder) || empty($card_number) || empty($expiry_date) || empty($cvv)) {
                $_SESSION['toast'] = [
                    'type' => 'warning',
                    'message' => '⚠️ Vui lòng nhập đầy đủ thông tin thẻ!'
                ];
                header("Location: index.php?controller=auth&action=addNewCard");
                exit;
            }

            $userModel = new UserModel();
            $success = $userModel->addUserCard(
                $userId,
                $card_holder,
                $card_number,     // sẽ tự cắt 4 số cuối trong model
                $expiry_date,
                $card_brand,
                $card_number      // full_card_number
            );

            if ($success) {
                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => 'Thêm thẻ mới thành công!'
                ];
            } else {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Lưu thẻ thất bại, vui lòng thử lại!'
                ];
            }

            header("Location: index.php?controller=auth&action=Info");
            exit;
        } else {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Bạn cần đăng nhập để thêm thẻ!'
            ];
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
    }
    public function deleteCard()
    {
        if (isset($_GET['id']) && isset($_SESSION['user'])) {
            $userId = $_SESSION['user']['user_id'];
            $cardId = (int)$_GET['id'];

            $userModel = new UserModel();
            $deleted = $userModel->deleteUserCard($cardId, $userId);

            if ($deleted) {
                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => 'Xóa thẻ thành công!'
                ];
            } else {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Xóa thẻ thất bại, vui lòng thử lại!'
                ];
            }

            header("Location: index.php?controller=auth&action=Info");
            exit;
        } else {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Bạn cần đăng nhập để thực hiện thao tác này!'
            ];
            header("Location: index.php?controller=auth&action=login");
            exit;
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

        // Kiểm tra có user thật không
        $user = $userModel->getUserById($userId);
        if (!$user) {
            echo "<script>alert('Không tìm thấy thông tin người dùng!'); 
                window.location.href='" . BASE_URL . "index.php?controller=auth&action=login';</script>";
            exit();
        }

        // Lấy danh sách địa chỉ theo user_id
        $address = $userModel->getAddress($userId);


        // ✅ Truyền dữ liệu sang view
        $this->loadView('User.Address', [
            'user' => $user,
            'addresses' => $address
        ]);
    }
    public function HandleUpdateAddress()
{
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['user'])) {
        $userId = $_SESSION['user']['user_id'];
        $address = trim($_POST['address'] ?? '');

        if (empty($address)) {
            $_SESSION['toast'] = [
                'type' => 'warning',
                'message' => '⚠️ Vui lòng nhập địa chỉ!'
            ];
            header("Location: index.php?controller=auth&action=Info");
            exit;
        }

        $userModel = new UserModel();
        $updated = $userModel->updateAddress($userId, $address);

        if ($updated) {
            $_SESSION['user']['address'] = $address;
            $_SESSION['toast'] = [
                'type' => 'success',
                'message' => '✅ Cập nhật địa chỉ thành công!'
            ];
        } else {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => '❌ Cập nhật địa chỉ thất bại!'
            ];
        }

        header("Location: index.php?controller=auth&action=Info");
        exit;
    } else {
        $_SESSION['toast'] = [
            'type' => 'error',
            'message' => 'Vui lòng đăng nhập để cập nhật địa chỉ!'
        ];
        header("Location: index.php?controller=auth&action=login");
        exit;
    }
}




    // Đổi mật khẩu
    public function HandleChangePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user'])) {
            $userId = $_SESSION['user']['user_id'];
            $currentPassword = trim($_POST['current_password'] ?? '');
            $newPassword     = trim($_POST['new_password'] ?? '');
            $confirmPassword = trim($_POST['confirm_password'] ?? '');

            // ⚠️ Kiểm tra nhập thiếu
            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                $_SESSION['toast'] = [
                    'type' => 'warning',
                    'message' => 'Vui lòng nhập đầy đủ các trường mật khẩu!'
                ];
                header("Location: index.php?controller=auth&action=Info");
                exit;
            }

            // ⚠️ Kiểm tra mật khẩu mới khớp xác nhận không
            if ($newPassword !== $confirmPassword) {
                $_SESSION['toast'] = [
                    'type' => 'warning',
                    'message' => 'Mật khẩu mới và xác nhận không khớp!'
                ];
                header("Location: index.php?controller=auth&action=Info");
                exit;
            }

            $userModel = new UserModel();
            $user = $userModel->getUserById($userId);

            // 🧩 Kiểm tra mật khẩu hiện tại
            if (!$user || $user['password'] !== $currentPassword) {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Mật khẩu hiện tại không đúng!'
                ];
                header("Location: index.php?controller=auth&action=Info");
                exit;
            }

            // 🔐 Cập nhật mật khẩu mới (hash nếu cần)
            $success = $userModel->changePassword($userId, $newPassword);

            if ($success) {
                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => 'Đổi mật khẩu thành công!'
                ];
            } else {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Có lỗi xảy ra khi đổi mật khẩu!'
                ];
            }

            header("Location: index.php?controller=auth&action=Info");
            exit;
        } else {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Bạn cần đăng nhập để đổi mật khẩu!'
            ];
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
    }

}
