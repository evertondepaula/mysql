<?php

namespace EpClasses\DataBase;

use EpClasses\Helpers\Filters;

/**
 * <b>MySqlConection: </b> Esta Classe realiza os comandos em banco de dados MySql
 * @author tom
 */
class MySqlConection extends Conection
{
    /**
     * Constantes
     */
    const SQL_STRING = 1;
    const SQL_OBJECT = 2;
    
    /** @var self Utilizada para padrão singleton*/
    private static $instance;
    
    /** @var String Query montada para submissao a base de dados */
    private $query = null;
    
    /** @var Int Último Id inserido no banco de dados*/
    private $lastInsertId = null;
    
    /** @var Objeto \PDOStatement criado para execução de comandos na base de dados */
    private $stmt = null;
    
    /** @var Objeto \PDOConection criado para exercer a ponte de conexao com o bando de dados */
    private $dbInstance = null;
    
    /** @var Array  Armazena os valores que seram feitos bindValues */
    private $toPrepare = array();
    
    /** @var String contém a string sql final */
    private $select = null;
    
    /** @var Array contém as tables, entidades from da query */
    private $from = array();
    
    /** @var Array contém a string sql com todos os joins da aplicação */
    private $joins = array();
    
    /** @var Array contém a string sql com todos os havings da aplicação */
    private $having = null;
    
    /** @var String $where contém os valores de filtros where*/
    private $where = null;
    
    /** @var Array contém a configurações para order */
    private $order = array();
    
    /** @var Array contém a configurações para group */
    private $group = array();
    
    /** @var String contém a string limit */
    private $limit = null;
    
    /** @var String contém a procedure a ser executada*/
    private $procedure = null;
    
    /**
     * Evita que a classe seja instanciada publicamente.
     *
     * @return void
    */
    protected function __construct(){}
    
    /**
     * Evita que a classe seja clonada.
     *
     * @return void
    */
    private function __clone(){}

    /**
     * Método unserialize do tipo privado para prevenir a 
     * desserialização da instância dessa classe.
     *
     * @return void
    */
    private function __wakeup(){}

