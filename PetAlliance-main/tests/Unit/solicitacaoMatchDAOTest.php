<?php
use PHPUnit\Framework\TestCase;

class solicitacaoMatchDAOTest extends TestCase
{
    private SolicitacaoMatchDAO $dao;

    protected function setUp(): void
    {
        $this->dao = new SolicitacaoMatchDAO();
    }

    // ----------------------------------------------------------------
    //  criar()
    // ----------------------------------------------------------------

    public function testCriar_DeveInserirSolicitacaoComSucesso(): void
    {
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->method('prepare')->willReturn($stmtMock);
        $pdoMock->method('lastInsertId')->willReturn('1');

        $dao = $this->createDaoWithMockPdo($pdoMock);

        $solicitacao = new SolicitacaoMatch(1, 2);
        $resultado = $dao->criar($solicitacao);

        $this->assertNotNull($resultado);
        $this->assertInstanceOf(SolicitacaoMatch::class, $resultado);
        $this->assertEquals(1, $resultado->getId());
    }

    // ----------------------------------------------------------------
    //  buscar()
    // ----------------------------------------------------------------

    public function testBuscar_DeveRetornarDadosDaSolicitacao(): void
    {
        $dados = [
            'id' => 1,
            'pet_id' => 1,
            'remetente_id' => 2,
            'status' => 'pendente',
            'dono_id' => 1,
            'pet_nome' => 'Rex',
            'criado_em' => '2026-07-08 12:00:00',
        ];

        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('fetch')->willReturn($dados);

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->method('prepare')->willReturn($stmtMock);

        $dao = $this->createDaoWithMockPdo($pdoMock);
        $resultado = $dao->buscar(1);

        $this->assertIsArray($resultado);
        $this->assertEquals(1, $resultado['pet_id']);
        $this->assertEquals('pendente', $resultado['status']);
        $this->assertEquals('Rex', $resultado['pet_nome']);
    }

    public function testBuscar_DeveRetornarNullQuandoNaoExiste(): void
    {
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('fetch')->willReturn(false);

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->method('prepare')->willReturn($stmtMock);

        $dao = $this->createDaoWithMockPdo($pdoMock);
        $resultado = $dao->buscar(999);

        $this->assertNull($resultado);
    }

    // ----------------------------------------------------------------
    //  verificarExistente()
    // ----------------------------------------------------------------

    public function testVerificarExistente_DeveRetornarTrueQuandoSolicitacaoExiste(): void
    {
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('fetch')->willReturn(['id' => 1]);

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->method('prepare')->willReturn($stmtMock);

        $dao = $this->createDaoWithMockPdo($pdoMock);
        $resultado = $dao->verificarExistente(1, 2);

        $this->assertTrue($resultado);
    }

    public function testVerificarExistente_DeveRetornarFalseQuandoSolicitacaoNaoExiste(): void
    {
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('fetch')->willReturn(false);

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->method('prepare')->willReturn($stmtMock);

        $dao = $this->createDaoWithMockPdo($pdoMock);
        $resultado = $dao->verificarExistente(1, 2);

        $this->assertFalse($resultado);
    }

    // ----------------------------------------------------------------
    //  atualizarStatus()
    // ----------------------------------------------------------------

    public function testAtualizarStatus_DeveAlterarParaAceito(): void
    {
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->method('prepare')->willReturn($stmtMock);

        $dao = $this->createDaoWithMockPdo($pdoMock);

        $dao->atualizarStatus(1, 'aceito');
        $this->assertTrue(true);
    }

    public function testAtualizarStatus_DeveAlterarParaRecusado(): void
    {
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->method('prepare')->willReturn($stmtMock);

        $dao = $this->createDaoWithMockPdo($pdoMock);

        $dao->atualizarStatus(1, 'recusado');
        $this->assertTrue(true);
    }

    // ----------------------------------------------------------------
    //  listarPorDono()
    // ----------------------------------------------------------------

    public function testListarPorDono_DeveRetornarArrayDeSolicitacoes(): void
    {
        $dados = [
            ['id' => 1, 'pet_id' => 1, 'status' => 'pendente'],
            ['id' => 2, 'pet_id' => 2, 'status' => 'aceito'],
        ];

        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('fetchAll')->willReturn($dados);

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->method('prepare')->willReturn($stmtMock);

        $dao = $this->createDaoWithMockPdo($pdoMock);
        $solicitacoes = $dao->listarPorDono(1);

        $this->assertIsArray($solicitacoes);
        $this->assertCount(2, $solicitacoes);
        $this->assertEquals('pendente', $solicitacoes[0]['status']);
        $this->assertEquals('aceito', $solicitacoes[1]['status']);
    }

    public function testListarPorDono_DeveRetornarArrayVazio(): void
    {
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('fetchAll')->willReturn([]);

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->method('prepare')->willReturn($stmtMock);

        $dao = $this->createDaoWithMockPdo($pdoMock);
        $solicitacoes = $dao->listarPorDono(1);

        $this->assertIsArray($solicitacoes);
        $this->assertCount(0, $solicitacoes);
    }

    // ----------------------------------------------------------------
    //  helpers
    // ----------------------------------------------------------------

    private function createDaoWithMockPdo(PDO $pdoMock): SolicitacaoMatchDAO
    {
        $dao = new SolicitacaoMatchDAO();
        $ref = new ReflectionClass($dao);
        $prop = $ref->getProperty('pdo');
        $prop->setAccessible(true);
        $prop->setValue($dao, $pdoMock);
        return $dao;
    }
}
