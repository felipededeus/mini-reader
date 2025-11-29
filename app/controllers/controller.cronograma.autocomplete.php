<?php
include '../../conexao.class.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['acao'] == 'carregar_versiculos') {
    $livro = urlencode($_POST['livro']);
    $capitulo = urlencode($_POST['capitulo']);

    // URL da Bible API com parâmetros escapados
    $url = "https://bible-api.com/{$livro}%20{$capitulo}";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Erro no cURL: ' . curl_error($ch);
    } else {
        $versiculos = json_decode($response, true);

        if (!empty($versiculos['verses'])) {
            echo '<form method="POST" action="../controllers/controller.cronograma.php">';
            echo '<table class="table">';
            echo '<thead><tr><th>Versículo</th><th>Texto</th><th>Incluir</th></tr></thead><tbody>';
            foreach ($versiculos['verses'] as $versiculo) {
                echo '<tr>';
                echo '<td>' . $versiculo['verse'] . '</td>';
                echo '<td>' . $versiculo['text'] . '</td>';
                echo '<td><input type="checkbox" name="versiculos[' . $versiculo['verse'] . ']"></td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
            echo '<input type="hidden" name="livro" value="' . $livro . '">';
            echo '<input type="hidden" name="capitulo" value="' . $capitulo . '">';
            echo '<input type="hidden" name="semana" value="1">'; // Atualize conforme necessário
            echo '<input type="submit" value="Salvar Cronograma">';
            echo '</form>';
        } else {
            echo 'Nenhum versículo encontrado.';
        }
    }
    curl_close($ch);
}

?>
