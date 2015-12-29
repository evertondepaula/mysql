<?php
header('Content-Type: text/html; charset=utf-8');

require "vendor/autoload.php";

use EpClasses\DataBase\Adapter;

class Cliente extends Adapter
{
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getCliente()
    {
        $this->select(array( 'users u' => array( 'nome', 'sobrenome')))
             ->functions(array( "somar" => array("users" => array('idUsers'), array(":v1"=> "4.40")), "somar"))
             ->where("u.idUsers = :id AND u.nome LIKE :nome", array("1", "Everton"))
             ->order(array('u.nome'=> 'ASC', 'u.idUsers'))
             ->group(array('u.nome'))
             ->limit(100);
        var_dump($this->getQuery(1), $this->getQuery(2));
        var_dump($this->fetch(\PDO::FETCH_ASSOC));
        $valor = 4556.569;
        var_dump($this->procedure(array("gosoma" => 
                                  array(
                                      '?' => array(5, $valor)
                                    ) 
                                ), \PDO::FETCH_CLASS));
        var_dump($this->procedure(array("gosoma" => 
                                  array(
                                      ':v1' => 5, 
                                      ':v2' => $valor
                                       )
                                ), \PDO::FETCH_CLASS));
    }
}
$cliente = new Cliente();
$cliente->getCliente();
