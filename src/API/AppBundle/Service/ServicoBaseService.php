<?php
namespace API\AppBundle\Service;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Form\FormFactory;
use Doctrine\ORM\EntityManager;

abstract class ServicoBaseService
{

    /**
     * @var EntityManager
     * $em Doctrine EntityManager
    */
    protected $em;

    /**
     * @var FormFactory
     *
     * $formFactory Symfony Form Factory
    */
    protected $formFactory;

    /**
     * @var SecurityContext
     *
     * $security Symfony SecurityContext
    */
    protected $security;

    /**
     * @var ObjectRepository|null
     *
     * $repositorio ObjectRepository
    */
    protected $repositorio = null;

    /**
     * @var Array
     *
     * $erro Array para armazenar erros do serviço
    */
    protected $erros = array();

    /**
     * Seta serviço EntityManager
     *
     * @param  EntityManager $em
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Seta serviço FormFactory
     *
     * @param  FormFactory $formFactory
     */
    public function setFormFactory(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * Seta serviço SecurityContext
     *
     * @param  SecurityContext $security
     */
    public function setSecurityContext(SecurityContext $security)
    {
        $this->security = $security;
    }

    /**
     * Seta variavel de repositório
     * @param string $repository Nome do repositório
     */
    public function setRepository($repository)
    {
        $this->repositorio = $this->em->getRepository($repository);
    }

    /**
     * Busca usuário logado na sessão
     *
     * @return TUsuario|null Entidade do usuário logado ou null
     */
    public function getUsuario()
    {
        return $this->security->getToken()->getUser();
    }

    /**
     * Retorna um array contendo erros do serviço
     *
     * @return Array    array contendo erros
     */
    public function getErros()
    {
        return $this->erros;
    }

    /**
     * Busca entidade pelo status
     *
     * @param  integer $status id do Status
     * @return array
     */
    protected function buscaPorStatus($status)
    {
        return $this->repositorio->findByFnStatus($status);
    }

    /**
     * Formata erros de formulários para um array
     *
     * @param  \Symfony\Component\Form\Form $form
     * @return array
     */
    protected function formataErrosForm(\Symfony\Component\Form\Form $form)
    {
        $erros = array();
        //Itera sobre os erros do formulário root
        foreach ($form->getErrors() as $erro) {
            if ($form->isRoot()) {
                $erros['#'][] = $erro->getMessage();
            } else {
                $erros[$form->createView()->vars['full_name']][] = $erro->getMessage();
            }
        }
        //Valida todos os erros dos formulários filhos
        foreach ($form->all() as $filho) {
            if (!$filho->isValid()) {
                $erros = array_merge($erros, $this->formataErrosForm($filho));
            }
        }
        $this->erros = $erros;
        return $erros;
    }

    /**
     * Remove os indices cujos valores sejam nulos
     * @param  mixed $dados
     * @return mixed
     */
    protected function filtraNull($dados)
    {
        return array_filter(
            $dados,
            function ($valor) {
                return !is_null($valor);
            }
        );
    }
}
