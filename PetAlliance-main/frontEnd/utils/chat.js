const API_BASE = "/backEnd/chatApi.php?route=";
let conversaAtual = null;
let ultimoId = 0;
let polling = null;
let dadosConversaAtual = null;

document.addEventListener("DOMContentLoaded", () => {
    carregarConversas();
    if (window.SOLICITACAO_ID) {
        abrirConversaPorSolicitacao(window.SOLICITACAO_ID);
    }
});

async function carregarConversas() {
    try {
        const resp = await fetch(API_BASE + "listar_conversas&usuario_id=" + window.USUARIO_ID, { credentials: "same-origin" });
        const data = await resp.json();
        const container = document.getElementById("conversas-list");
        if (!data.sucesso || !data.conversas.length) {
            container.innerHTML = "<p style='color:#888;font-size:.9rem;'>Nenhuma conversa ainda.</p>";
            return;
        }
        let html = "";
        data.conversas.forEach(c => {
            const foto = c.outro_foto || "placeholder.webp";
            const ativo = conversaAtual && conversaAtual.solicitacao_id == c.solicitacao_id ? "active" : "";
            const convIdAttr = c.id ? `data-conversa-id="${c.id}"` : '';
            html += `<div class="conversa-item ${ativo}" data-solicitacao-id="${c.solicitacao_id}" ${convIdAttr}>
                <img src="/uploads/usuario/${foto}" alt="">
                <div class="conversa-info">
                    <strong>${c.outro_nome}</strong>
                    <small>${c.pet_nome}</small>
                </div>
            </div>`;
        });
        container.innerHTML = html;

        container.querySelectorAll(".conversa-item").forEach(el => {
            el.addEventListener("click", () => {
                const convId = el.dataset.conversaId || null;
                const solId = el.dataset.solicitacaoId;
                abrirConversa(convId, solId);
            });
        });
    } catch (e) {
        console.error("Erro ao carregar conversas:", e);
    }
}

async function abrirConversaPorSolicitacao(solicitacaoId) {
    try {
        const resp = await fetch(API_BASE + "dados_conversa&solicitacao_id=" + solicitacaoId, { credentials: "same-origin" });
        const data = await resp.json();
        if (!data.sucesso) return;
        dadosConversaAtual = data.dados;
        const convId = data.dados.conversa_id;
        if (convId) {
            abrirConversa(convId, solicitacaoId);
        } else {
            abrirConversa(null, solicitacaoId);
        }
    } catch (e) {
        console.error(e);
    }
}

async function abrirConversa(conversaId, solicitacaoId) {
    pararPolling();
    conversaAtual = { id: conversaId, solicitacao_id: solicitacaoId };
    ultimoId = 0;

    document.getElementById("chat-placeholder").style.display = "none";
    document.getElementById("chat-active").style.display = "flex";


    if (!dadosConversaAtual || dadosConversaAtual.id != solicitacaoId) {
        try {
            const resp = await fetch(API_BASE + "dados_conversa&solicitacao_id=" + solicitacaoId, { credentials: "same-origin" });
            const data = await resp.json();
            if (data.sucesso) dadosConversaAtual = data.dados;
        } catch (e) { console.error(e); }
    }

    carregarHeader();
    await carregarMensagens();
    carregarConversas();
    if (conversaId) iniciarPolling();
}

function carregarHeader() {
    const header = document.getElementById("chat-header");
    if (!dadosConversaAtual) return;
    const d = dadosConversaAtual;
    const usuarioId = parseInt(window.USUARIO_ID);
    const ehDono = d.dono_id == usuarioId;
    const nome = ehDono ? d.remetente_nome : d.dono_nome;
    const foto = ehDono ? d.remetente_foto : d.dono_foto;
    header.innerHTML = `<img src="/uploads/usuario/${foto || 'placeholder.webp'}" alt=""> ${nome} — ${d.pet_nome}`;
}

async function carregarMensagens() {
    if (!conversaAtual || !conversaAtual.id) {
        document.getElementById("chat-messages").innerHTML = '<p class="msg-nova-conversa">Nova conversa. Envie a primeira mensagem!</p>';
        return;
    }
    try {
        const resp = await fetch(API_BASE + "listar_mensagens&conversa_id=" + conversaAtual.id, { credentials: "same-origin" });
        const data = await resp.json();
        if (data.sucesso) {
            renderizarMensagens(data.mensagens);
            if (data.mensagens.length) {
                ultimoId = data.mensagens[data.mensagens.length - 1].id;
            }
        }
    } catch (e) {
        console.error(e);
    }
}

