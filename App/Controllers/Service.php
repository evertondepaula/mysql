<?php

namespace App\Controllers;

use EpClasses\Controller\Controller;

/**
 * Controller pagina Home
 *
 * @author tom
 */
class Service extends Controller
{
    public function getCallender()
    {
        $this->render("callender", "layout");
    }
}