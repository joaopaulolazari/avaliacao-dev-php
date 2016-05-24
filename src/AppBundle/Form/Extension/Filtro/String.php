<?php

namespace AppBundle\Form\Extension\Filtro;

use AppBundle\Form\Extension\Filtro\FiltroBase;

class String implements FiltroBase
{
    public function filtrar($valor, $parametros)
    {
        if (!$parametros) {
            return $valor;
        }

        return preg_replace('/[^A-Z]*/i', '', $valor);
    }
}
