<?php

namespace AppBundle\Entity;

/**
 * TMaterialDicionario
 */
class TMaterialDicionario extends TMaterial
{
    /**
     * @var string
     */
    private $fsEdicao;

    /**
     * @var string
     */
    private $fsClassificacao;


    /**
     * Set fsEdicao
     *
     * @param string $fsEdicao
     *
     * @return TMaterialDicionario
     */
    public function setFsEdicao($fsEdicao)
    {
        $this->fsEdicao = $fsEdicao;

        return $this;
    }

    /**
     * Get fsEdicao
     *
     * @return string
     */
    public function getFsEdicao()
    {
        return $this->fsEdicao;
    }

    /**
     * Set fsClassificacao
     *
     * @param string $fsClassificacao
     *
     * @return TMaterialDicionario
     */
    public function setFsClassificacao($fsClassificacao)
    {
        $this->fsClassificacao = $fsClassificacao;

        return $this;
    }

    /**
     * Get fsClassificacao
     *
     * @return string
     */
    public function getFsClassificacao()
    {
        return $this->fsClassificacao;
    }

    public function getFsTipoMaterial()
    {
        return 'dicionario';
    }
}
