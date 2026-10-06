document.addEventListener("DOMContentLoaded", () => {
    async function carregarSistema() {
        try {
            const response = await fetch("/backEnd/admin/estatisticas.php?route=sistema", { credentials: "same-origin" });
            const data = await response.json();

            const container = document.getElementById("sistema-cards");
            if (!container) return;

            if (!data.sucesso) {
                container.innerHTML = `<p>Erro: ${data.erro}</p>`;
                return;
            }

            const s = data.sistema;
            container.innerHTML = `
                <div class="stat-card"><strong>Usuários Cadastrados</strong><span>${s.totalUsuarios}</span></div>
                <div class="stat-card"><strong>Animais Cadastrados</strong><span>${s.totalAnimais}</span></div>
                <div class="stat-card"><strong>Denúncias</strong><span>${s.totalDenuncias}</span></div>
            `;
        } catch (error) {
            console.error("Erro ao carregar estatísticas do sistema:", error);
        }
    }

    carregarSistema();
});