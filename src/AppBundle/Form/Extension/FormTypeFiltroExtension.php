<?php
namespace AppBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class FormTypeFiltroExtension extends AbstractTypeExtension
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function ($formEvent) {
                $form = $formEvent->getForm();
                $options = $form->getConfig()->getOptions();
                foreach ($options['filtro'] as $filtro => $parametros) {
                    $valor = $formEvent->getData();
                    $classeFiltro = '\AppBundle\Form\Extension\Filtro\\'.$filtro;
                    if (class_exists($classeFiltro)) {
                        $filtro = new $classeFiltro();
                        $formEvent->setData($filtro->filtrar($valor, $parametros));
                    }
                }
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        //Adiciona opção "filtro" para formType
        $resolver->setDefaults(array('filtro' => array()));
    }
}
