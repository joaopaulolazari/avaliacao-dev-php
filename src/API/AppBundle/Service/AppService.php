<?php
namespace API\AppBundle\Service;

use API\AppBundle\Service\ServicoBaseService;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AppService extends ServicoBaseService
{
    /**
     * @var string
     *
     * $kernelRootDir ditetório do kernel
    */
    protected $kernelRootDir = '';

    /**
     * @var \Symfony\Component\Form\FormFactory
     *
    */
    protected $formFactory;

    /**
     * Construtor
     * @param string $kernelRootDir
     * @param \Symfony\Component\Form\FormFactory $formFactory
    */
    public function __construct(
        $kernelRootDir,
        \Symfony\Component\Form\FormFactory $formFactory
    ) {
        $this->formFactory = $formFactory;
        $this->kernelRootDir = $kernelRootDir;
    }

    /**
     * Realiza upload de miltiplos arquivos
     * @param array array contendo caminhos de arquivos
     *
     * @return array caminhos de arquivos movidos para pasta temporária
     */
    public function upload($arquivos)
    {
        $caminhoArquivos = array();
        foreach ($arquivos as $arquivo) {
            //Pega extensão do arquivo
            $ext = explode('.', $arquivo->getClientOriginalName());
            $ext = strtolower(is_array($ext) ? end($ext) : '');
            //Cria nome temporário e move para pasta temporária
            $nomeTemp = $arquivo->getFilename() . '.' . $ext;
            $arquivo = $arquivo->move($this->getDiretorioPublico($this->getSufixoUpload('temp')), $nomeTemp);
            //Adiciona ao array para retornar o caminho temporário
            $caminhoArquivos[] = $this->getSufixoUpload('temp') . '/' . $arquivo->getFileName();
        }

        return $caminhoArquivos;
    }

    /**
     * Retorna o caminho absoluto da pasta pública
     * @param string sufixo para caminho do diretório público
     *
     * @return string caminho público do arquivo
     */
    private function getDiretorioPublico($sufixo = '')
    {
        return $this->kernelRootDir . '/../web/' . ltrim($sufixo, '/');
    }

    /**
     * Retorna o sufixo do caminho da pasta de upload
     * @param string string sufixo para caminho do diretório público
     *
     * @return string contem o sufico do diretório de uploads
     */
    private function getSufixoUpload($sufixo = '')
    {
        return '/uploads/' . ltrim($sufixo, '/');
    }
}
