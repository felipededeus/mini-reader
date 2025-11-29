<?php include '../global/header.php'; ?>
<h2>Gerenciar Turmas</h2>

<!-- Botão para adicionar uma nova turma -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#turmaModalAdicionar">
    Adicionar Turma
</button>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php
        include '../../conexao.class.php';
        $conexao = new Conexao();
        $pdo = $conexao->conectar();
        $sql = "SELECT * FROM turmas";
        foreach ($pdo->query($sql) as $row) {
            $modalId = "turmaModal" . $row['id'];
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['nome']}</td>
                    <td>
                        <button class='btn btn-warning' data-bs-toggle='modal' data-bs-target='#{$modalId}'>Editar</button>
                        <button class='btn btn-danger' data-id='{$row['id']}'>Excluir</button>
                    </td>
                  </tr>";
            
            // Modal de edição específico para cada turma
            echo "<div class='modal fade' id='{$modalId}' tabindex='-1' role='dialog' aria-labelledby='{$modalId}Label' aria-hidden='true'>
                    <div class='modal-dialog' role='document'>
                        <div class='modal-content'>
                            <div class='modal-header'>
                                <h5 class='modal-title' id='{$modalId}Label'>Editar Turma</h5>
                                <button type='button' class='close' data-bs-dismiss='modal' aria-label='Close'>
                                    <span aria-hidden='true'>&times;</span>
                                </button>
                            </div>
                            <div class='modal-body'>
                                <form id='turmaForm{$row['id']}' method='post'>
                                    <input type='hidden' name='acao' value='editarTurma'>
                                    <input type='hidden' name='turma_id' value='{$row['id']}'>
                                    <div class='form-group'>
                                        <label for='nome'>Nome da Turma:</label>
                                        <input type='text' class='form-control' name='nome' value='{$row['nome']}' required>
                                    </div>
                                    <button type='button' id='submitEditar{$row['id']}' class='btn btn-primary'>Salvar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                  </div>";
        }
        ?>
    </tbody>
</table>

<!-- Modal para Adicionar Nova Turma -->
<div class="modal fade" id="turmaModalAdicionar" tabindex="-1" role="dialog" aria-labelledby="turmaModalAdicionarLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="turmaModalAdicionarLabel">Adicionar Turma</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="turmaFormAdicionar" method="post">
                    <input type="hidden" name="acao" value="adicionarTurma">
                    <div class='form-group'>
                        <label for='nome'>Nome da Turma:</label>
                        <input type='text' class='form-control' name='nome' id='nomeAdicionar' required>
                    </div>
                    <button type='button' id='submitAdicionar' class='btn btn-primary'>Salvar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../global/footer.php'; ?>

<script>
$(document).ready(function() {
    // Submeter formulário de adição ao clicar no botão com ID específico
    $("#submitAdicionar").click(function(e) {
        e.preventDefault();
        var $form = $("#turmaFormAdicionar");
        var $button = $(this);
        
        // Desabilitar o botão
        $button.prop("disabled", true);
        
        $.ajax({
            url: '../controllers/controller.crud.alunos.turmas.php',
            type: 'POST',
            data: $form.serialize(),
            success: function(response) {
                alert(response);
                location.reload();
            },
            error: function() {
                alert('Erro ao adicionar turma.');
                // Reabilitar o botão em caso de erro
                $button.prop("disabled", false);
            }
        });
    });

    // Submeter formulário de edição ao clicar no botão com ID específico
    <?php
    foreach ($pdo->query($sql) as $row) {
        $modalId = "turmaModal" . $row['id'];
        echo "$('#submitEditar{$row['id']}').click(function(e) {
            e.preventDefault();
            var \$form = $('#turmaForm{$row['id']}');
            var \$button = $(this);
            
            // Desabilitar o botão
            \$button.prop('disabled', true);
            
            $.ajax({
                url: '../controllers/controller.crud.alunos.turmas.php',
                type: 'POST',
                data: \$form.serialize(),
                success: function(response) {
                    alert(response);
                    location.reload();
                },
                error: function() {
                    alert('Erro ao editar turma.');
                    // Reabilitar o botão em caso de erro
                    \$button.prop('disabled', false);
                }
            });
        });";
    }
    ?>

    // Excluir turma
    $(".btn-danger").click(function() {
        var id = $(this).data('id');
        if (confirm('Tem certeza que deseja excluir esta turma?')) {
            $.ajax({
                url: '../controllers/controller.crud.alunos.turmas.php',
                type: 'POST',
                data: { acao: 'excluirTurma', turma_id: id },
                success: function(response) {
                    alert(response);
                    location.reload();
                },
                error: function() {
                    alert('Erro ao excluir turma.');
                }
            });
        }
    });
});
</script>
