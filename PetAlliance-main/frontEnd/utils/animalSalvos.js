window.addEventListener("DOMContentLoaded", () => {
    const formulario = document.querySelector("form");
    const certificado = document.getElementById("certificado");
    const vacinado = document.getElementById("vacinado");
    const campoCertificado = document.getElementById("campoCertificado");
    const campoVacinacao = document.getElementById("campoVacinacao");

    if (formulario) {
        formulario.addEventListener("submit", () => {
            sessionStorage.setItem("animalNome", document.getElementById("nome").value);
            sessionStorage.setItem("animalRaca", document.getElementById("raca").value);
            sessionStorage.setItem("animalCor", document.getElementById("cor").value);
            sessionStorage.setItem("animalSexo", document.getElementById("sexo").value);
            sessionStorage.setItem("animalTipo", document.getElementById("tipo").value);
            sessionStorage.setItem("animalPorte", document.getElementById("porte").value);
            sessionStorage.setItem("animalNascimento", document.getElementById("dt_nascimento").value);
            sessionStorage.setItem("animalPeso", document.getElementById("peso").value);
            sessionStorage.setItem("animalDescricao", document.getElementById("descricao").value);
        });
    }

    if (document.getElementById("nome")) {
        document.getElementById("nome").value = sessionStorage.getItem("animalNome") || "";
        document.getElementById("raca").value = sessionStorage.getItem("animalRaca") || "";
        document.getElementById("cor").value = sessionStorage.getItem("animalCor") || "";
        document.getElementById("sexo").value = sessionStorage.getItem("animalSexo") || "";
        document.getElementById("tipo").value = sessionStorage.getItem("animalTipo") || "";
        document.getElementById("porte").value = sessionStorage.getItem("animalPorte") || "";
        document.getElementById("dt_nascimento").value = sessionStorage.getItem("animalNascimento") || "";
        document.getElementById("peso").value = sessionStorage.getItem("animalPeso") || "";
        document.getElementById("descricao").value = sessionStorage.getItem("animalDescricao") || "";
    }
});


