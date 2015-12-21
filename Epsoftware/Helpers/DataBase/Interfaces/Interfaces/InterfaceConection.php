<?php

namespace Epsoftware\Helpers\DataBase\Interfaces;

/**
 * <b>InterfaceConection: </b> Obrigatóridade de métodos e propriedades
 * @author tom
 */
interface InterfaceConection {
    
    abstract public function select($table, array $args = null);
    abstract public function join(array $args = null);
    abstract public function leftJoin(array $args = null);
    abstract public function rightJoin(array $args = null);
    abstract public function where(array $args = null);
    abstract public function order(array $args = null);
    abstract public function group(array $args = null);
    abstract public function limit(array $args = null);
    abstract public function insert($table, array $args = null);
    abstract public function delete($table, array $args = null);
    abstract public function update($table, array $args = null);
    abstract public function fetch($class = null, $type = null);
    abstract public function execute();
    abstract public function getStringQuery();
    abstract public function getlastInsertId();
    
}
