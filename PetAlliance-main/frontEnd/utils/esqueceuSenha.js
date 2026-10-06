document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-recuperar');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const cpf = document.getElementById('cpf').value.trim();
        const btn = document.getElementById('btn-enviar');
        const erroDiv = document.getElementById('mensagem-erro');
        const sucessoDiv = document.getElementById('mensagem-sucesso');

        erroDiv.style.display = 'none';
        sucessoDiv.style.display = 'none';
        btn.disabled = true;
        btn.textContent = 'Enviando...';

        fetch('/backEnd/usuario/esqueceuSenha.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'cpf=' + encodeURIComponent(cpf)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                sucessoDiv.textContent = data.message;
                sucessoDiv.style.display = 'block';
                setTimeout(function() {
                    window.location.href = '/backEnd/usuario/inserirCodigo.php';
                }, 2000);
            } else {
                erroDiv.textContent = data.message;
                erroDiv.style.display = 'block';
            }
        })
        .catch(function() {
            erroDiv.textContent = 'Erro ao conectar com o servidor.';
            erroDiv.style.display = 'block';
        })
        .finally(function() {
            btn.disabled = false;
            btn.textContent = 'Enviar';
        });
    });
});
