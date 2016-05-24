<?php

namespace AppBundle\Form\Extension\Filtro;

use AppBundle\Form\Extension\Filtro\FiltroBase;

class Decimal implements FiltroBase
{
    public function filtrar($valor, $parametros)
    {
        if (!$parametros || !is_numeric($valor)) {
            return $valor;
        }

        if (is_array($valor) || is_object($valor)) {
            return null;
        }

        return floatval($valor);
    }
}
