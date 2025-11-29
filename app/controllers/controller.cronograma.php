<?php
include '../../conexao.class.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'];
    $conexao = new Conexao();
    $pdo = $conexao->conectar();

    if ($acao === 'criar') {
        $titulo = $_POST['titulo'];
        $descricao = $_POST['descricao'];
        $data_inicial = $_POST['data_inicial'];
        $data_final = $_POST['data_final'];
        $versiculosSelecionados = json_encode($_POST['versiculos']);
        $turma_id = $_POST['turma_id'];

        $sql = "INSERT INTO cronogramas (titulo, descricao, data_inicial, data_final, versiculos_json, turma_id) VALUES (:titulo, :descricao, :data_inicial, :data_final, :versiculos_json, :turma_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'titulo' => $titulo,
            'descricao' => $descricao,
            'data_inicial' => $data_inicial,
            'data_final' => $data_final,
            'versiculos_json' => $versiculosSelecionados,
            'turma_id' => $turma_id
        ]);

        echo "Cronograma criado com sucesso!";
    } else {
        echo "Ação não reconhecida.";
    }
}
?>
