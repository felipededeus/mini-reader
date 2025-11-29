<?php
include '../../conexao.class.php';

session_start();

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

        header("Location: ../telas/index.php");
    } else {
        echo "Credenciais inválidas.";
    }
}
?>
