<?php
$contador = 0;
include '../../conexao.class.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' and $contador < 1) {
    $contador = $contador + 1;
    $acao = $_POST['acao'];
    $conexao = new Conexao();
    $pdo = $conexao->conectar();

    switch ($acao) {
        case 'adicionarUsuario':
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $senha = $_POST['senha'];
            $tipo = $_POST['tipo'];
            $turma_id = !empty($_POST['turma_id']) ? $_POST['turma_id'] : null;

            if (!empty($nome) && !empty($email) && !empty($senha) && !empty($tipo)) {
                $sql = "INSERT INTO usuarios (nome, email, senha, tipo, turma_id) VALUES (:nome, :email, :senha, :tipo, :turma_id)";
                $stmt = $pdo->prepare($sql);
                $senhaHash = $conexao->hashSenha($senha);
                $stmt->execute(['nome' => $nome, 'email' => $email, 'senha' => $senhaHash, 'tipo' => $tipo, 'turma_id' => $turma_id]);
                echo "Usuário adicionado com sucesso!";
            } else {
                echo "Todos os campos são obrigatórios.";
            }
            break;

        case 'editarUsuario':
            $usuario_id = $_POST['usuario_id'];
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $senha = $_POST['senha'];
            $tipo = $_POST['tipo'];
            $turma_id = !empty($_POST['turma_id']) ? $_POST['turma_id'] : null;

            if (!empty($nome) && !empty($email) && !empty($senha) && !empty($tipo) && !empty($usuario_id)) {
                $sql = "UPDATE usuarios SET nome = :nome, email = :email, senha = :senha, tipo = :tipo, turma_id = :turma_id WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $senhaHash = $conexao->hashSenha($senha);
                $stmt->execute(['nome' => $nome, 'email' => $email, 'senha' => $senhaHash, 'tipo' => $tipo, 'turma_id' => $turma_id, 'id' => $usuario_id]);
                echo "Usuário editado com sucesso!";
            } else {
                echo "Todos os campos são obrigatórios.";
            }
            break;

        case 'excluirUsuario':
            $usuario_id = $_POST['usuario_id'];

            if (!empty($usuario_id)) {
                $sql = "DELETE FROM usuarios WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['id' => $usuario_id]);
                echo "Usuário excluído com sucesso!";
            } else {
                echo "ID do usuário é obrigatório.";
            }
            break;

        case 'buscarUsuario':
            $usuario_id = $_POST['usuario_id'];

            if (!empty($usuario_id)) {
                $sql = "SELECT * FROM usuarios WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['id' => $usuario_id]);
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                echo json_encode($usuario);
            } else {
                echo "ID do usuário é obrigatório.";
            }
            break;

        case 'adicionarTurma':
            $nome = $_POST['nome'];

            if (!empty($nome)) {
                $sql = "INSERT INTO turmas (nome) VALUES (:nome)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['nome' => $nome]);
                echo "Turma adicionada com sucesso! e possivelmente".$contador."vezes";
            } else {
                echo "Nome da turma é obrigatório.";
            }
            break;

        case 'editarTurma':
            $turma_id = $_POST['turma_id'];
            $nome = $_POST['nome'];

            if (!empty($nome) && !empty($turma_id)) {
                $sql = "UPDATE turmas SET nome = :nome WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['nome' => $nome, 'id' => $turma_id]);
                echo "Turma editada com sucesso!";
            } else {
                echo "Todos os campos são obrigatórios.";
            }
            break;

        case 'excluirTurma':
            $turma_id = $_POST['turma_id'];

            if (!empty($turma_id)) {
                $sql = "DELETE FROM turmas WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['id' => $turma_id]);
                echo "Turma excluída com sucesso!";
            } else {
                echo "ID da turma é obrigatório.";
            }
            break;

        case 'buscarTurma':
            $turma_id = $_POST['turma_id'];

            if (!empty($turma_id)) {
                $sql = "SELECT * FROM turmas WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['id' => $turma_id]);
                $turma = $stmt->fetch(PDO::FETCH_ASSOC);
                echo json_encode($turma);
            } else {
                echo "ID da turma é obrigatório.";
            }
            break;

        default:
            echo "Ação não reconhecida.";
            break;
    }
}
?>
