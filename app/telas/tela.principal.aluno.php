<?php
session_start();
include '../../conexao.class.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'aluno') {
    header("Location: tela.login.php");
    exit;
}

$aluno_id = $_SESSION['usuario_id'];

$conexao = new Conexao();
$pdo = $conexao->conectar();

// Buscando a turma do aluno
$stmt = $pdo->prepare("SELECT turma_id FROM usuarios WHERE id = :aluno_id AND tipo = 'aluno'");
$stmt->execute(['aluno_id' => $aluno_id]);
$turma = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$turma) {
    die("Turma não encontrada para o aluno.");
}

$turma_id = $turma['turma_id'];





// Buscando as leituras destinadas à turma que ainda não passaram da data final
$stmt = $pdo->prepare("SELECT * FROM cronogramas WHERE turma_id = :turma_id AND data_final >= :data_atual");
$data_atual = date('Y-m-d'); // Obtendo a data atual no formato Y-m-d
$stmt->execute(['turma_id' => $turma_id, 'data_atual' => $data_atual]);
$leituras = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$leituras) {
    die("Nenhuma leitura encontrada para a turma.");
}

//busca nome da turma
$stmt = $pdo->prepare("SELECT nome AS nome_turma FROM turmas WHERE id = :turma_id"); $stmt->execute(['turma_id' => $turma_id]); 
$turma_info = $stmt->fetch(PDO::FETCH_ASSOC); 
if (!$turma_info) { 
    die("Nome da turma não encontrado."); 
} 

$nome_turma = $turma_info['nome_turma'];



function carregarBiblia() {
    $json = file_get_contents('../assets/biblia.json');
    if ($json === false) {
        die("Erro ao carregar o arquivo JSON.");
    }
    // Remover o BOM se existir
    $json = preg_replace('/^\xEF\xBB\xBF/', '', $json);
    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        die("Erro ao decodificar JSON: " . json_last_error_msg());
    }
    return $data;
}

$biblia = carregarBiblia();




// Função para mapear o livro para sua abreviação
function getLivroAbrev($livro) {
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

    return $livrosMap[$livro] ?? null;
}

?>
<?php include '../global/header.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Leituras</title>
    <style>
        .versiculo {
            display: block;
            padding: 5px;
            border: 1px solid #ccc;
            cursor: pointer;
            margin-bottom: 5px;
        }
        .versiculo.checked {
            background-color: #d9edf7;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
    <h2>Leituras para a Turma: <?= htmlspecialchars($nome_turma) ?> </h2>
    <p> Toque para expandir a leitura: </p>
        <div class="accordion" id="leiturasAccordion">
            <?php foreach ($leituras as $index => $leitura): 
                $versiculos_json = json_decode($leitura['versiculos_json'], true); ?>
                <div class="card">
                    <div class="card-header" id="heading<?= $index ?>">
                        <h2 class="mb-0">
                            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse<?= $index ?>" aria-expanded="true" aria-controls="collapse<?= $index ?>">
                                <?= htmlspecialchars($leitura['titulo']) ?>
                            </button>
                        </h2>
                    </div>

                    <div id="collapse<?= $index ?>" class="collapse" aria-labelledby="heading<?= $index ?>" data-parent="#leiturasAccordion">
                        <div class="card-body">
                            <p><?= htmlspecialchars($leitura['descricao']) ?></p>
                            <p>Período: <?= htmlspecialchars(date('d/m/Y', strtotime($leitura['data_inicial']))) ?> a <?= htmlspecialchars(date('d/m/Y', strtotime($leitura['data_final']))) ?></p>
                            
                            <div class="versiculos-container">
                                <?php 
                                foreach ($versiculos_json as $versiculo_ref):
                                    // Identificar e separar corretamente o nome do livro que pode conter números
        if (preg_match('/^\d+\s/', $versiculo_ref)) {
            // Livro começa com um número
            $parts = explode(' ', $versiculo_ref, 3);
            if (count($parts) === 3) {
                $livro = $parts[0] . ' ' . $parts[1]; // Combina a primeira e a segunda parte como o nome do livro
                $capitulo_versiculo = $parts[2];
            } else {
                continue;
            }
        } else {
            // Livro não começa com um número
            $parts = explode(' ', $versiculo_ref, 2);
            if (count($parts) === 2) {
                $livro = $parts[0];
                $capitulo_versiculo = $parts[1];
            } else {
                continue;
            }
        }

        list($capitulo, $versiculo) = explode(':', $capitulo_versiculo);

        $livroAbrev = getLivroAbrev($livro);

        if ($livroAbrev) {
            $texto = "Livro não encontrado."; // Inicialize o texto como padrão
            foreach ($biblia as $b) {
                if (strcasecmp($b['abbrev'], $livroAbrev) == 0) {
                    $texto = $b['chapters'][$capitulo - 1][$versiculo - 1] ?? "Versículo não encontrado"; // Ajustar índice
                    break;
                }
            }
        } else {
            $texto = "Livro não encontrado.";
        }
                                ?>
                                    <!-- Atual -->
                                    <input type="hidden" class="aluno_id" data-leitura-id="<?= $leitura['id'] ?>" value="<?= $aluno_id ?>">
                                    <input type="hidden" class="leitura_id" data-leitura-id="<?= $leitura['id'] ?>" value="<?= $leitura['id'] ?>">

                                    <div class="versiculo" data-ref="<?= $livro . '-' . $capitulo . '-' . $versiculo ?>" data-leitura-id="<?= $leitura['id'] ?>" onclick="toggleCheckbox(this)">
                                         <input type="checkbox" class="checkbox" value="<?= $livro . '-' . $capitulo . '-' . $versiculo ?>">
                                         <span><?= htmlspecialchars($livro . ' ' . $capitulo . ':' . $versiculo . ' - ' . $texto) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
            <?php endforeach; ?>
        </div>
    </div>

    <?php include '../global/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.leitura_id').forEach(leituraInput => {
        const leituraId = leituraInput.value;
        const alunoId = document.querySelector(`.aluno_id[data-leitura-id="${leituraId}"]`).value;

        fetch(`../controllers/obter_progresso.php?aluno_id=${alunoId}&leitura_id=${leituraId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'sucesso') {
                    const progresso = data.progresso;
                    Object.keys(progresso).forEach(versiculo => {
                        const checkbox = document.querySelector(`.checkbox[value="${versiculo}"]`);
                        if (checkbox) {
                            checkbox.checked = progresso[versiculo];
                            if (progresso[versiculo]) {
                                checkbox.closest('.versiculo').classList.add('checked');
                            }
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Erro ao obter progresso:', error);
            });
    });
});

function toggleCheckbox(element) {
    let checkbox = element.querySelector('input[type="checkbox"]');
    checkbox.checked = !checkbox.checked;

    if (checkbox.checked) {
        element.classList.add('checked');
    } else {
        element.classList.remove('checked');
    }

    salvarProgresso(checkbox, element.dataset.leituraId);
}

function salvarProgresso(checkbox, leituraId) {
    const versiculo = checkbox.value;
    const lido = checkbox.checked;
    const aluno_id = document.querySelector(`.aluno_id[data-leitura-id="${leituraId}"]`).value;
    const leitura_id = document.querySelector(`.leitura_id[data-leitura-id="${leituraId}"]`).value;

    fetch('../controllers/salvar_progresso.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ versiculo: versiculo, lido: lido, aluno_id: aluno_id, leitura_id: leitura_id })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Progresso salvo:', data);
    })
    .catch(error => {
        console.error('Erro ao salvar progresso:', error);
    });
}
</script>


</body>
