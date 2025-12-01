<?php
// public/ajax/salvar_progresso.php

// 1. INÍCIO DA SESSÃO E INCLUSÕES
session_start();

$raiz = dirname(dirname(__DIR__)); 

require_once $raiz . '/src/Config/config.php';
require_once $raiz . '/src/Config/Database.php';


// 2. CONFIGURAÇÃO DE RESPOSTA AJAX
if (ob_get_level() > 0) {
    ob_end_clean();
}
header('Content-Type: application/json');

// 3. VALIDAÇÃO DE USUÁRIO E ID
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'aluno') {
    echo json_encode(['sucesso' => false, 'msg' => 'Acesso negado. Sessão inválida.']);
    exit;
}
$aluno_id = $_SESSION['usuario_id'];

// 4. RECEBIMENTO E VALIDAÇÃO DOS DADOS
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['sucesso' => false, 'msg' => 'Erro: Dados de entrada inválidos.']);
    exit;
}

$leitura_id = $input['leitura_id'];
$versiculo_ref = $input['versiculo']; 
// CRÍTICO: Força o status a ser um booleano (true ou false) para manter a compatibilidade com o DB.
$status = (bool)$input['lido']; 


// 5. CONEXÃO COM O BANCO DE DADOS
try {
    $db = new Database();
    $pdo = $db->getConnection();
} catch (\Exception $e) {
    echo json_encode(['sucesso' => false, 'msg' => 'Erro de conexão DB.']);
    exit;
}


// 6. LÓGICA DE ATUALIZAÇÃO DO PROGRESSO (Com Normalização Estrita)
$success = false;
$msg = 'Nenhuma linha afetada.';

try {
    // Busca progresso atual
    $stmt = $pdo->prepare("SELECT id_progresso, progresso_versiculos_json FROM progresso WHERE usuario_id = :uid AND leitura_id = :lid");
    $stmt->execute(['uid' => $aluno_id, 'lid' => $leitura_id]);
    $registro = $stmt->fetch(PDO::FETCH_ASSOC);

    $dadosProgresso = [];
    
    if ($registro) {
        // Atualiza o progresso existente
        $dadosProgresso = json_decode($registro['progresso_versiculos_json'], true);
        
        // Garante que é um array, caso o JSON lido esteja vazio ou corrompido
        if (!is_array($dadosProgresso)) {
             $dadosProgresso = [];
        }
        
        // Aplica o novo valor (true/false)
        $dadosProgresso[$versiculo_ref] = $status;
        
        // JSON_PRESERVE_ZERO_FRACTION garante que o formato do float não se altere,
        // mas o crucial é que a variável $status é um booleano
        $novoJson = json_encode($dadosProgresso); 
        
        $update = $pdo->prepare("UPDATE progresso SET progresso_versiculos_json = :json WHERE id_progresso = :id");
        $update_exec = $update->execute(['json' => $novoJson, 'id' => $registro['id_progresso']]);

        if ($update_exec) {
             $success = true;
             $msg = 'Progresso atualizado com sucesso.';
        }
        
    } else {
        // Cria um novo registro (se for o primeiro versículo lido)
        $dadosProgresso[$versiculo_ref] = $status;
        $novoJson = json_encode($dadosProgresso);
        
        $insert = $pdo->prepare("INSERT INTO progresso (usuario_id, leitura_id, progresso_versiculos_json) VALUES (:uid, :lid, :json)");
        $insert_exec = $insert->execute(['uid' => $aluno_id, 'lid' => $leitura_id, 'json' => $novoJson]);
        
        if ($insert_exec) {
            $success = true;
            $msg = 'Novo progresso inserido com sucesso.';
        }
    }
    
    // Retorna o JSON com o status real
    echo json_encode(['sucesso' => $success, 'msg' => $msg, 'progresso_salvo' => $versiculo_ref]);

} catch (\PDOException $e) {
    // FALHA NA QUERY: Retorna o erro do PDO
    echo json_encode(['sucesso' => false, 'msg' => 'Erro interno na Query: ' . $e->getMessage()]);
}

exit;