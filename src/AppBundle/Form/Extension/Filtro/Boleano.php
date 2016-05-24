<?php

namespace AppBundle\Form\Extension\Filtro;

use AppBundle\Form\Extension\Filtro\FiltroBase;

class Boleano implements FiltroBase
{
    public function filtrar($valor, $parametros)
    {
        if (!$parametros) {
            return $valor;
        }

        return ($valor) ? true : false;
    }
}
