<?php
require_once __DIR__ . '/BaseModel.php';

class UserModel extends BaseModel
{
    // =========================
    // LẤY THÔNG TIN NGƯỜI DÙNG
    // =========================
    public function getUserById($id): ?array
    {
        if (empty($id) || !is_numeric($id)) {
            return null;
        }

        $stmt = $this->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    // =========================
    // KIỂM TRA EMAIL TỒN TẠI
    // =========================
    public function emailExists($email): bool
    {
        $stmt = $this->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->rowCount() > 0;
    }

    // =========================
    // TẠO NGƯỜI DÙNG MỚI
    // =========================
    public function createUser($data): bool
    {
        $hashed = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt = $this->prepare("
            INSERT INTO users (email, password, password_hash, first_name, last_name, phone, address)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $data['email'],
            $data['password'],   // lưu mật khẩu bình thường
            $hashed,             // lưu mật khẩu hash
            $data['first_name'],
            $data['last_name'],
            $data['phone'],
            $data['address']
        ]);
    }

    // =========================
    // QUÊN MẬT KHẨU → CẬP NHẬT CẢ 2 CỘT
    // =========================
    public function updatePasswordByEmail($email, $password, $hashed): bool
    {
        $sql = "UPDATE users SET password = ?, password_hash = ? WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$password, $hashed, $email]);
    }

    // =========================
    // ĐỔI MẬT KHẨU (THEO user_id)
    // =========================
    public function changePassword($userId, $newPassword): bool
    {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ?, password_hash = ? WHERE id = ?";
        $stmt = $this->prepare($sql);
        return $stmt->execute([$newPassword, $hashed, $userId]);
    }

    // =========================
    // CẬP NHẬT THÔNG TIN NGƯỜI DÙNG
    // =========================
    public function updateUserInfo($userId, $data): bool
    {
        $sql = "UPDATE users 
                SET first_name = :first_name,
                    last_name = :last_name,
                    phone = :phone,
                    gender = :gender
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'phone'      => $data['phone'],
            'gender'     => $data['gender'],
            'id'         => $userId
        ]);
    }

    // QUẢN LÝ ĐỊA CHỈ
    public function getAddress($userId): ?string
    {
        if (empty($userId) || !is_numeric($userId)) {
            return null;
        }

        $sql = "SELECT address FROM users WHERE id = ?";
        $stmt = $this->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchColumn() ?: null;
    }

    public function updateAddress($userId, $address): bool
    {
        if (empty($userId) || empty($address)) {
            return false;
        }

        $sql = "UPDATE users SET address = ? WHERE id = ?";
        $stmt = $this->prepare($sql);
        return $stmt->execute([$address, $userId]);
    }

    // =========================
    // QUẢN LÝ THẺ THANH TOÁN
    // =========================
    public function addUserCard($userId, $cardHolder, $cardNumber, $expiry, $brand, $fullCardNumber): bool
    {
        $last4 = substr($cardNumber, -4);
        $sql = "INSERT INTO user_cards (user_id, card_holder, card_number_last4, expiry_date, card_brand, full_card_number)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->prepare($sql);
        return $stmt->execute([$userId, $cardHolder, $last4, $expiry, $brand, $fullCardNumber]);
    }

    public function getUserCards($userId): array
    {
        $stmt = $this->prepare("SELECT * FROM user_cards WHERE user_id = ? ORDER BY id DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteUserCard($cardId, $userId): bool
    {
        $sql = "DELETE FROM user_cards WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'id' => $cardId,
            'user_id' => $userId
        ]);
    }

    // =========================
    // LỊCH SỬ ĐƠN HÀNG
    // =========================
    public function getUserOrdersPaginated($userId, $limit = 10, $offset = 0): array
    {
        $sql = "SELECT 
                    id,
                    CONCAT('OD', LPAD(id, 5, '0')) AS code,
                    DATE_FORMAT(created_at, '%d/%m/%Y') AS created_date,
                    status,
                    grand_total,
                    payment_method
                FROM orders
                WHERE user_id = ?
                ORDER BY created_at DESC
                LIMIT $limit OFFSET $offset";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countUserOrders($userId): int
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }
}
