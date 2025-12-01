<div style="padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; color: #444;">Suas Turmas e Leituras</h2>
        <a href="<?php echo BASE_URL; ?>professor/criar" class="btn-glossy" style="background: linear-gradient(#4caf50, #2e7d32); border-color: #1b5e20;">
            + Nova Leitura
        </a>
    </div>

    <?php if (empty($cronogramas)): ?>
        <div style="text-align: center; padding: 40px; background: #fff; border-radius: 10px; border: 1px dashed #ccc;">
            <p style="color: #888;">Nenhum cronograma ativo.</p>
            <p style="font-size: 14px;">Clique no botão acima para criar o primeiro!</p>
        </div>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
            <thead>
                <tr style="background: #f5f5f5; text-align: left; color: #666;">
                    <th style="padding: 12px;">Título</th>
                    <th style="padding: 12px;">Turma</th>
                    <th style="padding: 12px;">Prazo</th>
                    <th style="padding: 12px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cronogramas as $c): ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px; font-weight: bold; color: #333;"><?php echo $c['titulo']; ?></td>
                    <td style="padding: 12px; color: #555;"><?php echo $c['nome_turma']; ?></td>
                    <td style="padding: 12px; font-size: 13px; color: #777;">
                        <?php echo date('d/m', strtotime($c['data_inicial'])); ?> até 
                        <?php echo date('d/m', strtotime($c['data_final'])); ?>
                    </td>
                    <td style="padding: 12px;">
                        <a href="#" style="color: #d32f2f; text-decoration: none; font-size: 12px;">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div style="margin-top: 30px; text-align: center;">
        <a href="<?php echo BASE_URL; ?>login/sair" style="color: #777; font-size: 12px;">Sair</a>
    </div>
</div>