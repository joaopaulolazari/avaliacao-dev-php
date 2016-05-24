<?php

namespace AppBundle\Form\Extension\Filtro;

use AppBundle\Form\Extension\Filtro\FiltroBase;

class Minuscula implements FiltroBase
{
    public function filtrar($valor, $parametros)
    {
        if (!$parametros) {
            return $valor;
        }

        return mb_strtolower((string) $valor);

    }
}
