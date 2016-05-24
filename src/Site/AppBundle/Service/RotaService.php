<?php
namespace Site\AppBundle\Service;

class RotaService
{
    private $router;

    public function __construct(\Symfony\Bundle\FrameworkBundle\Routing\Router $router)
    {
        $this->router = $router;
    }

    public function getRotas($json = false)
    {
        $rotas = array();
        $baseUrl = $this->router->getContext()->getBaseUrl();

        foreach ($this->router->getRouteCollection()->all() as $nome => $rota) {
            if (strpos($nome, "api_") === 0 || strpos($nome, "site_") === 0) {
                $rotas[$nome] = $baseUrl . $rota->getPath();
            }
        }
        return $json ? json_encode($rotas) : $rotas;
    }
}
