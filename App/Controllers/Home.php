<?php

namespace App\Controllers;

use EpClasses\Controller\Controller;
use App\Model\User;

/**
 * Controller pagina Home
 *
 * @author tom
 */
class Home extends Controller
{
    public function index()
    {
        $this->setTitle("Home");
        $user = new User();
        $this->view->id = $user->newUser();
        $this->render("index", "layout");
    }
}