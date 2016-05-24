<?php

namespace API\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use FOS\RestBundle\Controller\Annotations as FOS;

class MaterialLivroController extends Controller
{
    /**
     * @FOS\Get("/", name="api_app_material_livros", options={"method_prefix" = false })
     * @FOS\View(statusCode=200, serializerEnableMaxDepthChecks=true, serializerGroups={"Default", "listagem"})
     */
    public function buscarMaterialLivrosAction()
    {
        $servico = $this->get('api.app.material.material_livro');
        $materialLivros = $servico->buscarMateriais();

        return array(
            'dados' => array(
                'materialLivros' => $materialLivros,
            )
        );
    }

    /**
     * @FOS\Get("/{id}", name="api_app_material_livro", options={"method_prefix" = false })
     * @FOS\View(statusCode=200, serializerEnableMaxDepthChecks=true, serializerGroups={"Default", "form"})
     */
    public function buscarMaterialLivroAction($id)
    {
        $servico = $this->get('api.app.material.material_livro');
        $materialLivro = $servico->buscarMaterial($id);

        return array(
            'dados' => array(
                'materialLivro' => $materialLivro,
            )
        );
    }

    /**
     * @FOS\Post("/", name="api_app_material_livro_cadastrar", options={"method_prefix" = false })
     * @FOS\View(statusCode=201, serializerEnableMaxDepthChecks=true, serializerGroups={"Default", "form"})
     * @param $request
     * @return mixed
     */
    public function cadastrarMaterialLivroAction(Request $request)
    {
        $servico = $this->get('api.app.material.material_livro');
        $materialLivro = $servico->cadastrar($request);

        return array(
            'mensagem' => 'Registro salvo com sucesso!',
            'dados'=> array(
                'materialLivro' => $materialLivro,
            )
        );
    }

    /**
     * @FOS\Put("/{id}", name="api_app_material_livro_atualizar", options={"method_prefix" = false })
     * @FOS\View(statusCode=200, serializerEnableMaxDepthChecks=true, serializerGroups={"Default", "form"})
     * @param $request
     * @return mixed
     */
    public function atualizarMaterialLivroAction(Request $request, $id)
    {
        $servico = $this->get('api.app.material.material_livro');
        $materialLivro = $servico->buscarMaterial($id);
        $materialLivro = $servico->editar($materialLivro, $request);

        return array(
            'mensagem' => 'Registro salvo com sucesso!',
            'dados'=> array(
                'materialLivro' => $materialLivro,
            )
        );
    }

    /**
     * @FOS\Delete("/{id}", name="api_app_material_livro_remover", options={"method_prefix" = false })
     * @FOS\View(statusCode=200, serializerEnableMaxDepthChecks=true, serializerGroups={"Default", "form"})
     */
    public function removerMaterialLivroAction($id)
    {
        $servico = $this->get('api.app.material.material_livro');
        $materialLivro = $servico->buscarMaterial($id);

        try {
            $servico->remover($materialLivro);
        } catch (Exception $e) {
            throw new HttpException(Codes::HTTP_INTERNAL_SERVER_ERROR, 'Erro ao remover registro.');
        }

        return array(
            'mensagem' => 'Registro removido com sucesso!',
            'dados' => array(),
        );
    }
}
