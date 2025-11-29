<?php include '../global/header.php'; ?>

<h2>Gerenciar Alunos</h2>

<table id="alunosTable" class="display">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Turma</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <!-- Conteúdo dinâmico via AJAX -->
    </tbody>
</table>

<!-- Modal para Editar Aluno -->
<div class="modal fade" id="editarAlunoModal" tabindex="-1" aria-labelledby="editarAlunoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editarAlunoModalLabel">Editar Aluno</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="alunoForm">
          <input type="hidden" name="acao" id="acao">
          <input type="hidden" id="alunoId" name="aluno_id">
          <div class="mb-3">
            <label for="alunoNome" class="form-label">Nome</label>
            <input type="text" class="form-control" id="alunoNome" name="nome">
          </div>
          <div class="mb-3">
            <label for="alunoTurma" class="form-label">Turma</label>
            <select class="form-control" id="alunoTurma" name="turma_id">
              <!-- Opções dinâmicas via AJAX -->
            </select>
          </div>
          <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
<script>
$(document).ready(function() {
    var table = $('#alunosTable').DataTable({
        ajax: '../controllers/controller.alunos.php?acao=listar',
        columns: [
            { data: 'nome' },
            { data: 'turma' },
            {
                data: 'id',
                render: function(data, type, row) {
                    return '<button class="btn btn-sm btn-primary editar-aluno" data-id="' + data + '">Editar</button>' +
                           '<button class="btn btn-sm btn-danger excluir-aluno" data-id="' + data + '">Excluir</button>';
                }
            }
        ]
    });

    $('#alunosTable').on('click', '.editar-aluno', function() {
        var aluno_id = $(this).data('id');
        $.get('../controllers/controller.alunos.php', { acao: 'obter', aluno_id: aluno_id }, function(data) {
            var aluno = JSON.parse(data);
            $('#alunoId').val(aluno.id);
            $('#alunoNome').val(aluno.nome);
            $('#alunoTurma').val(aluno.turma_id);
            $('#acao').val('editar');
            $('#editarAlunoModal').modal('show');
        });
    });

    $('#alunoForm').submit(function(e) {
        e.preventDefault();
        $.post('../controllers/controller.alunos.php', $(this).serialize(), function() {
            table.ajax.reload();
            $('#editarAlunoModal').modal('hide');
        });
    });

    $('#alunosTable').on('click', '.excluir-aluno', function() {
        if (confirm('Tem certeza que deseja excluir este aluno?')) {
            var aluno_id = $(this).data('id');
            $.post('../controllers/controller.alunos.php', { acao: 'excluir', aluno_id: aluno_id }, function() {
                table.ajax.reload();
            });
        }
    });
});
</script>

<?php include '../global/footer.php'; ?>
