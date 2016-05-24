<?php
namespace API\AppBundle\Service\Material;

use API\AppBundle\Service\ServicoBaseService;
use Symfony\Component\HttpKernel\Exception;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\TMaterial;

class MaterialService extends ServicoBaseService
{
    public function buscarMateriais()
    {
        $materiais = $this->repositorio->findAll();
        return $materiais;
    }

    public function buscarMaterial($id)
    {
        $material = $this->repositorio->find($id);
        if (!$material) {
            throw new Exception\NotFoundHttpException('O registro não foi encontrado.');
        }

        return $material;
    }
}
