<?php

namespace AppBundle\Form\Extension\Filtro;

use AppBundle\Form\Extension\Filtro\FiltroBase;

class Inteiro implements FiltroBase
{
    public function filtrar($valor, $parametros)
    {
        if (!$parametros) {
            return $valor;
        }

        return (int) $valor;
    }
}
