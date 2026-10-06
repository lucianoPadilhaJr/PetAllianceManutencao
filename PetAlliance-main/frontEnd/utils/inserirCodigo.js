document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-codigo');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const codigo = form.querySelector('input[name="codigo"]').value.trim();
        const erroDiv = document.getElementById('mensagem-erro');
        const sucessoDiv = document.getElementById('mensagem-sucesso');

        erroDiv.style.display = 'none';
        sucessoDiv.style.display = 'none';

        if (codigo.length !== 6) {
            erroDiv.textContent = 'Digite o código completo de 6 dígitos.';
            erroDiv.style.display = 'block';
            return;
        }

        fetch('/backEnd/usuario/inserirCodigo.php?route=verificar_codigo', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'codigo=' + encodeURIComponent(codigo)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                sucessoDiv.textContent = data.message;
                sucessoDiv.style.display = 'block';
                setTimeout(function() {
                    window.location.href = '/backEnd/usuario/redefinirSenha.php';
                }, 1500);
            } else {
                erroDiv.textContent = data.message;
                erroDiv.style.display = 'block';
            }
        })
        .catch(function() {
            erroDiv.textContent = 'Erro ao conectar com o servidor.';
            erroDiv.style.display = 'block';
        });
    });

    // Reenviar com cooldown de 30s
    document.getElementById('btn-reenviar').addEventListener('click', function() {
        const btn = this;
        btn.disabled = true;
        btn.textContent = 'Enviando...';

        fetch('/backEnd/usuario/inserirCodigo.php?route=reenviar', {
            method: 'POST'
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
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
            let segundos = 30;
            btn.textContent = 'Aguarde ' + segundos + 's';
            const timer = setInterval(function() {
                segundos--;
                if (segundos > 0) {
                    btn.textContent = 'Aguarde ' + segundos + 's';
                } else {
                    clearInterval(timer);
                    btn.disabled = false;
                    btn.textContent = 'Reenviar código';
                }
            }, 1000);
        });
    });
});
