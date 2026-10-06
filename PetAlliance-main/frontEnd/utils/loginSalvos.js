const formulario = document.querySelector("form");

formulario.addEventListener("submit", () => {
    sessionStorage.setItem("cpfDigitado", document.getElementById("cpf").value);
});

window.addEventListener("DOMContentLoaded", () => {
    const cpfSalvo = sessionStorage.getItem("cpfDigitado");

    if (cpfSalvo) {
        document.getElementById("cpf").value = cpfSalvo;
    }
});

    
