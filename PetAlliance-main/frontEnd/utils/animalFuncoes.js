window.addEventListener('DOMContentLoaded', () => {
    const vacinado = document.getElementById('vacinado');
    const certificado = document.getElementById('certificado');
    const campoVacinacao = document.getElementById('campoVacinacao');
    const campoCertificado = document.getElementById('campoCertificado');
    const arquivoVacinacao = document.getElementById('arquivoVacinacao');
    const arquivoCertificado = document.getElementById('arquivoCertificado');

    if (vacinado) {
        vacinado.addEventListener('change', () => {
            if (vacinado.value === '1') {
                if (campoVacinacao) campoVacinacao.style.display = 'block';
                if (arquivoVacinacao) arquivoVacinacao.required = true;
            } else {
                if (campoVacinacao) campoVacinacao.style.display = 'none';
                if (arquivoVacinacao) {
                    arquivoVacinacao.required = false;
                    arquivoVacinacao.value = '';
                }
            }
        });
    }

    if (certificado) {
        certificado.addEventListener('change', () => {
            if (certificado.value === '1') {
                if (campoCertificado) campoCertificado.style.display = 'block';
                if (arquivoCertificado) arquivoCertificado.required = true;
            } else {
                if (campoCertificado) campoCertificado.style.display = 'none';
                if (arquivoCertificado) {
                    arquivoCertificado.required = false;
                    arquivoCertificado.value = '';
                }
            }
        });
    }
});