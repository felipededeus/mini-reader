<?php
session_start();
include '../../conexao.class.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $manter_conectado = isset($_POST['manter_conectado']);

    $conexao = new Conexao();
    $pdo = $conexao->conectar();

    $sql = "SELECT * FROM usuarios WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && $conexao->verificarSenha($senha, $usuario['senha'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_tipo'] = $usuario['tipo'];

        if ($manter_conectado) {
            setcookie('usuario_id', $usuario['id'], time() + (86400 * 30), "/");
            setcookie('usuario_tipo', $usuario['tipo'], time() + (86400 * 30), "/");
        }

        header("Location: index.php");
    } else {
        $erro = "Credenciais inválidas.";
    }
}
?>

<?php include '../global/header.php'; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mt-5">
                <div class="card-header">
                    <h3>Login</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($erro)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $erro; ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST" action="../controllers/controller.login.php">
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" name="email" id="email" required>
                        </div>
                        <div class="form-group">
                            <label for="senha">Senha:</label>
                            <input type="password" class="form-control" name="senha" id="senha" required>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" name="manter_conectado" id="manter_conectado">
                            <label class="form-check-label" for="manter_conectado">Manter conectado</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../global/footer.php'; ?>

