<?php
use PHPUnit\Framework\TestCase;

class SolicitacaoMatchControllerTest extends TestCase
{
    public function testCriarConversaSeNecessaria_DeveCriarConversaQuandoNaoExistir(): void
    {
        $reflection = new ReflectionClass(SolicitacaoMatchController::class);
        $controller = $reflection->newInstanceWithoutConstructor();

        $conversaDaoMock = $this->createMock(ConversaDAO::class);
        $conversaDaoMock->expects($this->once())
            ->method('buscarPorSolicitacao')
            ->with(42)
            ->willReturn(null);
        $conversaDaoMock->expects($this->once())
            ->method('criar')
            ->with(42)
            ->willReturn(99);

        $ref = new ReflectionClass($controller);
        $prop = $ref->getProperty('conversaDAO');
        $prop->setAccessible(true);
        $prop->setValue($controller, $conversaDaoMock);

        $this->assertSame(99, $controller->criarConversaSeNecessaria(42));
    }
}
