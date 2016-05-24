<?php

namespace AppBundle\Form\Extension\Filtro;

use AppBundle\Form\Extension\Filtro\FiltroBase;

class Hora implements FiltroBase
{
    public function filtrar($valor, $parametros)
    {
        if (!$parametros) {
            return $valor;
        }

        $data = new \DateTime();

        if ($parametros !== true) {
            // Ncessário colocar o "!" antes do valor, pois caso venha somente a
            // data sem o tempo, o datetime não vai auto-completar com o tempo atual
            $data = $data->createFromFormat($parametros, $valor);
        } else {
            $data = new \DateTime($valor);
        }

        // Caso a conversão de data não tenha ocorrido corretamente
        if (!($data instanceof \DateTime)) {
            return null;
        }

        // Caso haja algum erro na conversão, retornar um valor inválido que sera
        // barrado nas validações da entidade
        $erros = $data->getLastErrors();
        if ($erros['error_count'] > 0 || $erros['warning_count'] > 0) {
            return null;
        }

        return $data;
    }
}
