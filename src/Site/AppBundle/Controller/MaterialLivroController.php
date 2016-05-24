<?php

namespace Site\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use AppBundle\Entity\TMaterialLivro;

/**
 * @Route("/material-livro")
 */
class MaterialLivroController extends Controller
{
    /**
     * @Route("/cadastrar", name="site_material_livro_cadastrar")
     * @Template()
     */
    public function cadastrarAction()
    {
        $form = $this
            ->get('api.app.material.material_livro')
            ->formMaterialLivro(new TMaterialLivro());

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/editar/{id}", name="site_material_livro_editar")
     * @Template()
     */
    public function editarAction($id)
    {
        $form = $this
            ->get('api.app.material.material_livro')
            ->formMaterialLivro(new TMaterialLivro());

        return array(
            'form' => $form->createView(),
        );
    }
}
