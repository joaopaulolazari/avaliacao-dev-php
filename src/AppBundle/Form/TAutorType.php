<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TAutorType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'fsNome',
                'text',
                array(
                    'label' => 'Nome',
                    'required' => true,
                    'attr' => array(
                        'minlength' => 3,
                        'maxlength' => 255,
                    )
                )
            )
            ->add(
                'fsNotacaoAutor',
                'text',
                array(
                    'label' => 'Notação autor',
                    'required' => true,
                    'attr' => array(
                        'minlength' => 3,
                        'maxlength' => 3,
                        'data-behavior' => 'uppercase'
                    )
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
                'data_class' => 'AppBundle\Entity\TAutor',
                'csrf_protection' => false,
                'allow_extra_fields' => true,
                'cascade_validation' => true,
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'autor';
    }
}
