<?php
require_once __DIR__ . '/BaseModel.php';

class UserModel extends BaseModel {
    public function emailExists($email): bool {
        $stmt = $this->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->rowCount() > 0;
    }

    public function createUser($data): bool {
        $stmt = $this->prepare("INSERT INTO users (email, password, first_name, last_name, phone, address)
                                VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['email'],
            $data['password'],  // đã hash rồi
            $data['first_name'],
            $data['last_name'],
            $data['phone'],
            $data['address']
        ]);
    }
}
