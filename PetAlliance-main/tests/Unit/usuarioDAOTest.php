<?php
use PHPUnit\Framework\TestCase;

class usuarioDAOTest extends TestCase
{
    private UsuarioDAO $dao;

    protected function setUp(): void
    {
        $this->dao = new UsuarioDAO();
    }

    // ----------------------------------------------------------------
    //  validaCPF()
    // ----------------------------------------------------------------

    public function testValidaCPF_DeveRetornarVerdadeiroParaCPFValido(): void
    {
        $resultado = $this->dao->validaCPF('529.982.247-25');
        $this->assertTrue($resultado);
    }

    public function testValidaCPF_DeveRetornarFalsoParaCPFComDigitosInvalidos(): void
    {
        $resultado = $this->dao->validaCPF('123.456.789-00');
        $this->assertFalse($resultado);
    }

    public function testValidaCPF_DeveRetornarFalsoParaCPFComSequenciaRepetida(): void
    {
        $resultado = $this->dao->validaCPF('111.111.111-11');
        $this->assertFalse($resultado);
    }

    public function testValidaCPF_DeveRetornarFalsoParaCPFComMenosDeOnzeDigitos(): void
    {
        $resultado = $this->dao->validaCPF('123.456.789-0');
        $this->assertFalse($resultado);
    }

    public function testValidaCPF_DeveRetornarFalsoParaCPFComMaisDeOnzeDigitos(): void
    {
        $resultado = $this->dao->validaCPF('123.456.789-000');
        $this->assertFalse($resultado);
    }

    public function testValidaCPF_DeveRetornarFalsoParaStringVazia(): void
    {
        $resultado = $this->dao->validaCPF('');
        $this->assertFalse($resultado);
    }

    // ----------------------------------------------------------------
    //  read()
    // ----------------------------------------------------------------

    public function testRead_DeveRetornarNullQuandoCPFNaoExiste(): void
    {
        $pdoMock = $this->createMock(PDO::class);
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('fetch')->willReturn(false);
        $pdoMock->method('prepare')->willReturn($stmtMock);

        $dao = $this->createDaoWithMockPdo($pdoMock);
        $resultado = $dao->read('00000000000');
        $this->assertNull($resultado);
    }

    public function testRead_DeveRetornarUsuarioQuandoCPFExiste(): void
    {
        $dados = [
            'id' => 1,
            'foto_perfil' => 'placeholder.webp',
            'cpf' => '52998224725',
            'cep' => '01001000',
            'tipo_usuario' => 0,
            'nome' => 'João',
            'email' => 'joao@email.com',
            'senha' => password_hash('Senha123', PASSWORD_DEFAULT),
            'verificado' => true,
            'tentativas_login' => 0,
            'bloqueado' => false,
        ];

        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('fetch')->willReturn($dados);

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->method('prepare')->willReturn($stmtMock);

        $dao = $this->createDaoWithMockPdo($pdoMock);
        $usuario = $dao->read('52998224725');
        $this->assertNotNull($usuario);
        $this->assertInstanceOf(Usuario::class, $usuario);
        $this->assertEquals('João', $usuario->getNome());
        $this->assertEquals('52998224725', $usuario->getCpf());
    }

    // ----------------------------------------------------------------
    //  buscarPorEmail()
    // ----------------------------------------------------------------

    public function testBuscarPorEmail_DeveRetornarNullQuandoEmailNaoExiste(): void
    {
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('fetch')->willReturn(false);

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->method('prepare')->willReturn($stmtMock);

        $dao = $this->createDaoWithMockPdo($pdoMock);
        $resultado = $dao->buscarPorEmail('inexistente@email.com');
        $this->assertNull($resultado);
    }

    // ----------------------------------------------------------------
    //  cadastrarUsuario()
    // ----------------------------------------------------------------

    public function testCadastrarUsuario_DeveLancarExcecaoParaCPFInvalido(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $usuario = new Usuario(
            'placeholder.webp', '00000000000', '01001000', 0, 'Teste', 'teste@email.com', 'Senha123'
        );
        $this->dao->cadastrarUsuario($usuario);
    }

    // ----------------------------------------------------------------
    //  helpers
    // ----------------------------------------------------------------

    private function createDaoWithMockPdo(PDO $pdoMock): UsuarioDAO
    {
        $dao = new UsuarioDAO();
        $ref = new ReflectionClass($dao);
        $prop = $ref->getProperty('pdo');
        $prop->setAccessible(true);
        $prop->setValue($dao, $pdoMock);
        return $dao;
    }
}
