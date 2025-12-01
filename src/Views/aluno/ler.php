<div id="leitura-container" class="leitura-container" data-leitura-id="<?php echo $leituraId; ?>">
    
    <header class="leitura-header">
        <h1 class="leitura-title"><?php echo htmlspecialchars($cronograma['titulo']); ?></h1>
        <a href="<?php echo BASE_URL; ?>aluno/dashboard" class="back-link">← Voltar para Missões</a>
    </header>

    <div class="font-controls text-center">
        <span class="text-muted mr-3">Tamanho da Fonte:</span>
        <button id="btnDiminuirFonte" class="btn btn-sm btn-outline-secondary" title="Diminuir Fonte">A-</button>
        <button id="btnAumentarFonte" class="btn btn-sm btn-outline-secondary" title="Aumentar Fonte">A+</button>
    </div>

    <div id="leitura-content" class="leitura-content">
        <?php $capituloAtual = 0; ?>
        <?php $livroAtual = ''; ?>
        
        <?php 
        // Verifica se o array existe e não está vazio
        if (empty($leiturasComTexto)): ?>
            <p class="error-message">
                ⚠️ Não foi possível carregar os textos dos versículos. Verifique se o arquivo biblia.json está completo.
            </p>
        <?php else: ?>
        
            <?php foreach ($leiturasComTexto as $item): ?>
                
                <?php 
                // 🛑 CORREÇÃO LIVRO: VERIFICA SE O LIVRO MUDOU
                if ($item['livro'] !== $livroAtual): 
                    $livroAtual = $item['livro']; 
                    $capituloAtual = 0; // Reseta o capítulo para garantir que o Capítulo 1 seja exibido
                ?>
                    <h3 class="book-title">
                        <?php echo htmlspecialchars($livroAtual); ?>
                    </h3>
                <?php endif; ?>
                
                <?php 
                // VERIFICA SE O CAPÍTULO MUDOU
                if ($item['capitulo'] != $capituloAtual): ?>
                    <h2 class="chapter-title">
                        Capítulo <?php echo $item['capitulo'] ?? 'N/A'; ?>
                    </h2>
                    <?php $capituloAtual = $item['capitulo']; ?>
                <?php endif; ?>

                <p class="versiculo-item" 
                   data-ref="<?php echo $item['referencia'] ?? ''; ?>"
                   data-id="<?php echo $item['versiculo'] ?? ''; ?>"
                   data-lido="<?php echo (isset($item['lido']) && $item['lido']) ? 'true' : 'false'; ?>">
                    
                    <sup class="verse-number" style="color: <?php echo (isset($item['lido']) && $item['lido']) ? '#4caf50' : '#ff5722'; ?>;">
                        <?php echo $item['versiculo'] ?? '-'; ?>
                    </sup>
                    
                    <?php 
                    // CORREÇÃO: O TEXTO REAL DO VERSÍCULO
                    echo htmlspecialchars($item['texto'] ?? 'Versículo não encontrado no JSON.'); 
                    ?>
                </p>

            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    // Variáveis específicas desta tela:
    const ALUNO_ID = <?php echo (int)$_SESSION['usuario_id']; ?>;
    const LEITURA_ID = <?php echo (int)$leituraId; ?>;
    
    const versiculos = document.querySelectorAll('.versiculo-item');
    const scrollHandler = debounce(checkVersiculosVisibility, 100); 

    // Adiciona o listener de scroll à janela
    window.addEventListener('scroll', scrollHandler);
    window.addEventListener('resize', checkVersiculosVisibility);

    // ===================================
    // LÓGICA DE CONTROLE DE FONTE (A+ / A-)
    // ===================================
    
    const textoContainer = document.getElementById('leitura-content');
    const btnAumentar = document.getElementById('btnAumentarFonte');
    const btnDiminuir = document.getElementById('btnDiminuirFonte');
    
    const passo = 1; // Aumenta ou diminui 1px por clique
    const minTamanho = 14;
    const maxTamanho = 26;

    // Obtém o tamanho atual da CSS Variable
    function getTamanhoAtual() {
        const style = getComputedStyle(textoContainer);
        let currentSize = parseFloat(style.getPropertyValue('--leitura-font-size'));
        return currentSize || 18; // Retorna o padrão de 18px se a leitura falhar
    }

    // Aplica o novo tamanho (grava na CSS Variable)
    function aplicarTamanhoFonte(novoTamanho) {
        textoContainer.style.setProperty('--leitura-font-size', `${novoTamanho}px`);
    }

    // Listener Aumentar
    btnAumentar.addEventListener('click', () => {
        let tamanhoFonteAtual = getTamanhoAtual();
        if (tamanhoFonteAtual < maxTamanho) {
            tamanhoFonteAtual += passo;
            aplicarTamanhoFonte(tamanhoFonteAtual);
        }
    });

    // Listener Diminuir
    btnDiminuir.addEventListener('click', () => {
        let tamanhoFonteAtual = getTamanhoAtual();
        if (tamanhoFonteAtual > minTamanho) {
            tamanhoFonteAtual -= passo;
            aplicarTamanhoFonte(tamanhoFonteAtual);
        }
    });
    
    // Aplica o tamanho padrão do CSS (18px)
    aplicarTamanhoFonte(getTamanhoAtual()); 
    
    // ===================================
    // LÓGICA DE SCROLL IMERSIVA E SALVAMENTO
    // ===================================

    function checkVersiculosVisibility() {
        const viewportHeight = window.innerHeight;
        // Ponto de Referência: Marca o versículo quando ele passa da metade da tela (50%)
        const MARCA_Y = viewportHeight * 0.50; 

        versiculos.forEach(item => {
            // Se o item já foi marcado, ignora
            if (item.dataset.lido === 'true') return; 

            const rect = item.getBoundingClientRect();

            if (rect.top <= MARCA_Y) {
                // Atualiza o estado JS
                item.dataset.lido = 'true'; 
                
                // Salva no Banco de Dados (Chamada AJAX)
                salvarProgressoAutomatico(item.dataset.ref, true);
            }
        });
    }

    function salvarProgressoAutomatico(versiculoRef, status) {
        // Aponta para o endpoint AJAX dedicado (que resolveu o erro do console)
        fetch(window.BASE_URL_JS + 'ajax/salvar_progresso.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                leitura_id: LEITURA_ID,
                versiculo: versiculoRef,
                lido: status // true/false
            })
        })
        .then(res => res.json())
        .then(data => {
            if(!data.sucesso) {
                console.error('Erro ao salvar progresso automático:', data.msg);
            } else {
                console.log('Progresso salvo com sucesso:', data.progresso_salvo);
            }
        })
        .catch(err => console.error('Falha na comunicação AJAX:', err));
    }

    // Função para otimizar o scroll (Debounce)
    function debounce(func, timeout = 300) {
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => { func.apply(this, args); }, timeout);
        };
    }
    
    window.addEventListener('load', checkVersiculosVisibility);

</script>