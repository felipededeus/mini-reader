<?php
// controller.cronograma.ajax.php

include '../../conexao.class.php';

if ($_POST['acao'] === 'carregar_versiculos') {
    $livro = $_POST['livro'];
    $capitulo = (int)$_POST['capitulo'] - 1; // Subtrair 1 porque os índices do array começam em 0

    $livrosMap = [
        "Gênesis" => "gn",
        "Êxodo" => "ex",
        "Levítico" => "lv",
        "Números" => "nm",
        "Deuteronômio" => "dt",
        "Josué" => "js",
        "Juízes" => "jz",
        "Rute" => "rt",
        "1 Samuel" => "1sm",
        "2 Samuel" => "2sm",
        "1 Reis" => "1rs",
        "2 Reis" => "2rs",
        "1 Crônicas" => "1cr",
        "2 Crônicas" => "2cr",
        "Esdras" => "ed",
        "Neemias" => "ne",
        "Ester" => "et",
        "Jó" => "jó",
        "Salmos" => "sl",
        "Provérbios" => "pv",
        "Eclesiastes" => "ec",
        "Cantares de Salomão" => "ct",
        "Isaías" => "is",
        "Jeremias" => "jr",
        "Lamentações" => "lm",
        "Ezequiel" => "ez",
        "Daniel" => "dn",
        "Oséias" => "os",
        "Joel" => "jl",
        "Amós" => "am",
        "Obadias" => "ob",
        "Jonas" => "jn",
        "Miquéias" => "mq",
        "Naum" => "na",
        "Habacuque" => "hc",
        "Sofonias" => "sf",
        "Ageu" => "ag",
        "Zacarias" => "zc",
        "Malaquias" => "ml",
        "Mateus" => "mt",
        "Marcos" => "mc",
        "Lucas" => "lc",
        "João" => "jo",
        "Atos" => "atos",
        "Romanos" => "rm",
        "1 Coríntios" => "1co",
        "2 Coríntios" => "2co",
        "Gálatas" => "gl",
        "Efésios" => "ef",
        "Filipenses" => "fp",
        "Colossenses" => "cl",
        "1 Tessalonicenses" => "1ts",
        "2 Tessalonicenses" => "2ts",
        "1 Timóteo" => "1tm",
        "2 Timóteo" => "2tm",
        "Tito" => "tt",
        "Filemom" => "fm",
        "Hebreus" => "hb",
        "Tiago" => "tg",
        "1 Pedro" => "1pe",
        "2 Pedro" => "2pe",
        "1 João" => "1jo",
        "2 João" => "2jo",
        "3 João" => "3jo",
        "Judas" => "jd",
        "Apocalipse" => "ap"
    ];

    if (isset($livrosMap[$livro])) {
        $livroAbrev = $livrosMap[$livro];
    } else {
        echo "Livro não encontrado no mapa de abreviações.";
        exit;
    }

    $biblia = file_get_contents('../assets/biblia.json');
    $biblia = preg_replace('/^\xEF\xBB\xBF/', '', $biblia);
    $bibliaData = json_decode($biblia, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        die("Erro ao decodificar JSON: " . json_last_error_msg());
    }

    $versiculos = null;

    foreach ($bibliaData as $b) {
        if (strcasecmp($b['abbrev'], $livroAbrev) == 0) {
            if (isset($b['chapters'][$capitulo])) {
                $versiculos = $b['chapters'][$capitulo];
            }
            break;
        }
    }

    if ($versiculos) {
        $html = '';
        $capituloExibicao = $capitulo + 1;
        foreach ($versiculos as $indice => $texto) {
            $versiculoNumero = $indice + 1;
            $html .= "<label class='versiculo' style='display: block; padding: 5px; border: 1px solid #ccc; cursor: pointer;'>
                        <input type='checkbox' name='versiculos[]' value='$livro $capituloExibicao:$versiculoNumero' style='margin-right: 10px;'>
                        $livro $capituloExibicao:$versiculoNumero - $texto
                      </label>";
        }
        echo $html;
    } else {
        echo "Nenhum versículo encontrado para o livro $livro, capítulo " . ($capitulo + 1) . ".";
    }
}
?>
