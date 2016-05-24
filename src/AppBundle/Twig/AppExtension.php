<?php

namespace AppBundle\Twig;

class AppExtension extends \Twig_Extension
{
    public function getRootFormName($form)
    {
        $parent = $form;
        while (!is_null($parent->parent)) {
            $parent = $parent->parent;
        }
        return $parent->vars['name'];
    }


    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getRootFormName', array($this, 'getRootFormName')),
        );
    }

    public function getName()
    {
        return 'app_extension';
    }
}
