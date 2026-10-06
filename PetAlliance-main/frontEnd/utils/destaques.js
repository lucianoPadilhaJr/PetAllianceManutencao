document.addEventListener("DOMContentLoaded", () => {
    let matchStatusMap = {};

    async function carregarMatchStatus() {
        try {
            const resp = await fetch("/backEnd/match.php?route=minhas_solicitacoes", { credentials: "same-origin" });
            const data = await resp.json();
            if (data.sucesso && data.solicitacoes) {
                data.solicitacoes.forEach(s => {
                    matchStatusMap[s.pet_id] = { status: s.status, solicitacao_id: s.id };
                });
            }
        } catch (e) {
            console.error("Erro ao carregar status dos matchs:", e);
        }
    }

    function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}

async function carregarDestaques() {
        try {
            const resp = await fetch('/backEnd/home.php?route=animais_destaque');
            const animais = await resp.json();
            const container = document.getElementById('destaques-container');
            if (!animais || animais.length === 0) {
                container.innerHTML = '<div style="min-width:100%;text-align:center;color:#999;padding:1rem;">Nenhum animal em destaque no momento.</div>';
                return;
            }
            var cardsHtml = animais.map(a => {
                const foto = a.foto_pet || 'placeholder.webp';
                return '<div class="destaque-card" onclick="destaquesVerDetalhes(' + a.id + ')">' +
                    '<img src="/uploads/animais/' + foto + '" alt="' + (a.nome || '') + '">' +
                    '<div class="destaque-card-body">' +
                        '<strong>' + (a.nome || '') + '</strong>' +
                        '<p>' + (a.raca || a.tipo || '') + '</p>' +
                    '</div>' +
                '</div>';
            }).join('');
            container.innerHTML = cardsHtml + cardsHtml;
        } catch (e) {
            const container = document.getElementById('destaques-container');
            if (container) {
                container.innerHTML = '<div style="min-width:100%;text-align:center;color:#999;padding:1rem;">Erro ao carregar destaques.</div>';
            }
        }
    }

    window.destaquesVerDetalhes = async function(animalId) {
        try {
            const resp = await fetch('/backEnd/home.php?route=detalhes_animal&id=' + animalId);
            const data = await resp.json();
            const animal = Array.isArray(data) ? data[0] : data;
            if (!animal) return;

            const fotos = (animal.fotos && animal.fotos.length > 0) ? animal.fotos : ["placeholder.webp"];
            const donoId = animal.dono_id;
            const isOwner = donoId == window.USUARIO_ID;

            var coracao = animal.favoritado ? '\u2764' : '\u2661';
            var favoritarHtml = !isOwner
                ? '<button type="button" class="favoritar-btn" data-animal-id="' + animal.id + '" style="width:2.2rem;height:2.2rem;border:none;border-radius:50%;background:#f5f5f5;color:' + (animal.favoritado ? '#e74c3c' : '#999') + ';font-size:1.2rem;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:background 0.2s;" title="' + (animal.favoritado ? 'Remover dos Favoritos' : 'Favoritar') + '">' + coracao + '</button>'
                : '';

            let matchHtml = '';
            if (!isOwner) {
                const matchInfo = matchStatusMap[animal.id];
                if (matchInfo) {
                    if (matchInfo.status === 'aceito') {
                        matchHtml = '<a href="/backEnd/chat.php?solicitacao_id=' + matchInfo.solicitacao_id + '" class="btn-chat-home">Iniciar Chat</a>';
                    } else if (matchInfo.status === 'pendente') {
                        matchHtml = '<button type="button" class="match-btn" disabled>Pendente</button>';
                    } else {
                        matchHtml = '<button type="button" class="match-btn" data-animal-id="' + animal.id + '">Enviar Match</button>';
                    }
                } else {
                    matchHtml = '<button type="button" class="match-btn" data-animal-id="' + animal.id + '">Enviar Match</button>';
                }
            }

            const modal = document.getElementById('animais-modal') || (() => {
                const el = document.createElement('div');
                el.id = 'animais-modal';
                document.body.appendChild(el);
                return el;
            })();

            document.body.style.overflow = 'hidden';
            modal.className = 'animal-modal';
            modal.style.cssText = '';

            var fotosJson = JSON.stringify(fotos).replace(/</g, '\\u003C');
            var setasGaleria = fotos.length > 1
                ? '<button type="button" class="gallery-prev">&#10094;</button><button type="button" class="gallery-next">&#10095;</button>'
                : '';

            var galeriaHtml = '<div class="animal-modal-gallery" data-fotos=\'' + fotosJson + '\'>' +
                '<div class="gallery-track">' +
                    fotos.map(function(f) {
                        return '<img src="/uploads/animais/' + f + '" alt="Foto" onerror="this.onerror=null;this.src=\'/uploads/animais/placeholder.webp\'">';
                    }).join('') +
                '</div>' +
                setasGaleria +
            '</div>';

            modal.innerHTML =
                '<div class="animal-modal-content">' +
                    '<div class="animal-modal-header">' +
                        '<h3>' + (animal.nome || 'Sem nome') + '</h3>' +
                        '<div style="display:flex;align-items:center;gap:0.5rem;">' +
                            favoritarHtml +
                            '<button type="button" class="animal-modal-close">✕</button>' +
                        '</div>' +
                    '</div>' +
                    '<div class="animal-modal-body">' +
                        galeriaHtml +
                        '<div class="animal-modal-info">' +
                            '<p><strong>Raça:</strong> ' + (animal.raca || 'Nao informada') + '</p>' +
                            '<p><strong>Sexo:</strong> ' + (animal.sexo || 'Nao informado') + '</p>' +
                            '<p><strong>Tipo:</strong> ' + (animal.tipo || 'Nao informado') + '</p>' +
                            '<p><strong>Porte:</strong> ' + (animal.porte || 'Nao informado') + '</p>' +
                            '<p><strong>Cor:</strong> ' + (animal.cor || 'Nao informada') + '</p>' +
                            '<p><strong>Data de Nasc.:</strong> ' + (animal.data_nascimento || 'Nao informada') + '</p>' +
                            '<p><strong>Peso:</strong> ' + (animal.peso || 'Nao informado') + ' Kg</p>' +
                            '<p><strong>Vacinado:</strong> ' + (animal.vacinado == 1 ? 'Sim' : 'Nao') + '</p>' +
                            '<p><strong>Certificado:</strong> ' + (animal.certificado == 1 ? 'Sim' : 'Nao') + '</p>' +
                            '<p style="margin-top:0.5rem;font-size:0.85rem;color:#666;">' + (animal.descricao || '') + '</p>' +
                            '<div class="animal-modal-actions">' +
                                matchHtml +
                                '<button type="button" onclick="window.location.href=\'/backEnd/denuncias/cadastrarDenuncia.php?tipo=animal&id=' + animal.id + '\'" style="flex:1;min-width:100px;font-size:0.8rem;padding:0.45rem 0.75rem;border-radius:0.5rem;border:1px solid #d32f2f;background:transparent;color:#d32f2f;cursor:pointer;font-weight:600;">Reportar</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>';

            modal.onclick = (e) => {
                if (e.target === modal) {
                    modal.className = '';
                    modal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
            };

            var closeBtn = modal.querySelector('.animal-modal-close');
            if (closeBtn) {
                closeBtn.onclick = function() {
                    modal.className = '';
                    modal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                };
            }

            setTimeout(function() {
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
        } catch (e) {
            console.error(e);
        }
    };

    carregarMatchStatus();
    carregarDestaques();
});
