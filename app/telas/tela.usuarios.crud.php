<?php include '../global/header.php'; ?>
<h2>Gerenciar Usuários</h2>

<!-- Botão para adicionar um novo usuário -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#usuarioModalAdicionar">
    Adicionar Usuário
</button>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Tipo</th>
            <th>Turma</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php
        include '../../conexao.class.php';
        $conexao = new Conexao();
        $pdo = $conexao->conectar();
        $sql = "SELECT usuarios.id, usuarios.nome, usuarios.email, usuarios.tipo, turmas.nome AS turma
                FROM usuarios
                LEFT JOIN turmas ON usuarios.turma_id = turmas.id";
        foreach ($pdo->query($sql) as $row) {
            $modalId = "usuarioModal" . $row['id'];
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['nome']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['tipo']}</td>
                    <td>{$row['turma']}</td>
                    <td>
                        <button class='btn btn-warning' data-bs-toggle='modal' data-bs-target='#{$modalId}'>Editar</button>
                        <button class='btn btn-danger' data-id='{$row['id']}'>Excluir</button>
                    </td>
                  </tr>";
            
            // Modal de edição específico para cada usuário
            echo "<div class='modal fade' id='{$modalId}' tabindex='-1' role='dialog' aria-labelledby='{$modalId}Label' aria-hidden='true'>
                    <div class='modal-dialog' role='document'>
                        <div class='modal-content'>
                            <div class='modal-header'>
                                <h5 class='modal-title' id='{$modalId}Label'>Editar Usuário</h5>
                                <button type='button' class='close' data-bs-dismiss='modal' aria-label='Close'>
                                    <span aria-hidden='true'>&times;</span>
                                </button>
                            </div>
                            <div class='modal-body'>
                                <form id='usuarioForm{$row['id']}' method='post'>
                                    <input type='hidden' name='acao' value='editarUsuario'>
                                    <input type='hidden' name='usuario_id' value='{$row['id']}'>
                                    <div class='form-group'>
                                        <label for='nome'>Nome:</label>
                                        <input type='text' class='form-control' name='nome' value='{$row['nome']}' required>
                                    </div>
                                    <div class='form-group'>
                                        <label for='email'>Email:</label>
                                        <input type='email' class='form-control' name='email' value='{$row['email']}' required>
                                    </div>
                                    <div class='form-group'>
                                        <label for='senha'>Senha:</label>
                                        <input type='password' class='form-control' name='senha' required>
                                    </div>
                                    <div class='form-group'>
                                        <label for='tipo'>Tipo:</label>
                                        <select class='form-control' name='tipo' required>
                                            <option value='professor' ".($row['tipo'] == 'professor' ? 'selected' : '').">Professor</option>
                                            <option value='aluno' ".($row['tipo'] == 'aluno' ? 'selected' : '').">Aluno</option>
                                        </select>
                                    </div>
                                    <div class='form-group'>
                                        <label for='turma_id'>Turma:</label>
                                        <select class='form-control' name='turma_id'>
                                            <option value=''>Nenhuma</option>";
                                            $turmas = $pdo->query("SELECT id, nome FROM turmas");
                                            foreach ($turmas as $turma) {
                                                echo "<option value='{$turma['id']}' ".($row['turma_id'] == $turma['id'] ? 'selected' : '').">{$turma['nome']}</option>";
                                            }
            echo "                          </select>
                                    </div>
                                    <button type='submit' class='btn btn-primary'>Salvar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                  </div>";
        }
        ?>
    </tbody>
</table>

<!-- Modal para Adicionar Novo Usuário -->
<div class="modal fade" id="usuarioModalAdicionar" tabindex="-1" role="dialog" aria-labelledby="usuarioModalAdicionarLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="usuarioModalAdicionarLabel">Adicionar Usuário</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="usuarioFormAdicionar" method="post">
                    <input type="hidden" name="acao" value="adicionarUsuario">
                    <div class="form-group">
                        <label for="nome">Nome:</label>
                        <input type="text" class="form-control" name="nome" id="nomeAdicionar" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" name="email" id="emailAdicionar" required>
                    </div>
                    <div class="form-group">
                        <label for="senha">Senha:</label>
                        <input type="password" class="form-control" name="senha" id="senhaAdicionar" required>
                    </div>
                    <div class="form-group">
                        <label for="tipo">Tipo:</label>
                        <select class="form-control" name="tipo" id="tipoAdicionar" required>
                            <option value="professor">Professor</option>
                            <option value="aluno">Aluno</option>
                        </select>
                    </div>
                    <div class="form-group" id="turmaGroupAdicionar" style="display:none;">
                        <label for="turma_id">Turma:</label>
                        <select class="form-control" name="turma_id" id="turma_idAdicionar">
                            <option value="">Nenhuma</option>
                            <?php
                            $sql = "SELECT id, nome FROM turmas";
                            foreach ($pdo->query($sql) as $row) {
                                echo "<option value='{$row['id']}'>{$row['nome']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../global/footer.php'; ?>

<script>
$(document).ready(function() {
    // Mostrar ou esconder o campo Turma com base no tipo para adicionar usuário
    $("#tipoAdicionar").change(function() {
        if ($(this).val() == "aluno") {
            $("#turmaGroupAdicionar").show();
        } else {
            $("#turmaGroupAdicionar").hide();
        }
    });

    // Submeter formulário de adição
    $("#usuarioFormAdicionar").submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: '../controllers/controller.crud.alunos.turmas.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                alert(response);
                location.reload();
            },
            error: function() {
                alert('Erro ao adicionar usuário.');
            }
        });
    });

    // Submeter formulário de edição
    $('form[id^="usuarioForm"]').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: '../controllers/controller.crud.alunos.turmas.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                alert(response);
                location.reload();
            },
            error: function() {
                alert('Erro ao editar usuário.');
            }
        });
    });

    // Excluir usuário
    $(".btn-danger").click(function() {
        var id = $(this).data('id');
        if (confirm('Tem certeza que deseja excluir este usuário?')) {
            $.ajax({
                url: '../controllers/controller.crud.alunos.turmas.php',
                type: 'POST',
                data: { acao: 'excluirUsuario', usuario_id: id },
                success: function(response) {
                    alert(response);
                    location.reload();
                },
                error: function() {
                    alert('Erro ao excluir usuário.');
                }
            });
        }
    });
});


</script>
