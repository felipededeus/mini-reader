<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'mini_reader';
    private $username = 'minireaderdbu'; 
    private $password = 'passwrd';
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            echo "Erro de Conexão: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
