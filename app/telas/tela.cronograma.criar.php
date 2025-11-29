<?php include '../global/header.php'; ?>

<h2>Criar Novo Cronograma</h2>

<form id="cronogramaForm" method="post">
    <div class="form-group">
        <label for="titulo">Título:</label>
        <input type="text" class="form-control" name="titulo" id="titulo" required>
    </div>
    <div class="form-group">
        <label for="descricao">Descrição:</label>
        <textarea class="form-control" name="descricao" id="descricao" required></textarea>
    </div>
    <div class="form-group">
        <label for="data_inicial">Data Inicial:</label>
        <input type="date" class="form-control" name="data_inicial" id="data_inicial" required>
    </div>
    <div class="form-group">
        <label for="data_final">Data Final:</label>
        <input type="date" class="form-control" name="data_final" id="data_final" required>
    </div>
    <div class="form-group">
        <label for="turma_id">Turma:</label>
        <select class="form-control" name="turma_id" id="turma_id" required>
            <?php
            include '../../conexao.class.php';
            $conexao = new Conexao();
            $pdo = $conexao->conectar();
            $stmt = $pdo->query("SELECT id, nome FROM turmas");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='{$row['id']}'>{$row['nome']}</option>";
            }
            ?>
        </select>
    </div>
    <div class="form-group">
        <label for="livro">Livro:</label>
        <input type="text" class="form-control" name="livro" id="livro" required>
    </div>
    <div class="form-group">
        <label for="capitulo">Capítulo:</label>
        <input type="number" class="form-control" name="capitulo" id="capitulo" required>
    </div>
    <button type="button" class="btn btn-primary" id="btnCarregarVersiculos">Carregar Versículos</button>
    <div id="loading" style="display:none;"><img src="../assets/loading.gif" alt="Carregando..."></div>
    <div id="versiculosContainer" class="mt-4"></div>
    <button type="submit" class="btn btn-primary mt-3" id="btnSalvarCronograma">Salvar Cronograma</button>
</form>

<?php include '../global/footer.php'; ?>

<script>
$(document).ready(function() {
    var livros = ["Gênesis", "Êxodo", "Levítico", "Números", "Deuteronômio", "Josué", "Juízes", "Rute", "1 Samuel", "2 Samuel", "1 Reis", "2 Reis", "1 Crônicas", "2 Crônicas", "Esdras", "Neemias", "Ester", "Jó", "Salmos", "Provérbios", "Eclesiastes", "Cantares de Salomão", "Isaías", "Jeremias", "Lamentações", "Ezequiel", "Daniel", "Oséias", "Joel", "Amós", "Obadias", "Jonas", "Miquéias", "Naum", "Habacuque", "Sofonias", "Ageu", "Zacarias", "Malaquias", "Mateus", "Marcos", "Lucas", "João", "Atos", "Romanos", "1 Coríntios", "2 Coríntios", "Gálatas", "Efésios", "Filipenses", "Colossenses", "1 Tessalonicenses", "2 Tessalonicenses", "1 Timóteo", "2 Timóteo", "Tito", "Filemom", "Hebreus", "Tiago", "1 Pedro", "2 Pedro", "1 João", "2 João", "3 João", "Judas", "Apocalipse"];

    $("#livro").autocomplete({ source: livros });

    $("#btnCarregarVersiculos").click(function() {
        $("#loading").show();
        console.log("Iniciando solicitação AJAX para carregar versículos.");
        $.ajax({
            url: '../controllers/controller.cronograma.ajax.php',
            type: 'POST',
            data: $('#cronogramaForm').serialize() + '&acao=carregar_versiculos',
            success: function(response) {
                $("#loading").hide();
                console.log("Resposta recebida:", response);
                $("#versiculosContainer").html(response);

                if ($("#marcarTodos").length === 0) {
                    $("#versiculosContainer").prepend('<button id="marcarTodos" type="button" class="btn btn-secondary mb-3">Marcar Todos</button>');
                }

                // Adicionar classe de destaque ao clicar
                $("input[type=checkbox]").change(function() {
                    if (this.checked) {
                        $(this).parent().css("background-color", "#d9edf7");
                    } else {
                        $(this).parent().css("background-color", "");
                    }
                });

                $("#marcarTodos").click(function() {
                    $("input[type=checkbox]").each(function() {
                        $(this).prop('checked', true);
                        $(this).parent().css("background-color", "#d9edf7");
                    });
                });
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $("#loading").hide();
                console.error("Erro ao carregar versículos:", textStatus, errorThrown);
                alert('Erro ao carregar versículos.');
            }
        });
    });

    // Evitar o refresh do formulário ao salvar
    $("#cronogramaForm").submit(function(e) {
        e.preventDefault();
        console.log("Iniciando submissão AJAX do formulário.");
        $.ajax({
            url: '../controllers/controller.cronograma.php',
            type: 'POST',
            data: $(this).serialize() + '&acao=criar',
            success: function(response) {
                console.log("Resposta de salvar cronograma:", response);
                alert(response); // Exibir resposta do servidor
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Erro ao salvar cronograma:", textStatus, errorThrown);
                alert('Erro ao salvar cronograma.');
            }
        });
    });
});
</script>