    /**
     * Método para iniciar instancia de banco de dados, utilizando-se do padrão singleton
     * @param Object $config Dados para conexão com o banco de dados
     */
    static public function getInstance($config)
    {
        try
        {
            if(!isset(self::$instance)):
                
                self::$instance = new self; 
            endif;
            
            if(self::$instance->dbInstance === null):
                
                $conection = new \PDO("mysql:host={$config->host};dbname={$config->dbname};port={$config->port}", $config->user, $config->password);
                self::$instance->dbInstance = $conection;
                self::$instance->dbInstance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                self::$instance->dbInstance->setAttribute(\PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8");
            endif;
            
            return self::$instance;
        }
        catch(\PDOException $ex)
        {
            echo "ERRO AO ATRIBUIR PARAMETROS E SETAR INSTÂNCIA DO BANCO DE DADOS: ".$ex->getMessage();
        }
    }
    
    /**
     * Select de dados em banco de dados MySql
     * @param Array $args Lista de de table(entidades) e campos que deveram retornar da consulta
     */
    protected function select(array $args)
    {
        try
        {
            if(is_array($args) && !empty($args)):
                
                $this->select = ($this->select === null) ? "SELECT" : $this->select. ",";
                $countTable = 1;
                foreach ($args as $table => $fields):
                    
                    $nickname = $this->makeTableFrom($table);
                    $countFields = 1;
                    foreach ($fields as $field  => $alias):
                        
                        $vrgl = (count($fields) === $countFields && count($args) === $countTable ) ? "" : ",";
                        if(is_numeric($field)):
                            
                            $this->select .= " {$nickname}.{$alias}{$vrgl}";
                        else:
                            
                            $this->select .= " {$nickname}.{$field} AS '{$alias}'{$vrgl}";
                        endif; 
                    $countFields++;  
                    endforeach;
                    $countTable++;
                endforeach;
            endif;
        }
        catch (\Exception $ex)
        {
            echo "ERRO DE CONSTRUÇÃO DE SQL(SELECT): ".$ex->getMessage();
        }
    }
    
    /**
     * Select de dados em banco de dados MySql
     * @param Array $args Lista de de table(entidades) e campos que deveram retornar da consulta
     */
    protected function functions(array $args)
    {
        try
        {
            if(is_array($args) && !empty($args)):
                
                $this->select = ($this->select === null) ? "SELECT" : $this->select . ",";
               
                $countFunctions = 1;
                $countAlias = 0;
                foreach ($args as $function => $tables):

                    if(!is_numeric($function)):
                        
                        $vrgl = ($this->select[strlen($this->select)-1] == ")") ? "," : "";
                        $this->select .= "{$vrgl} {$function}(";
                        $countTable = 1;
                        foreach ($tables as $table => $fields):

                            $countFields = 1;
                            if(!is_numeric($table)):
                                
                                $nickname = $this->makeTableFrom($table);
                                foreach ($fields as $field):

                                    $vrgl = (count($fields) === $countFields && count($tables) === $countTable ) ? "" : ",";
                                    $this->select .= "{$nickname}.{$field}{$vrgl}";
                                    $countFields++;  
                                endforeach;
                            else:
                                foreach ($fields as $bind => $value):

                                    $vrgl = (count($fields) === $countFields && count($tables) === $countTable ) ? "" : ",";
                                    $this->setToPrepare(array($bind => $value));
                                    $this->select .= "{$bind}{$vrgl}";
                                    $countFields++;  
                                endforeach;
                            endif;
                        $countTable++;
                        endforeach;
                        $this->select .= ")";
                    else:
                        
                        $vrgl = (count($args) === $countFunctions) ? "" : ",";
                        if(!empty($args[$countAlias])):
                            $this->select .= " AS '{$args[$countAlias]}'{$vrgl}";
                        else:
                            $this->select .= "{$vrgl}";
                        endif;
                        $countAlias++;
                    endif;
                    $countFunctions++;
                endforeach;
            endif;
        }
        catch (\Exception $ex)
        {
            echo "ERRO DE CONSTRUÇÃO DE SQL(SELECT P/ FUNCTIONS): ".$ex->getMessage();
        }
    }
    
    /**
     * Condição Join em banco de dados MySql
     * @param Array $args Tabela e condição de join
     * @param Array $fields Lista de campos a serem retornados da tabela join
    */
    protected function join(array $args, array $fields = null)
    {
        $this->constructJoins($args, $fields, "INNER JOIN");
    }
    
    /**
     * Condição Join em banco de dados MySql
     * @param Array $args Tabela e condição de join
     * @param Array $fields Lista de campos a serem retornados da tabela join
    */
    protected function leftJoin(array $args, array $fields = null)
    {
        $this->constructJoins($args, $fields, "LEFT JOIN");
    }
    
    /**
     * Condição Join em banco de dados MySql
     * @param Array $args Tabela e condição de join
     * @param Array $fields Lista de campos a serem retornados da tabela join
    */
    protected function rightJoin(array $args, array $fields = null)
    {
        $this->constructJoins($args, $fields, "RIGHT JOIN");
    }
    
    /**
     * Condição having em banco de dados MySql
     * @param type $terms Lista de condições having da consulta
     * @param array $parameters lista de parametros a serem utilizados
     */
    protected function having($terms, array $parameters = null)
    {
       try
        {
            if(!empty($terms)):
                
                $words = explode(" ",$terms);
                $index = 0;
                $itsLike = false;
                foreach ($words as $word):
                    
                    $posParam = strripos($word, "?");
                    $posWord = strripos($word, ":");
                    if($posWord !== false || $posParam !== false):
                        
                        $word = str_replace(array("%","'"," "),"",$word);
                        $this->setToPrepare(array( $word => ($itsLike !== true) ? $parameters[$index] : "%{$parameters[$index]}%"));
                        $index++;
                        $itsLike = false;
                    elseif(strtoupper($word) === "LIKE"):
                        $itsLike = true;
                    endif;
                    
                endforeach;
                $this->having = ($this->having === null) ? $terms : $this->having . $terms;
            endif;
        }
        catch (\Exception $ex)
        {
            echo "ERRO AO CONSTRUIR 'HAVING': ".$ex->getMessage();
        }       
    }

    /**
     * Condição where em banco de dados MySql
     * @param String $terms Lista de condições WHERE da consulta
     * @param array $parameters lista de parametros where a serem utilizados
    */
    protected function where($terms, array $parameters)
    {
        try
        {
            if(!empty($terms)):
                
                $words = explode(" ",$terms);
                $index = 0;
                $itsLike = false;
                foreach ($words as $word):
                    
                    $posParam = strripos($word, "?");
                    $posWord = strripos($word, ":");
                    if($posWord !== false || $posParam !== false):
                        
                        $word = str_replace(array("%","'"," "),"",$word);
                        $this->setToPrepare(array( $word => ($itsLike !== true) ? $parameters[$index] : "%{$parameters[$index]}%"));
                        $index++;
                        $itsLike = false;
                    elseif(strtoupper($word) === "LIKE"):
                        $itsLike = true;
                    endif;
                    
                endforeach;
                $this->where = ($this->where === null) ? $terms : $this->where . $terms;
            endif;
        }
        catch (\Exception $ex)
        {
            echo "ERRO AO CONSTRUIR 'WHERE': ".$ex->getMessage();
        }       
    }
    
    /**
     * Condição order em banco de dados MySql
     * @param array $args Lista de condições ORDER da consulta
    */
    protected function order(array $args)
    {
        try
        {
            if(!empty($args)):

                foreach ($args as $field => $value):

                    if(is_numeric($field)):
                        $field = $value;
                        $value = "ASC";
                    endif;  
                    
                    array_push($this->order, array($field => strtoupper($value)));
                endforeach;
            endif;
        }
        catch(\Exception $ex)
        {
            echo "ERRO AO CONSTRUIR 'ORDER BY': ".$ex->getMessage();
        }
    }
    
    /**
     * Condição group em banco de dados MySql
     * @param array $args Lista de condições GROUP da consulta
    */
    protected function group(array $args)
    {
        try
        {
            if(!empty($args)):
                
                foreach ($args as $field):
                    
                    array_push($this->group, $field);
                endforeach;
            endif;
        }
        catch (\Exception $ex)
        {
            echo "ERRO AO MONTAR GROUP BY: ".$ex->getMessage();
        }
    }
    
    /**
     * Condição limit em banco de dados MySql
     * @param Int $args int com LIMIT de retorno da consulta
    */
    protected function limit($args)
    {
        try
        {
            $this->limit = (int)$args;
        }
        catch (\Exception $ex)
        {
            echo "ERRO DE CONSTRUÇÃO DE SQL (LIMIT): ".$ex->getMessage();
        }
    }
    
    /**
     * Insert de dados em banco de dados MySql
     * @param type $table Tabela a ser feito insert no bando de dados
     * @param array $args Lista de campos e valores a serem inseridos
     * @return boolean true|false default false $getQueryString, Utilizada para exibir a string sql montada para o insert
     */
    protected function insert($table, array $fields, array $args, $getQueryString = false)
    {
        try
        {
            if(!empty($table) && !empty($fields) && !empty($args)):
                $queryHeader = null;
                $this->clear();
                $queryHeader = "INSERT INTO {$table}(";
                $countField = 1;
                foreach ($fields as $field):
                    
                    $vrgl = ($countField === count($fields)) ? ")" : "," ;
                    $queryHeader .= " {$table}.{$field}{$vrgl}";
                    $countField++;
                endforeach;
                
                if(count($args) == count($args, COUNT_RECURSIVE)):
                    
                    $this->select .= "{$queryHeader} VALUES (";
                    $valuesCount = 1;
                    foreach ($args as $value):
                        
                        $vrgl = ($valuesCount === count($args)) ? ")" : "," ;
                        
                        if($getQueryString):
                            $this->select .= " '{$value}'{$vrgl}";
                        else:
                            $this->setToPrepare(array('?' => $value));
                            $this->select .= " ?{$vrgl}";
                        endif;
                        $valuesCount++;
                    endforeach;
                    $this->select .= ";";
                else:
                    foreach ($args as $bind):
                        $this->select .= " {$queryHeader} VALUES (";
                        $valuesCount = 1;
                        foreach ($bind as $value):
                            
                            $vrgl = ($valuesCount === count($bind)) ? ")" : "," ;
                            
                            if($getQueryString):
                                $this->select .= " '{$value}'{$vrgl}";
                            else:
                                $this->setToPrepare(array('?' => $value));
                                $this->select .= " ?{$vrgl}";
                            endif;
                            $valuesCount++;
                        endforeach;
                        $this->select .= ";";
                    endforeach;
                endif;
                
                if($getQueryString):
                    return $this->select;
                endif;
                
                $this->stmt = $this->dbInstance->prepare($this->select);
                $this->bindValues();
                if($this->execute()):
                    
                    $this->lastInsertId = $this->dbInstance->lastInsertId();
                    $this->clear();
                    return true;
                endif;
                
                $this->clear();
                return false;
            endif;
        }
        catch(\PDOException $ex)
        {
            echo "ERRO AO TENTAR REALIZAR INSERT: ".$ex->getMessage();
        }
    }
    
    /**
     * Delete de dados em banco de dados MySql
     * @param type $table Tabela a ser feito delete no bando de dados
     * @return boolean true|false
     */
    protected function delete($table)
    {
        
    }
    
    /**
     * Update de dados em banco de dados MySql
     * @param type $table Tabela, View a ser feito insert no bando de dados
     * @param array $args Lista de campos e valores a serem feitos atualização
     * @return boolean true|false
     */
    protected function update($table, array $args = null)
    {
        
    }
    
    /**
     * Chama procedures no banco de dados
     * @param Array $arg argumentos com nome da procedure
     * @param Object $type Tipo de fetch a ser realizado
     *                    PDO::FETCH_ASSOC
     *                    PDO::FETCH_BOTH
     *                    PDO::FETCH_CLASS
     *                    e outros
     */
    protected function procedure(array $args, $type = null)
    {
        try
        {
            if(is_array($args) && !empty($args)):
                
                $this->clear();
                $this->select = "CALL";
                foreach ($args as $procedure => $fields):
                    
                    $this->select .= " {$procedure}(";
                    $countBinds = 1;
                    foreach ($fields as $value):
                    
                        $vrgl = (count($fields) === $countBinds) ? "" : ",";
                        $this->setToPrepare(array('?' => $value));
                        $this->select .= "?{$vrgl}";
                        $countBinds++;
                    endforeach;
                endforeach;
                $this->select .= ")";
                return $this->fetch($type);
            endif;
            return null;
        }
        catch (\Exception $ex)
        {
            echo "ERRO DE CONSTRUÇÃO DE SQL(PROCEDURE): ".$ex->getMessage();
        }
    }
    
    /**
     * Serialização dos valores recebido de consulta.
     * @param Object $type Tipo de fetch a ser realizado
     *                    PDO::FETCH_ASSOC
     *                    PDO::FETCH_BOTH
     *                    PDO::FETCH_CLASS
     *                    e outros
     * @param String $class Indica a qual object deseja-se transforma o retorno da consulta
     * @return Object|Array
     */
    protected function fetch($type = null , $class = null)
    {
        try
        {
            $this->query = $this->constructQueryString();
            if($this->query !== null):
                
                $callback = null;
                $this->stmt = $this->dbInstance->prepare($this->query);
                $this->bindValues();

                if($this->execute()):

                    if($type === null && $class === null):

                        $callback = $this->stmt->fetchAll();
                    elseif($class !== null && $type !== null):

                        $callback = $this->stmt->fetchAll($type, $class);
                    elseif(is_string($type) && $class === null):

                        $callback = $this->stmt->fetchAll(\PDO::FETCH_CLASS, $type);
                    else:
                        
                        $callback = $this->stmt->fetchAll($type);
                    endif;
                endif;

                $this->clear();

                return $callback;
            endif;
            return array('AVISO'=>'É preciso estabelecer o método de pesquisa novamente, após a execução do fetch() ou execute(), a consulta é deletada');
        }
        catch(\Exception $ex)
        {
            echo "ERRO AO TENTAR FAZER FETCH DOS DADOS: ".$ex->getMessage();
        }
    }
    
    /**
     * Procedimento executa as operações no banco de dados
     * @return boolean true|false
     * @throws \Exception Erro na tentiva de execução junto ao bando de dados
     */
    private function execute()
    {
        try
        {
            if($this->stmt instanceof \PDOStatement):
                
                return $this->stmt->execute();
            endif;
        }
        catch (\PDOException $e)
        {
            throw new \Exception("ERRO SMTP: ".$e->getMessage());
        }
    }
       
    /**
     * Retorna a Query formada a ser submetida a base de dados
     * @param int $operation Seleciona o tipo de retorno
     *                       ::SQL_STRING - 1 retornará uma \String com a query (default)
     *                       ::SQL_OBJECT - 2 retornará um \ArrayObject separado por procedimentos sql
     * @return \String|\ArrayObject 
     */
    protected function getQuery($operation = 1)
    {
        if($operation === self::SQL_STRING):
            
            return $this->getToPrepare($this->constructQueryString());
        endif;
        
        if($operation === self::SQL_OBJECT):
            
            return $this->constructQueryObject();
        endif;
    }
    
    /**
     * Retorna o último Id inserido
     * @return Int
     */
    protected function getLastInsertId()
    {
        return (int) $this->lastInsertId;
    }
        
    /**
     * Limpar as propriedades da Classe
     */
    protected function clear()
    {
        $this->select = null;
        
        $this->from = array();
        unset($this->from);
        
        unset($this->joins);
        $this->joins = array();
        
        $this->where = null;
        
        unset($this->group);
        $this->group = array();
        
        unset($this->having);
        $this->having = array();
        
        unset($this->order);
        $this->order = array();
        
        $this->limit = null;
        
        $this->procedure = null;
        
        $this->stmt = null;
        $this->query = null;
        
        unset($this->toPrepare);
        $this->toPrepare = array();
    }
    
    /**
     * Esta função constroe tada a query para execução no banco de dados
     * @return String Query construida para perpetuar select
     */
    private function constructQueryString()
    {
        $query = null;
        if($this->select !== null):
            
            $query = $this->select;
        
            if(!empty($this->from)):
                
                $query .= " FROM";
                $count = 1;
                foreach ($this->from as $from):

                    $vrgl = (count($this->from) === $count) ? "" : "," ;
                    $query .= " {$from['table']} {$from['nickname']}{$vrgl}";
                    $count++;
                endforeach;
            endif;
                
            
            if(!empty($this->joins)):
                
                foreach ($this->joins as $join):
                    $query .= " {$join}";
                endforeach;
            endif;
                        
            if($this->where !== null):
                
                $query .= " WHERE {$this->where}";
            endif;
            
            if(!empty($this->group)):
                $count = 1;
                $query .= " GROUP BY";
                foreach ($this->group as $field):
                    
                    $vrgl = (count($this->group) === $count) ? "" : "," ;
                    $query .= " {$field}{$vrgl}";
                    $count++;
                endforeach;
            endif;
            
             if($this->having !== null):
                
                $query .= " HAVING {$this->having}";
            endif;
            
            if(!empty($this->order)):
                
                $count = 1;
                $query .= " ORDER BY";
                    foreach ($this->order as $fields):
                        
                        foreach($fields as $field => $value):

                            $vrgl = (count($this->order) === $count) ? "" : "," ;
                            $query .= " {$field} {$value}{$vrgl}";
                            $count++;
                        endforeach;
                    endforeach;
            endif;
            
            if($this->limit !== null):
                
                $query .= " LIMIT ".$this->limit;
            endif;
        endif;
        return $query;
    }
    
    /**
     * Esta função constroe tada a query para execução no banco de dados
     * @return String Query construida para perpetuar select
     */
    private function constructQueryObject()
    {
        $object = new \ArrayObject();
        if($this->select !== null):
            
            $object['SELECT'] = str_replace("SELECT ", "", $this->select);
            
            if(!empty($this->from)):
                
                $count = 1;
                $arrayFrom = array();
                foreach ($this->from as $from):

                    $vrgl = (count($this->from) === $count) ? "" : "," ;
                    array_push($arrayFrom,"{$from['table']} {$from['nickname']}");
                    $count++;
                endforeach;
                $object['FROM'] = $arrayFrom;
            endif;
            
            if(!empty($this->joins)):
                
                $object['JOIN'] = $this->joins;
            endif;
            
            if($this->where !== null):
                
                $object['WHERE'] = $this->where;
            endif;
            
            if(!empty($this->group)):
                
                $object['GROUP BY'] = $this->group;
            endif;
            
             if($this->having !== null):
                
                $object['HAVING'] = $this->having;
            endif;
            
            if(!empty($this->order)):
                
                $object['ORDER BY'] = $this->order;
            endif;
            
            if($this->limit !== null):
                
                $object['LIMIT'] = $this->limit;
            endif;
            
            if(!empty($this->toPrepare)):
                
                $bind = array();
                foreach ($this->toPrepare as $prepare):
                    
                    array_push($bind, $prepare);
                endforeach;
                $object['BIND'] = $bind;
            endif;
            
        endif;
        return $object;
    }
    
    /**
     * submitNickname (apelidos) a serem usados nas entidades objetos de pesquisa
     * @param String $table Nome da tabela (entidade)
     * @return String
     */
    private function setFrom($table)
    {
        $find = false;
        while(!$find):
            
            if(empty($this->getFrom($table))):
                
                $table = explode(" ",$table);
                array_push($this->from, array('table' => $table[0], 'nickname' => (isset($table[1])) ? $table[1] : null));
                $find = true;
            endif;
        endwhile;
    }
    
    /**
     * Recupera o nickname da tabela dos apelidos contanstes no array $this->$nickname
     * @param String $tableName Nome da tabela a ser resgatado o nickname, apelido
     * @return Array->ArrayObject com nickname da tabela na consulta
     */
    private function getFrom($table)
    {
        $filter = new Filters\FilterArrayObject(new \ArrayObject($this->from), new \ArrayIterator(array('table')), $table);
        return $filter->getObjFiltered();
    }
    
    /**
     * Set os valores para serem feitos os  BindValues
     * @param Array $args array(':campo' => 'valor')
     */
    private function setToPrepare(array $args)
    {
        if(!empty($args)):
            
            foreach ($args as $prepare => $value):

                array_push($this->toPrepare, array($prepare => $value));
            endforeach;
        endif;
    }
    
    /**
     * Retorna uma string sem os marcadores bindValue (Ex.: :campos), ao invés disso retorna seus valores
     * @param String $query String a ser convertida para substituição de dos :campos por seus valores
     * @return String $query String tratada para exibição
     */
    private function getToPrepare($query)
    {
        if(!empty($query) && !empty($this->toPrepare)):
            
            foreach ($this->toPrepare as $prepare):
                
                foreach ($prepare as $bind => $value):
                    
                    if($bind === "?"):
                        
                        $query = preg_replace("/\\{$bind}/", ($value[0] === "%") ? "'{$value}'": $value , $query,1);
                    else:
                        
                        $query = str_replace($bind, ($value[0] === "%") ? "'{$value}'": $value, $query);
                    endif;
                endforeach;
            endforeach;
        endif;
        
        return $query;
    }
    
    /**
     * Função registras os values para serem realizados no banco de dados
    */
    private function bindValues()
    {
        try
        {
            if(!empty($this->toPrepare) && $this->stmt instanceof \PDOStatement):
            
                $param = 0;
                foreach ($this->toPrepare as $key => $binds):
                        
                    foreach ($binds as $bind => $value):
                
                        $param++;
                        if($bind === "?"):
                            
                            $this->stmt->bindValue($param, $value, \PDO::PARAM_STR|\PDO::PARAM_INPUT_OUTPUT);
                        else:
                            
                            $this->stmt->bindValue($bind , $value, \PDO::PARAM_STR|\PDO::PARAM_INPUT_OUTPUT);
                        endif;
                    endforeach;
                endforeach;
            endif;
        } 
        catch(\PDOException $ex) {
            echo "ERRO AO REALIZAR OS BINDVALUE: ".$ex->getMessage();
        }
    }
    
    /**
     * Set and Get de novas tabelas
     * @param String $table String com nome da tabela
     * @return String $nickname O retorno será o nick da tabela ou o proprio nome da tabela
     */
    private function makeTableFrom($table)
    {
        $tableExplode = explode(" ", $table);
        $nickname = $this->getFrom($tableExplode[0]);
        if(empty($nickname)):

            $this->setFrom($table);
            $nickname = $this->getFrom($tableExplode[0]);
        endif;
        return ($nickname[0]['nickname'] !== null) ? $nickname[0]['nickname'] : $tableExplode[0];  
    }
    
    /**
     * Set and Get de novas tabelas
     * @param String $table String com nome da tabela
     * @return String $nickname O retorno será o nick da tabela ou o proprio nome da tabela
     */
    private function makeTableJoin($table)
    {
        $tableExplode = explode(" ", $table);
        return (isset($tableExplode[1]) && $tableExplode[1] !== null) ? $tableExplode[1] : $tableExplode[0];  
    }
    
    /**
     * Este método cria, de acordo com os parametros, e seta na variável $this->joins os JOINS no que serão realizados na pesquisa
     * @param array $args Array com tabela e condição para criação de joins
     * @param array $fields Array com campos que serão retornados da tabela join
     * @param type $type Tipo de JOIN podendo ser <b>INNER JOIN, LEFT JOIN ou RIGHT JOIN</b>
     */
    private function constructJoins(array $args, array $fields = null, $type)
    {
        try
        {
            if(!empty($args)):
                
                $nickname = null;
                foreach ($args as $table => $conditions):
                    
                    $nickname = $this->makeTableJoin($table);
                    $sintaxe = "{$type} {$table} ON";
                    $countConditions = 1;
                    foreach ($conditions as $condition):
                        
                        $and = (count($conditions) !== $countConditions) ? " AND" : "";
                        $sintaxe .= " {$condition}{$and}";
                        $countConditions++;
                    endforeach;
                    array_push($this->joins, $sintaxe);
                    break;
                endforeach;
                $this->select = ($this->select === null) ? "SELECT" : $this->select . ",";
                if($fields !== null && !empty($fields)):
                    
                    $countFields = 1;
                    foreach ($fields as $field => $alias):

                        $vrgl = (count($fields) === $countFields) ? "" : ",";
                        if(is_numeric($field)):
                            
                            $this->select .= " {$nickname}.{$alias}{$vrgl}";
                        else:
                            
                            $this->select .= " {$nickname}.{$field} AS '{$alias}'{$vrgl}";
                        endif; 
                        $countFields++;  
                    endforeach;
                else:
                    $this->select .= " {$nickname}.*";
                endif;
            endif;
        }
        catch(\Exception $ex)
        {
            "ERRO DE CONSTRUÇÃO DE SQL({$type}): ".$ex->getMessage();
        }
    }
}