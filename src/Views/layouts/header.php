<!DOCTYPE html>
<html>
<head>
    <title>Mini-Reader</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/jquery-ui-1.14.1.custom/jquery-ui.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <script>
        const BASE_URL_JS = '<?php echo trim(BASE_URL); ?>'; 
        // Se BASE_URL não terminar com /, adicione aqui, ou garanta no config.php.
        window.BASE_URL_JS = BASE_URL_JS.endsWith('/') ? BASE_URL_JS : BASE_URL_JS + '/';
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
</head>
<body>
    <div class="ios-card">
        <header class="ios-header">
            <h1>Mini-Reader</h1>
            <nav>
                <?php if (isset($_SESSION['usuario_id'])): 
                    // Se estiver logado, mostra o link de SAIR
                    $perfil = ucfirst($_SESSION['usuario_tipo']);
                ?>
                    <span style="font-size: 12px; color: #888; margin-right: 10px;">
                        <strong><?php echo htmlspecialchars($perfil); ?></strong>
                    </span>
                    
                    <a href="<?php echo BASE_URL; ?>login/sair" class="btn-glossy" style="font-size: 12px; background-color: #f44336; border-color: #d32f2f;">
                        Sair
                    </a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>home" class="btn-glossy" style="font-size: 12px;">Início</a>
                    <a href="<?php echo BASE_URL; ?>login" class="btn-glossy" style="font-size: 12px;">Entrar</a>
                <?php endif; ?>
            </nav>
        </header>
        <div class="content-body">