<?php
session_start();
include '../../conexao.class.php';

$input = json_decode(file_get_contents('php://input'), true);
$usuario_id = $input['usuario_id'];
$leitura_id = $input['leitura_id'];
$progresso_versiculos_json = json_encode($input['progresso_versiculos_json']);

$conexao = new Conexao();
$pdo = $conexao->conectar();

// Verificar se já existe um registro de progresso para o aluno/leitura
$stmt = $pdo->prepare('SELECT id_progresso FROM progresso WHERE usuario_id = :usuario_id AND leitura_id = :leitura_id');
$stmt->execute(['usuario_id' => $usuario_id, 'leitura_id' => $leitura_id]);
$id_progresso = $stmt->fetchColumn();

if ($id_progresso) {
    // Atualizar o progresso existente
    $stmt = $pdo->prepare('UPDATE progresso SET progresso_versiculos_json = :progresso_versiculos_json WHERE id_progresso = :id_progresso');
    $success = $stmt->execute([
        'progresso_versiculos_json' => $progresso_versiculos_json,
        'id_progresso' => $id_progresso
    ]);
} else {
    // Inserir novo progresso
    $stmt = $pdo->prepare('INSERT INTO progresso (usuario_id, leitura_id, progresso_versiculos_json) VALUES (:usuario_id, :leitura_id, :progresso_versiculos_json)');
    $success = $stmt->execute([
        'usuario_id' => $usuario_id,
        'leitura_id' => $leitura_id,
        'progresso_versiculos_json' => $progresso_versiculos_json
    ]);
}

if ($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->errorInfo()]);
}
?>
