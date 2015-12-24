<?php
header('Content-Type: text/html; charset=utf-8');

require "vendor/autoload.php";

$a = new \EpClasses\DataBase\Adapter();

$a->functions(array( "functionName1" => array( "tabela1" => array( "parametro1", "parametro2" ), "tabela2" => array( "parametro3", "parametro4" ), array("parametro_sem_tabela") ), "functionName2" => array( "tabela1" => array( "parametro1", "parametro2" ), "tabela2" => array( "parametro3", "parametro4" ), array("parametro_sem_tabela") ), "alias" ));
echo $a->getStringQuery();