<?php
include '../../conexao.class.php';


$leitura_id = $_GET['id'];
$conexao = new Conexao();
$pdo = $conexao->conectar();

$stmt = $pdo->prepare("SELECT versiculos_json FROM cronogramas WHERE id = :id");
$stmt->execute(['id' => $leitura_id]);
$versiculos_json = $stmt->fetch(PDO::FETCH_ASSOC)['versiculos_json'];

$versiculos_referencias = json_decode($versiculos_json, true);

$biblia = file_get_contents('../assets/biblia.json');
$bibliaData = json_decode($biblia, true);

function obterTextoVersiculo($bibliaData, $referencia) {
    // Separar livro, capítulo e versículo
    if (preg_match('/^(\D+)\s(\d+):(\d+)$/', $referencia, $matches)) {
        list($full, $livro, $capitulo, $versiculo) = $matches;
        $capitulo -= 1; // Ajustar índice do capítulo

        // Encontrar o livro correto
        foreach ($bibliaData as $b) {
            if (strcasecmp($b['book'], $livro) == 0) {
                // Verificar se o capítulo e o versículo existem
                if (isset($b['chapters'][$capitulo][$versiculo - 1])) {
                    return $b['chapters'][$capitulo][$versiculo - 1];
                }
            }
        }
    }
    return null;
}

$html = '';

foreach ($versiculos_referencias as $referencia) {
    $texto_versiculo = obterTextoVersiculo($bibliaData, $referencia);
    if ($texto_versiculo) {
        $html .= "<label class='versiculo'>
                    <input type='checkbox' value='$referencia'>
                    <span>$referencia - $texto_versiculo</span>
                  </label>";
    }
}

echo $html;
?>
