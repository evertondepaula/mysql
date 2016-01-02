<?php

namespace App\Controllers;

use EpClasses\Controller\Controller;
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
       $this->render("index", "layout");
    }
    
}