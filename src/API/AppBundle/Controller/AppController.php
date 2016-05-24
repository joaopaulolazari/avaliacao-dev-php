<?php

namespace API\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use FOS\RestBundle\Controller\Annotations as FOS;
use FOS\RestBundle\Util\Codes;

class AppController extends Controller
{

    /**
     * @FOS\Post("/upload", name="api_app_upload", options={"method_prefix" = false })
     * @FOS\View(statusCode=200)
     */
    public function uploadAction(Request $request)
    {
        $arquivos = $request->files->all();

        if (empty($arquivos)) {
            throw new HttpException(Codes::HTTP_PRECONDITION_FAILED, 'Nenhum arquivo foi enviado.');
        }

        $servico = $this->get('api.app.app');
        $caminhoArquivos = $servico->upload($arquivos);

        return array(
            'dados' => $caminhoArquivos,
        );
    }

    /**
     * @FOS\Post("/validar_formulario", name="api_app_validar_formulario", options={"method_prefix" = false })
     * @FOS\View(statusCode=201, serializerEnableMaxDepthChecks=true, serializerGroups={"Default", "form"})
     * @param $request
     * @return mixed
     */
    public function validarFormularioAction(Request $request)
    {
        if (is_null($request->get('dados')) ||
            is_null($request->get('bundle')) ||
            is_null($request->get('entidade'))
        ) {
            throw new HttpException(Codes::HTTP_PRECONDITION_FAILED, 'Nenhum dado foi enviado para validação');
        }

        $servico = $this->get('api.app.app');
        $erros = $servico->validaFormulario($request->get('dados'), $request->get('bundle'), $request->get('entidade'));


        return array(
            'status' => (empty($erros)) ? Codes::HTTP_OK: Codes::HTTP_PRECONDITION_FAILED,
            'mensagem' => (empty($erros)) ? "Formulário Válido": "Formulário Inválido",
            'dados' => array(
                'erros' => $erros
            )
        );
    }

     /**
     * @FOS\Post("/validar_propriedade", name="api_app_validar_propriedade", options={"method_prefix" = false })
     * @FOS\View(statusCode=201, serializerEnableMaxDepthChecks=true, serializerGroups={"Default", "form"})
     * @param $request
     * @return mixed
     */

    public function validarPropriedadeAction(Request $request)
    {
        if (is_null($request->get('dados')) ||
            is_null($request->get('bundle')) ||
            is_null($request->get('entidade')) ||
            is_null($request->get('propriedade'))
        ) {
            throw new HttpException(Codes::HTTP_PRECONDITION_FAILED, 'Nenhum dado foi enviado para validação');
        }

        $servico = $this->get('api.app.app');
        $erros = $servico->validaPropriedade($request->get('dados'), $request->get('bundle'), $request->get('entidade'), $request->get('propriedade'));

        return array(
            'status' => (empty($erros)) ? Codes::HTTP_OK: Codes::HTTP_PRECONDITION_FAILED,
            'mensagem' => (empty($erros)) ? "Propriedade Válida": "Propriedade Inválida",
            'dados' => array(
                'erros' => $erros
            )
        );
    }
}
