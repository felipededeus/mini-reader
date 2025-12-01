// public/assets/js/professor.js
$(document).ready(function() {
    
    // --- Lógica do Autocomplete (agora na função ready) ---
    var livrosBiblia = [
        "Gênesis", "Êxodo", "Levítico", "Números", "Deuteronômio", "Josué", "Juízes", "Rute", 
        "1 Samuel", "2 Samuel", "1 Reis", "2 Reis", "1 Crônicas", "2 Crônicas", "Esdras", "Neemias", "Ester", 
        "Jó", "Salmos", "Provérbios", "Eclesiastes", "Cantares de Salomão", "Isaías", "Jeremias", "Lamentações", 
        "Ezequiel", "Daniel", "Oséias", "Joel", "Amós", "Obadias", "Jonas", "Miquéias", "Naum", "Habacuque", 
        "Sofonias", "Ageu", "Zacarias", "Malaquias", "Mateus", "Marcos", "Lucas", "João", "Atos", "Romanos", 
        "1 Coríntios", "2 Coríntios", "Gálatas", "Efésios", "Filipenses", "Colossenses", "1 Tessalonicenses", 
        "2 Tessalonicenses", "1 Timóteo", "2 Timóteo", "Tito", "Filemom", "Hebreus", "Tiago", "1 Pedro", 
        "2 Pedro", "1 João", "2 João", "3 João", "Judas", "Apocalipse"
    ];
    
    $("#buscaLivro").autocomplete({
        source: livrosBiblia
    });

    // --- Lógica da Busca (AJAX) ---
    window.buscarNaBiblia = function() {
        // ... (Mantenha o código da função buscarNaBiblia aqui, sem a palavra 'function' antes do nome) ...
        const livro = document.getElementById('buscaLivro').value;
        const ref = document.getElementById('buscaReferencia').value;
        const divResult = document.getElementById('resultadoVersiculos');

        if(!livro || !ref) {
            alert("Preencha o Livro e a Referência!");
            return;
        }

        divResult.innerHTML = "<div style='padding:20px;'>Carregando...</div>";
        divResult.style.display = "block";

        const formData = new FormData();
        formData.append('livro', livro);
        formData.append('referencia', ref);

        fetch(BASE_URL_JS + 'professor/ajaxBuscarVersiculos', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(html => {
            divResult.innerHTML = html;
        })
        .catch(error => {
            console.error(error);
            divResult.innerHTML = "Erro ao buscar.";
        });
    };
    
    // --- Lógica do Prazo (Calculadora de Data) ---
    window.definirPrazo = function(dias) {
        // ... (Mantenha o código da função definirPrazo aqui, sem a palavra 'function' antes do nome) ...
        const elInicio = document.getElementById('data_inicial');
        const elFim = document.getElementById('data_final');

        if (!elInicio.value) {
            alert("Defina a data inicial primeiro!");
            return;
        }

        const dataBase = new Date(elInicio.value + 'T00:00:00');
        dataBase.setDate(dataBase.getDate() + dias);

        const yyyy = dataBase.getFullYear();
        const mm = String(dataBase.getMonth() + 1).padStart(2, '0');
        const dd = String(dataBase.getDate()).padStart(2, '0');

        elFim.value = `${yyyy}-${mm}-${dd}`;
        
        elFim.style.backgroundColor = "#e3f2fd";
        setTimeout(() => { elFim.style.backgroundColor = "#fff"; }, 500);
    };

});