// tela.cronogramas.php

<?php
include '../global/header.php'; 
include '../../conexao.class.php';
?>

<h2>Gerenciamento de Cronogramas</h2>
<a href="tela.cronograma.criar.php" class="btn btn-success">Criar Novo Cronograma</a>

<table class="table">
    <thead>
        <tr>
            <th>Título</th>
            <th>Descrição</th>
            <th>Data Inicial</th>
            <th>Data Final</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $conexao = new Conexao();
        $pdo = $conexao->conectar();

        $stmt = $pdo->query("SELECT * FROM cronogramas");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>{$row['titulo']}</td>";
            echo "<td>{$row['descricao']}</td>";
            echo "<td>{$row['data_inicial']}</td>";
            echo "<td>{$row['data_final']}</td>";
            echo "<td>
                    <form method='post' action='../controllers/controller.cronograma.php' style='display:inline;'>
                        <input type='hidden' name='acao' value='excluir'>
                        <input type='hidden' name='id' value='{$row['id']}'>
                        <button type='submit' class='btn btn-danger'>Excluir</button>
                    </form>
                    <a href='tela.cronograma.editar.php?id={$row['id']}' class='btn btn-primary'>Editar</a>
                  </td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>

<?php include '../global/footer.php'; ?>
