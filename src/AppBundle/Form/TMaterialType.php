<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TMaterialType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'fsTitulo',
                'text',
                array(
                    'label' => 'Título',
                    'required' => true
                )
            )
            ->add(
                'fsSubtitulo',
                'text',
                array(
                    'label' => 'Subtítulo',
                    'required' => false,
                )
            )
            ->add(
                'fsCaminhoImagem',
                'hidden',
                array(
                    'required' => true,
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
                        'multiplo-checkbox' => 'material.autores',
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
                'data_class' => 'AppBundle\Entity\TMaterial',
                'csrf_protection' => false,
                'allow_extra_fields' => true
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'material';
    }
}
