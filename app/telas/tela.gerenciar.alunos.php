<?php
session_start();
include '../../conexao.class.php';

// Verificar se o usuário está logado e é professor
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'professor') {
    header("Location: tela.login.php");
    exit;
}

$conexao = new Conexao();
$pdo = $conexao->conectar();

// Buscando todos os alunos e turmas
$stmt = $pdo->query("SELECT alunos.*, turmas.nome as turma_nome FROM alunos LEFT JOIN turmas ON alunos.turma_id = turmas.id");
$alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscando todas as turmas
$stmt = $pdo->query("SELECT * FROM turmas");
$turmas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gerenciar Alunos</title>
    <link rel="stylesheet" href="../assets/jquery-ui-1.14.1.custom/jquery-ui.min.css" type="text/css">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/custom.css" type="text/css">
</head>
<body>
    <div class="container">
        <h2>Gerenciar Alunos</h2>

        <!-- Botão para abrir o modal de adicionar aluno -->
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#alunoModal">Adicionar Novo Aluno</button>

        <!-- Tabela de alunos -->
        <h3>Alunos Cadastrados</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Turma</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alunos as $aluno): ?>
                    <tr>
                        <td><?= htmlspecialchars($aluno['nome']) ?></td>
                        <td><?= htmlspecialchars($aluno['email']) ?></td>
                        <td><?= htmlspecialchars($aluno['turma_nome']) ?></td>
                        <td>
                            <button class="btn btn-warning editarAluno" data-id="<?= $aluno['id'] ?>">Editar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Modal para adicionar/editar aluno -->
        <div class="modal fade" id="alunoModal" tabindex="-1" role="dialog" aria-labelledby="alunoModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="alunoModalLabel">Adicionar Novo Aluno</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="alunoForm">
                            <input type="hidden" name="acao" id="acao" value="adicionar">
                            <input type="hidden" name="id" id="aluno_id">
                            <div class="form-group">
                                <label for="nome">Nome:</label>
                                <input type="text" class="form-control" name="nome" id="nome" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" class="form-control" name="email" id="email" required>
                            </div>
                            <div class="form-group">
                                <label for="senha">Senha:</label>
                                <input type="password" class="form-control" name="senha" id="senha">
                            </div>
                            <div class="form-group">
                                <label for="turma_id">Turma:</label>
                                <select class="form-control" name="turma_id" id="turma_id" required>
                                    <?php foreach ($turmas as $turma): ?>
                                        <option value="<?= $turma['id'] ?>"><?= htmlspecialchars($turma['nome']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/jquery.min.js"></script>
    <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function() {
        $('.editarAluno').click(function() {
            const alunoId = $(this).data('id');
            $.get('../controllers/controller.crud.aluno.php', { acao: 'obter', id: alunoId }, function(data) {
                const aluno = JSON.parse(data);
                $('#acao').val('editar');
                $('#aluno_id').val(aluno.id);
                $('#nome').val(aluno.nome);
                $('#email').val(aluno.email);
                $('#senha').val(''); // senha vazia
                $('#turma_id').val(aluno.turma_id);
                $('#alunoModalLabel').text('Editar Aluno');
                $('#alunoModal').