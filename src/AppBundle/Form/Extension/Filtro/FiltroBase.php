<?php

namespace AppBundle\Form\Extension\Filtro;

interface FiltroBase
{
    /**
     * Recebe $valor e $parametros para filtrar
     *
     * @param mixed $valor - Valor para preencher o Form
     * @param mixed $parametros - Parâmetros para aplicar o filtro
     *
     * @return mixed $valor filtrado
     */
    public function filtrar($valor, $parametros);
}
