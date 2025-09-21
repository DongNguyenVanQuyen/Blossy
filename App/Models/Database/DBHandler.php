<?php
class DBHandler extends database {

    public function __construct() {
        $db = new database();
        $this->conn = $db->connection_database();
    }

    function readitem($sql): array {
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function execute($sql): void {
        $this->conn->query($sql);
    }
}

?>