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
    
    abstract protected function initRoutes();
    
    protected function run($url)
    {
        array_walk($this->routes, function($route) use($url){
            if($url === $route['route']):
                $class = "App\\Controllers\\".ucfirst($route['controller']);
                $controller = new $class;
                return $controller->$route['action']();
            endif;
        });
        $controller = new \App\Controllers\NotFound;
        return $controller->index();
    }
    
    protected function setRoutes(array $routes)
    {
        $this->routes = $routes;
    }
    
    
    protected function getUrl()
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }
}