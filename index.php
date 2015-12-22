<?php
header('Content-Type: text/html; charset=utf-8');

require "vendor/autoload.php";

$a = new \EpClasses\DataBase\Adapter();

$stdClass = $a->select("users")
              ->fetch(\PDO::FETCH_CLASS);

var_dump($stdClass);