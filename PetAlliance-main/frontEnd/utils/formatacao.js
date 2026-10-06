document.addEventListener("DOMContentLoaded", function () {
    const cpfInput = document.getElementById("cpf");
    const cepInput = document.getElementById("cep");
    const formCadastro = document.querySelector("form");
    const temCampoCep = !!document.getElementById("cep");

    if (cpfInput) {
        cpfInput.addEventListener("input", function () {
            this.value = mascaraCPF(this.value);
        });
    }

    if (cepInput) {
        cepInput.addEventListener("input", function () {
            this.value = mascaraCEP(this.value);
        });

        cepInput.addEventListener("blur", validarCep);
    }

    if (formCadastro && temCampoCep) {
        formCadastro.addEventListener("submit", async function (event) {
            event.preventDefault();

            const cepValido = await validarCep();
            if (cepValido) {
                const submitter = event.submitter;
                if (submitter && submitter.name) {
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = submitter.name;
                    hidden.value = submitter.value || '1';
                    this.appendChild(hidden);
                }
                formCadastro.submit();
            }
        });
    }
});

function mascaraCPF(valor) {
    return valor
        .replace(/\D/g, "")
        .substring(0, 11)
        .replace(/(\d{3})(\d)/, "$1.$2")
        .replace(/(\d{3})(\d)/, "$1.$2")
        .replace(/(\d{3})(\d{1,2})$/, "$1-$2");
};

function mascaraCEP(valor) {
    return valor
        .replace(/\D/g, "")
        .substring(0, 8)
        .replace(/(\d{5})(\d)/, "$1-$2");
}

async function validarCep() {
    const cepInput = document.querySelector("#cep");
    if (!cepInput) return false;

    const cep = cepInput.value.replace(/\D/g, "");

    if (cep.length !== 8) {
        alert('CEP inválido! Informe um CEP com 8 dígitos.');
        return false;
    }

    try {
        const r = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
        const d = await r.json();

        if (d.erro) {
            alert('CEP não encontrado!');
            return false;
        }

        return true;
    } catch (error) {
        alert('Não foi possível validar o CEP no momento.');
        return false;
    }
}

function avancar() {
    var aceite = document.getElementById('aceite_termos');
    if (aceite && !aceite.checked) {
        aceite.focus();
        aceite.parentElement.classList.add('checkbox-lgpd-erro');
        setTimeout(function() {
            aceite.parentElement.classList.remove('checkbox-lgpd-erro');
        }, 2000);
        return;
    }
    document.getElementById('etapa1').style.display = 'none';
    document.getElementById('etapa2').style.display = 'block';
    sessionStorage.setItem('etapa', 'etapa2');
}

function voltar() {
    document.getElementById('etapa2').style.display = 'none';
    document.getElementById('etapa1').style.display = 'block';
    sessionStorage.setItem('etapa', 'etapa1');
}

function limparFoto() {
    document.getElementById('imagemPerfil').value = '';
    const preview = document.getElementById('preview-usuario');
    if (preview) {
        preview.src = '/uploads/usuario/placeholder.webp';
    }
}
