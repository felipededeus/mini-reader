<?php
class LoginController {
    
    // Mostra a tela de login (GET)
    public function index() {
        $titulo = "Entrar";
        require_once '../src/Views/layouts/header.php';
        require_once '../src/Views/auth/login.php';
        require_once '../src/Views/layouts/footer.php';
    }

public function professor() {
        // Podemos usar a mesma tela, mas mudamos o título se quiser
        $titulo = "Login do Professor";
        require_once '../src/Views/layouts/header.php';
        require_once '../src/Views/auth/login.php';
        require_once '../src/Views/layouts/footer.php';
    }
    
    // Processa os dados do formulário (POST)
    public function auth() {
        // Se não enviou nada, volta pro login
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';
        $manter = isset($_POST['manter_conectado']);

        // Conecta ao banco
        $db = new Database();
        $pdo = $db->getConnection();

        // Busca o usuário (Lógica original adaptada)
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica senha
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            // Sucesso! Salva na sessão
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_tipo'] = $usuario['tipo'];
            $_SESSION['usuario_nome'] = $usuario['nome']; // Adicionei para usar na saudação

            // Lógica do Cookie "Manter Conectado"
            if ($manter) {
                setcookie('usuario_id', $usuario['id'], time() + (86400 * 30), "/");
                setcookie('usuario_tipo', $usuario['tipo'], time() + (86400 * 30), "/");
            }

            // Redireciona baseado no tipo de usuário
            if ($usuario['tipo'] === 'aluno') {
                header('Location: ' . BASE_URL . 'aluno/dashboard');
            } else {
                header('Location: ' . BASE_URL . 'professor/dashboard');
            }
            exit;

        } else {
            // Falha: Volta pro login com erro
            $_SESSION['erro_login'] = "Ops! E-mail ou senha incorretos.";
            header('Location: ' . BASE_URL . 'login');
            exit;
        }
    }

    // Faz logout
    public function sair() {
        session_destroy();
        // Limpa cookies também
        setcookie('usuario_id', '', time() - 3600, "/");
        setcookie('usuario_tipo', '', time() - 3600, "/");
        header('Location: ' . BASE_URL . 'home');
        exit;
    }
}
?>