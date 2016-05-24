<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RegistroUnicoValidator extends ConstraintValidator
{

    /**
     * @var EntityManager
     * $em Doctrine EntityManager
    */
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function validate($valor, Constraint $constraint)
    {
        $campoUnico = $this->context->getPropertyName();
        $classe = $this->context->getClassName();
        $entidade = $this->context->getObject();
        $condicoes = $constraint->condicao;

        $identificadores = $this->em->getClassMetadata($classe)->getIdentifierValues($entidade);

        $qb = $this->em->createQueryBuilder()
            ->select('c')
            ->from($classe, 'c')
            ->where("c.$campoUnico = :valor")
            ->setParameter(':valor', $valor);

        $cont = 1;
        // Adicionando condições extras
        if ($condicoes) {
            foreach ($condicoes as $campo => $valor) {
                $qb
                    ->andWhere('c.'.$campo.' = :param'.$cont)
                    ->setParameter('param'.$cont, $valor);
                $cont++;
            }
        }
        // Caso o registro já exista no banco, adicionar um novo where para cada primary key dessa entidade
        foreach ($identificadores as $campo => $valor) {
            $qb
                ->andWhere('c.'.$campo.' != :param'.$cont)
                ->setParameter('param'.$cont, $valor);
            $cont++;
        }

        $entidadeUnica = $qb->getQuery()->setMaxResults(1)->getOneOrNullResult();

        if (!is_null($entidadeUnica)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%string%', $valor)
                ->addViolation();
        }
    }
}
