<?php

namespace AppBundle\Tests\Entity;

use API\AppBundle\Tests\TestCase;
use AppBundle\Entity\TMaterialDicionario;
use AppBundle\Entity\TAutor;

class TMaterialDicionarioTest extends TestCase
{

    protected $material;

    public function setUp()
    {
        parent::setUp();

        $this->material = new TMaterialDicionario();
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

    public function testSetFsEdicao()
    {
        $edicao = $this->randomString(5);
        $this->material->setFsEdicao($edicao);

        $this->assertEquals($edicao, $this->material->getFsEdicao());
    }

    public function testSetFsClassificacao()
    {
        $classificacao = $this->randomString(100);
        $this->material->setFsClassificacao($classificacao);

        $this->assertEquals($classificacao, $this->material->getFsClassificacao());
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
