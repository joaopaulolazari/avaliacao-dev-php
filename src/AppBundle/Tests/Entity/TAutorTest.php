<?php

namespace AppBundle\Tests\Entity;

use API\AppBundle\Tests\TestCase;
use AppBundle\Entity\TAutor;

class TAutorTest extends TestCase
{

    protected $autor;

    public function setUp()
    {
        parent::setUp();

        $this->autor = new TAutor();
    }

    public function testGetFnAutorId()
    {
        $id = $this->autor->getFnAutorId();
        $this->assertNull($id);
    }

    public function testSetFsNome()
    {
        $nome = $this->randomString(50);
        $this->autor->setFsNome($nome);

        $this->assertEquals($nome, $this->autor->getFsNome());
        $this->assertEquals($nome, $this->autor->__toString());
    }

    public function testSetFsNotacaoAutor()
    {
        $notacao = $this->randomString(3);
        $this->autor->setFsNotacaoAutor($notacao);

        $this->assertEquals($notacao, $this->autor->getFsNotacaoAutor());
    }

    public function testSetFdInclusao()
    {
        $now = new \DateTime();

        $this->autor->setFdInclusao($now);
        $this->assertEquals($now->getTimestamp(), $this->autor->getFdInclusao()->getTimestamp());
    }

    public function testSetFdAlteracao()
    {
        $now = new \DateTime();

        $this->autor->setFdAlteracao($now);
        $this->assertEquals($now->getTimestamp(), $this->autor->getFdAlteracao()->getTimestamp());
    }
}
