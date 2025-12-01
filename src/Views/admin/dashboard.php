<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0">Bem-vindo(a) ao Painel Administrativo</h2>
        </div>
        <div class="card-body">
            <p class="lead">Utilize os links abaixo para gerenciar os dados centrais do sistema: usuários e turmas.</p>
            
            <div class="row mt-4">
                
                <div class="col-md-6 mb-4">
                    <div class="card text-center h-100 border-success">
                        <div class="card-body">
                            <h5 class="card-title text-success">Gerenciar Usuários (CRUD)</h5>
                            <p class="card-text">Crie, edite e visualize todos os perfis (Admin, Professor, Aluno).</p>
                            <a href="<?php echo BASE_URL; ?>admin/gerenciarUsuarios" class="btn btn-success mt-2">
                                Acessar Usuários
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card text-center h-100 border-info">
                        <div class="card-body">
                            <h5 class="card-title text-info">Gerenciar Turmas</h5>
                            <p class="card-text">Crie, edite e exclua as turmas destinadas aos alunos.</p>
                            <a href="<?php echo BASE_URL; ?>admin/gerenciarTurmas" class="btn btn-info mt-2">
                                Acessar Turmas
                            </a>
                        </div>
                    </div>
                </div>

            </div>
            
        </div>
    </div>
</div>