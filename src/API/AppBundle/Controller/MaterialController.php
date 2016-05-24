<?php

namespace API\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use FOS\RestBundle\Controller\Annotations as FOS;

class MaterialController extends Controller
{
    /**
     * @FOS\Get("/", name="api_app_materiais", options={"method_prefix" = false })
     * @FOS\View(statusCode=200, serializerEnableMaxDepthChecks=true, serializerGroups={"Default", "listagem"})
     */
    public function buscarMateriaisAction()
    {
        $servico = $this->get('api.app.material.material');
        $materiais = $servico->buscarMateriais();

        return array(
            'dados' => array(
                'materiais' => $materiais,
            )
        );
    }
}
