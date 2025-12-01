$(document).ready(function() {
    const BASE_URL_JS = window.BASE_URL_JS;
    const usuarioModal = $('#usuarioModal');
    const usuarioForm = $('#usuarioForm');
    let usuariosTable;

    // Inicialização do DataTables
    function inicializarDataTable() {
        usuariosTable = $('#usuariosTable').DataTable({
            "processing": true,
            "serverSide": false, // Usaremos carregamento de dados via AJAX no lado do cliente
            "ajax": {
                "url": BASE_URL_JS + 'admin/listarUsuarios',
                "type": 'GET',
                "dataSrc": 'data' // O AdminController retorna {"data": [...]}
            },
            "columns": [
                { "data": "id" },
                { "data": "nome" },
                { "data": "email" },
                { "data": "tipo" },
                { "data": "turma_nome" },
                { 
                    "data": "id",
                    "render": function (data, type, row) {
                        return `
                            <button class="btn btn-sm btn-warning editar-usuario" 
                                    data-id="${data}"
                                    data-nome="${row.nome}"
                                    data-email="${row.email}"
                                    data-tipo="${row.tipo}"
                                    data-turma-id="${row.turma_id || ''}">
                                Editar
                            </button>
                            <button class="btn btn-sm btn-danger excluir-usuario" data-id="${data}">
                                Excluir
                            </button>
                        `;
                    }
                }
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json"
            }
        });
    }

    // Inicializa a tabela
    inicializarDataTable();

    // Função para recarregar a tabela
    function recarregarTabela() {
        usuariosTable.ajax.reload(null, false); // false para manter a página atual
    }
    
    // 1. Ação ao abrir o Modal (Adicionar/Editar)
    usuarioModal.on('show.bs.modal', function(event) {
        // ... (Lógica de preenchimento do modal que você já tinha, mantida) ...
        const button = $(event.relatedTarget);
        const acao = button.data('acao');
        const modalLabel = $('#usuarioModalLabel');
        
        usuarioForm[0].reset(); 
        $('#acaoUsuario').val(acao);

        if (acao === 'adicionar') {
            modalLabel.text('Adicionar Novo Usuário');
            $('#usuarioId').val('');
            $('#senhaUsuario').attr('required', true);
        } else if (acao === 'editar') {
            modalLabel.text('Editar Usuário');
            $('#senhaUsuario').attr('required', false);
            
            // Preenche os campos do modal com os dados do usuário
            $('#usuarioId').val(button.data('id'));
            $('#nomeUsuario').val(button.data('nome'));
            $('#emailUsuario').val(button.data('email'));
            $('#tipoUsuario').val(button.data('tipo'));
            $('#turmaUsuario').val(button.data('turma-id'));
        }
    });
    
    // 2. Submeter o Formulário (Adicionar/Editar)
    usuarioForm.submit(function(e) {
        e.preventDefault();
        
        if ($('#acaoUsuario').val() === 'adicionar' && $('#senhaUsuario').val().length < 6) {
            alert('A senha deve ter no mínimo 6 caracteres.');
            return;
        }

        $.ajax({
            url: BASE_URL_JS + 'admin/crudUsuario',
            type: 'POST',
            data: usuarioForm.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.sucesso) {
                    alert(response.msg);
                    usuarioModal.modal('hide');
                    recarregarTabela(); // Atualiza a tabela
                } else {
                    alert('Erro: ' + response.msg);
                }
            },
            error: function(jqXHR) {
                const response = jqXHR.responseJSON;
                const errorMsg = response && response.msg ? response.msg : 'Erro desconhecido ao comunicar com o servidor.';
                alert('Erro ao salvar usuário: ' + errorMsg);
            }
        });
    });

    // 3. Ação de Excluir
    $('#usuariosTable tbody').on('click', '.excluir-usuario', function() {
        const userId = $(this).data('id');
        const userName = $(this).closest('tr').find('td:nth-child(2)').text();

        if (confirm(`Tem certeza que deseja excluir o usuário: ${userName} (ID: ${userId})?`)) {
             $.ajax({
                url: BASE_URL_JS + 'admin/crudUsuario',
                type: 'POST',
                data: { acao: 'excluir', id: userId },
                dataType: 'json',
                success: function(response) {
                    if (response.sucesso) {
                        alert(response.msg);
                        recarregarTabela(); // Atualiza a tabela
                    } else {
                        alert('Erro: ' + response.msg);
                    }
                },
                error: function(jqXHR) {
                    const response = jqXHR.responseJSON;
                    const errorMsg = response && response.msg ? response.msg : 'Erro desconhecido ao excluir.';
                    alert('Erro ao excluir usuário: ' + errorMsg);
                }
            });
        }
    });
    
    // 4. Delegação de Evento para o botão Editar (o DataTables recria o HTML)
    $('#usuariosTable tbody').on('click', '.editar-usuario', function() {
        // Encontra a linha de dados do DataTables para garantir a leitura correta
        const data = usuariosTable.row($(this).parents('tr')).data(); 
        
        // Abre o modal
        $('#usuarioModal').modal('show');
        
        // Preenche os campos do modal com os dados da linha
        $('#acaoUsuario').val('editar');
        $('#usuarioModalLabel').text('Editar Usuário');
        $('#senhaUsuario').attr('required', false);
        
        $('#usuarioId').val(data.id);
        $('#nomeUsuario').val(data.nome);
        $('#emailUsuario').val(data.email);
        $('#tipoUsuario').val(data.tipo);
        $('#turmaUsuario').val(data.turma_id); // Usa turma_id
    });
});

