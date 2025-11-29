<?php
include '../../conexao.class.php';

$aluno_id = $_GET['aluno_id'];
$leitura_id = $_GET['leitura_id'];

$conexao = new Conexao();
$pdo = $conexao->conectar();

if ($pdo) {
    try {
        $sql = "SELECT progresso_versiculos_json FROM progresso WHERE usuario_id = :usuario_id AND leitura_id = :leitura_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['usuario_id' => $aluno_id, 'leitura_id' => $leitura_id]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            echo json_encode(['status' => 'sucesso', 'progresso' => json_decode($resultado['progresso_versiculos_json'], true)]);
        } else {
            echo json_encode(['status' => 'sucesso', 'progresso' => []]);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Falha na conexão com o banco de dados']);
}
?>
