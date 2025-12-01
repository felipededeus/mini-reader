<?php
class HomeController {
    public function index() {
        // Carrega a view da home
        // (Em um framework real teríamos um motor de template, aqui faremos manual)
        $titulo = "Bem-vindo";
        require_once '../src/Views/layouts/header.php';
        require_once '../src/Views/home.php';
        require_once '../src/Views/layouts/footer.php';
    }
}
?>