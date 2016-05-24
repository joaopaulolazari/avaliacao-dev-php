<?php

namespace AppBundle\Entity;

/**
 * TMaterialLivro
 */
class TMaterialLivro extends TMaterial
{
    /**
     * @var string
     */
    private $fsIsbn;

    /**
     * @var integer
     */
    private $fnNumeroPagina;

    /**
     * @var string
     */
    private $fsResumo;


    /**
     * Set fsIsbn
     *
     * @param string $fsIsbn
     *
     * @return TMaterialLivro
     */
    public function setFsIsbn($fsIsbn)
    {
        $this->fsIsbn = $fsIsbn;

        return $this;
    }

    /**
     * Get fsIsbn
     *
     * @return string
     */
    public function getFsIsbn()
    {
        return $this->fsIsbn;
    }

    /**
     * Set fnNumeroPagina
     *
     * @param integer $fnNumeroPagina
     *
     * @return TMaterialLivro
     */
    public function setFnNumeroPagina($fnNumeroPagina)
    {
        $this->fnNumeroPagina = $fnNumeroPagina;

        return $this;
    }

    /**
     * Get fnNumeroPagina
     *
     * @return integer
     */
    public function getFnNumeroPagina()
    {
        return $this->fnNumeroPagina;
    }

    /**
     * Set fsResumo
     *
     * @param string $fsResumo
     *
     * @return TMaterialLivro
     */
    public function setFsResumo($fsResumo)
    {
        $this->fsResumo = $fsResumo;

        return $this;
    }

    /**
     * Get fsResumo
     *
     * @return string
     */
    public function getFsResumo()
    {
        return $this->fsResumo;
    }

    public function getFsTipoMaterial()
    {
        return 'livro';
    }

    public function trataCaminhoFoto()
    {
        die('aqui');
        if (strpos($this->fsCaminhoImagem, '/temp/') === false) {
            return;
        }

        if (file_exists($this->getDiretorioPublico($this->fsCaminhoImagem))) {
            $fotoExpl = explode('.', $this->fsCaminhoImagem);
            $nome = time() . '-' . substr(md5($this->fsCaminhoImagem), 5, 5);
            $caminhoTemp = $this->getDiretorioPublico($this->fsCaminhoImagem);
            $this->fsCaminhoImagem = $this->getSufixoUpload() . $nome .'.'. end($fotoExpl);

            if (!is_dir($this->getDiretorioPublico($this->getSufixoUpload()))) {
                mkdir($this->getDiretorioPublico($this->getSufixoUpload()));
            }
            rename($caminhoTemp, $this->getCaminhoCompleto());
        }
        return;
    }
}
