<div class="container mt-4">
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><?php echo $titulo; ?></h2>
    <a href="<?php echo BASE_URL; ?>admin/dashboard" class="btn btn-sm btn-secondary">
        ← Voltar para Dashboard
    </a>
</div>
<hr>
    
    <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#turmaModal" data-acao="adicionar">
        Adicionar Nova Turma
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
            <?php foreach ($turmas as $turma): ?>
            <tr>
                <td><?php echo $turma['id']; ?></td>
                <td><?php echo htmlspecialchars($turma['nome']); ?></td>
                <td>
                    <button class="btn btn-sm btn-primary editar-turma" 
                            data-bs-toggle="modal" 
                            data-bs-target="#turmaModal"
                            data-acao="editar"
                            data-id="<?php echo $turma['id']; ?>"
                            data-nome="<?php echo htmlspecialchars($turma['nome']); ?>">
                        Editar
                    </button>
                    <button class="btn btn-sm btn-danger excluir-turma" data-id="<?php echo $turma['id']; ?>">
                        Excluir
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="modal fade" id="turmaModal" tabindex="-1" aria-labelledby="turmaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="turmaModalLabel">Adicionar Turma</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="turmaForm" action="<?php echo BASE_URL; ?>admin/crudTurma" method="POST">
                        <input type="hidden" name="acao" id="acaoTurma">
                        <input type="hidden" name="id" id="turmaId">

                        <div class="mb-3">
                            <label for="nomeTurma" class="form-label">Nome da Turma</label>
                            <input type="text" class="form-control" name="nome" id="nomeTurma" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// O código JavaScript (AJAX) para o CRUD de Turmas virá na próxima etapa.
</script>