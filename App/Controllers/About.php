<?php

namespace App\Controllers;

use EpClasses\Controller\Controller;
use App\Model\User;

/**
 * Controller pagina Home
 *
 * @author tom
 */
class About extends Controller
{
    public function index()
    {
        $data = $this->postAll();
        $this->setTitle("About");
        $this->view->nome = $data['nome'];
        $this->render("index", "layout");
    }
}