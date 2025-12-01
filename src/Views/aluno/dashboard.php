<div style="padding: 10px;">
    
    <div style="text-align: center; margin-bottom: 25px;">
        <h2 style="color: #444; margin-bottom: 5px;">Olá, <?php echo htmlspecialchars($nome_aluno); ?>! 👋</h2>
        <p style="color: #888; font-size: 14px;">Suas missões de leitura estão esperando. Boa jornada!</p>
    </div>

    <?php if (empty($cronogramas)): ?>
        <div style="text-align: center; padding: 40px; color: #999; background: #fff; border-radius: 12px; border: 1px dashed #ccc;">
            <p>🎉 Oba! Nenhuma leitura pendente por enquanto.</p>
        </div>
    <?php else: ?>

        <?php foreach ($cronogramas as $cronograma): ?>
            
            <a href="<?php echo BASE_URL; ?>aluno/ler/<?php echo $cronograma['id']; ?>"
               class="card-leitura"
               style="text-decoration: none; display: block; background: #fff; border: 1px solid #ddd; border-radius: 12px; margin-bottom: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.05); overflow: hidden; transition: transform 0.2s, box-shadow 0.2s;">
                
                <div style="padding: 15px; background: linear-gradient(to right, #fff59d, #fff); border-bottom: 1px solid #ffee58;">
                    <strong style="font-size: 18px; color: #ff5722; display: block;">📖 <?php echo htmlspecialchars($cronograma['titulo']); ?></strong>
                    <span style="font-size: 14px; color: #777; display: block; margin-top: 5px;">
                        Prazo: <?php echo date('d/m/Y', strtotime($cronograma['data_final'])); ?>
                    </span>
                    <p style="font-size: 12px; color: #999; margin: 5px 0 0;">
                        <?php echo htmlspecialchars($cronograma['descricao']); ?>
                    </p>
                    
                    <div style="background: #eee; height: 10px; border-radius: 5px; margin-top: 10px; overflow: hidden;">
                        <div style="background: #4caf50; width: <?php echo $cronograma['porcentagem']; ?>%; height: 100%; transition: width 0.5s;"></div>
                    </div>
                    <div style="text-align: right; font-size: 11px; color: #666; margin-top: 2px;">
                        <?php echo $cronograma['porcentagem']; ?>% concluído 
                        (<?php echo $cronograma['lidos_count']; ?>/<?php echo $cronograma['total_count']; ?> versículos)
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>

    <div style="text-align: center; margin-top: 30px;">
        <a href="<?php echo BASE_URL; ?>login/sair" style="color: #ff5722; font-size: 12px; text-decoration: none;">Sair do aplicativo</a>
    </div>
</div>

<script>
    // Efeito visual de hover/toque para o card (iOS feel)
    document.querySelectorAll('.card-leitura').forEach(card => {
        card.addEventListener('touchstart', function() {
            this.style.boxShadow = '0 0 15px rgba(255, 87, 34, 0.5)';
            this.style.transform = 'scale(0.98)';
        });
        card.addEventListener('touchend', function() {
            this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.05)';
            this.style.transform = 'scale(1)';
        });
        // Adiciona um listener de clique (desktop)
        card.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = this.href;
        });
    });
</script>