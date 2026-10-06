document.addEventListener("DOMContentLoaded", () => {
    const API_BASE = "/backEnd/match.php?route=";

    function dataFormatada(dataStr) {
        if (!dataStr) return '';
        try { return new Date(dataStr).toLocaleString('pt-BR'); } catch(e) { return dataStr; }
    }

    function notifCardHtml(htmlInterno) {
        return '<div class="notif-card">' + htmlInterno + '</div>';
    }

    async function carregarSolicitacoes() {
        try {
            const response = await fetch(API_BASE + "listar_solicitacoes", { credentials: "same-origin" });
            const data = await response.json();
            const container = document.getElementById("solicitacoes-list");
            if (!container) return;

            if (!data.sucesso || !data.solicitacoes.length) {
                container.innerHTML = '<div class="notif-empty">Nenhuma solicitação de match.</div>';
                return;
            }

            let html = '';
            data.solicitacoes.forEach(s => {
                const foto = s.pet_foto || "placeholder.webp";
                const fotoRemetente = s.remetente_imagem || "placeholder.webp";
                let statusCor = s.status === 'pendente' ? '#f57c00' : s.status === 'aceito' ? '#2e7d32' : '#d32f2f';
                let statusLabel = s.status === 'pendente' ? 'Pendente' : s.status === 'aceito' ? 'Aceito' : 'Recusado';

                html += '<div class="notif-card notif-card-solicitacao">' +
                    '<div class="notif-card-avatar"><img src="/uploads/animais/' + foto + '" alt="Pet"></div>' +
                    '<div class="notif-card-body">' +
                        '<div class="notif-card-title">' + s.pet_nome + '</div>' +
                        '<div class="notif-card-sub"><img src="/uploads/usuario/' + fotoRemetente + '" class="notif-avatar-mini"> ' + s.remetente_nome + '</div>' +
                        '<div class="notif-card-date">' + dataFormatada(s.criado_em) + '</div>' +
                    '</div>' +
                    '<div class="notif-card-right">' +
                        '<span class="notif-status-badge" style="background:' + statusCor + '1a;color:' + statusCor + ';">' + statusLabel + '</span>' +
                        '<div class="notif-card-actions">' +
                            (s.status === 'pendente'
                                ? '<button class="btn-aceitar" data-id="' + s.id + '">Aceitar</button><button class="btn-recusar" data-id="' + s.id + '">Recusar</button>'
                                : s.status === 'aceito'
                                    ? '<a href="/backEnd/chat.php?solicitacao_id=' + s.id + '" class="btn-chat">Iniciar Chat</a>'
                                    : '<span class="match-recusado">Recusado</span>') +
                        '</div>' +
                    '</div>' +
                '</div>';
            });
            container.innerHTML = html;
        } catch (error) {
            console.error("Erro ao carregar solicitações:", error);
        }
    }

    async function carregarNotificacoes() {
        try {
            const response = await fetch(API_BASE + "notificacoes", { credentials: "same-origin" });
            const data = await response.json();
            if (!data.sucesso) return;

            function renderLista(lista, container, icone, cor, vazio) {
                if (!container) return;
                if (!lista || !lista.length) {
                    container.innerHTML = '<div class="notif-empty">' + vazio + '</div>';
                    return;
                }
                let h = '';
                lista.forEach(n => {
                    var lidaClass = n.lida ? '' : ' notif-card-nao-lida';
                    h += '<div class="notif-card' + lidaClass + '">' +
                        '<div class="notif-card-icon" style="background:' + cor + '20;color:' + cor + ';">' + icone + '</div>' +
                        '<div class="notif-card-body">' +
                            '<div class="notif-card-title">' + n.mensagem + '</div>' +
                            '<div class="notif-card-date">' + dataFormatada(n.criado_em) + '</div>' +
                        '</div>' +
                        '<div class="notif-card-actions">' +
                            (!n.lida ? '<button class="btn-marcar-lida" data-id="' + n.id + '">Marcar como lida</button>' : '') +
                        '</div>' +
                    '</div>';
                });
                container.innerHTML = h;
            }

            renderLista(data.matchAceitos, document.getElementById("notif-match-aceitos-list"), '\u2713', '#2e7d32', 'Nenhum match aceito.');
            renderLista(data.matchRecusados, document.getElementById("notif-match-recusados-list"), '\u2717', '#d32f2f', 'Nenhum match recusado.');
            renderLista(data.outras, document.getElementById("notif-outras-list"), '\u2139', '#2e7d32', 'Nenhuma notificação.');
        } catch (error) {
            console.error("Erro ao carregar notificações:", error);
        }
    }

    async function carregarMinhasSolicitacoes() {
        try {
            const response = await fetch(API_BASE + "minhas_solicitacoes", { credentials: "same-origin" });
            const data = await response.json();
            const container = document.getElementById("minhas-solicitacoes-list");
            if (!container) return;

            if (!data.sucesso || !data.solicitacoes.length) {
                container.innerHTML = '<div class="notif-empty">Você ainda não enviou nenhuma solicitação.</div>';
                return;
            }

            let html = '';
            data.solicitacoes.forEach(s => {
                const foto = s.pet_foto || "placeholder.webp";
                let statusCor = s.status === 'pendente' ? '#f57c00' : s.status === 'aceito' ? '#2e7d32' : '#d32f2f';
                let statusLabel = s.status === 'pendente' ? 'Pendente' : s.status === 'aceito' ? 'Aceito' : 'Recusado';

                html += '<div class="notif-card">' +
                    '<div class="notif-card-avatar"><img src="/uploads/animais/' + foto + '" alt="Pet"></div>' +
                    '<div class="notif-card-body">' +
                        '<div class="notif-card-title">' + s.pet_nome + '</div>' +
                        '<div class="notif-card-sub">' + (s.status === 'pendente' ? 'Aguardando resposta' : s.status === 'aceito' ? 'Match aceito!' : 'Match recusado') + '</div>' +
                        '<div class="notif-card-date">' + dataFormatada(s.criado_em) + '</div>' +
                    '</div>' +
                    '<div class="notif-card-actions">' +
                        (s.status === 'aceito' ? '<a href="/backEnd/chat.php?solicitacao_id=' + s.id + '" class="btn-chat">Iniciar Chat</a>' : '<span class="notif-status-badge" style="background:' + statusCor + '1a;color:' + statusCor + ';">' + statusLabel + '</span>') +
                    '</div>' +
                '</div>';
            });
            container.innerHTML = html;
        } catch (error) {
            console.error("Erro ao carregar minhas solicitações:", error);
        }
    }

    document.getElementById("solicitacoes-list")?.addEventListener("click", async e => {
        const botao = e.target.closest(".btn-aceitar, .btn-recusar");
        if (!botao) return;

        const id = parseInt(botao.dataset.id);
        const route = botao.classList.contains("btn-aceitar") ? "aceitar" : "recusar";

        try {
            const response = await fetch(API_BASE + route, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id }),
                credentials: "same-origin"
            });
            const data = await response.json();
            if (data.sucesso) {
                alert(data.mensagem);
                carregarSolicitacoes();
                carregarNotificacoes();
            } else {
                alert("Erro: " + (data.erro || "Erro desconhecido"));
            }
        } catch (error) {
            alert("Erro de conexão.");
        }
    });

    document.addEventListener("click", async e => {
        const botao = e.target.closest(".btn-marcar-lida");
        if (!botao) return;

        const id = parseInt(botao.dataset.id);
        try {
            await fetch(API_BASE + "marcarLida", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id }),
                credentials: "same-origin"
            });
            carregarNotificacoes();
        } catch (error) {
            console.error("Erro ao marcar como lida:", error);
        }
    });

    carregarMinhasSolicitacoes();
    carregarSolicitacoes();
    carregarNotificacoes();
});