document.addEventListener("DOMContentLoaded", async () => {
    try {
        const response = await fetch("/backEnd/match.php?route=naoLidas", { credentials: "same-origin" });
        const data = await response.json();
        const link = document.querySelector('a[href="/backEnd/match.php"]');
        if (link && data.naoLidas > 0) {
            const badge = document.createElement("span");
            badge.className = "badge-notificacoes";
            badge.textContent = data.naoLidas;
            link.appendChild(badge);
        }
    } catch (error) {
        console.error("Erro ao carregar badge:", error);
    }
});
