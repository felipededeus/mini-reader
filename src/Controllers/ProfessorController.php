<?php
class ProfessorController {

    private $db;
    private $pdo;

    public function __construct() {
        // Segurança: Só professor entra aqui
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'professor') {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }
        
        $this->db = new Database();
        $this->pdo = $this->db->getConnection();
    }

    // Tela Principal do Professor
    public function dashboard() {
        $id_prof = $_SESSION['usuario_id'];
        
        // Busca cronogramas criados
        $sql = "SELECT c.*, t.nome as nome_turma 
                FROM cronogramas c 
                JOIN turmas t ON c.turma_id = t.id 
                ORDER BY c.id DESC";
        $stmt = $this->pdo->query($sql);
        $cronogramas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $titulo = "Painel do Professor";
        require_once '../src/Views/layouts/header.php';
        require_once '../src/Views/professor/dashboard.php';
        require_once '../src/Views/layouts/footer.php';
    }

    // Tela de Criação
    public function criar() {
        $turmas = $this->pdo->query("SELECT * FROM turmas")->fetchAll(PDO::FETCH_ASSOC);

        $titulo = "Novo Cronograma";
        require_once '../src/Views/layouts/header.php';
        require_once '../src/Views/professor/criar.php';
        require_once '../src/Views/layouts/footer.php';
    }

    // Salvar Cronograma
    public function salvar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titulo = $_POST['titulo'];
            $descricao = $_POST['descricao'];
            $data_inicial = $_POST['data_inicial'];
            $data_final = $_POST['data_final'];
            $turma_id = $_POST['turma_id'];
            
            $versiculos = isset($_POST['versiculos']) ? json_encode($_POST['versiculos']) : json_encode([]);

            $sql = "INSERT INTO cronogramas (titulo, descricao, data_inicial, data_final, versiculos_json, turma_id) 
                    VALUES (:titulo, :descricao, :data_inicial, :data_final, :versiculos_json, :turma_id)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'titulo' => $titulo,
                'descricao' => $descricao,
                'data_inicial' => $data_inicial,
                'data_final' => $data_final,
                'versiculos_json' => $versiculos,
                'turma_id' => $turma_id
            ]);

            header('Location: ' . BASE_URL . 'professor/dashboard');
            exit;
        }
    }

    // --- LÓGICA DE BUSCA INTELIGENTE ---
    public function ajaxBuscarVersiculos() {
        $livroInput = $_POST['livro'] ?? '';
        $referenciaInput = $_POST['referencia'] ?? ''; // Ex: "1", "1-3", "1:1-5"

        // 1. Carregar Mapa de Livros
        $livrosMap = [
            "Gênesis" => "gn", "Êxodo" => "ex", "Levítico" => "lv", "Números" => "nm", 
            "Deuteronômio" => "dt", "Josué" => "js", "Juízes" => "jz", "Rute" => "rt", 
            "1 Samuel" => "1sm", "2 Samuel" => "2sm", "1 Reis" => "1rs", "2 Reis" => "2rs", 
            "1 Crônicas" => "1cr", "2 Crônicas" => "2cr", "Esdras" => "ed", "Neemias" => "ne", 
            "Ester" => "et", "Jó" => "jó", "Salmos" => "sl", "Provérbios" => "pv", 
            "Eclesiastes" => "ec", "Cantares de Salomão" => "ct", "Isaías" => "is", 
            "Jeremias" => "jr", "Lamentações" => "lm", "Ezequiel" => "ez", "Daniel" => "dn", 
            "Oséias" => "os", "Joel" => "jl", "Amós" => "am", "Obadias" => "ob", 
            "Jonas" => "jn", "Miquéias" => "mq", "Naum" => "na", "Habacuque" => "hc", 
            "Sofonias" => "sf", "Ageu" => "ag", "Zacarias" => "zc", "Malaquias" => "ml", 
            "Mateus" => "mt", "Marcos" => "mc", "Lucas" => "lc", "João" => "jo", 
            "Atos" => "atos", "Romanos" => "rm", "1 Coríntios" => "1co", "2 Coríntios" => "2co", 
            "Gálatas" => "gl", "Efésios" => "ef", "Filipenses" => "fp", "Colossenses" => "cl", 
            "1 Tessalonicenses" => "1ts", "2 Tessalonicenses" => "2ts", "1 Timóteo" => "1tm", 
            "2 Timóteo" => "2tm", "Tito" => "tt", "Filemom" => "fm", "Hebreus" => "hb", 
            "Tiago" => "tg", "1 Pedro" => "1pe", "2 Pedro" => "2pe", "1 João" => "1jo", 
            "2 João" => "2jo", "3 João" => "3jo", "Judas" => "jd", "Apocalipse" => "ap"
        ];

        $sigla = $livrosMap[$livroInput] ?? null;

        if (!$sigla) {
            echo "<div style='padding:10px; color: #d32f2f; background: #ffebee;'>Livro não encontrado. Verifique a acentuação.</div>";
            return;
        }

        // 2. Carregar JSON
        $caminho = '../public/assets/json/biblia.json';
        if (!file_exists($caminho)) $caminho = '../public/assets/biblia.json';
        
        if (!file_exists($caminho)) {
            echo "Erro: Banco de dados bíblico não encontrado.";
            return;
        }

        $conteudo = file_get_contents($caminho);
        $conteudo = preg_replace('/^\xEF\xBB\xBF/', '', $conteudo); 
        $dados = json_decode($conteudo, true);

        // Encontrar o livro no JSON
        $livroData = null;
        foreach ($dados as $l) {
            if ($l['abbrev'] === $sigla) {
                $livroData = $l;
                break;
            }
        }

        if (!$livroData) {
            echo "Erro interno: Livro encontrado no mapa mas não no JSON.";
            return;
        }

        // 3. Interpretador de Expressões
        $versiculosParaMostrar = []; // Formato: ['ref' => 'João 3:16', 'texto' => '...']

        // Caso 1: Versículos Específicos (ex: "1:1-5")
        if (strpos($referenciaInput, ':') !== false) {
            // Regex para pegar CAPITULO:INICIO-FIM
            if (preg_match('/^(\d+):(\d+)-(\d+)$/', $referenciaInput, $matches)) {
                $cap = (int)$matches[1];
                $vInicio = (int)$matches[2];
                $vFim = (int)$matches[3];

                if (isset($livroData['chapters'][$cap - 1])) {
                    $capituloCompleto = $livroData['chapters'][$cap - 1];
                    // Pega o slice do array (ajustando índices)
                    for ($i = $vInicio; $i <= $vFim; $i++) {
                        if (isset($capituloCompleto[$i - 1])) {
                            $versiculosParaMostrar[] = [
                                'ref' => "$livroInput $cap:$i",
                                'texto' => $capituloCompleto[$i - 1]
                            ];
                        }
                    }
                }
            } 
            // Regex para Versículo Único (ex: "3:16")
            elseif (preg_match('/^(\d+):(\d+)$/', $referenciaInput, $matches)) {
                 $cap = (int)$matches[1];
                 $ver = (int)$matches[2];
                 if (isset($livroData['chapters'][$cap - 1][$ver - 1])) {
                     $versiculosParaMostrar[] = [
                         'ref' => "$livroInput $cap:$ver",
                         'texto' => $livroData['chapters'][$cap - 1][$ver - 1]
                     ];
                 }
            }

        } 
        // Caso 2: Faixa de Capítulos (ex: "1-3")
        elseif (strpos($referenciaInput, '-') !== false) {
            if (preg_match('/^(\d+)-(\d+)$/', $referenciaInput, $matches)) {
                $capInicio = (int)$matches[1];
                $capFim = (int)$matches[2];

                for ($c = $capInicio; $c <= $capFim; $c++) {
                    if (isset($livroData['chapters'][$c - 1])) {
                        foreach ($livroData['chapters'][$c - 1] as $idx => $txt) {
                            $vNum = $idx + 1;
                            $versiculosParaMostrar[] = [
                                'ref' => "$livroInput $c:$vNum",
                                'texto' => $txt
                            ];
                        }
                    }
                }
            }
        } 
        // Caso 3: Capítulo Único (ex: "1")
        elseif (is_numeric($referenciaInput)) {
            $c = (int)$referenciaInput;
            if (isset($livroData['chapters'][$c - 1])) {
                foreach ($livroData['chapters'][$c - 1] as $idx => $txt) {
                    $vNum = $idx + 1;
                    $versiculosParaMostrar[] = [
                        'ref' => "$livroInput $c:$vNum",
                        'texto' => $txt
                    ];
                }
            }
        }

        // 4. Renderizar Resultado
        if (empty($versiculosParaMostrar)) {
            echo "<div style='padding:10px; color: #f57f17; background: #fffde7;'>Nenhum versículo encontrado para a expressão '$referenciaInput'. Tente '1', '1-2' ou '1:1-5'.</div>";
            return;
        }

        echo '<div style="background: #fff; border: 1px solid #ccc; border-radius: 8px; padding: 10px; max-height: 400px; overflow-y: auto;">';
        
        // Botão discreto para desmarcar
        echo '<div style="margin-bottom:10px; text-align:right;">
                <small style="cursor:pointer; color:#777;" onclick="desmarcarTodos()">Desmarcar todos</small>
              </div>';

        foreach ($versiculosParaMostrar as $v) {
            echo "
                <label style='display: flex; gap: 10px; padding: 8px; border-bottom: 1px solid #eee; cursor: pointer; align-items: flex-start; background-color: #e8f5e9;'>
                    <input type='checkbox' name='versiculos[]' value='{$v['ref']}' checked style='margin-top: 4px;'>
                    <span style='font-size: 14px; line-height: 1.4;'><b>{$v['ref']}</b> {$v['texto']}</span>
                </label>
            ";
        }
        echo '</div>';
        
        // Scriptzinho inline para o botão desmarcar
        echo '<script>
            function desmarcarTodos() {
                const boxes = document.querySelectorAll("#resultadoVersiculos input[type=checkbox]");
                boxes.forEach(b => {
                    b.checked = false; 
                    b.parentElement.style.backgroundColor = "#fff";
                });
            }
            // Reativa o efeito visual de clique nos novos elementos
            document.querySelectorAll("#resultadoVersiculos input[type=checkbox]").forEach(el => {
                el.addEventListener("change", function() {
                    this.parentElement.style.backgroundColor = this.checked ? "#e8f5e9" : "#fff";
                });
            });
        </script>';
        exit;
    }
}
?>