<?php
include '../../conexao.class.php';

$conexao = new Conexao();
$pdo = $conexao->conectar();

$semana_atual = 1; // Defina a semana atual conforme necessário
$sql = "SELECT * FROM leituras_professor WHERE semana = :semana";
$stmt = $pdo->prepare($sql);
$stmt->execute(['semana' => $semana_atual]);

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$html = "<form>";
$html .= "<table class='table table-striped'>";
$html .= "<tr><th>Livro</th><th>Capítulo</th><th>Versículo</th><th>Lido</th></tr>";
foreach ($result as $row) {
    $html .= "<tr>";
    $html .= "<td>{$row['livro']}</td>";
    $html .= "<td>{$row['capitulo']}</td>";
    $html .= "<td>{$row['versiculo']}</td>";
    $html .= "<td><input type='checkbox' data-leitura-id='{$row['id']}'></td>";
    $html .= "</tr>";
}
$html .= "</table>";
$html .= "</form>";

echo $html;
?>
