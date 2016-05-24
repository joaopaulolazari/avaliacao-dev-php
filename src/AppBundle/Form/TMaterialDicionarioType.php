<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TMaterialDicionarioType extends TMaterialType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add(
                'fsEdicao',
                'text',
                array(
                    'label' => 'Edição',
                    'required' => true
                )
            )
            ->add(
                'fsClassificacao',
                'text',
                array(
                    'label' => 'Classificação',
                    'required' => false
                )
            )
            ->add(
                'autores',
                'entity',
                array(
                    'class' => 'AppBundle:TAutor',
                    'label' => 'Autores',
                    'placeholder' => 'Selecione um autor',
                    'multiple' => true,
                    'expanded' => true,
                    'label_attr' => array(
                        'class' => 'col-md-12',
                    ),
                    'attr' => array(
                        'ng-model' => false,
                        'multiplo-checkbox' => 'materialDicionario.autores',
                    ),
                    'query_builder' => function ($em) {
                        return $em->createQueryBuilder('a')
                            ->orderBy('a.fsNome', 'ASC');
                    }
                )
            );

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'AppBundle\Entity\TMaterialDicionario',
                'csrf_protection' => false,
                'allow_extra_fields' => true,
                'cascade_validation' => true
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'materialDicionario';
    }
}
