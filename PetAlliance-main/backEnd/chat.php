<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: /backEnd/usuario/loginUsuario.php');
    exit;
}

$usuarioId = $_SESSION['usuario_id'];
$solicitacaoId = $_GET['solicitacao_id'] ?? null;

require __DIR__ . '/views/navBar.php';
?>
<link rel="stylesheet" href="/frontEnd/style/chat.css">
<main class="main-conteudo">
    <div class="chat-layout">
        <aside class="chat-sidebar" id="chat-sidebar">
            <div class="chat-sidebar-header">
                <h2>Conversas</h2>
                <button type="button" class="chat-toggle-sidebar" id="chat-toggle-sidebar" title="Fechar">âœ•</button>
            </div>
            <div id="conversas-list"></div>
        </aside>
        <button type="button" class="chat-toggle-main" id="chat-toggle-main" title="Abrir conversas">â˜°</button>
        <section class="chat-main">
            <div id="chat-placeholder" class="chat-placeholder">
                <p>Selecione uma conversa</p>
            </div>
            <div id="chat-active" class="chat-active" style="display:none;">
                <div class="chat-header" id="chat-header"></div>
                <div class="chat-messages" id="chat-messages"></div>
                <div class="chat-input-area">
                    <textarea id="chat-input" rows="2" placeholder="Digite sua mensagem..."></textarea>
                    <button id="chat-send">Enviar</button>
                </div>
            </div>
        </section>
    </div>
</main>
<script>
    window.USUARIO_ID = <?= (int) $usuarioId ?>;
    window.SOLICITACAO_ID = <?= $solicitacaoId ? json_encode($solicitacaoId) : 'null' ?>;
</script>
<script src="/frontEnd/utils/chat.js"></script>
<?php require __DIR__ . '/../frontEnd/views/footer.html'; ?>
