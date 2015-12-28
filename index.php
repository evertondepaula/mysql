<?php
header('Content-Type: text/html; charset=utf-8');

require "vendor/autoload.php";

use EpClasses\DataBase\Adapter;
use EpClasses\DataBase\MySqlConection;


class Cliente extends Adapter
{
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getCliente()
    {
        $this->select(array( 'users u' => array( '*')))
             ->where("u.nome = ? AND u.sobrenome = ?", array('everton', 'paula'))
             ->limit(2);
        var_dump($this->getQuery(MySqlConection::SQL_OBJECT));
    }
}

$cliente = new Cliente();
$cliente->getCliente();