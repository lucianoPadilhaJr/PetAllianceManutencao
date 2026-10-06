function validarSenhaForte(senha) {
    if (senha.length < 8) {
        return 'A senha deve ter pelo menos 8 caracteres.';
    }
    if (!/[A-Z]/.test(senha)) {
        return 'A senha deve conter pelo menos uma letra maiúscula.';
    }
    if (!/[a-z]/.test(senha)) {
        return 'A senha deve conter pelo menos uma letra minúscula.';
    }
    if (!/[0-9]/.test(senha)) {
        return 'A senha deve conter pelo menos um número.';
    }
    return null;
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-senha');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const senha = document.getElementById('senha').value;
        const confirmar = document.getElementById('confirmar_senha').value;
        const erroDiv = document.getElementById('mensagem-erro');
        const sucessoDiv = document.getElementById('mensagem-sucesso');
        const btn = form.querySelector('button');

        erroDiv.style.display = 'none';
        sucessoDiv.style.display = 'none';

        const erroSenha = validarSenhaForte(senha);
        if (erroSenha) {
            erroDiv.textContent = erroSenha;
            erroDiv.style.display = 'block';
            return;
        }

        if (senha !== confirmar) {
            erroDiv.textContent = 'As senhas não conferem.';
            erroDiv.style.display = 'block';
            return;
        }

        btn.disabled = true;
        btn.textContent = 'Salvando...';

        fetch('/backEnd/usuario/redefinirSenha.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'senha=' + encodeURIComponent(senha) + '&confirmar_senha=' + encodeURIComponent(confirmar)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                sucessoDiv.textContent = data.message;
                sucessoDiv.style.display = 'block';
                form.style.display = 'none';
                setTimeout(function() {
                    window.location.href = '/backEnd/home.php';
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
            btn.textContent = 'Salvar nova senha';
        });
    });
});
