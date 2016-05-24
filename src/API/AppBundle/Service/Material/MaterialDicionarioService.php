<?php
namespace API\AppBundle\Service\Material;

use API\AppBundle\Service\ServicoBaseService;
use API\AppBundle\Service\Material\MaterialService;
use Symfony\Component\HttpKernel\Exception;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactory;
use AppBundle\Entity\TMaterialDicionario;
use AppBundle\Form\TMaterialDicionarioType;

class MaterialDicionarioService extends MaterialService
{
    public function buscarMaterialDicionario($id)
    {
        $materialDicionario = $this->repositorio->find($id);
        if (!$materialDicionario) {
            throw new Exception\NotFoundHttpException('O registro não foi encontrado.');
        }

        return $materialDicionario;
    }

    public function formMaterialDicionario(TMaterialDicionario $materialDicionario)
    {
        $form = $this->formFactory->create(new TMaterialDicionarioType(), $materialDicionario);
        return $form;
    }

    public function cadastrar($request)
    {
        if (!$request->get('materialDicionario')) {
            throw new Exception\PreconditionFailedHttpException('Nenhum parâmetro enviado.');
        }

        $materialDicionario = new TMaterialDicionario();
        $materialDicionario = $this->populaMaterialDicionario($materialDicionario, $request);

        $this->em->persist($materialDicionario);
        $this->em->flush();

        return $materialDicionario;
    }

    public function editar(TMaterialDicionario $materialDicionario, $request)
    {
        if (!$request->get('materialDicionario')) {
            throw new Exception\PreconditionFailedHttpException('Nenhum parâmetro enviado.');
        }

        return $this->populaMaterialDicionario($materialDicionario, $request);
    }

    public function remover(TMaterialDicionario $materialDicionario)
    {
        $this->em->remove($materialDicionario);
        $this->em->flush();

        return true;
    }

    private function populaMaterialDicionario(TMaterialDicionario $materialDicionario, $request)
    {
        $form = $this->formMaterialDicionario($materialDicionario);
        $form->submit($request);

        if (!$form->isValid()) {
            $this->formataErrosForm($form);
            throw new Exception\PreconditionFailedHttpException(json_encode($this->getErros()));
        }

        $this->em->persist($materialDicionario);
        $this->em->flush();

        return $materialDicionario;
    }
}
