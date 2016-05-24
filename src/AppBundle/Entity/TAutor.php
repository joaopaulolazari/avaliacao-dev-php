<?php

namespace AppBundle\Entity;

use AppBundle\Entity\EntidadeBase;

/**
 * TAutor
 */
class TAutor extends EntidadeBase
{
    /**
     * @var integer
     */
    private $fnAutorId;

    /**
     * @var string
     */
    private $fsNome;

    /**
     * @var string
     */
    private $fsNotacaoAutor;

    /**
     * @var \DateTime
     */
    private $fdInclusao;

    /**
     * @var \DateTime
     */
    private $fdAlteracao;

    /**
     * Get __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getFsNome();
    }

    /**
     * Get fnAutorId
     *
     * @return integer
     */
    public function getFnAutorId()
    {
        return $this->fnAutorId;
    }

    /**
     * Set fsNome
     *
     * @param string $fsNome
     *
     * @return TAutor
     */
    public function setFsNome($fsNome)
    {
        $this->fsNome = $fsNome;

        return $this;
    }

    /**
     * Get fsNome
     *
     * @return string
     */
    public function getFsNome()
    {
        return $this->fsNome;
    }

    /**
     * Set fsNotacaoAutor
     *
     * @param string $fsNotacaoAutor
     *
     * @return TAutor
     */
    public function setFsNotacaoAutor($fsNotacaoAutor)
    {
        $this->fsNotacaoAutor = $fsNotacaoAutor;

        return $this;
    }

    /**
     * Get fsNotacaoAutor
     *
     * @return string
     */
    public function getFsNotacaoAutor()
    {
        return $this->fsNotacaoAutor;
    }

    /**
     * Set fdInclusao
     *
     * @param \DateTime $fdInclusao
     *
     * @return TAutor
     */
    public function setFdInclusao($fdInclusao)
    {
        $this->fdInclusao = $fdInclusao;

        return $this;
    }

    /**
     * Get fdInclusao
     *
     * @return \DateTime
     */
    public function getFdInclusao()
    {
        return $this->fdInclusao;
    }

    /**
     * Set fdAlteracao
     *
     * @param \DateTime $fdAlteracao
     *
     * @return TAutor
     */
    public function setFdAlteracao($fdAlteracao)
    {
        $this->fdAlteracao = $fdAlteracao;

        return $this;
    }

    /**
     * Get fdAlteracao
     *
     * @return \DateTime
     */
    public function getFdAlteracao()
    {
        return $this->fdAlteracao;
    }
}
