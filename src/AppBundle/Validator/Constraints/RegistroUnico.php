<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class RegistroUnico extends Constraint
{
    public $message = 'Já existe um "%string%" em nossa base de dados.';
    public $condicao = false;

    public function validatedBy()
    {
        return 'RegistroUnicoValidator';
    }

    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
