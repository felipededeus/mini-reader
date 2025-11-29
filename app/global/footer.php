<?php
function converterCaminhoAbsoluto($caminhoRelativo) {
    $raizDoSite = 'http://localhost/mini-reader/app/'; // Ajuste isso para o URL do seu site
    return $raizDoSite . ltrim($caminhoRelativo, '/');
}
?>

<footer class="site-footer bg-light text-center">
    <p>&copy; 2024 Felipe de Deus. Nenhum direito reservado pois sou canhoto.</p>
    
    <p class="text-muted small"> Erros e bugs podem ser reportados com as informações de contato em <a href="https://felipededeus.com"> felipededeus.com </a> </p>
</footer>
<script src="<?php echo converterCaminhoAbsoluto('assets/jquery-3.7.1.min.js'); ?>" type="application/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
<script src="<?php echo converterCaminhoAbsoluto('assets/jquery-ui-1.14.1.custom/jquery-ui.min.js'); ?>" type="application/javascript"></script>
<script src="<?php echo converterCaminhoAbsoluto('assets/bootstrap/js/bootstrap.min.js'); ?>" type="application/javascript"></script>
</body>
</html>