function renderizarMensagens(mensagens) {
    const container = document.getElementById("chat-messages");
    let html = "";
    mensagens.forEach(m => {
        const ehMinha = parseInt(m.remetente_id) === parseInt(window.USUARIO_ID);
        html += `<div class="msg ${ehMinha ? 'minha' : 'outra'}">
            ${m.conteudo}
            <small>${m.data_envio || ''}</small>
        </div>`;
    });
    container.innerHTML = html;
    container.scrollTop = container.scrollHeight;
}

function adicionarMensagemNaTela(m) {
    if (conversaAtual && m.conversa_id && conversaAtual.id != m.conversa_id) return;
    const container = document.getElementById("chat-messages");
    const ehMinha = parseInt(m.remetente_id) === parseInt(window.USUARIO_ID);
    const div = document.createElement("div");
    div.className = "msg " + (ehMinha ? "minha" : "outra");
    div.innerHTML = `${m.conteudo} <small>${m.data_envio || ''}</small>`;
    container.appendChild(div);
    container.scrollTop = container.scrollHeight;
}

function iniciarPolling() {
    pararPolling();
    polling = setInterval(buscarNovas, 2000);
}

function pararPolling() {
    if (polling) {
        clearInterval(polling);
        polling = null;
    }
}

async function buscarNovas() {
    if (!conversaAtual || !conversaAtual.id) return;
    try {
        const resp = await fetch(API_BASE + "novas_mensagens&conversa_id=" + conversaAtual.id + "&ultimo_id=" + ultimoId, { credentials: "same-origin" });
        const data = await resp.json();
        if (data.sucesso && data.mensagens.length) {
            const novas = data.mensagens.filter(m => parseInt(m.id) > parseInt(ultimoId));
            if (novas.length) {
                novas.forEach(m => adicionarMensagemNaTela(m));
                ultimoId = parseInt(novas[novas.length - 1].id);
            }
        }
    } catch (e) {
        console.error("Polling error:", e);
    }
}

document.getElementById("chat-send")?.addEventListener("click", enviarMensagem);
document.getElementById("chat-input")?.addEventListener("keydown", e => {
    if (e.key === "Enter" && !e.shiftKey) {
        e.preventDefault();
        enviarMensagem();
    }
});

/* === Sidebar toggle (mobile) === */
function initChatToggle() {
    var sidebar = document.getElementById("chat-sidebar");
    var btnFechar = document.getElementById("chat-toggle-sidebar");
    var btnAbrir = document.getElementById("chat-toggle-main");
    if (!sidebar) return;

    function isMobile() { return window.innerWidth <= 768; }

    if (btnFechar) {
        btnFechar.addEventListener("click", function() {
            sidebar.classList.add("oculta");
            if (btnAbrir) btnAbrir.style.display = "flex";
        });
    }

    if (btnAbrir) {
        btnAbrir.addEventListener("click", function() {
            sidebar.classList.remove("oculta");
            btnAbrir.style.display = "none";
        });
    }

    if (isMobile()) {
        sidebar.classList.add("oculta");
        if (btnAbrir) btnAbrir.style.display = "flex";
    }

    window.addEventListener("resize", function() {
        if (isMobile()) {
            if (!sidebar.classList.contains("oculta")) {
                sidebar.classList.add("oculta");
                if (btnAbrir) btnAbrir.style.display = "flex";
            }
        } else {
            sidebar.classList.remove("oculta");
            if (btnAbrir) btnAbrir.style.display = "none";
        }
    });
}

document.addEventListener("DOMContentLoaded", initChatToggle);

async function enviarMensagem() {
    const input = document.getElementById("chat-input");
    const conteudo = input.value.trim();
    if (!conteudo) return;

    if (!conversaAtual) return;

    input.value = "";
    input.disabled = true;
    document.getElementById("chat-send").disabled = true;

    pararPolling();

    try {
        const resp = await fetch(API_BASE + "enviar", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                solicitacao_id: parseInt(conversaAtual.solicitacao_id),
                remetente_id: parseInt(window.USUARIO_ID),
                conteudo: conteudo
            }),
            credentials: "same-origin"
        });
        const data = await resp.json();
        if (data.sucesso) {
            if (!conversaAtual.id) {
                conversaAtual.id = data.mensagem.conversa_id;
                carregarConversas();
                await carregarMensagens();
            } else if (data.mensagem.id > ultimoId) {
                adicionarMensagemNaTela(data.mensagem);
            }
            ultimoId = data.mensagem.id;
            iniciarPolling();
        } else {
            alert("Erro: " + (data.erro || "Erro ao enviar"));
        }
    } catch (e) {
        alert("Erro de conexão.");
    } finally {
        input.disabled = false;
        document.getElementById("chat-send").disabled = false;
        input.focus();
    }
}
