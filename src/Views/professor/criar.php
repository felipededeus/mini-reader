<div style="padding: 20px;">
    <h2 style="color: #444; margin-bottom: 20px;">Criar Novo Cronograma</h2>

    <form action="<?php echo BASE_URL; ?>professor/salvar" method="POST">
        
        <div style="background: #fff; padding: 15px; border-radius: 10px; border: 1px solid #ddd; margin-bottom: 20px;">
            <div style="margin-bottom: 15px;">
                <label style="display: block; color: #666; font-size: 13px; margin-bottom: 5px;">Título da Leitura</label>
                <input type="text" name="titulo" required placeholder="Ex: A Criação"
                       style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; color: #666; font-size: 13px; margin-bottom: 5px;">Para qual turma?</label>
                <select name="turma_id" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; background: #fff;">
                    <?php foreach ($turmas as $t): ?>
                        <option value="<?php echo $t['id']; ?>"><?php echo $t['nome']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

          <div style="margin-bottom: 15px;">
                <div style="display: flex; gap: 10px; flex-wrap: wrap;"> 
                    <div style="flex: 1 1 100%; min-width: 120px;"> 
                        <label style="display: block; color: #666; font-size: 13px; margin-bottom: 5px;">Início</label>
                        <input type="date" name="data_inicial" id="data_inicial" 
                               value="<?php echo date('Y-m-d'); ?>" 
                               required 
                               style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                    </div>
                    <div style="flex: 1 1 100%; min-width: 120px;">
                        <label style="display: block; color: #666; font-size: 13px; margin-bottom: 5px;">Fim</label>
                        <input type="date" name="data_final" id="data_final" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                    </div>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 10px; flex-wrap: wrap;">
                    <button type="button" onclick="definirPrazo(7)" class="btn-glossy" 
                            style="/* ... estilos ... */ flex: 1; min-width: 80px;">+1 Semana</button>
                    <button type="button" onclick="definirPrazo(15)" class="btn-glossy" 
                            style="/* ... estilos ... */ flex: 1; min-width: 80px;">+15 Dias</button>
                    <button type="button" onclick="definirPrazo(30)" class="btn-glossy" 
                            style="/* ... estilos ... */ flex: 1; min-width: 80px;">+1 Mês</button>
                </div>
            </div>
            
            <div style="margin-top: 15px;">
                <label style="display: block; color: #666; font-size: 13px; margin-bottom: 5px;">Descrição (Opcional)</label>
                <textarea name="descricao" rows="2" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;"></textarea>
            </div>
        </div>

        <div style="background: #fff; padding: 15px; border-radius: 10px; border: 1px solid #ddd; margin-bottom: 20px;">
            <h3 style="margin-top: 0; font-size: 16px; color: #ff5722;">Selecionar Versículos</h3>
            
            <div style="display: flex; gap: 10px; margin-bottom: 10px; flex-wrap: wrap; align-items: flex-end;">
                
                <div style="flex: 1 1 48%; min-width: 120px;">
                    <label style="font-size: 12px; color: #888;">Livro</label>
                    <input type="text" id="buscaLivro" placeholder="Digite o nome..." style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                </div>
                <div style="flex: 1 1 48%; min-width: 80px;">
                    <label style="font-size: 12px; color: #888;">Referência</label>
                    <input type="text" id="buscaReferencia" placeholder="Ex: 1 ou 1-3 ou 1:1-5" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                </div>
                
                <button type="button" onclick="buscarNaBiblia()" class="btn-glossy" 
                        style="font-size: 13px; padding: 10px 20px; height: 42px; 
                               /* Garante que ocupe a linha toda no mobile */
                               flex: 1 1 100%; 
                               margin-top: 5px;">
                    Buscar Versículos
                </button>
            </div>
            
            <p style="font-size: 11px; color: #999; margin-top: -5px; margin-bottom: 15px;">
                Dica: Digite <b>1</b> para capítulo inteiro, <b>1-3</b> para vários capítulos ou <b>1:1-5</b> para versículos específicos.
            </p>

            <div id="resultadoVersiculos" style="min-height: 50px; background: #fafafa; border: 1px dashed #ccc; border-radius: 6px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #999;">
                Os versículos aparecerão aqui...
            </div>
        </div>

        <button type="submit" class="btn-glossy" style="width: 100%; font-size: 18px; padding: 15px;">
            Salvar Cronograma
        </button>

    </form>
</div>
