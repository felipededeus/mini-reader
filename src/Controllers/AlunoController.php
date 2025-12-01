<?php
class AlunoController {

    private $db;
    private $pdo;

    public function __construct() {
        // Verifica se está logado como aluno
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'aluno') {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }
        
        $this->db = new Database();
        $this->pdo = $this->db->getConnection();
    }

    public function dashboard() {
        $aluno_id = $_SESSION['usuario_id'];
        $nome_aluno = $_SESSION['usuario_nome'] ?? 'Aluno';
        $cronogramas = [];
        $meuProgresso = [];
        
        // 1. Descobrir a turma do aluno
        $stmt = $this->pdo->prepare("SELECT turma_id FROM usuarios WHERE id = :id");
        $stmt->execute(['id' => $aluno_id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // 2. Só prossegue se o aluno estiver em uma turma
        if ($usuario && $usuario['turma_id']) {
            $turma_id = $usuario['turma_id'];

            // Buscar cronogramas ativos da turma
            $hoje = date('Y-m-d');
            $stmt = $this->pdo->prepare("SELECT * FROM cronogramas WHERE turma_id = :turma_id AND data_final >= :hoje ORDER BY data_inicial ASC");
            $stmt->execute(['turma_id' => $turma_id, 'hoje' => $hoje]);
            $cronogramas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 3. Carregar o progresso do aluno para as leituras encontradas
            if (!empty($cronogramas)) {
                $stmt = $this->pdo->prepare("SELECT leitura_id, progresso_versiculos_json FROM progresso WHERE usuario_id = :id");
                $stmt->execute(['id' => $aluno_id]);
                $progressos_raw = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); 

                foreach ($progressos_raw as $lid => $json) {
                    $meuProgresso[$lid] = json_decode($json, true);
                }
            }

            // 4. Calcular porcentagem e anexar ao cronograma (Dashboard Card Info)
            foreach ($cronogramas as &$c) {
                $versiculosPlanejados = json_decode($c['versiculos_json'], true);
                $progressoAtual = $meuProgresso[$c['id']] ?? [];
                
                $totalV = count($versiculosPlanejados);
                $lidosV = 0;
                
                // Contar quantos versículos estão marcados como TRUE
                foreach($versiculosPlanejados as $v) {
                    if(isset($progressoAtual[$v]) && $progressoAtual[$v] === true) {
                        $lidosV++;
                    }
                }
                $c['porcentagem'] = $totalV > 0 ? round(($lidosV / $totalV) * 100) : 0;
                $c['lidos_count'] = $lidosV;
                $c['total_count'] = $totalV;
            }
            unset($c); // Boa prática para limpar a referência
        }

        // Passa tudo para a View
        $titulo = "Minhas Missões";
        require_once '../src/Views/layouts/header.php';
        require_once '../src/Views/aluno/dashboard.php';
        require_once '../src/Views/layouts/footer.php';
    }


    // Carregar a Interface de Leitura Imersiva (Ler)
    public function ler($leituraId) {
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'aluno') {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }
        $aluno_id = $_SESSION['usuario_id'];
        
        // 1. Busca o cronograma específico e a turma
        $stmt = $this->pdo->prepare("
            SELECT c.*, u.turma_id FROM cronogramas c
            JOIN usuarios u ON u.turma_id = c.turma_id
            WHERE c.id = :id AND u.id = :uid
        ");
        $stmt->execute(['id' => $leituraId, 'uid' => $aluno_id]);
        $cronograma = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cronograma) {
            header('Location: ' . BASE_URL . 'aluno/dashboard');
            exit;
        }

        // 2. Carrega Bíblia (JSON)
        $caminhoBiblia = '../public/assets/json/biblia.json';
        if (!file_exists($caminhoBiblia)) $caminhoBiblia = '../public/assets/biblia.json';
        
        if (!file_exists($caminhoBiblia)) {
            $titulo = "Erro";
            require_once '../src/Views/layouts/header.php';
            echo "<div class='content-body'><p>Erro: Banco de dados da Bíblia não encontrado.</p></div>";
            require_once '../src/Views/layouts/footer.php';
            return;
        }

        $conteudo = file_get_contents($caminhoBiblia);
        $conteudo = preg_replace('/^\xEF\xBB\xBF/', '', $conteudo); 
        $bibliaData = json_decode($conteudo, true);

        // Progresso do aluno para esta leitura específica
        $stmt = $this->pdo->prepare("SELECT progresso_versiculos_json FROM progresso WHERE usuario_id = :uid AND leitura_id = :lid");
        $stmt->execute(['uid' => $aluno_id, 'lid' => $leituraId]);
        $progressoAtual = json_decode($stmt->fetchColumn() ?: '[]', true);

        // 3. Mapa de Livros (COPIADO DO PROFESSOR CONTROLLER PARA GARANTIR CONSISTÊNCIA)
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
        
        // 4. Processa Versículos (Monta o array que será exibido na tela)
        $versiculosReferencias = json_decode($cronograma['versiculos_json'], true);
        $leiturasComTexto = [];
        
        foreach ($versiculosReferencias as $ref) {
            if (preg_match('/^(.+?)\s(\d+):(\d+)$/', $ref, $matches)) {
                $livroNome = $matches[1];
                $cap = (int)$matches[2];
                $ver = (int)$matches[3];

                $texto = "Texto não encontrado.";
                
                // Encontra a abreviação
                $sigla = $livrosMap[$livroNome] ?? null;

                // Busca o texto usando a sigla (mais robusto)
                if ($sigla) {
                    foreach ($bibliaData as $b) {
                        // Verifica se o livro no JSON corresponde à sigla
                        if (isset($b['abbrev']) && $b['abbrev'] == $sigla) {
                            if (isset($b['chapters'][$cap - 1][$ver - 1])) {
                                $texto = $b['chapters'][$cap - 1][$ver - 1];
                            }
                            break;
                        }
                    }
                }
                
                $leiturasComTexto[] = [
                    'referencia' => $ref,
                    'capitulo' => $cap,
                    'versiculo' => $ver,
                    'livro' => $livroNome,
                    'texto' => $texto,
                    'lido' => isset($progressoAtual[$ref]) && $progressoAtual[$ref] === true
                ];
            }
        }

        // Passa os dados processados para a nova View
        $titulo = $cronograma['titulo'];
        require_once '../src/Views/layouts/header.php';
        require_once '../src/Views/aluno/ler.php'; // Interface de Leitura Imersiva
        require_once '../src/Views/layouts/footer.php';
    }


    // Método para salvar o progresso (Chamado via AJAX pelo scroll)
    public function salvarProgresso() {
        
        // 1. Define o cabeçalho de resposta como JSON.
        header('Content-Type: application/json');

        // Recebe o JSON do Javascript
        $input = json_decode(file_get_contents('php://input'), true);
        
        // 2. Limpa o buffer de saída (CRÍTICO)
        // Discarda qualquer HTML que tenha sido escrito antes ou depois do Controller
        if (ob_get_level() > 0) {
            ob_end_clean();
        }

        if (!$input) {
            echo json_encode(['sucesso' => false, 'msg' => 'Erro 1: Dados de entrada inválidos ou JSON vazio.']);
            exit;
        }

        // --- DADOS RECEBIDOS ---
        $aluno_id = $_SESSION['usuario_id']; 
        $leitura_id = $input['leitura_id'];
        $versiculo_ref = $input['versiculo']; 
        $status = $input['lido']; 
        // -------------------------

        // Tenta salvar no banco de dados
        try {
            // 1. Busca progresso atual
            $stmt = $this->pdo->prepare("SELECT id_progresso, progresso_versiculos_json FROM progresso WHERE usuario_id = :uid AND leitura_id = :lid");
            $stmt->execute(['uid' => $aluno_id, 'lid' => $leitura_id]);
            $registro = $stmt->fetch(PDO::FETCH_ASSOC);

            $dadosProgresso = [];
            
            if ($registro) {
                // Se já existe, atualiza
                $dadosProgresso = json_decode($registro['progresso_versiculos_json'], true);
                $dadosProgresso[$versiculo_ref] = $status;
                
                $novoJson = json_encode($dadosProgresso);
                
                $update = $this->pdo->prepare("UPDATE progresso SET progresso_versiculos_json = :json WHERE id_progresso = :id");
                $update->execute(['json' => $novoJson, 'id' => $registro['id_progresso']]);
            } else {
                // Se não existe, cria
                $dadosProgresso[$versiculo_ref] = $status;
                $novoJson = json_encode($dadosProgresso);
                
                $insert = $this->pdo->prepare("INSERT INTO progresso (usuario_id, leitura_id, progresso_versiculos_json) VALUES (:uid, :lid, :json)");
                $insert->execute(['uid' => $aluno_id, 'lid' => $leitura_id, 'json' => $novoJson]);
            }
            
            // SUCESSO: Retorna JSON COM DADOS DE DEBUG
            echo json_encode([
                'sucesso' => true,
                'debug' => [
                    'received_leitura_id' => $leitura_id,
                    'received_versiculo' => $versiculo_ref,
                    'session_aluno_id' => $aluno_id,
                    'message' => 'Progresso salvo no DB. (Limpeza de buffer ativada).'
                ]
            ]);

        } catch (\PDOException $e) {
            // FALHA NO BANCO: Retorna JSON de erro
            error_log("Erro ao salvar progresso: " . $e->getMessage());
            echo json_encode(['sucesso' => false, 'msg' => 'Erro 2: Falha no DB: ' . $e->getMessage()]);
        }
        
        // Garante a parada imediata
        exit;
    }
}