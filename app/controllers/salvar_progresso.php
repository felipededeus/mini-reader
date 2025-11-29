<?php
include '../../conexao.class.php';

// Receber dados do JavaScript
$input = json_decode(file_get_contents('php://input'), true);
$versiculo = $input['versiculo'];
$lido = $input['lido'];
$aluno_id = $input['aluno_id'];
$leitura_id = $input['leitura_id'];

// var_dump($leitura_id); // Debug para verificar leitura_id

$conexao = new Conexao();
$pdo = $conexao->conectar();

if ($pdo) {
    try {
        // Verificar se o registro de progresso já existe para este usuário e leitura
        $sql = "SELECT progresso_versiculos_json FROM progresso WHERE usuario_id = :usuario_id AND leitura_id = :leitura_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['usuario_id' => $aluno_id, 'leitura_id' => $leitura_id]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            // Atualizar o estado de leitura no progresso existente
            $progresso = json_decode($resultado['progresso_versiculos_json'], true);
            $progresso[$versiculo] = $lido;
            $progresso_json = json_encode($progresso);

            $sql = "UPDATE progresso SET progresso_versiculos_json = :progresso WHERE usuario_id = :usuario_id AND leitura_id = :leitura_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['progresso' => $progresso_json, 'usuario_id' => $aluno_id, 'leitura_id' => $leitura_id]);
        } else {
            // Criar um novo registro de progresso se não existir
            $progresso = [$versiculo => $lido];
            $progresso_json = json_encode($progresso);

            $sql = "INSERT INTO progresso (usuario_id, leitura_id, progresso_versiculos_json) VALUES (:usuario_id, :leitura_id, :progresso)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['usuario_id' => $aluno_id, 'leitura_id' => $leitura_id, 'progresso' => $progresso_json]);
        }

        echo json_encode(['status' => 'sucesso']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Falha na conexão com o banco de dados']);
}
?>
