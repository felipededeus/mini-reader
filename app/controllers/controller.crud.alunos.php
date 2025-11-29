<?php
include '../../conexao.class.php';

$conexao = new Conexao();
$pdo = $conexao->conectar();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $acao = $_POST['acao'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $turma_id = $_POST['turma_id'];

    if ($acao === 'adicionar') {
        $senha = password_hash($_POST['senha'], PASSWORD_BCRYPT);
        $sql = "INSERT INTO alunos (nome, email, senha, turma_id) VALUES (:nome, :email, :senha, :turma_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nome' => $nome,
            'email' => $email,
            'senha' => $senha,
            'turma_id' => $turma_id
        ]);
        echo "Aluno adicionado com sucesso!";
    } elseif ($acao === 'editar') {
        $id = $_POST['id'];
        $senha = !empty($_POST['senha']) ? password_hash($_POST['senha'], PASSWORD_BCRYPT) : null;
        if ($senha) {
            $sql = "UPDATE alunos SET nome = :nome, email = :email, senha = :senha, turma_id = :turma_id WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'nome' => $nome,
                'email' => $email,
                'senha' => $senha,
                'turma_id' => $turma_id,
                'id' => $id
            ]);
        } else {
            $sql = "UPDATE alunos SET nome = :nome, email = :email, turma_id = :turma_id WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'nome' => $nome,
                'email' => $email,
                'turma_id' => $turma_id,
                'id' => $id
            ]);
        }
        echo "Aluno editado com sucesso!";
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
    if ($_GET['acao'] === 'obter') {
        $id = $_GET['id'];
        $stmt = $pdo->prepare("SELECT * FROM alunos WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $aluno = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($aluno);
    }
}
?>
