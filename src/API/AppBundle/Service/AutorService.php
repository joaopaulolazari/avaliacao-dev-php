<?php
namespace API\AppBundle\Service;

use API\AppBundle\Service\ServicoBaseService;
use Symfony\Component\HttpKernel\Exception;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactory;
use AppBundle\Entity\TAutor;
use AppBundle\Form\TAutorType;

class AutorService extends ServicoBaseService
{
    public function buscarTodosAutores()
    {
        $autores = $this->repositorio->findAll();
        return $autores;
    }

    public function buscarAutores(Request $request)
    {
        if (!$request->get('autor')) {
            throw new Exception\PreconditionFailedHttpException('Nenhum parâmetro enviado.');
        }

        $autores = $this->repositorio->buscarAutores();
        if (!$autores) {
            throw new Exception\NotFoundHttpException('Nenhum registro encontrado.');
        }
        return $autores;
    }

    public function buscarAutor($id)
    {
        $autor = $this->repositorio->find($id);
        if (!$autor) {
            throw new Exception\NotFoundHttpException('O registro não foi encontrado.');
        }

        return $autor;
    }

    public function formAutor(TAutor $autor)
    {
        $form = $this->formFactory->create(new TAutorType(), $autor);
        return $form;
    }

    public function cadastrar($request)
    {
        if (!$request->get('autor')) {
            throw new Exception\PreconditionFailedHttpException('Nenhum parâmetro enviado.');
        }

        $autor = new TAutor();
        $autor = $this->populaAutor($autor, $request);

        $this->em->persist($autor);
        $this->em->flush();

        return $autor;
    }

    public function editar(TAutor $autor, $request)
    {
        if (!$request->get('autor')) {
            throw new Exception\PreconditionFailedHttpException('Nenhum parâmetro enviado.');
        }

        return $this->populaAutor($autor, $request);
    }

    public function remover(TAutor $autor)
    {
        $this->em->remove($autor);
        $this->em->flush();

        return true;
    }

    private function populaAutor(TAutor $autor, $request)
    {
        $form = $this->formAutor($autor);
        $form->submit($request);

        if (!$form->isValid()) {
            $this->formataErrosForm($form);
            throw new Exception\PreconditionFailedHttpException(json_encode($this->getErros()));
        }

        $fsNotacaoAutor = $autor->getFsNotacaoAutor();
        $autor->setFsNotacaoAutor(strtoupper($fsNotacaoAutor));

        $this->em->persist($autor);
        $this->em->flush();

        return $autor;
    }
}
