<?php

namespace AppBundle\Entity;

use AppBundle\Entity\EntidadeBase;

/**
 * TMaterial
 */
abstract class TMaterial extends EntidadeBase
{
    /**
     * @var integer
     */
    private $fnMaterialId;

    /**
     * @var string
     */
    private $fsTitulo;

    /**
     * @var string
     */
    private $fsSubtitulo;

    /**
     * @var string
     */
    private $fsCaminhoImagem;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $autores;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->autores = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get fnMaterialId
     *
     * @return integer
     */
    public function getFnMaterialId()
    {
        return $this->fnMaterialId;
    }

    /**
     * Set fsTitulo
     *
     * @param string $fsTitulo
     *
     * @return TMaterial
     */
    public function setFsTitulo($fsTitulo)
    {
        $this->fsTitulo = $fsTitulo;

        return $this;
    }

    /**
     * Get fsTitulo
     *
     * @return string
     */
    public function getFsTitulo()
    {
        return $this->fsTitulo;
    }

    /**
     * Set fsSubtitulo
     *
     * @param string $fsSubtitulo
     *
     * @return TMaterial
     */
    public function setFsSubtitulo($fsSubtitulo)
    {
        $this->fsSubtitulo = $fsSubtitulo;

        return $this;
    }

    /**
     * Get fsSubtitulo
     *
     * @return string
     */
    public function getFsSubtitulo()
    {
        return $this->fsSubtitulo;
    }

    /**
     * Set fsCaminhoImagem
     *
     * @param string $fsCaminhoImagem
     *
     * @return TMaterial
     */
    public function setFsCaminhoImagem($fsCaminhoImagem)
    {
        $this->fsCaminhoImagem = $fsCaminhoImagem;

        return $this;
    }

    /**
     * Get fsCaminhoImagem
     *
     * @return string
     */
    public function getFsCaminhoImagem()
    {
        return $this->fsCaminhoImagem;
    }

    /**
     * Add autore
     *
     * @param \AppBundle\Entity\TAutor $autore
     *
     * @return TMaterial
     */
    public function addAutore(\AppBundle\Entity\TAutor $autore)
    {
        $this->autores[] = $autore;

        return $this;
    }

    /**
     * Remove autore
     *
     * @param \AppBundle\Entity\TAutor $autore
     */
    public function removeAutore(\AppBundle\Entity\TAutor $autore)
    {
        $this->autores->removeElement($autore);
    }

    /**
     * Get autores
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAutores()
    {
        return $this->autores;
    }

    public function getAutoresId()
    {
        return $this->autores->map(
            function ($autor) {
                return $autor->getFnAutorId();
            }
        );
    }
}
