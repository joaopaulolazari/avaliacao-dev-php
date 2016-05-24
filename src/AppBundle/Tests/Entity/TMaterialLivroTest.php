<?php

namespace AppBundle\Tests\Entity;

use API\AppBundle\Tests\TestCase;
use AppBundle\Entity\TMaterialLivro;
use AppBundle\Entity\TAutor;

class TMaterialLivroTest extends TestCase
{

    protected $material;

    public function setUp()
    {
        parent::setUp();

        $this->material = new TMaterialLivro();
    }

    public function testGetFnMaterialId()
    {
        $id = $this->material->getFnMaterialId();
        $this->assertNull($id);
    }

    public function testSetFsTitulo()
    {
        $titulo = $this->randomString(50);
        $this->material->setFsTitulo($titulo);

        $this->assertEquals($titulo, $this->material->getFsTitulo());
    }

    public function testSetFsSubtitulo()
    {
        $subtitulo = $this->randomString(50);
        $this->material->setFsSubtitulo($subtitulo);

        $this->assertEquals($subtitulo, $this->material->getFsSubtitulo());
    }

    public function testSetFsIsbn()
    {
        $isbn = $this->randomString(5);
        $this->material->setFsIsbn($isbn);

        $this->assertEquals($isbn, $this->material->getFsIsbn());
    }

    public function testSetFsResumo()
    {
        $resumo = $this->randomString(500);
        $this->material->setFsResumo($resumo);

        $this->assertEquals($resumo, $this->material->getFsResumo());
    }

    public function testSetFnNumeroPagina()
    {
        $numero = rand(0, 9999);
        $this->material->setFnNumeroPagina($numero);

        $this->assertEquals($numero, $this->material->getFnNumeroPagina());
    }

    public function testSetFsCaminhoImagem()
    {
        $caminho = $this->randomString(50);
        $this->material->setFsCaminhoImagem($caminho);

        $this->assertEquals($caminho, $this->material->getFsCaminhoImagem());
    }

    public function testRemoveAutores()
    {
        $autor1 = new TAutor();
        $autor2 = new TAutor();

        $this->material->addAutore($autor1);
        $this->material->addAutore($autor2);

        $this->assertContainsOnlyInstancesOf('\AppBundle\Entity\TAutor', $this->material->getAutores());
        $this->assertCount(2, $this->material->getAutores());
        $this->assertCount(2, $this->material->getAutoresId());

        $this->material->removeAutore($autor2);
        $this->assertCount(1, $this->material->getAutores());
        $this->assertCount(1, $this->material->getAutoresId());
    }
}
