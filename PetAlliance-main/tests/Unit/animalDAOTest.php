<?php
use PHPUnit\Framework\TestCase;

class animalDAOTest extends TestCase
{
    private AnimalDAO $dao;

    protected function setUp(): void
    {
        $this->dao = new AnimalDAO();
    }

    // ----------------------------------------------------------------
    //  read()
    // ----------------------------------------------------------------

    public function testRead_DeveRetornarNullQuandoAnimalNaoExiste(): void
    {
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('fetch')->willReturn(false);

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->method('prepare')->willReturn($stmtMock);

        $dao = $this->createDaoWithMockPdo($pdoMock);
        $resultado = $dao->read(999);
        $this->assertNull($resultado);
    }

    public function testRead_DeveRetornarAnimalComFotosQuandoExiste(): void
    {
        $dadosAnimal = [
            'id' => 1,
            'dono_id' => 1,
            'nome' => 'Rex',
            'raca' => 'Pastor Alemão',
            'cor' => 'Marrom',
            'sexo' => 'Macho',
            'tipo' => 'Cachorro',
            'porte' => 'Grande',
            'data_nascimento' => '2022-01-15',
            'peso' => '35.50',
            'descricao' => 'Cão dócil',
            'vacinado' => 1,
            'certificado_raca' => 0,
            'foto_vacinas' => 'vacinas.jpg',
            'foto_certificado' => null,
        ];
        $fotos = ['foto1.jpg', 'foto2.jpg'];

        $stmtAnimal = $this->createMock(PDOStatement::class);
        $stmtAnimal->method('execute')->willReturn(true);
        $stmtAnimal->method('fetch')->willReturn($dadosAnimal);

        $stmtFav = $this->createMock(PDOStatement::class);
        $stmtFav->method('execute')->willReturn(true);
        $stmtFav->method('fetchColumn')->willReturn(0);

        $stmtFotos = $this->createMock(PDOStatement::class);
        $stmtFotos->method('execute')->willReturn(true);
        $stmtFotos->method('fetchAll')->willReturn($fotos);

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->method('prepare')->willReturnCallback(function ($sql) use ($stmtAnimal, $stmtFav, $stmtFotos) {
            if (str_contains($sql, 'tb_favoritos')) return $stmtFav;
            if (str_contains($sql, 'tb_pets_fotos')) return $stmtFotos;
            return $stmtAnimal;
        });

        $dao = $this->createDaoWithMockPdo($pdoMock);
        $animal = $dao->read(1, 1);

        $this->assertNotNull($animal);
        $this->assertInstanceOf(Animal::class, $animal);
        $this->assertEquals('Rex', $animal->getNome());
        $this->assertEquals('Pastor Alemão', $animal->getRaca());
        $this->assertCount(2, $animal->getFotos());
    }

    // ----------------------------------------------------------------
    //  favoritarAnimal()
    // ----------------------------------------------------------------

    public function testFavoritarAnimal_DeveInserirQuandoNaoFavoritado(): void
    {
        $stmtSelect = $this->createMock(PDOStatement::class);
        $stmtSelect->method('execute')->willReturn(true);
        $stmtSelect->method('fetch')->willReturn(false);

        $stmtInsert = $this->createMock(PDOStatement::class);
        $stmtInsert->method('execute')->willReturn(true);

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->method('prepare')->willReturnCallback(function ($sql) use ($stmtSelect, $stmtInsert) {
            return str_contains($sql, 'SELECT') ? $stmtSelect : $stmtInsert;
        });

        $dao = $this->createDaoWithMockPdo($pdoMock);
        $resultado = $dao->favoritarAnimal(1, 1);
        $this->assertTrue($resultado);
    }

    public function testFavoritarAnimal_DeveRemoverQuandoJaFavoritado(): void
    {
        $stmtSelect = $this->createMock(PDOStatement::class);
        $stmtSelect->method('execute')->willReturn(true);
        $stmtSelect->method('fetch')->willReturn(['1' => 1]);

        $stmtDelete = $this->createMock(PDOStatement::class);
        $stmtDelete->method('execute')->willReturn(true);

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->method('prepare')->willReturnCallback(function ($sql) use ($stmtSelect, $stmtDelete) {
            return str_contains($sql, 'SELECT') ? $stmtSelect : $stmtDelete;
        });

        $dao = $this->createDaoWithMockPdo($pdoMock);
        $resultado = $dao->favoritarAnimal(1, 1);
        $this->assertFalse($resultado);
    }

    // ----------------------------------------------------------------
    //  search()
    // ----------------------------------------------------------------

    public function testSearch_DeveRetornarArrayVazioQuandoNaoEncontra(): void
    {
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('fetch')->willReturn(false);

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->method('prepare')->willReturn($stmtMock);

        $dao = $this->createDaoWithMockPdo($pdoMock);
        $resultado = $dao->search('ZZZZ_inexistente', 1);
        $this->assertIsArray($resultado);
        $this->assertCount(0, $resultado);
    }

    // ----------------------------------------------------------------
    //  helpers
    // ----------------------------------------------------------------

    private function createDaoWithMockPdo(PDO $pdoMock): AnimalDAO
    {
        $dao = new AnimalDAO();
        $ref = new ReflectionClass($dao);
        $prop = $ref->getProperty('pdo');
        $prop->setAccessible(true);
        $prop->setValue($dao, $pdoMock);
        return $dao;
    }
}
