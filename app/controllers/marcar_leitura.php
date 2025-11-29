<?php
include '../conexao.class.php';

$leitura_id = $_POST['leitura_id'];
$lido = $_POST['lido'];
$aluno_id = 1; // ID do aluno, pode ser dinâmico conforme o login do aluno

$conexao = new Conexao();
$pdo = $conexao->conectar();

$sql = "INSERT INTO progresso_alunos (aluno_id, leitura_id, lido) VALUES (:aluno_id, :leitura_id, :lido)
        ON DUPLICATE KEY UPDATE lido = :lido";
$stmt = $pdo->prepare($sql);
$stmt->execute(['aluno_id' => $aluno_id, 'leitura_id' => $leitura_id, 'lido' => $lido]);

echo "Leitura marcada com sucesso";
?>
