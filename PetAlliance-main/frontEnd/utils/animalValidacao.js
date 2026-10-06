window.addEventListener('DOMContentLoaded', () => {
    const campoData = document.getElementById('dt_nascimento');
    const formulario = document.querySelector('form');

    if (!campoData) {
        console.warn('animalValidacao: elemento #dt_nascimento não encontrado');
        return;
    }
    if (!formulario) {
        console.warn('animalValidacao: elemento <form> não encontrado');
        return;
    }

    // Descobre a data de hoje exata do seu computador
    const hoje = new Date().toLocaleDateString('sv-SE'); // Gera o formato AAAA-MM-DD direto

    // Trava o calendário na hora
    campoData.max = hoje;

    // Se o usuário tentar digitar o ano de 2030 manualmente, barra no envio
    formulario.addEventListener('submit', (event) => {
        if (campoData.value > hoje) {
            event.preventDefault();
            alert('Erro: A data de nascimento não pode ser maior que a data atual.');
            campoData.focus();
        }
    });
});