<?php

namespace AppBundle\Form\Extension\Filtro;

use AppBundle\Form\Extension\Filtro\FiltroBase;

class Numerico implements FiltroBase
{
    public function filtrar($valor, $parametros = true)
    {
        if (is_null($valor)) {
            return $valor;
        }

        if (!$parametros) {
            return $valor;
        }

        return preg_replace('/[^0-9]*/', '', $valor);
    }
}
