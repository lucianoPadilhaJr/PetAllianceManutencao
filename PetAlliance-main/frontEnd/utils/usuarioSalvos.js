const params = new URLSearchParams(window.location.search);
const formulario = document.querySelector("form");

if (params.get("sucesso") === "1") {
    [
        "nomeDigitado",
        "cpfDigitado",
        "cepDigitado",
        "emailDigitado",
        "imagemEscolhida",
        "etapa",
    ].forEach(chave => sessionStorage.removeItem(chave));
    [
        "animalNome",
        "animalRaca",
        "animalCor",
        "animalSexo",
        "animalTipo",
        "animalPorte",
        "animalNascimento",
        "animalPeso",
        "animalDescricao"
    ].forEach(chave => sessionStorage.removeItem(chave));
}

if (formulario) {
    formulario.addEventListener("submit", () => {
        const nomeInput = document.getElementById("nome");
        const cpfInput = document.getElementById("cpf");
        const cepInput = document.getElementById("cep");
        const emailInput = document.getElementById("email");
        const imagemInput = document.getElementById("imagemPerfil");

        if (nomeInput) sessionStorage.setItem("nomeDigitado", nomeInput.value);
        if (cpfInput) sessionStorage.setItem("cpfDigitado", cpfInput.value);
        if (cepInput) sessionStorage.setItem("cepDigitado", cepInput.value);
        if (emailInput) sessionStorage.setItem("emailDigitado", emailInput.value);
        if (imagemInput) sessionStorage.setItem("imagemEscolhida", imagemInput.value);
    });
}

window.addEventListener("DOMContentLoaded", () => {
    const nomeSalvo = sessionStorage.getItem("nomeDigitado");
    const cpfSalvo = sessionStorage.getItem("cpfDigitado");
    const cepSalvo = sessionStorage.getItem("cepDigitado");
    const emailSalvo = sessionStorage.getItem("emailDigitado");
    const imagemSalva = sessionStorage.getItem("imagemEscolhida");



    const nomeInput = document.getElementById("nome");
    const cpfInput = document.getElementById("cpf");
    const cepInput = document.getElementById("cep");
    const emailInput = document.getElementById("email");
    const imagemInput = document.getElementById("imagemPerfil");

    if (nomeSalvo && nomeInput) nomeInput.value = nomeSalvo;
    if (cpfSalvo && cpfInput) cpfInput.value = cpfSalvo;
    if (cepSalvo && cepInput) cepInput.value = cepSalvo;
    if (emailSalvo && emailInput) emailInput.value = emailSalvo;
    if (imagemSalva && imagemInput) imagemInput.value = imagemSalva;
});



