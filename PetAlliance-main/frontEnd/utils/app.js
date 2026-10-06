function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}

document.addEventListener("DOMContentLoaded", async () => {
    const API_URL_ANIMAIS = window.API_URL_ANIMAIS || "/backEnd/home.php?route=animais";
    const container = document.getElementById("animais-container");
    const paginationEl = document.getElementById("animais-pagination");
    const buscaInput = document.getElementById("busca-input");
    let matchStatusMap = {};
    let paginaAtual = 1;
    let totalPaginas = 1;

    if (!container) {
        console.error("Elemento #animais-container não encontrado.");
        return;
    }

    const filtroCheckboxes = document.querySelectorAll(".filtro-check");
    const btnFiltros = document.getElementById("btn-filtros");
    const filtrosDropdown = document.getElementById("filtros-dropdown");
    const btnLimparFiltros = document.getElementById("btn-limpar-filtros");

    let debounceTimer;

    function getFiltrosAtivos() {
        const filtros = {};
        filtroCheckboxes.forEach(cb => {
            if (cb.checked) {
                const chave = cb.dataset.filtro;
                if (!filtros[chave]) filtros[chave] = [];
                filtros[chave].push(cb.value);
            }
        });
        return filtros;
    }

    function montarUrlBusca(pagina) {
        const termo = buscaInput ? buscaInput.value.trim() : "";
        const filtros = getFiltrosAtivos();
        const params = new URLSearchParams();
        params.set("route", "buscar_animais");
        if (termo) params.set("termo", termo);
        params.set("pagina", pagina || 1);
        Object.keys(filtros).forEach(chave => {
            params.set(chave, filtros[chave].join(","));
        });
        return "/backEnd/home.php?" + params.toString();
    }

    function temFiltrosAtivos() {
        const termo = buscaInput ? buscaInput.value.trim() : "";
        const filtros = getFiltrosAtivos();
        return termo !== "" || Object.keys(filtros).length > 0;
    }

    let paginaBusca = 1;

    function executarBusca(pagina) {
        clearTimeout(debounceTimer);
        if (pagina !== undefined) paginaBusca = pagina;
        if (!temFiltrosAtivos()) {
            paginaBusca = 1;
            carregarAnimais(1);
            return;
        }
        debounceTimer = setTimeout(async () => {
            const url = montarUrlBusca(paginaBusca);
            try {
                const response = await fetch(url, { credentials: 'same-origin' });
                if (!response.ok) throw new Error(`Erro na busca: ${response.status}`);
                const data = await response.json();
                var animais = Array.isArray(data) ? data : (data.animais || []);
                var totalPag = Array.isArray(data) ? 1 : (data.totalPaginas || 1);
                renderizarAnimais(animais, paginaBusca, totalPag);
            } catch (error) {
                console.error("Erro ao buscar animais:", error);
            }
        }, pagina !== undefined ? 0 : 300);
    }

    if (buscaInput) {
        buscaInput.addEventListener("input", () => executarBusca());
    }

    filtroCheckboxes.forEach(cb => {
        cb.addEventListener("change", () => executarBusca());
    });

    if (btnFiltros && filtrosDropdown) {
        btnFiltros.addEventListener("click", () => {
            const aberto = filtrosDropdown.style.display === "block";
            filtrosDropdown.style.display = aberto ? "none" : "block";
        });
        document.addEventListener("click", (e) => {
            if (!btnFiltros.contains(e.target) && !filtrosDropdown.contains(e.target)) {
                filtrosDropdown.style.display = "none";
            }
        });
    }

    if (btnLimparFiltros) {
        btnLimparFiltros.addEventListener("click", () => {
            filtroCheckboxes.forEach(cb => cb.checked = false);
            if (buscaInput) buscaInput.value = "";
            filtrosDropdown.style.display = "none";
            carregarAnimais(1);
        });
    }

    function getCarrosselHtml(animal) {
        const fotos = (animal.fotos && animal.fotos.length > 0) ? animal.fotos : ["placeholder.webp"];
        const fotosJson = JSON.stringify(fotos).replace(/</g, "\\u003C");
        const imgs = fotos.map(f =>
            `<img src="/uploads/animais/${f}" alt="Foto" onerror="this.onerror=null;this.src='/uploads/animais/placeholder.webp'">`
        ).join('');
        return `<div class="animal-fotos-carrossel" data-fotos='${fotosJson}'><div class="carrossel-track">${imgs}</div></div>`;
    }

    function carregarCarrosselEventos(card) {
        const carrossel = card.querySelector('.animal-fotos-carrossel');
        if (!carrossel) return;
        const fotos = (() => { try { return JSON.parse(carrossel.dataset.fotos); } catch(e) { return []; } })();
        if (fotos.length <= 1) return;

        let intervalo = null;
        let idx = 0;

        carrossel.addEventListener('mouseenter', function() {
            const track = this.querySelector('.carrossel-track');
            if (!track) return;
            intervalo = setInterval(() => {
                idx = (idx + 1) % fotos.length;
                track.style.transform = 'translateX(-' + (idx * 100) + '%)';
            }, 1800);
        });

        carrossel.addEventListener('mouseleave', function() {
            clearInterval(intervalo);
            intervalo = null;
            idx = 0;
            const track = this.querySelector('.carrossel-track');
            if (track) track.style.transform = 'translateX(0)';
        });
    }

    function renderizarAnimais(animais, pagina, totalPag) {
        paginaAtual = pagina;
        totalPaginas = totalPag;

        container.innerHTML = "";

        if (animais.length === 0) {
            container.innerHTML = "<p style='grid-column:1/-1;text-align:center;padding:2rem;color:#666;'>Nenhum animal encontrado.</p>";
            renderizarPaginacao();
            return;
        }

        animais.forEach(animal => {
            const fotoSrc = (animal.fotos && animal.fotos.length > 0)
                ? "/uploads/animais/" + animal.fotos[0]
                : "/uploads/animais/placeholder.webp";

            var matchHtml = "";
            if (animal.dono_id != window.USUARIO_ID) {
                if (matchStatusMap[animal.id]) {
                    if (matchStatusMap[animal.id].status === 'aceito') {
                        matchHtml = '<a href="/backEnd/chat.php?solicitacao_id=' + matchStatusMap[animal.id].solicitacao_id + '" class="btn btn-primary" style="width:100%;font-size:0.8rem;padding:0.4rem 0;">Iniciar Chat</a>';
                    } else if (matchStatusMap[animal.id].status === 'pendente') {
                        matchHtml = '<button type="button" class="btn" style="width:100%;font-size:0.8rem;padding:0.4rem 0;background:#ccc;color:#666;cursor:default;" disabled>Pendente</button>';
                    } else {
                        matchHtml = '<button type="button" class="btn btn-primary match-btn" data-animal-id="' + animal.id + '" style="width:100%;font-size:0.8rem;padding:0.4rem 0;">Enviar Match</button>';
                    }
                } else {
                    matchHtml = '<button type="button" class="btn btn-primary match-btn" data-animal-id="' + animal.id + '" style="width:100%;font-size:0.8rem;padding:0.4rem 0;">Enviar Match</button>';
                }
            }

            var adminBtn = window.EH_ADMIN
                ? '<button type="button" class="excluir-btn" data-animal-id="' + animal.id + '" style="width:100%;margin-top:0.25rem;font-size:0.75rem;padding:0.3rem 0;background:transparent;border:1px solid #d32f2f;border-radius:0.5rem;color:#d32f2f;cursor:pointer;">Excluir</button>'
                : "";

            var badges = "";
            if (animal.raca) badges += '<span class="animal-card-badge">' + animal.raca + '</span>';
            if (animal.porte) badges += '<span class="animal-card-badge">' + animal.porte + '</span>';
            if (animal.sexo) badges += '<span class="animal-card-badge">' + animal.sexo + '</span>';

            var favoritarText = animal.favoritado === true ? "Remover dos Favoritos" : "Favoritar";

            const card = document.createElement("article");
            card.className = "animal-card";
            card.innerHTML =
                getCarrosselHtml(animal) +
                '<div class="animal-card-body">' +
                    '<h3>' + (animal.nome || "Sem nome") + '</h3>' +
                    '<p class="animal-card-desc">' + (animal.descricao || "Não informada") + '</p>' +
                    '<div class="animal-card-badges">' + badges + '</div>' +
                    '<div class="animal-card-actions">' +
                        '<button type="button" class="detalhes-btn btn btn-ghost" data-animal-id="' + animal.id + '" style="width:100%;font-size:0.8rem;padding:0.25rem 0;">Ver Detalhes</button>' +
                        matchHtml +
                        adminBtn +
                    '</div>' +
                '</div>' +
                '<button type="button" class="favoritar-btn" data-animal-id="' + animal.id + '" style="position:absolute;top:0.4rem;right:0.4rem;background:rgba(255,255,255,0.85);border:none;border-radius:50%;width:2rem;height:2rem;font-size:1.1rem;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background 0.2s;color:' + (animal.favoritado === true ? '#e74c3c' : '#999') + ';" title="' + favoritarText + '">' +
                    (animal.favoritado === true ? '\u2764' : '\u2661') +
                '</button>';
            container.appendChild(card);
            carregarCarrosselEventos(card);
        });

        renderizarPaginacao();
    }

    function renderizarPaginacao() {
        if (!paginationEl) return;
        if (totalPaginas <= 1) {
            paginationEl.innerHTML = "";
            return;
        }

        var html = '<button class="pagination-btn" data-page="' + (paginaAtual - 1) + '"' + (paginaAtual <= 1 ? ' disabled' : '') + '>&laquo;</button>';
        for (var i = 1; i <= totalPaginas; i++) {
            html += '<button class="pagination-btn' + (i === paginaAtual ? ' active' : '') + '" data-page="' + i + '">' + i + '</button>';
        }
        html += '<button class="pagination-btn" data-page="' + (paginaAtual + 1) + '"' + (paginaAtual >= totalPaginas ? ' disabled' : '') + '>&raquo;</button>';
        paginationEl.innerHTML = html;
    }

    if (paginationEl) {
        paginationEl.addEventListener("click", function(e) {
            var btn = e.target.closest(".pagination-btn");
            if (!btn || btn.disabled) return;
            var page = parseInt(btn.dataset.page);
            if (isNaN(page) || page < 1 || page > totalPaginas) return;
            if (temFiltrosAtivos()) {
                executarBusca(page);
            } else {
                carregarAnimais(page);
            }
        });
    }

    async function carregarMatchStatus() {
        try {
            const response = await fetch("/backEnd/match.php?route=minhas_solicitacoes", { credentials: "same-origin" });
            const data = await response.json();
            if (data.sucesso && data.solicitacoes) {
                data.solicitacoes.forEach(s => {
                    matchStatusMap[s.pet_id] = { status: s.status, solicitacao_id: s.id };
                });
            }
        } catch (error) {
            console.error("Erro ao carregar status dos matchs:", error);
        }
    }

    async function carregarAnimais(pagina) {
        pagina = pagina || 1;
        try {
            var url = API_URL_ANIMAIS;
            if (url.indexOf("pagina=") === -1) {
                url += (url.indexOf("?") === -1 ? "?" : "&") + "pagina=" + pagina;
            } else {
                url = url.replace(/pagina=\d+/, "pagina=" + pagina);
            }
            const response = await fetch(url, { credentials: 'same-origin' });
            if (!response.ok) {
                throw new Error('Erro ao buscar lista de animais: ' + response.status);
            }

            const data = await response.json();

            if (data.erro) {
                throw new Error(data.erro);
            }

            // Suporta formato paginado {animais, totalPaginas} e array simples (legado)
            var animais = Array.isArray(data) ? data : (data.animais || []);
            var totalPag = Array.isArray(data) ? 1 : (data.totalPaginas || 1);

            renderizarAnimais(animais, pagina, totalPag);
        } catch (error) {
            console.error("Erro ao carregar animais:", error);
            container.innerHTML = "<p style='grid-column:1/-1;text-align:center;padding:2rem;color:#666;'>Não foi possível carregar os animais.</p>";
        }
    }

    document.addEventListener("click", async (event) => {
        const target = event.target;

        const favBtn = target instanceof Element ? target.closest(".favoritar-btn") : null;
        if (favBtn) {
            const animalId = favBtn.dataset.animalId;
            if (animalId) {
                const estaFavoritado = favBtn.textContent.trim() === '\u2764';
                favBtn.textContent = estaFavoritado ? '\u2661' : '\u2764';
                favBtn.style.color = estaFavoritado ? '#999' : '#e74c3c';
                favBtn.title = estaFavoritado ? "Favoritar" : "Remover dos Favoritos";
                try {
                    fetch('/backEnd/home.php?route=favoritar_animal&idAnimal=' + animalId);
                } catch (error) {
                    console.error("Erro ao favoritar animal:", error);
                }
            }
            return;
        }

        const matchBtn = target instanceof Element ? target.closest(".match-btn") : null;
        if (matchBtn) {
            const animalId = matchBtn.dataset.animalId;
            if (!confirm("Enviar solicitação de match para este animal?")) return;

            try {
                const response = await fetch("/backEnd/match.php?route=enviar", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ pet_id: animalId, remetente_id: window.USUARIO_ID }),
                    credentials: "same-origin"
                });
                const data = await response.json();
                if (data.sucesso) {
                    alert(data.mensagem);
                    matchStatusMap[animalId] = { status: "pendente", solicitacao_id: data.solicitacao_id };
                    matchBtn.textContent = "Pendente";
                    matchBtn.disabled = true;
                    matchBtn.style.background = "#ccc";
                    matchBtn.style.color = "#666";
                    matchBtn.style.cursor = "default";
                } else {
                    alert("Erro: " + (data.erro || "Erro desconhecido"));
                }
            } catch (error) {
                alert("Erro de conexão.");
            }
        }
    });

    container.addEventListener("click", (event) => {
        const target = event.target;

        if (!(target instanceof Element)) {
            return;
        }

        const detalhesBtn = target.closest(".detalhes-btn");
        if (detalhesBtn) {
            carregarDetalhesAnimal(detalhesBtn.dataset.animalId);
            return;
        }

        const excluirBtn = target.closest(".excluir-btn");
        if (excluirBtn) {
            excluirAnimal(excluirBtn.dataset.animalId);
            return;
        }
    });

    function getModalElement() {
        let modal = document.getElementById("animais-modal");
        if (!modal) {
            modal = document.createElement("div");
            modal.id = "animais-modal";
            document.body.appendChild(modal);
        }
        return modal;
    }

    function closeModal(modal) {
        modal.classList.remove("animal-modal");
        modal.style.display = "none";
        document.body.style.overflow = "auto";
    }

    function openModal(contentHtml) {
        const modal = getModalElement();
        const body = document.body;

        body.style.overflow = "hidden";
        modal.className = "animal-modal";
        modal.style.cssText = "";
        modal.innerHTML = contentHtml;

        const closeButton = modal.querySelector(".animal-modal-close");
        if (closeButton) {
            closeButton.onclick = () => closeModal(modal);
        }

        modal.onclick = (event) => {
            if (event.target === modal) {
                closeModal(modal);
            }
        };
    }

    async function carregarDetalhesAnimal(animalId) {
        try {
            const response = await fetch(`/backEnd/home.php?route=detalhes_animal&id=${animalId}`, { credentials: 'same-origin' });
            if (!response.ok) {
                throw new Error(`Erro ao buscar detalhes do animal: ${response.status}`);
            }

            const data = await response.json();
            if (!data || data.erro) {
                throw new Error(data?.erro || "Detalhes do animal não foram encontrados.");
            }

            const animal = Array.isArray(data) ? data[0] : data;
            if (!animal) {
                throw new Error("Animal inválido recebido do servidor.");
            }

            const fotos = (animal.fotos && animal.fotos.length > 0) ? animal.fotos : ["placeholder.webp"];
            const fotosJson = encodeURIComponent(JSON.stringify(fotos));
            const imgsHtml = fotos.map(f =>
                `<img src="/uploads/animais/${f}" alt="Foto" onerror="this.onerror=null;this.src='/uploads/animais/placeholder.webp'">`
            ).join('');

            var setasGaleria = fotos.length > 1
                ? '<button type="button" class="gallery-prev">&#10094;</button><button type="button" class="gallery-next">&#10095;</button>'
                : '';

            var galeriaHtml = '<div class="animal-modal-gallery" data-fotos=\'' + fotosJson + '\'>' +
                '<div class="gallery-track">' + imgsHtml + '</div>' +
                setasGaleria +
            '</div>';

            var matchHtml = '';
            if (animal.dono_id != window.USUARIO_ID) {
                var mStatus = matchStatusMap[animal.id];
                if (mStatus) {
                    if (mStatus.status === 'aceito') {
                        matchHtml = '<a href="/backEnd/chat.php?solicitacao_id=' + mStatus.solicitacao_id + '" class="btn btn-primary" style="flex:1;min-width:100px;font-size:0.8rem;padding:0.45rem 0.75rem;border-radius:0.5rem;border:none;cursor:pointer;font-weight:600;text-decoration:none;text-align:center;">Iniciar Chat</a>';
                    } else if (mStatus.status === 'pendente') {
                        matchHtml = '<button type="button" class="btn" style="flex:1;min-width:100px;font-size:0.8rem;padding:0.45rem 0.75rem;border-radius:0.5rem;background:#ccc;color:#666;cursor:default;" disabled>Pendente</button>';
                    } else {
                        matchHtml = '<button type="button" class="match-btn" data-animal-id="' + animal.id + '" style="flex:1;min-width:100px;font-size:0.8rem;padding:0.45rem 0.75rem;border-radius:0.5rem;border:none;cursor:pointer;font-weight:600;background:var(--primary);color:var(--white);">Enviar Match</button>';
                    }
                } else {
                    matchHtml = '<button type="button" class="match-btn" data-animal-id="' + animal.id + '" style="flex:1;min-width:100px;font-size:0.8rem;padding:0.45rem 0.75rem;border-radius:0.5rem;border:none;cursor:pointer;font-weight:600;background:var(--primary);color:var(--white);">Enviar Match</button>';
                }
            }

            var coracao = animal.favoritado === true ? '\u2764' : '\u2661';
            var favoritarHeartHtml = animal.dono_id != window.USUARIO_ID
                ? '<button type="button" class="favoritar-btn" data-animal-id="' + animal.id + '" style="width:2.2rem;height:2.2rem;border:none;border-radius:50%;background:#f5f5f5;color:' + (animal.favoritado === true ? '#e74c3c' : '#999') + ';font-size:1.2rem;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:background 0.2s;" title="' + (animal.favoritado === true ? 'Remover dos Favoritos' : 'Favoritar') + '">' + coracao + '</button>'
                : '';

            var reportHtml = '<button type="button" onclick="window.location.href=\'/backEnd/denuncias/cadastrarDenuncia.php?tipo=animal&id=' + animal.id + '\'" style="flex:1;min-width:100px;font-size:0.8rem;padding:0.45rem 0.75rem;border-radius:0.5rem;border:1px solid #d32f2f;background:transparent;color:#d32f2f;cursor:pointer;font-weight:600;">Reportar</button>';

            openModal(
                '<div class="animal-modal-content">' +
                    '<div class="animal-modal-header">' +
                        '<h3>' + (animal.nome || "Sem nome") + '</h3>' +
                        '<div style="display:flex;align-items:center;gap:0.5rem;">' +
                            favoritarHeartHtml +
                            '<button type="button" class="animal-modal-close">✕</button>' +
                        '</div>' +
                    '</div>' +
                    '<div class="animal-modal-body">' +
                        galeriaHtml +
                        '<div class="animal-modal-info">' +
                            '<p><strong>Raça:</strong> ' + (animal.raca || "Não informada") + '</p>' +
                            '<p><strong>Sexo:</strong> ' + (animal.sexo || "Não informado") + '</p>' +
                            '<p><strong>Tipo:</strong> ' + (animal.tipo || "Não informado") + '</p>' +
                            '<p><strong>Porte:</strong> ' + (animal.porte || "Não informado") + '</p>' +
                            '<p><strong>Cor:</strong> ' + (animal.cor || "Não informada") + '</p>' +
                            '<p><strong>Data de Nasc.:</strong> ' + (animal.data_nascimento || "Não informada") + '</p>' +
                            '<p><strong>Peso:</strong> ' + (animal.peso || "Não informado") + ' Kg</p>' +
                            '<p><strong>Vacinado:</strong> ' + (animal.vacinado == 1 ? "Sim" : "Não") + '</p>' +
                            '<p><strong>Certificado:</strong> ' + (animal.certificado == 1 ? "Sim" : "Não") + '</p>' +
                            '<p style="margin-top:0.5rem;font-size:0.85rem;color:var(--text-muted);">' + (animal.descricao || "") + '</p>' +
                            '<div class="animal-modal-actions">' +
                                matchHtml +
                                reportHtml +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>'
            );

            setTimeout(function() {
                var modal = document.getElementById('animais-modal');
                if (!modal) return;
                var galeria = modal.querySelector('.animal-modal-gallery');
                if (!galeria) return;
                var track = galeria.querySelector('.gallery-track');
                if (!track) return;
                var imgs = track.querySelectorAll('img');
                if (imgs.length <= 1) return;

                var prevBtn = galeria.querySelector('.gallery-prev');
                var nextBtn = galeria.querySelector('.gallery-next');
                var idx = 0;

                function atualizarGaleria() {
                    track.style.transform = 'translateX(-' + (idx * 100) + '%)';
                }

                if (nextBtn) {
                    nextBtn.addEventListener('click', function() {
                        idx = (idx + 1) % imgs.length;
                        atualizarGaleria();
                    });
                }
                if (prevBtn) {
                    prevBtn.addEventListener('click', function() {
                        idx = (idx - 1 + imgs.length) % imgs.length;
                        atualizarGaleria();
                    });
                }
            }, 0);
        } catch (error) {
            console.error("Erro ao carregar detalhes do animal:", error);
        }
    }

    async function excluirAnimal(id) {
        if (!confirm("Deseja realmente excluir este animal?")) {
            return;
        }

        try {
            const response = await fetch(`/backEnd/home.php?route=excluir_animal&id=${id}`, {
                method: "DELETE",
                credentials: "same-origin"
            });

            const data = await response.json();

            if (data.sucesso) {
                alert("Animal excluído com sucesso!");
                carregarAnimais(paginaAtual);
            } else {
                alert(data.erro || "Erro ao excluir o animal.");
            }

        } catch (error) {
            console.error(error);
            alert("Erro ao excluir o animal.");
        }
    }

    await carregarMatchStatus();
    carregarAnimais(1);
});