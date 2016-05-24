<?php

namespace Site\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use AppBundle\Entity\TMaterialDicionario;

/**
 * @Route("/material-dicionario")
 */
class MaterialDicionarioController extends Controller
{
    /**
     * @Route("/cadastrar", name="site_material_dicionario_cadastrar")
     * @Template()
     */
    public function cadastrarAction()
    {
        $form = $this
            ->get('api.app.material.material_dicionario')
            ->formMaterialDicionario(new TMaterialDicionario());

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/editar/{id}", name="site_material_dicionario_editar")
     * @Template()
     */
    public function editarAction($id)
    {
        $form = $this
            ->get('api.app.material.material_dicionario')
            ->formMaterialDicionario(new TMaterialDicionario());

        return array(
            'form' => $form->createView(),
        );
    }
}
