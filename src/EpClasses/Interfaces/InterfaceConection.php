<?php

namespace EpClasses\Interfaces;

/**
 * <b>InterfaceConection: </b> Obrigatóridade de métodos para estrutura de conexão e manipulação de dados ao banco de dados
 * @author tom
 */
interface InterfaceConection
{
    public function select($table, array $args = null);
    public function join(array $args);
    public function leftJoin(array $args);
    public function rightJoin(array $args);
    public function where(array $args);
    public function order(array $args);
    public function group(array $args);
    public function limit($args);
    public function insert($table, array $args);
    public function delete($table);
    public function update($table, array $args);
    public function fetch($type = null, $class = null);
    public function execute();
    public function getStringQuery();
    public function getlastInsertId();
}