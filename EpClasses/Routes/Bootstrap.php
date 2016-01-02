<?php

namespace EpClasses\Routes;

/**
 * Recursos para inicialização de aplicativo web
 * Responsável por padronizar sistema de rotas e segurança
 *
 * @author tom
 */
abstract class Bootstrap
{
    private $routes;
    
    public function __construct()
    {
        $this->initRoutes();
        $this->run($this->getUrl());
    }
    
    //Método obrigatorio para inicialização das rotas
    abstract protected function initRoutes();
    
    /**
     * Executa o controller e action da rota
     * @param type $url
     */
    protected function run($url)
    {
        $find = false;
        foreach ($this->routes as $route):
            if($url === $route['route']):
                $class = "App\\Controllers\\".ucfirst($route['controller']);
                $controller = new $class;
                $controller->$route['action']();
                $find = true;
            endif;
        endforeach;
        
        if($find === false):
            $controller = new \App\Controllers\NotFound;
            $controller->index();
        endif;
    }
    
    /**
     * Seta todas as rotas
     * @param array $routes
     */
    protected function setRoutes(array $routes)
    {
        $this->routes = $routes;
    }
        
    /**
     * retorna a rota solicitada
     * @return type
     */
    protected function getUrl()
    {
        return parse_url( filter_input(INPUT_SERVER, "REQUEST_URI"), PHP_URL_PATH);
    }
}