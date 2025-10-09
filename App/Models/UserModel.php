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
        $stmt = $this->prepare("
            INSERT INTO users (email, password, first_name, last_name, phone, address)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['email'],
            $data['password'],  // đã hash bên ngoài
            $data['first_name'],
            $data['last_name'],
            $data['phone'],
            $data['address']
        ]);
    }

    // =========================
    // CẬP NHẬT THÔNG TIN NGƯỜI DÙNG
    // =========================
    public function updateUser($data, $userId): bool
    {
        $sql = "
            UPDATE users 
            SET first_name = ?, last_name = ?, phone = ?, address = ?, gender = ?
            WHERE id = ?
        ";
        $stmt = $this->prepare($sql);
        return $stmt->execute([
            $data['first_name'],
            $data['last_name'],
            $data['phone'],
            $data['address'],
            $data['gender'],
            $userId
        ]);
    }

    // =========================
    // QUẢN LÝ ĐỊA CHỈ (BẢNG addresses)
    // =========================
    public function getAddresses($userId): array
    {
        if (empty($userId) || !is_numeric($userId)) {
            return [];
        }
        $sql = "SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC, id DESC";
        $stmt = $this->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addAddress($userId, $address): bool
    {
        if (empty($userId) || empty($address)) {
            return false;
        }
        $sql = "INSERT INTO addresses (user_id, line1, is_default) VALUES (?, ?, 0)";
        $stmt = $this->prepare($sql);
        return $stmt->execute([$userId, $address]);
    }

    public function updateAddress($id, $userId, $address): bool
    {
        if (empty($id) || empty($userId) || empty($address)) {
            return false;
        }
        $sql = "UPDATE addresses SET line1 = ? WHERE id = ? AND user_id = ?";
        $stmt = $this->prepare($sql);
        return $stmt->execute([$address, $id, $userId]);
    }

    public function deleteAddress($id, $userId): bool
    {
        if (empty($id) || empty($userId)) {
            return false;
        }
        $sql = "DELETE FROM addresses WHERE id = ? AND user_id = ?";
        $stmt = $this->prepare($sql);
        return $stmt->execute([$id, $userId]);
    }

    public function changePassword($userId, $newPassword): bool
    {
        if (empty($userId) || empty($newPassword)) {
            return false;
        }
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $this->prepare($sql);
        return $stmt->execute([$newPassword, $userId]);
    }



    // Add Card
      public function addUserCard($userId, $cardHolder, $cardNumber, $expiry, $brand, $fullCardNumber): bool
    {
        // Chỉ lấy 4 số cuối để lưu
        $last4 = substr($cardNumber, -4);
        $fullCardNumber = $cardNumber;

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

    public function deleteUserCard($id, $userId): bool
    {
        $stmt = $this->prepare("DELETE FROM user_cards WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }



    // Order History
    public function getUserOrders($userId): array
    {
        if (empty($userId) || !is_numeric($userId)) {
            return [];
        }

        $sql = "SELECT 
                    id,
                    CONCAT('OD', LPAD(id, 5, '0')) AS code,
                    DATE_FORMAT(created_at, '%d/%m/%Y') AS created_date,
                    status,
                    grand_total,
                    payment_method
                FROM orders
                WHERE user_id = ?
                ORDER BY created_at DESC";
        $stmt = $this->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
