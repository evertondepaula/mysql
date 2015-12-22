<?php

require "vendor/autoload.php";

//function __autoload($className)
//{
//    $className = ltrim($className, '\\');
//    $fileName  = '';
//    $namespace = '';
//    if ($lastNsPos = strrpos($className, '\\')) {
//        $namespace = substr($className, 0, $lastNsPos);
//        $className = substr($className, $lastNsPos + 1);
//        $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
//    }
//    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
//    
//    require 'src/'.$fileName;
//}

//require "src/EpClasses/Interfaces/InterfaceConection.php";
//require "src/EpClasses/DataBase/Conection.php";
//require "src/EpClasses/DataBase/MySqlConection.php";
//require "src/EpClasses/DataBase/Adapter.php";
//

$a = new EpClasses\DataBase\Adapter();
var_dump($a);
