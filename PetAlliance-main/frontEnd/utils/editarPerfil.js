// Editar perfil - apenas máscara de CEP, submit normal via formulário
document.addEventListener("DOMContentLoaded", function() {
    const cepInput = document.querySelector('input[name="cep"]');
    if (cepInput) {
        cepInput.addEventListener("input", function() {
            this.value = this.value
                .replace(/\D/g, "")
                .substring(0, 8)
                .replace(/(\d{5})(\d)/, "$1-$2");
        });
    }
});