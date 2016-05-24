<?php

namespace AppBundle\Form\Extension\Filtro;

use AppBundle\Form\Extension\Filtro\FiltroBase;

class Limite implements FiltroBase
{
    public function filtrar($valor, $parametros)
    {
        if (strlen($valor) <= $parametros) {
            return $valor;
        }

        return substr($valor, 0, $parametros);
    }
}
