// tela.cronograma.criar.php

<?php include '../global/header.php'; ?>

<h2>Criar Novo Cronograma</h2>

<form id="cronogramaForm">
    <input type="hidden" name="acao" value="carregar_versiculos">
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
    <button type="submit" class="btn btn-primary mt-3">Salvar Cronograma</button>
</form>

<?php include '../global/footer.php'; ?>

<script>
$(document).ready(function() {
    var livros = ["Gênesis", "Êxodo", "Levítico", "Números", "Deuteronômio", "Josué", "Juízes", "Rute", "1 Samuel", "2 Samuel", "1 Reis", "2 Reis", "1 Crônicas", "2 Crônicas", "Esdras", "Neemias", "Ester", "Jó", "Salmos", "Provérbios", "Eclesiastes", "Cantares de Salomão", "Isaías", "Jeremias", "Lamentações", "Ezequiel", "Daniel", "Oséias", "Joel", "Amós", "Obadias", "Jonas", "Miquéias", "Naum", "Habacuque", "Sofonias", "Ageu", "Zacarias", "Malaquias", "Mateus", "Marcos", "Lucas", "João", "Atos", "Romanos", "1 Coríntios", "2 Coríntios", "Gálatas", "Efésios", "Filipenses", "Colossenses", "1 Tessalonicenses", "2 Tessalonicenses", "1 Timóteo", "2 Timóteo", "Tito", "Filemom", "Hebreus", "Tiago", "1 Pedro", "2 Pedro", "1 João", "2 João", "3 João", "Judas", "Apocalipse"];

    $("#livro").autocomplete({ source: livros });

    $("#btnCarregarVersiculos").click(function() {
        $("#loading").show();

        $.ajax({
            url: '../controllers/controller.cronograma.ajax.php',
            type: 'POST',
            data: $('#cronogramaForm').serialize(),
            success: function(response) {
                $("#loading").hide();
                $("#versiculosContainer").html(response);

                if ($("#marcarTodos").length === 0) {
                    $("#versiculosContainer").prepend('<button id="marcarTodos" class="btn btn-secondary mb-3">Marcar Todos</button>');
                }

                $("#marcarTodos").click(function() {
                    $("input[type=checkbox]").prop('checked', true);
                });
            },
            error: function() {
                $("#loading").hide();
                alert('Erro ao carregar versículos.');
            }
        });
    });
});
</script>
