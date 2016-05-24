<?php

namespace API\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use FOS\RestBundle\Controller\Annotations as FOS;

class MaterialDicionarioController extends Controller
{
    /**
     * @FOS\Get("/", name="api_app_material_dicionarios", options={"method_prefix" = false })
     * @FOS\View(statusCode=200, serializerEnableMaxDepthChecks=true, serializerGroups={"Default", "listagem"})
     */
    public function buscarMaterialDicionariosAction()
    {
        $servico = $this->get('api.app.material.material_dicionario');
        $materialDicionarios = $servico->buscarMaterialDicionarios();

        return array(
            'dados' => array(
                'materialDicionarios' => $materialDicionarios,
            )
        );
    }

    /**
     * @FOS\Get("/{id}", name="api_app_material_dicionario", options={"method_prefix" = false })
     * @FOS\View(statusCode=200, serializerEnableMaxDepthChecks=true, serializerGroups={"Default", "form"})
     */
    public function buscarMaterialDicionarioAction($id)
    {
        $servico = $this->get('api.app.material.material_dicionario');
        $materialDicionario = $servico->buscarMaterialDicionario($id);

        return array(
            'dados' => array(
               'materialDicionario' => $materialDicionario,
            )
        );
    }

    /**
     * @FOS\Post("/", name="api_app_material_dicionario_cadastrar", options={"method_prefix" = false })
     * @FOS\View(statusCode=201, serializerEnableMaxDepthChecks=true, serializerGroups={"Default", "form"})
     * @param $request
     * @return mixed
     */
    public function cadastrarMaterialDicionarioAction(Request $request)
    {
        $servico = $this->get('api.app.material.material_dicionario');
        $materialDicionario = $servico->cadastrar($request);

        return array(
            'mensagem' => 'Registro salvo com sucesso!',
            'dados'=> array(
                'materialDicionario' => $materialDicionario,
            )
        );
    }

    /**
     * @FOS\Put("/{id}", name="api_app_material_dicionario_atualizar", options={"method_prefix" = false })
     * @FOS\View(statusCode=200, serializerEnableMaxDepthChecks=true, serializerGroups={"Default", "form"})
     * @param $request
     * @return mixed
     */
    public function atualizarMaterialDicionarioAction(Request $request, $id)
    {
        $servico = $this->get('api.app.material.material_dicionario');
        $materialDicionario = $servico->buscarMaterialDicionario($id);
        $materialDicionario = $servico->editar($materialDicionario, $request);

        return array(
            'mensagem' => 'Registro salvo com sucesso!',
            'dados'=> array(
                'materialDicionario' => $materialDicionario,
            )
        );
    }

    /**
     * @FOS\Delete("/{id}", name="api_app_material_dicionario_remover", options={"method_prefix" = false })
     * @FOS\View(statusCode=200, serializerEnableMaxDepthChecks=true, serializerGroups={"Default", "form"})
     */
    public function removerMaterialDicionarioAction($id)
    {
        $servico = $this->get('api.app.material.material_dicionario');
        $materialDicionario = $servico->buscarMaterialDicionario($id);

        try {
            $servico->remover($materialDicionario);
        } catch (Exception $e) {
            throw new HttpException(Codes::HTTP_INTERNAL_SERVER_ERROR, 'Erro ao remover registro.');
        }

        return array(
            'mensagem' => 'Registro removido com sucesso!',
            'dados' => array(),
        );
    }
}
