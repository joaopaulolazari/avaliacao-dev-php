<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ContatoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'fsEmail',
                'email',
                array(
                    'label' => 'E-mail',
                    'required' => true,
                    'attr' => array(
                        'minlength' => 5,
                        'maxlength' => 100,
                    )
                )
            )
            ->add(
                'fsTelefone',
                'text',
                array(
                    'label' => 'Telefone',
                    'required' => true,
                    'filtro' => array(
                        'Numerico' => true,
                    ),
                    'attr' => array(
                        'mascara' => 'telefone',
                        'minlength' => 14,
                        'maxlength' => 15,
                    )
                )
            )
            ->add(
                'fsCelular',
                'text',
                array(
                    'label' => 'Celular',
                    'required' => false,
                    'filtro' => array(
                        'Numerico' => true,
                    ),
                    'attr' => array(
                        'mascara' => 'telefone',
                        'minlength' => 14,
                        'maxlength' => 15,
                    )
                )
            )
            ->add(
                'fsNome',
                'text',
                array(
                    'label' => 'Nome',
                    'required' => true,
                    'attr' => array(
                        'minlength' => 1,
                        'maxlength' => 50,
                    )
                )
            )
            ->add(
                'fsMensagem',
                'textarea',
                array(
                    'label' => 'Mensagem',
                    'required' => true,
                    'attr' => array(
                        'maxlength' => 200,
                        'cols' => 50,
                        'rows' => 5
                    )
                )
            )
            ->add(
                'fsAssunto',
                'choice',
                array(
                    'label' => 'Assunto',
                    'required' => true,
                    'empty_value' => 'Selecione',
                    'choices' => array(
                        'duvidas' => 'Dúvidas',
                        'sugestoes' => 'Sugestões'
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
                'csrf_protection' => false
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'contato';
    }
}
