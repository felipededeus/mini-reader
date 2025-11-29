<?php
//setup.php
include '../conexao.class.php';

class SetupDB {
    private $pdo;

    public function __construct() {
        $conexao = new Conexao();
        $this->pdo = $conexao->conectar();
    }

    public function criarTabelas() {
        $sqls = [
            "CREATE TABLE IF NOT EXISTS turmas (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(255) NOT NULL
            );",
            "CREATE TABLE IF NOT EXISTS cronogramas (
                id INT AUTO_INCREMENT PRIMARY KEY,
                turma_id INT,
                titulo VARCHAR(255) NOT NULL,
                descricao TEXT NOT NULL,
                data_inicial DATE NOT NULL,
                data_final DATE NOT NULL,
                versiculos_json JSON NOT NULL,
                FOREIGN KEY (turma_id) REFERENCES turmas(id)
            );",
            "CREATE TABLE IF NOT EXISTS progresso (
                id_progresso INT AUTO_INCREMENT PRIMARY KEY,
                usuario_id INT,
                leitura_id INT,
                progresso_versiculos_json JSON NOT NULL
            );",
            "CREATE TABLE IF NOT EXISTS usuarios (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                senha VARCHAR(255) NOT NULL,
                tipo ENUM('professor', 'aluno') NOT NULL,
                turma_id INT,
                FOREIGN KEY (turma_id) REFERENCES turmas(id)
            );"
        ];

        try {
            foreach ($sqls as $sql) {
                $this->pdo->exec($sql);
            }
            echo "Tabelas criadas com sucesso!<br>";
        } catch (PDOException $e) {
            echo 'Erro ao criar tabelas: ' . $e->getMessage();
        }
    }

    public function adicionarTurma($nome) {
        $sql = "INSERT INTO turmas (nome) VALUES (:nome)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['nome' => $nome]);
        return $this->pdo->lastInsertId();
    }

    public function adicionarUsuario($nome, $email, $senha, $tipo, $turma_id = null) {
        // Verificar se o email já existe
        $sql = "SELECT COUNT(*) FROM usuarios WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        if ($stmt->fetchColumn() > 0) {
            echo "Usuário com email $email já existe.<br>";
            return;
        }

        $sql = "INSERT INTO usuarios (nome, email, senha, tipo, turma_id) VALUES (:nome, :email, :senha, :tipo, :turma_id)";
        $stmt = $this->pdo->prepare($sql);
        $senhaHash = (new Conexao())->hashSenha($senha);
        $stmt->execute(['nome' => $nome, 'email' => $email, 'senha' => $senhaHash, 'tipo' => $tipo, 'turma_id' => $turma_id]);
    }
}

$setup = new SetupDB();
$setup->criarTabelas();

// Criar uma turma antes de adicionar os alunos
$turma_id = $setup->adicionarTurma('Turma Exemplo');

// Exemplo de adição de usuário: professor
$setup->adicionarUsuario('Professor Exemplo', 'professor@example.com', 'senha123', 'professor');
// Exemplo de adição de usuário: aluno
$setup->adicionarUsuario('Aluno Exemplo', 'aluno@example.com', 'senha123', 'aluno', $turma_id);
?>