$(document).ready(function() {
    const BASE_URL_JS = window.BASE_URL_JS;
    const turmaModal = $('#turmaModal');
    const turmaForm = $('#turmaForm');
    let turmasTable;

    // Inicialização do DataTables
    function inicializarDataTable() {
        turmasTable = $('#turmasTable').DataTable({
            "ajax": {
                "url": BASE_URL_JS + 'admin/listarTurmas',
                "type": 'GET',
                "dataSrc": 'data'
            },
            "columns": [
                { "data": "id" },
                { "data": "nome" },
                { 
                    "data": "id",
                    "render": function (data, type, row) {
                        return `
                            <button class="btn btn-sm btn-warning editar-turma" 
                                    data-id="${data}"
                                    data-nome="${row.nome}"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#turmaModal"
                                    data-acao="editar">
                                Editar
                            </button>
                            <button class="btn btn-sm btn-danger excluir-turma" data-id="${data}">
                                Excluir
                            </button>
                        `;
                    }
                }
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json"
            },
            "columnDefs": [ // Ajusta a coluna de IDs para ser menor
                { "width": "10%", "targets": 0 } 
            ]
        });
    }

    // Inicializa a tabela (a tabela no PHP deve ter o ID="turmasTable")
    // Note que você precisa mudar o HTML da tabela no PHP para usar o ID="turmasTable"
    inicializarDataTable();

    function recarregarTabela() {
        turmasTable.ajax.reload(null, false);
    }

    // 1. Ação ao abrir o Modal (Adicionar/Editar)
    turmaModal.on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        const acao = button.data('acao');
        const modalLabel = $('#turmaModalLabel');
        
        turmaForm[0].reset(); 
        $('#acaoTurma').val(acao);

        if (acao === 'adicionar') {
            modalLabel.text('Adicionar Nova Turma');
            $('#turmaId').val('');
        } else if (acao === 'editar') {
            modalLabel.text('Editar Turma');
            $('#turmaId').val(button.data('id'));
            $('#nomeTurma').val(button.data('nome'));
        }
    });
    
    // 2. Submeter o Formulário (Adicionar/Editar)
    turmaForm.submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: BASE_URL_JS + 'admin/crudTurma',
            type: 'POST',
            data: turmaForm.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.sucesso) {
                    alert(response.msg);
                    turmaModal.modal('hide');
                    recarregarTabela();
                } else {
                    alert('Erro: ' + response.msg);
                }
            },
            error: function(jqXHR) {
                const response = jqXHR.responseJSON;
                const errorMsg = response && response.msg ? response.msg : 'Erro desconhecido ao comunicar com o servidor.';
                alert('Erro ao salvar turma: ' + errorMsg);
            }
        });
    });

    // 3. Ação de Excluir
    $('#turmasTable tbody').on('click', '.excluir-turma', function() {
        const turmaId = $(this).data('id');
        const turmaNome = $(this).closest('tr').find('td:nth-child(2)').text();

        if (confirm(`ATENÇÃO: Isso excluirá todos os cronogramas associados. Tem certeza que deseja excluir a turma: ${turmaNome}?`)) {
             $.ajax({
                url: BASE_URL_JS + 'admin/crudTurma',
                type: 'POST',
                data: { acao: 'excluir', id: turmaId },
                dataType: 'json',
                success: function(response) {
                    if (response.sucesso) {
                        alert(response.msg);
                        recarregarTabela();
                    } else {
                        alert('Erro: ' + response.msg);
                    }
                },
                error: function(jqXHR) {
                    const response = jqXHR.responseJSON;
                    const errorMsg = response && response.msg ? response.msg : 'Erro desconhecido ao excluir.';
                    alert('Erro ao excluir turma: ' + errorMsg);
                }
            });
        }
    });
});