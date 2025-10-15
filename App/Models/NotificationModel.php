<?php
require_once 'BaseModel.php';

class NotificationModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }
    public function getAll($limit, $offset)
    {
        $sql = "SELECT m.*, u.email AS created_by_email
                FROM messages m
                LEFT JOIN users u ON m.created_by = u.id
                ORDER BY m.created_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAll()
    {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM messages");
        return (int)$stmt->fetchColumn();
    }


    /**
     * 🔹 Lấy danh sách thông báo của người dùng
     * Bao gồm:
     * - Hệ thống / khuyến mãi (cho tất cả user)
     * - Đơn hàng (chỉ user sở hữu)
     * Và tự động tạo dòng message_users nếu chưa có.
     */
    public function getUserNotifications($userId)
    {
        $userId = (int)$userId;

        // --- Lấy danh sách thông báo phù hợp ---
        $sql = "
            SELECT 
              m.*,
                COALESCE(mu.is_read, 0) AS is_read
            FROM messages m
            LEFT JOIN message_users mu 
                ON mu.message_id = m.id AND mu.user_id = :userId
            WHERE 
                -- 🔹 Hiển thị thông báo hệ thống / khuyến mãi cho tất cả user
                m.type IN ('system', 'promotion')

                OR 

                -- 🔹 Hiển thị thông báo đơn hàng nếu user sở hữu đơn đó
                (
                    m.type = 'order'
                    AND EXISTS (
                        SELECT 1 FROM orders o 
                        WHERE o.id = m.order_id 
                          AND o.user_id = :userId
                    )
                )
            ORDER BY m.created_at DESC
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['userId' => $userId]);
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // --- ✅ Tự động thêm vào message_users nếu chưa tồn tại ---
        foreach ($notifications as $n) {
            $check = $this->conn->prepare("
                SELECT 1 FROM message_users 
                WHERE message_id = ? AND user_id = ?
            ");
            $check->execute([$n['id'], $userId]);

            if (!$check->fetch()) {
                $insert = $this->conn->prepare("
                    INSERT INTO message_users (message_id, user_id, is_read)
                    VALUES (?, ?, 0)
                ");
                $insert->execute([$n['id'], $userId]);
            }
        }

        return $notifications;
    }

    /**
     * 🔹 Lấy 1 thông báo cụ thể
     */
    public function getMessageById($messageId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM messages WHERE id = ?");
        $stmt->execute([$messageId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * 🔹 Đánh dấu thông báo đã đọc
     * Nếu chưa có trong message_users thì tự thêm mới.
     */
    public function markAsRead($userId, $messageId)
    {
        $userId = (int)$userId;
        $messageId = (int)$messageId;

        // 🔹 Kiểm tra đã có dòng trong message_users chưa
        $check = $this->conn->prepare("SELECT 1 FROM message_users WHERE user_id = ? AND message_id = ?");
        $check->execute([$userId, $messageId]);
        $exists = $check->fetch(PDO::FETCH_ASSOC);

        if ($exists) {
            // ✅ Nếu có rồi → cập nhật thành đã đọc
            $sql = "UPDATE message_users 
                    SET is_read = 1, read_at = NOW() 
                    WHERE user_id = ? AND message_id = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$userId, $messageId]);
        } else {
            // ✅ Nếu chưa có → thêm mới
            $sql = "INSERT INTO message_users (user_id, message_id, is_read, read_at)
                    VALUES (?, ?, 1, NOW())";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$userId, $messageId]);
        }
    }



    //   Đánh dấu tất cả thông báo là đã đọc cho user

    public function markAllAsRead($userId)
    {
        $userId = (int)$userId;
        $sql = "
            UPDATE message_users 
            SET is_read = 1, read_at = NOW() 
            WHERE user_id = ?
        ";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$userId]);
    }

        public function delete($id)
    {
        // Xóa liên kết trước để tránh khóa ngoại
        $this->conn->prepare("DELETE FROM message_users WHERE message_id=?")->execute([$id]);
        $this->conn->prepare("DELETE FROM messages WHERE id=?")->execute([$id]);
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM messages WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}
