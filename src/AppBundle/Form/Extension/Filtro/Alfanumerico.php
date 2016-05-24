<?php

namespace AppBundle\Form\Extension\Filtro;

use AppBundle\Form\Extension\Filtro\FiltroBase;

class Alfanumerico implements FiltroBase
{
    public function filtrar($valor, $parametros)
    {
        if (!$parametros) {
            return $valor;
        }

        return preg_replace('/[^0-9A-Z]*/i', '', $valor);
    }

    /**
     * Filtra alfanumérico não removendo os espaços em brancos
     * @param  [type] $valor      [description]
     * @param  [type] $parametros [description]
     * @return [type]             [description]
     */
    public function filtrarComEspacos($valor, $parametros)
    {
        if (!$parametros) {
            return $valor;
        }

        return preg_replace('/[^0-9A-Z -]*/i', '', $valor);
    }
}
