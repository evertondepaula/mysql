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
    public function __construct($title) {
        parent::__construct($title);
    }
    
    public function index()
    {
        $user = new User();
        $this->view = $user->getUsers();
        $this->render("index", "layout");
    }
}