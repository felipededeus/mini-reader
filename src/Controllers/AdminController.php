<?php
// src/Controllers/AdminController.php

class AdminController {

    private $db;
    private $pdo;

    public function __construct() {
        // Verifica se está logado E se é ADMIN
        if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
            // Redireciona usuários não-admin para a rota base
            header('Location: ' . BASE_URL);
            exit;
        }
        
        // Verifica se a classe Database existe antes de tentar instanciar
        if (!class_exists('Database')) {
            die("Erro: Classe Database não encontrada. Verifique os requires no seu index.php.");
        }
        
        $this->db = new Database();
        $this->pdo = $this->db->getConnection();
    }

    public function dashboard() {
        $titulo = "Painel Administrativo";
        require_once '../src/Views/layouts/header.php';
        require_once '../src/Views/admin/dashboard.php';
        require_once '../src/Views/layouts/footer.php';
    }

    // Método para carregar a View Gerenciar Usuários
    public function gerenciarUsuarios() {
        $titulo = "Gerenciar Usuários";
        
        // Buscar todas as turmas para o dropdown no modal
        $turmas = $this->pdo->query("SELECT id, nome FROM turmas")->fetchAll(PDO::FETCH_ASSOC);

        require_once '../src/Views/layouts/header.php';
        require_once '../src/Views/admin/gerenciar_usuarios.php';
        require_once '../src/Views/layouts/footer.php';
    }

    // Método para carregar a View Gerenciar Turmas
    public function gerenciarTurmas() {
        $titulo = "Gerenciar Turmas";
        
        // Buscar todas as turmas
        $turmas = $this->pdo->query("SELECT * FROM turmas")->fetchAll(PDO::FETCH_ASSOC);

        require_once '../src/Views/layouts/header.php';
        require_once '../src/Views/admin/gerenciar_turmas.php';
        require_once '../src/Views/layouts/footer.php';
    }

    // ===========================================
    // ROTAS AJAX DE CRUD DE USUÁRIOS
    // ===========================================

    // Listar todos os usuários com nome da turma para a tabela (Endpoint AJAX)
    public function listarUsuarios() {
        if (ob_get_level() > 0) ob_end_clean();
        header('Content-Type: application/json');

        try {
            // SQL para buscar todos os usuários, juntando com o nome da turma
            $sql = "SELECT u.id, u.nome, u.email, u.tipo, t.nome AS turma_nome, u.turma_id
                    FROM usuarios u
                    LEFT JOIN turmas t ON u.turma_id = t.id";
            $stmt = $this->pdo->query($sql);
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Retorna o array de usuários no formato de DataTable
            echo json_encode(['data' => $usuarios]);
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao listar usuários: ' . $e->getMessage()]);
        }
        exit;
    }

    // CRUD para Usuários (Adicionar, Editar, Excluir) (Endpoint AJAX)
    public function crudUsuario() {
        // Limpar o buffer de saída (essencial para AJAX)
        if (ob_get_level() > 0) ob_end_clean();
        header('Content-Type: application/json');

        $input = $_POST;
        $acao = $input['acao'] ?? '';
        $id = $input['id'] ?? null;
        $nome = $input['nome'] ?? '';
        $email = $input['email'] ?? '';
        $senha = $input['senha'] ?? '';
        $tipo = $input['tipo'] ?? '';
        $turma_id = !empty($input['turma_id']) ? (int)$input['turma_id'] : null;

        try {
            switch ($acao) {
                case 'adicionar':
                    if (empty($nome) || empty($email) || empty($senha) || empty($tipo)) {
                        throw new \Exception("Todos os campos obrigatórios devem ser preenchidos.");
                    }
                    
                    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                    $sql = "INSERT INTO usuarios (nome, email, senha, tipo, turma_id) 
                            VALUES (:nome, :email, :senha, :tipo, :turma_id)";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([
                        'nome' => $nome,
                        'email' => $email,
                        'senha' => $senhaHash,
                        'tipo' => $tipo,
                        'turma_id' => $turma_id
                    ]);
                    echo json_encode(['sucesso' => true, 'msg' => 'Usuário adicionado com sucesso!']);
                    break;

                case 'editar':
                    if (empty($id) || empty($nome) || empty($email) || empty($tipo)) {
                        throw new \Exception("ID e campos obrigatórios devem ser preenchidos.");
                    }
                    
                    // Se a senha for fornecida, hash e atualiza
                    if (!empty($senha)) {
                        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                        $sql = "UPDATE usuarios SET nome = :nome, email = :email, senha = :senha, tipo = :tipo, turma_id = :turma_id WHERE id = :id";
                        $stmt = $this->pdo->prepare($sql);
                        $stmt->execute([
                            'nome' => $nome,
                            'email' => $email,
                            'senha' => $senhaHash,
                            'tipo' => $tipo,
                            'turma_id' => $turma_id,
                            'id' => $id
                        ]);
                    } else {
                        // Senha não fornecida, não atualiza o campo 'senha'
                        $sql = "UPDATE usuarios SET nome = :nome, email = :email, tipo = :tipo, turma_id = :turma_id WHERE id = :id";
                        $stmt = $this->pdo->prepare($sql);
                        $stmt->execute([
                            'nome' => $nome,
                            'email' => $email,
                            'tipo' => $tipo,
                            'turma_id' => $turma_id,
                            'id' => $id
                        ]);
                    }
                    echo json_encode(['sucesso' => true, 'msg' => 'Usuário editado com sucesso!']);
                    break;

                case 'excluir':
                    if (empty($id)) {
                        throw new \Exception("ID do usuário é obrigatório para exclusão.");
                    }

                    // Impedir que o Admin exclua a própria conta logada
                    if ((int)$id === (int)$_SESSION['usuario_id']) {
                        throw new \Exception("Você não pode excluir sua própria conta enquanto estiver logado.");
                    }
                    
                    // Excluir progresso do usuário antes de excluir o usuário (foreign key safety)
                    $this->pdo->prepare("DELETE FROM progresso WHERE usuario_id = :id")->execute(['id' => $id]);
                    
                    $sql = "DELETE FROM usuarios WHERE id = :id";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute(['id' => $id]);
                    echo json_encode(['sucesso' => true, 'msg' => 'Usuário excluído com sucesso!']);
                    break;
                
                default:
                    throw new \Exception("Ação não reconhecida.");
            }
        } catch (\PDOException $e) {
            http_response_code(500);
            // 23000 é geralmente a exceção de entrada duplicada (email)
            $msg = (strpos($e->getCode(), '23000') !== false) ? "O e-mail fornecido já está em uso." : $e->getMessage();
            echo json_encode(['sucesso' => false, 'msg' => 'Erro no banco de dados: ' . $msg]);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['sucesso' => false, 'msg' => 'Erro: ' . $e->getMessage()]);
        }
        exit;
    }

    // ===========================================
    // ROTAS AJAX DE CRUD DE TURMAS
    // ===========================================
    
    // Método para listar turmas (usado em gerenciarTurmas)
    public function listarTurmas() {
        if (ob_get_level() > 0) ob_end_clean();
        header('Content-Type: application/json');

        try {
            $sql = "SELECT id, nome FROM turmas ORDER BY nome";
            $stmt = $this->pdo->query($sql);
            $turmas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['data' => $turmas]);
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao listar turmas: ' . $e->getMessage()]);
        }
        exit;
    }

    // CRUD para Turmas (Adicionar, Editar, Excluir) (Endpoint AJAX)
    public function crudTurma() {
        if (ob_get_level() > 0) ob_end_clean();
        header('Content-Type: application/json');

        $input = $_POST;
        $acao = $input['acao'] ?? '';
        $id = $input['id'] ?? null;
        $nome = trim($input['nome'] ?? '');

        try {
            if (empty($nome) && $acao !== 'excluir') {
                throw new \Exception("O nome da turma é obrigatório.");
            }

            switch ($acao) {
                case 'adicionar':
                    $sql = "INSERT INTO turmas (nome) VALUES (:nome)";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute(['nome' => $nome]);
                    echo json_encode(['sucesso' => true, 'msg' => 'Turma adicionada com sucesso!']);
                    break;

                case 'editar':
                    if (empty($id)) {
                        throw new \Exception("ID da turma é obrigatório para edição.");
                    }
                    $sql = "UPDATE turmas SET nome = :nome WHERE id = :id";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute(['nome' => $nome, 'id' => $id]);
                    echo json_encode(['sucesso' => true, 'msg' => 'Turma editada com sucesso!']);
                    break;

                case 'excluir':
                    if (empty($id)) {
                        throw new \Exception("ID da turma é obrigatório para exclusão.");
                    }
                    // Segurança: Verifique se há alunos/cronogramas ligados a esta turma
                    
                    // 1. Desvincular usuários (alunos/professores) da turma
                    $this->pdo->prepare("UPDATE usuarios SET turma_id = NULL WHERE turma_id = :id")->execute(['id' => $id]);
                    // 2. Excluir cronogramas ligados a esta turma (Se necessário)
                    $this->pdo->prepare("DELETE FROM cronogramas WHERE turma_id = :id")->execute(['id' => $id]);
                    
                    // 3. Excluir a turma
                    $sql = "DELETE FROM turmas WHERE id = :id";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute(['id' => $id]);
                    echo json_encode(['sucesso' => true, 'msg' => 'Turma e dados associados excluídos com sucesso!']);
                    break;
                
                default:
                    throw new \Exception("Ação não reconhecida.");
            }
        } catch (\PDOException $e) {
            http_response_code(500);
            $msg = (strpos($e->getCode(), '23000') !== false) ? "Nome da turma já existe." : $e->getMessage();
            echo json_encode(['sucesso' => false, 'msg' => 'Erro no banco de dados: ' . $msg]);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['sucesso' => false, 'msg' => 'Erro: ' . $e->getMessage()]);
        }
        exit;
    }
}
