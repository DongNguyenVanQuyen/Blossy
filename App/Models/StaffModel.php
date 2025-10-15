<?php
require_once 'BaseModel.php';

class StaffModel extends BaseModel
{
    public function getAll()
    {
        $sql = "SELECT * FROM users WHERE role_id = 2 ORDER BY created_at DESC";
        return $this->query($sql);
    }

    public function create($data)
    {
        $sql = "INSERT INTO users (email, first_name, last_name, phone, gender, password_hash, role_id, is_blocked, created_at)
                VALUES (?, ?, ?, ?, ?, ?, 2, 0, NOW())";
        $stmt = $this->conn->prepare($sql);
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        return $stmt->execute([
            $data['email'],
            $data['first_name'],
            $data['last_name'],
            $data['phone'],
            $data['gender'],
            $password
        ]);
    }

    public function update($data)
    {
        $fields = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'gender' => $data['gender']
        ];

        $sql = "UPDATE users SET first_name=?, last_name=?, email=?, phone=?, gender=?";
        $params = array_values($fields);

        if (!empty($data['password'])) {
            $sql .= ", password_hash=?";
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id=?";
        $params[] = $data['id'];

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id=? AND role_id=2");
        return $stmt->execute([$id]);
    }

    public function toggleBlock($id)
    {
        $sql = "UPDATE users 
                SET is_blocked = CASE WHEN is_blocked = 1 THEN 0 ELSE 1 END 
                WHERE id=? AND role_id=2";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }
}
