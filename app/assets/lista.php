<?php
// Caminho para o arquivo JSON da Bíblia
$caminhoJSON = 'biblia.json'; // Certifique-se de que o nome do arquivo está correto

// Verificar se o arquivo JSON existe
if (!file_exists($caminhoJSON)) {
    die("Arquivo JSON não encontrado: $caminhoJSON");
}

// Carregar o JSON da Bíblia
$biblia = file_get_contents($caminhoJSON);

// Remover o BOM (Byte Order Mark) se presente
$biblia = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $biblia);

// Tentar decodificar o JSON
$bibliaData = json_decode($biblia, true);

// Verificar se o JSON foi decodificado corretamente
if (json_last_error() !== JSON_ERROR_NONE) {
    die("Erro ao decodificar JSON: " . json_last_error_msg());
} else {
    echo "JSON decodificado com sucesso!";
}

// Inspecionar os dados decodificados
echo "<pre>";
print_r($bibliaData);
echo "</pre>";
