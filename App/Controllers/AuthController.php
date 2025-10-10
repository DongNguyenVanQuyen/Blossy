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
                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => 'Đăng nhập thành công!'
                ];

                header("Location: " . BASE_URL . "index.php");
                exit();
            } else {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => '❌ Email hoặc mật khẩu không đúng!'
                ];
                header("Location: index.php?controller=auth&action=login");
                exit;
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
            $_SESSION['toast'] = [
                'type' => 'success',
                'message' => '✅ Đăng ký thành công! Vui lòng đăng nhập.'
            ];
            header("Location: index.php?controller=auth&action=login");
            exit;
        } else {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => '❌ Có lỗi xảy ra khi đăng ký!'
            ];
            header("Location: index.php?controller=auth&action=register");
            exit;
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
                'message' => '⚠️ Vui lòng nhập đầy đủ thông tin!'
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
                'message' => '✅ Cập nhật thông tin thành công!'
            ];
        } else {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => '❌ Có lỗi xảy ra khi cập nhật!'
            ];
        }

        header("Location: index.php?controller=auth&action=Info");
        exit;
    } else {
        // Nếu chưa đăng nhập
        $_SESSION['toast'] = [
            'type' => 'error',
            'message' => '⚠️ Bạn cần đăng nhập để thực hiện thao tác này!'
        ];
        header("Location: index.php?controller=auth&action=login");
        exit;
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
                    'message' => '✅ Thêm thẻ mới thành công!'
                ];
            } else {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => '❌ Lưu thẻ thất bại, vui lòng thử lại!'
                ];
            }

            header("Location: index.php?controller=auth&action=Info");
            exit;
        } else {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => '⚠️ Bạn cần đăng nhập để thêm thẻ!'
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
                    'message' => '✅ Xóa thẻ thành công!'
                ];
            } else {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => '❌ Xóa thẻ thất bại, vui lòng thử lại!'
                ];
            }

            header("Location: index.php?controller=auth&action=Info");
            exit;
        } else {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => '⚠️ Bạn cần đăng nhập để thực hiện thao tác này!'
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
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['user'])) {
            $userId = $_SESSION['user']['user_id'];
            $address = trim($_POST['address'] ?? '');
            $id = $_POST['id'] ?? '';

            if (empty($address)) {
                $_SESSION['toast'] = [
                    'type' => 'warning',
                    'message' => '⚠️ Vui lòng nhập địa chỉ!'
                ];
                header("Location: index.php?controller=auth&action=Info");
                exit;
            }

            $userModel = new UserModel();

            // Nếu có id => sửa, không có => thêm mới
            if (!empty($id)) {
                $updated = $userModel->updateAddress($id, $userId, $address);

                if ($updated) {
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
            } else {
                $added = $userModel->addAddress($userId, $address);

                if ($added) {
                    $_SESSION['toast'] = [
                        'type' => 'success',
                        'message' => '✅ Thêm địa chỉ mới thành công!'
                    ];
                } else {
                    $_SESSION['toast'] = [
                        'type' => 'error',
                        'message' => '❌ Không thể thêm địa chỉ, vui lòng thử lại!'
                    ];
                }
            }

            header("Location: index.php?controller=auth&action=Info");
            exit;
        } else {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => '⚠️ Bạn cần đăng nhập để quản lý địa chỉ!'
            ];
            header("Location: index.php?controller=auth&action=login");
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
            $deleted = $userModel->deleteAddress($id, $userId);

            if ($deleted) {
                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => '🗑️ Xóa địa chỉ thành công!'
                ];
            } else {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => '❌ Không thể xóa địa chỉ!'
                ];
            }

            header("Location: index.php?controller=auth&action=Info");
            exit;
        } else {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => '⚠️ Không xác định được địa chỉ cần xóa!'
            ];
            header("Location: index.php?controller=auth&action=Info");
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
                    'message' => '⚠️ Vui lòng nhập đầy đủ các trường mật khẩu!'
                ];
                header("Location: index.php?controller=auth&action=Info");
                exit;
            }

            // ⚠️ Kiểm tra mật khẩu mới khớp xác nhận không
            if ($newPassword !== $confirmPassword) {
                $_SESSION['toast'] = [
                    'type' => 'warning',
                    'message' => '⚠️ Mật khẩu mới và xác nhận không khớp!'
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
                    'message' => '❌ Mật khẩu hiện tại không đúng!'
                ];
                header("Location: index.php?controller=auth&action=Info");
                exit;
            }

            // 🔐 Cập nhật mật khẩu mới (hash nếu cần)
            $success = $userModel->changePassword($userId, $newPassword);

            if ($success) {
                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => '✅ Đổi mật khẩu thành công!'
                ];
            } else {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => '❌ Có lỗi xảy ra khi đổi mật khẩu!'
                ];
            }

            header("Location: index.php?controller=auth&action=Info");
            exit;
        } else {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => '⚠️ Bạn cần đăng nhập để đổi mật khẩu!'
            ];
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
    }

}
