<?php

namespace App;

use EpClasses\Routes\Bootstrap;

/**
 * Utilizada para inicializar sistemas de rotas e segurança
 *
 * @author tom
 */
class Init extends Bootstrap
{
    /**
     * Função obrigatório para definição das rotas do sistema
     */
    protected function initRoutes()
    {
        $routes['home'] = ['route' => '/', 'controller' => 'home', 'action' => 'index'];
        $this->setRoutes($routes);
    }
}