<?php include '../global/header.php'; ?>

<h2>Cronograma de Leituras</h2>
<div id="cronogramaContainer"></div>

<!-- Modal de Leitura -->
<div class="modal fade" id="leituraModal" tabindex="-1" aria-labelledby="leituraModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="leituraModalLabel">Leitura</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modalBodyLeitura">
        <!-- Conteúdo da leitura -->
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    carregarCronograma();

    $('#cronogramaContainer').on('click', '.open-modal', function() {
        var leitura_id = $(this).data('id');
        $.get('../controllers/get_leitura.php', { leitura_id: leitura_id }, function(data) {
            $('#modalBodyLeitura').html(data);
            $('#leituraModal').modal('show');
        });
    });

    $('#cronogramaContainer').on('change', 'input[type="checkbox"]', function() {
        var leitura_id = $(this).data('leitura-id');
        var lido = $(this).is(':checked') ? 1 : 0;
        $.post('../controllers/marcar_leitura.php', { leitura_id: leitura_id, lido: lido }, function(data) {
            alert('Leitura marcada com sucesso');
        });
    });
});

function carregarCronograma() {
    $.get('../controllers/obter_cronograma.php', function(data) {
        $('#cronogramaContainer').html(data);
    });
}
</script>

<?php include '../global/footer.php'; ?>
