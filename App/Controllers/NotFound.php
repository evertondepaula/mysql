<?php

namespace App\Controllers;

use EpClasses\Controller\Controller;

/**
 * Controller pagina Home
 *
 * @author tom
 */
class NotFound extends Controller
{
    public function index()
    {
        $this->setTitle("NotFound");
        $this->render("404", "layout");
    }
}