<?php
include '../conexao.class.php';

$conexao = new Conexao();
$pdo = $conexao->conectar();

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';

switch ($acao) {
    case 'adicionar':
        $nome = $_POST['nome'];
        $turma_id = $_POST['turma_id'];
        $sql = "INSERT INTO alunos (nome, turma_id) VALUES (:nome, :turma_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['nome' => $nome, 'turma_id' => $turma_id]);
        echo "Aluno adicionado com sucesso";
        break;

    case 'editar':
        $aluno_id = $_POST['aluno_id'];
        $nome = $_POST['nome'];
        $turma_id = $_POST['turma_id'];
        $sql = "UPDATE alunos SET nome = :nome, turma_id = :turma_id WHERE id = :aluno_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['nome' => $nome, 'turma_id' => $turma_id, 'aluno_id' => $aluno_id]);
        echo "Aluno atualizado com sucesso";
        break;

    case 'excluir':
        $aluno_id = $_POST['aluno_id'];
        $sql = "DELETE FROM alunos WHERE id = :aluno_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['aluno_id' => $aluno_id]);
        echo "Aluno excluído com sucesso";
        break;

    case 'obter':
        $aluno_id = $_GET['aluno_id'];
        $sql = "SELECT * FROM alunos WHERE id = :aluno_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['aluno_id' => $aluno_id]);
        $aluno = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($aluno);
        break;

    case 'listar':
        $sql = "SELECT a.id, a.nome, t.nome AS turma FROM alunos a JOIN turmas t ON a.turma_id = t.id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['data' => $result]);
        break;

    default:
        echo "Ação inválida.";
}
?>
