<?php

namespace Site\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use AppBundle\Entity\TUsuarioSite;

/**
 * @Route("/")
 */
class MaterialController extends Controller
{
    /**
     * @Route("/", name="site_material")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
}
