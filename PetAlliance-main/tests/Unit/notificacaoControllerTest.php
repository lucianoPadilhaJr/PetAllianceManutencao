<?php
use PHPUnit\Framework\TestCase;

class NotificacaoControllerTest extends TestCase
{
    public function testListar_DeveAdicionarSolicitacoesPendentesQuandoNotificacaoNaoExiste(): void
    {
        $controller = (new ReflectionClass(NotificacaoController::class))->newInstanceWithoutConstructor();

        $daoMock = $this->createMock(NotificacaoDAO::class);
        $daoMock->method('listarPorUsuario')->willReturn([]);
        $daoMock->method('naoLidas')->willReturn(1);

        $solicitacaoDaoMock = $this->createMock(SolicitacaoMatchDAO::class);
        $solicitacaoDaoMock->method('listarPorDono')->with(42)->willReturn([
            ['id' => 10, 'status' => 'pendente', 'pet_nome' => 'Rex', 'criado_em' => '2026-10-06 10:00:00']
        ]);

        $ref = new ReflectionClass($controller);
        $daoProp = $ref->getProperty('dao');
        $daoProp->setAccessible(true);
        $daoProp->setValue($controller, $daoMock);

        $solicitacaoProp = $ref->getProperty('solicitacaoDAO');
        $solicitacaoProp->setAccessible(true);
        $solicitacaoProp->setValue($controller, $solicitacaoDaoMock);

        ob_start();
        $controller->listar(42);
        $saida = ob_get_clean();

        $dados = json_decode($saida, true);

        $this->assertTrue($dados['sucesso']);
        $this->assertCount(1, $dados['matchRecebidas']);
        $this->assertSame('solicitacao_match', $dados['matchRecebidas'][0]['tipo']);
        $this->assertSame('Alguém quer um match com seu pet "Rex"', $dados['matchRecebidas'][0]['mensagem']);
    }
}
