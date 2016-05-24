<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 *
 * @author Afranio Martins <afranioce@gmail.com>
 *
 * @api
 */
class CpfCnpj extends Constraint
{
    public $cpf = false;
    public $cnpj = false;
    public $message = 'O {{ type }} informado é inválido.';
}
