<div class="container mt-4">
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><?php echo $titulo; ?></h2>
    <a href="<?php echo BASE_URL; ?>admin/dashboard" class="btn btn-sm btn-secondary">
        ← Voltar para Dashboard
    </a>
</div>

    <hr>
    
    <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#usuarioModal" data-acao="adicionar">
        Adicionar Novo Usuário
    </button>
    
    <table id="usuariosTable" class="table table-striped">
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
            </tbody>
    </table>

    <div class="modal fade" id="usuarioModal" tabindex="-1" aria-labelledby="usuarioModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="usuarioModalLabel">Adicionar/Editar Usuário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="usuarioForm" action="<?php echo BASE_URL; ?>admin/crudUsuario" method="POST">
                        <input type="hidden" name="acao" id="acaoUsuario">
                        <input type="hidden" name="id" id="usuarioId">

                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" class="form-control" name="nome" id="nomeUsuario" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="emailUsuario" required>
                        </div>
                        <div class="mb-3">
                            <label for="senha" class="form-label">Senha (Deixe em branco para manter a atual)</label>
                            <input type="password" class="form-control" name="senha" id="senhaUsuario">
                        </div>
                        <div class="mb-3">
                            <label for="tipo" class="form-label">Tipo de Usuário</label>
                            <select class="form-control" name="tipo" id="tipoUsuario" required>
                                <option value="admin">Admin</option>
                                <option value="professor">Professor</option>
                                <option value="aluno">Aluno</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="turma_id" class="form-label">Turma (Opcional)</label>
                            <select class="form-control" name="turma_id" id="turmaUsuario">
                                <option value="">Nenhuma</option>
                                <?php foreach ($turmas as $turma): ?>
                                    <option value="<?php echo $turma['id']; ?>"><?php echo htmlspecialchars($turma['nome']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



