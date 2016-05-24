<?php

namespace API\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use FOS\RestBundle\Controller\Annotations as FOS;

class AutorController extends Controller
{
    /**
     * @FOS\Get("/", name="api_app_autor_buscar_todos", options={"method_prefix" = false })
     * @FOS\View(statusCode=200, serializerEnableMaxDepthChecks=true, serializerGroups={"Default", "listagem"})
     */
    public function buscarTodosAutoresAction()
    {
        $servico = $this->get('api.app.autor');
        $autores = $servico->buscarTodosAutores();

        return array(
            'dados' => array(
                'autores' => $autores,
            )
        );
    }

    /**
     * @FOS\Get("/buscar", name="api_app_autor_buscar", options={"method_prefix" = false })
     * @FOS\View(statusCode=200, serializerEnableMaxDepthChecks=true, serializerGroups={"Default", "listagem"})
     */
    public function buscarAutoresAction(Request $request)
    {
        $servico = $this->get('api.app.autor');
        $autores = $servico->buscarAutores($request);

        return array(
            'dados' => array(
                'autores' => $autores,
            )
        );
    }

    /**
     * @FOS\Get("/{id}", name="api_app_autor", options={"method_prefix" = false })
     * @FOS\View(statusCode=200, serializerEnableMaxDepthChecks=true, serializerGroups={"Default", "form"})
     */
    public function buscarAutorAction($id)
    {
        $servico = $this->get('api.app.autor');
        $autor = $servico->buscarAutor($id);

        return array(
            'dados' => array(
                'autor' => $autor,
            )
        );
    }

    /**
     * @FOS\Post("/", name="api_app_autor_cadastrar", options={"method_prefix" = false })
     * @FOS\View(statusCode=201, serializerEnableMaxDepthChecks=true, serializerGroups={"Default", "form"})
     * @param $request
     * @return mixed
     */
    public function cadastrarUsuarioAction(Request $request)
    {
        $servico = $this->get('api.app.autor');
        $autor = $servico->cadastrar($request);

        return array(
            'mensagem' => 'Registro salvo com sucesso!',
            'dados'=> array(
                'autor' => $autor,
            )
        );
    }

    /**
     * @FOS\Put("/{id}", name="api_app_autor_atualizar", options={"method_prefix" = false })
     * @FOS\View(statusCode=200, serializerEnableMaxDepthChecks=true, serializerGroups={"Default", "form"})
     * @param $request
     * @return mixed
     */
    public function atualizarUsuarioAction(Request $request, $id)
    {
        $servico = $this->get('api.app.autor');
        $autor = $servico->buscarAutor($id);
        $autor = $servico->editar($autor, $request);

        return array(
            'mensagem' => 'Registro salvo com sucesso!',
            'dados'=> array(
                'autor' => $autor,
            )
        );
    }

    /**
     * @FOS\Delete("/{id}", name="api_app_autor_remover", options={"method_prefix" = false })
     * @FOS\View(statusCode=200, serializerEnableMaxDepthChecks=true, serializerGroups={"Default", "form"})
     */
    public function removerUsuarioAction($id)
    {
        $servico = $this->get('api.app.autor');
        $autor = $servico->buscarAutor($id);

        try {
            $servico->remover($autor);
        } catch (Exception $e) {
            throw new HttpException(Codes::HTTP_INTERNAL_SERVER_ERROR, 'Erro ao remover registro.');
        }

        return array(
            'mensagem' => 'Registro removido com sucesso!',
            'dados' => array(),
        );


    }
}
