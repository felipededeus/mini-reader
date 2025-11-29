<?php
class Conexao {
    private $host = 'localhost';
    private $db = 'mini_reader';
    private $user = 'minireaderdbu';
    private $pass = 'Minidbc5h7a1!';
    private $pdo;

    public function conectar() {
        try {
            $this->pdo = new PDO("mysql:host={$this->host};dbname={$this->db}", $this->user, $this->pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->pdo;
        } catch (PDOException $e) {
            echo 'Erro na conexão: ' . $e->getMessage();
        }
    }

    public function hashSenha($senha) {
        return password_hash($senha, PASSWORD_DEFAULT);
    }

    public function verificarSenha($senha, $hash) {
        return password_verify($senha, $hash);
    }
}
?>
