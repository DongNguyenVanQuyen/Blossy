<?php
require_once __DIR__ . '/Database/Database.php';

class BaseModel {
    public $conn;

    public function __construct() {
        $db = new database();
        $this->conn = $db->connection_database();
    }

    protected function query(string $sql): array {
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function execute(string $sql, array $params = []): bool {
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    protected function queryOne(string $sql): ?array {
        $stmt = $this->conn->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    protected function readitem(string $sql, array $params = []): array {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function prepare($sql): PDOStatement {
        return $this->conn->prepare($sql);
    }
}
