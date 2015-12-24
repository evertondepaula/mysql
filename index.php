<?php
header('Content-Type: text/html; charset=utf-8');

require "vendor/autoload.php";

use EpClasses\DataBase\Adapter;
use EpClasses\DataBase\MySqlConection;

$a = new Adapter();

$a->functions(array( "functionName1" => array( "tabela1" => array( "parametro1", "parametro2" ), "tabela2" => array( "parametro3", "parametro4" ), array("parametro_sem_tabela") ), "functionName2" => array( "tabela1" => array( "parametro1", "parametro2" ), "tabela2" => array( "parametro3", "parametro4" ), array("parametro_sem_tabela2") ), "alias" ));
$a->limit(2);
var_dump($a->getQuery(MySqlConection::SQL_OBJECT));