<?php
namespace API\AppBundle\Service;

use API\AppBundle\Service\ServicoBaseService;
use JMS\Serializer\SerializationContext;

class SerializadorService extends ServicoBaseService
{
    /**
     * Serializador
     * @var \JMS\Serializer\Serializer
     */
    protected $jms;

    /**
     * Construtor
     * @param \Doctrine\ORM\EntityManager $em
    */
    public function __construct(\Doctrine\ORM\EntityManager $em, \JMS\Serializer\Serializer $jms)
    {
        $this->em = $em;
        $this->jms = $jms;
    }

    /**
     * Funcão para serializar um objeto
     * @param  mixed $objeto
     * @param  string $tipo xml, json
     * @return string
     */
    public function serializarObjeto($objeto, $tipo = 'xml')
    {
        return $this->jms->serialize($objeto, $tipo, SerializationContext::create());
    }

    /**
     * Funcão para serializar um objeto
     * @param  mixed $xml
     * @param  string $tipo xml, json
     * @return string
     */
    public function deserializarObjeto($xml, $namespace, $format)
    {
        return $this->jms->deserialize($xml, $namespace, $format);
    }
}
