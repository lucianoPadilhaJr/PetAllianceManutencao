<?php
use PHPUnit\Framework\TestCase;

class denunciaDAOTest extends TestCase
{
    private DenunciaDAO $dao;

    protected function setUp(): void
    {
        $this->dao = new DenunciaDAO();
    }

    // ----------------------------------------------------------------
    //  cadastrar()
    // ----------------------------------------------------------------

    public function testCadastrar_DeveInserirDenunciaComSucesso(): void
    {
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('fetch')->willReturn(false);

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->method('prepare')->willReturn($stmtMock);
        $pdoMock->method('lastInsertId')->willReturn('1');

        $dao = $this->createDaoWithMockPdo($pdoMock);

        $denuncia = new Denuncia(1, 'usuario', 2, 'Usuário suspeito');
        $resultado = $dao->cadastrar($denuncia);

        $this->assertNotNull($resultado);
        $this->assertIsInt($resultado);
    }

    public function testCadastrar_DeveLancarExcecaoParaTipoAlvoInvalido(): void
    {
        $denuncia = new Denuncia(null, 'invalido', null, 'Teste');

        $this->expectException(InvalidArgumentException::class);
        $this->dao->cadastrar($denuncia);
    }

    // ----------------------------------------------------------------
    //  listar()
    // ----------------------------------------------------------------

    public function testListar_DeveRetornarArrayDeDenuncias(): void
    {
        $dados = [
            [
                'id' => 1,
                'usuario_id' => 1,
                'tipo_alvo' => 'animal',
                'alvo_id' => 5,
                'descricao' => 'Animal maltratado',
                'resolvido' => 0,
                'criado_em' => '2026-07-08 12:00:00',
            ],
            [
                'id' => 2,
                'usuario_id' => 2,
                'tipo_alvo' => 'usuario',
                'alvo_id' => 3,
                'descricao' => 'Usuário agressivo',
                'resolvido' => 1,
                'criado_em' => '2026-07-08 13:00:00',
            ],
        ];

        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('fetchAll')->willReturn($dados);

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->method('prepare')->willReturn($stmtMock);
        $pdoMock->method('query')->willReturn($stmtMock);

        $dao = $this->createDaoWithMockPdo($pdoMock);
        $denuncias = $dao->listar();

        $this->assertIsArray($denuncias);
        $this->assertCount(2, $denuncias);
        $this->assertEquals('Animal maltratado', $denuncias[0]['descricao']);
        $this->assertEquals(0, $denuncias[0]['resolvido']);
        $this->assertEquals(1, $denuncias[1]['resolvido']);
    }

    public function testListar_DeveRetornarArrayVazioQuandoNaoHaDenuncias(): void
    {
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('fetchAll')->willReturn([]);

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->method('prepare')->willReturn($stmtMock);
        $pdoMock->method('query')->willReturn($stmtMock);

        $dao = $this->createDaoWithMockPdo($pdoMock);
        $denuncias = $dao->listar();

        $this->assertIsArray($denuncias);
        $this->assertCount(0, $denuncias);
    }

    // ----------------------------------------------------------------
    //  atualizarStatus()
    // ----------------------------------------------------------------

    public function testAtualizarStatus_DeveAtualizarResolvido(): void
    {
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->method('prepare')->willReturn($stmtMock);

        $dao = $this->createDaoWithMockPdo($pdoMock);

        $dao->atualizarStatus(1, 1);
        $this->assertTrue(true);
    }

    // ----------------------------------------------------------------
    //  helpers
    // ----------------------------------------------------------------

    private function createDaoWithMockPdo(PDO $pdoMock): DenunciaDAO
    {
        $dao = new DenunciaDAO();
        $ref = new ReflectionClass($dao);
        $prop = $ref->getProperty('pdo');
        $prop->setAccessible(true);
        $prop->setValue($dao, $pdoMock);
        return $dao;
    }
}
