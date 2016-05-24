<?php
namespace API\AppBundle\Service\Material;

use API\AppBundle\Service\ServicoBaseService;
use API\AppBundle\Service\Material\MaterialService;
use Symfony\Component\HttpKernel\Exception;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactory;
use AppBundle\Entity\TMaterialLivro;
use AppBundle\Form\TMaterialLivroType;

class MaterialLivroService extends MaterialService
{
    public function buscarMaterialLivro($id)
    {
        $materialLivro = $this->repositorio->find($id);
        if (!$materialLivro) {
            throw new Exception\NotFoundHttpException('O registro não foi encontrado.');
        }

        return $materialLivro;
    }

    public function formMaterialLivro(TMaterialLivro $materialLivro)
    {
        $form = $this->formFactory->create(new TMaterialLivroType(), $materialLivro);
        return $form;
    }

    public function cadastrar($request)
    {
        if (!$request->get('materialLivro')) {
            throw new Exception\PreconditionFailedHttpException('Nenhum parâmetro enviado.');
        }

        $materialLivro = new TMaterialLivro();
        $materialLivro = $this->populaMaterialLivro($materialLivro, $request);

        $this->em->persist($materialLivro);
        $this->em->flush();

        return $materialLivro;
    }

    public function editar(TMaterialLivro $materialLivro, $request)
    {
        if (!$request->get('materialLivro')) {
            throw new Exception\PreconditionFailedHttpException('Nenhum parâmetro enviado.');
        }

        return $this->populaMaterialLivro($materialLivro, $request);
    }

    public function remover(TMaterialLivro $materialLivro)
    {
        $this->em->remove($materialLivro);
        $this->em->flush();

        return true;
    }

    private function populaMaterialLivro(TMaterialLivro $materialLivro, $request)
    {
        $form = $this->formMaterialLivro($materialLivro);
        $form->submit($request);

        if (!$form->isValid()) {
            $this->formataErrosForm($form);
            throw new Exception\PreconditionFailedHttpException(json_encode($this->getErros()));
        }

        $this->em->persist($materialLivro);
        $this->em->flush();

        return $materialLivro;
    }
}
