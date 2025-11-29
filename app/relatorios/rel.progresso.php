<?php
include '../conexao.class.php';

$aluno_id = 1; // ID do aluno, pode ser dinâmico conforme o login do aluno

$conexao = new Conexao();
$pdo = $conexao->conectar();

$sql = "SELECT lp.livro, lp.capitulo, lp.versiculo FROM progresso_alunos pa JOIN leituras_professor lp ON pa.leitura_id = lp.id WHERE pa.aluno_id = :aluno_id AND pa.lido = TRUE";
$stmt = $pdo->prepare($sql);
$stmt->execute(['aluno_id' => $aluno_id]);

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($result as $leitura) {
    echo "Livro: " . $leitura['livro'] . " Capítulo: " . $leitura['capitulo'] . " Versículo: " . $leitura['versiculo'] . "<br>";
}
?>
