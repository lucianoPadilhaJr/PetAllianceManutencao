document.addEventListener("DOMContentLoaded", () => {
    const API_URL_PERFIL = window.API_URL_PERFIL || "/backEnd/usuario/perfil.php";
    const container = document.getElementById("usuario-container");

    if (!container) {
        console.error("Elemento #usuario-container não encontrado.");
        return;
    }

    // ============================================
    // Função Principal: Carregar Perfil do Usuário
    // ============================================
    async function carregarPerfil() {
        try {
            const response = await fetch(API_URL_PERFIL, { 
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error(`Erro ao buscar perfil: ${response.status}`);
            }

            const usuario = await response.json();

            if (!usuario) {
                throw new Error("Servidor retornou usuário vazio/null. Verifique se está autenticado.");
            }

            if (usuario.erro) {
                throw new Error(usuario.erro);
            }

            renderizarPerfil(usuario);
        } catch (error) {
            console.error("Erro ao carregar perfil:", error);
            exibirErro(error.message);
        }
    }

    // ============================================
    // Renderizar Perfil do Usuário na Página
    // ============================================
    function renderizarPerfil(usuario) {
        const fotoUsuario = (usuario.imagem || "placeholder.webp").toString().trim() || "placeholder.webp";
        let cpfFormatado = usuario.cpf || "Não informado";
        if (usuario.cpf && usuario.cpf.length === 11) {
            cpfFormatado = `${usuario.cpf.slice(0, 3)}.${usuario.cpf.slice(3, 6)}.${usuario.cpf.slice(6, 9)}-${usuario.cpf.slice(9)}`;
        }
        let cepFormatado = usuario.cep || "Não informado";
        if (usuario.cep && usuario.cep.length === 8) {
            cepFormatado = `${usuario.cep.slice(0, 5)}-${usuario.cep.slice(5)}`;
        }
        container.innerHTML = `
            <article class="usuario-card">
                <img 
                    src="/uploads/usuario/${fotoUsuario}" 
                    alt="Foto de ${usuario.nome || 'Usuário'}" 
                    class="usuario-image"
                    onerror="this.onerror=null;this.src='/uploads/usuario/placeholder.webp'"
                >
                <h3>${usuario.nome || "Sem nome"}</h3>
                <div class="usuario-info">
                    <p><strong>CPF:</strong> ${cpfFormatado}</p>
                    <p><strong>Email:</strong> ${usuario.email || "Não informado"}</p>
                    <p><strong>CEP:</strong> ${cepFormatado}</p>
                </div>
                <div class="usuario-actions">
                    <a href="/backEnd/usuario/alterarEmail.php" class="btn btn-primary" style="width:100%;display:block;box-sizing:border-box;">Alterar Email</a>
                </div>
            </article>
        `;
    }

    // ============================================
    // Exibir Mensagem de Erro
    // ============================================
    function exibirErro(mensagem) {
        container.innerHTML = `<p class="erro-mensagem">Não foi possível carregar o perfil: ${mensagem}</p>`;
    }

    // ============================================
    // Inicializar: Carregar perfil ao abrir página
    carregarPerfil();
});