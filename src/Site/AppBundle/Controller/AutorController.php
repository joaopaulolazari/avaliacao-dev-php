<?php

namespace Site\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use AppBundle\Entity\TAutor;

/**
 * @Route("/autor")
 */
class AutorController extends Controller
{
    /**
     * @Route("/", name="site_autor")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/cadastrar", name="site_autor_cadastrar")
     * @Template()
     */
    public function cadastrarAction()
    {
        $form = $this
            ->get('api.app.autor')
            ->formAutor(new TAutor());

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/editar/{id}", name="site_autor_editar")
     * @Template()
     */
    public function editarAction($id)
    {
        $form = $this
            ->get('api.app.autor')
            ->formAutor(new TAutor());

        return array(
            'form' => $form->createView(),
        );
    }
}
