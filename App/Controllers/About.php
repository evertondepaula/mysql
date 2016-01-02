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
        $this->setTitle("About");
        $user = new User();
        $this->view = $user->getUsers();
        $this->render("index", "layout");
    }
}