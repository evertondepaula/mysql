<?php

namespace EpClasses\DataBase;

use EpClasses\Helpers\Filters;

/**
 * <b>MySqlConection: </b> Esta Classe realiza os comandos em banco de dados MySql
 * @author tom
 */
class MySqlConection extends Conection
{
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
     * @return void
    */
   protected function __construct(){}
    
    /**
     * Evita que a classe seja clonada.
     * @return void
    */
    private function __clone(){}

    /**
     * Método unserialize do tipo privado para prevenir a 
     * desserialização da instância dessa classe.
     * @return void
    */
    private function __wakeup(){}

    /**
     * Método para iniciar instancia de banco de dados, utilizando-se do padrão singleton
     * @param Object $config Dados para conexão com o banco de dados
     * @throws \PDOException
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
            throw new \PDOException("ERRO AO ATRIBUIR PARAMETROS E SETAR INSTÂNCIA DO BANCO DE DADOS: ".$ex->getMessage());
        }
    }
    
    /**
     * Select de dados em banco de dados MySql
     * @param Array $args Lista de de table(entidades) e campos que deveram retornar da consulta
     * @throws \Exception
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
            else:
                
                throw new \Exception('->select(array $args) obrigatório parametrização.');
            endif;
        }
        catch (\Exception $ex)
        {
            throw new \Exception("ERRO DE CONSTRUÇÃO DE SQL(SELECT): ".$ex->getMessage());
        }
    }
    
    /**
     * Select de dados em banco de dados MySql
     * @param Array $args Lista de de table(entidades) e campos que deveram retornar da consulta
     * @throws \Exception
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
            else:
                
                throw new \Exception('->functions(array $args) obrigatório parametrização.');
            endif;
        }
        catch (\Exception $ex)
        {
            throw new \Exception("ERRO DE CONSTRUÇÃO DE SQL(SELECT P/ FUNCTIONS): ".$ex->getMessage());
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
     * @throws \Exception
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
                
            else:
                
                throw new \Exception('->having($terms) obrigatório parametrização');
            endif;
        }
        catch (\Exception $ex)
        {
            throw new \Exception("ERRO AO CONSTRUIR 'HAVING': ".$ex->getMessage());
        }       
    }

    /**
     * Condição where em banco de dados MySql
     * @param String $terms Lista de condições WHERE da consulta
     * @param array $parameters lista de parametros where a serem utilizados
     * @throws \Exception
    */
    protected function where($terms, array $parameters = null)
    {
        try
        {
            if(!empty($terms)):
                
                if($parameters !== null):
                    
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
                endif;
                
                $this->where = ($this->where === null) ? $terms : $this->where . $terms;
            else:
                
                throw new \Exception('->where($terms) obrigatório parametrização');
            endif;
        }
        catch (\Exception $ex)
        {
            throw new \Exception("ERRO AO CONSTRUIR 'WHERE': ".$ex->getMessage());
        }       
    }
    
    /**
     * Condição order em banco de dados MySql
     * @param array $args Lista de condições ORDER da consulta
     * @throws \Exception
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
            else:
                
               throw new \Exception('->order($args) obrigatório parametrização');
            endif;
        }
        catch(\Exception $ex)
        {
            throw new \Exception("ERRO AO CONSTRUIR 'ORDER BY': ".$ex->getMessage());
        }
    }
    
    /**
     * Condição group em banco de dados MySql
     * @param array $args Lista de condições GROUP da consulta
     * @throws \Exception
    */
    protected function group(array $args)
    {
        try
        {
            if(!empty($args)):
                
                foreach ($args as $field):
                    
                    array_push($this->group, $field);
                endforeach;
            else:
                
                throw new \Exception('->group($args) obrigatório parametrização');
            endif;
        }
        catch (\Exception $ex)
        {
            throw new \Exception("ERRO AO MONTAR GROUP BY: ".$ex->getMessage());
        }
    }
    
    /**
     * Condição limit em banco de dados MySql
     * @param Int $args int com LIMIT de retorno da consulta
     * @throws \Exception
    */
    protected function limit($args)
    {
        try
        {
            $this->limit = (int)$args;
        }
        catch (\Exception $ex)
        {
            throw new \Exception("ERRO DE CONSTRUÇÃO DE SQL (LIMIT): ".$ex->getMessage());
        }
    }
    
    /**
     * Insert de dados em banco de dados MySql
     * @param type $table Tabela a ser feito insert no bando de dados
     * @param array $args Lista de campos e valores a serem inseridos
     * @return boolean true|false default false $getQueryString, Utilizada para exibir a string sql montada para o insert
     * @throws \PDOException
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
                
                if(!empty($this->toPrepare)):
                    $this->bindValues();
                endif;
                
                if($this->execute()):
                    
                    $this->lastInsertId = $this->dbInstance->lastInsertId();
                    $this->clear();
                    return true;
                endif;
                
                $this->clear();
                return false;
            else:    
                
                throw new \PDOException('Os Parametros update->($table, $args, $where) São obrigatórios.');
            endif;
        }
        catch(\PDOException $ex)
        {
            throw new \PDOException("ERRO AO TENTAR REALIZAR INSERT: ".$ex->getMessage());
        }
    }
    
    /**
     * Delete de dados em banco de dados MySql
     * @param string $table Tabela a ser feito delete no bando de dados
     * @param array $where termos e valores a serem considerados para delete
     * @return boolean true|false|string
     * @throws \PDOException
    */
    protected function delete($table, $where = null, $getQueryString = false)
    {
        try
        {
            if(!empty($table)):
                $this->clear();
                $this->select = "DELETE FROM {$table}";
                
                if(!empty($where)):
                    
                    foreach ($where as $terms => $values):

                        $this->where($terms, $values);
                    endforeach;
                    $this->select .= " WHERE {$this->where};";
                endif;
                
                if($getQueryString):
                    return $this->select;
                endif;
                
                $this->stmt = $this->dbInstance->prepare($this->select);
                
                if(!empty($this->toPrepare)):
                    $this->bindValues();
                endif;
                
                if($this->execute()):
                    
                    $this->clear();
                    return true;
                endif;
                
                $this->clear();
                return false;
                
            else:
                
                throw new \PDOException('Os Parametros dalete->($table) obrigatório parametrização.');
            endif;
        }
        catch(\PDOException $ex)
        {
            throw new \PDOException("ERRO AO TENTAR REALIZAR DELETE: ".$ex->getMessage());
        }
    }
    
    /**
     * Update de dados em banco de dados MySql
     * @param String $table Tabela, View a ser feito insert no bando de dados
     * @param array $args Lista de campos e valores a serem feitos atualização
     * @param String $where array de termos e valores a serem feitos atualização
     * @param Boolean $getQueryString TRUE|FALSE default FALSE
     * @return boolean true|false
     * @throws \PDOException
    */
    protected function update($table, array $args, $where = null, $getQueryString = false)
    {
        try
        {
            if(!empty($table) && !empty($args)):
                $this->clear();
                $this->select = "UPDATE {$table} SET";
                $countFields = 1;
                foreach ($args as $field => $value):
                
                    $vrgl = ($countFields === count($args)) ? ""  : ",";
                    if($getQueryString):
                        
                        $this->select .= " {$table}.{$field} = '{$value}'{$vrgl}";
                    else:
                        
                        $this->setToPrepare(array("?" => $value));
                        $this->select .= " {$table}.{$field} = ?{$vrgl}";
                    endif;
                    $countFields++;
                endforeach;
                
                if(!empty($where) && $where !== null):
                    
                    foreach ($where as $terms => $values):
                    
                        $this->where($terms, $values);
                    endforeach;
                    
                    $this->select .= " WHERE {$this->where};";
                endif;
                
                if($getQueryString):
                    return $this->select;
                endif;
                
                $this->stmt = $this->dbInstance->prepare($this->select);
                
                if(!empty($this->toPrepare)):
                    $this->bindValues();
                endif;
                
                if($this->execute()):
                    
                    $this->clear();
                    return true;
                endif;
                
                $this->clear();
                return false;
                
            else:
                throw new \PDOException('Os Parametros update->($table, $args, $where) São obrigatórios.');
            endif;
        }
        catch(\PDOException $ex)
        {
            throw new \PDOException("ERRO AO TENTAR REALIZAR UPDATE: ".$ex->getMessage());
        }
    }
    
    /**
     * Chama procedures no banco de dados
     * @param Array $arg argumentos com nome da procedure
     * @param Object $type Tipo de fetch a ser realizado
     *                    PDO::FETCH_ASSOC
     *                    PDO::FETCH_BOTH
     *                    PDO::FETCH_CLASS
     *                    PDO::FECTH_NUM
     *                    e outros
     * @throws \Exception
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
            else:
                
                throw new \Exception('->procedure($args) obrigatório parametrização.');
            endif;
        }
        catch (\Exception $ex)
        {
            throw new \Exception("ERRO DE CONSTRUÇÃO DE SQL(PROCEDURE): ".$ex->getMessage());
        }
    }
    
    /**
     * Serialização dos valores recebido de consulta.
     * @param Object $type Tipo de fetch a ser realizado
     *                    PDO::FETCH_ASSOC
     *                    PDO::FETCH_BOTH
     *                    PDO::FETCH_CLASS
     *                    PDO::FECTH_NUM
     *                    e outros
     * @param String $class Indica a qual object deseja-se transforma o retorno da consulta
     * @return Object|Array
     * @throws \Exception
    */
    protected function fetch($type = null , $class = null)
    {
        try
        {
            $this->query = $this->constructQueryString();
            if($this->query !== null):
                
                $callback = null;
                $this->stmt = $this->dbInstance->prepare($this->query);
                
                if(!empty($this->toPrepare)):
                    
                    $this->bindValues();
                endif;
                
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
            
            throw new \Exception('É preciso estabelecer o método de pesquisa novamente, após a execução do fetch() ou execute(), a consulta é deletada');
        }
        catch(\Exception $ex)
        {
            throw new \Exception("ERRO AO TENTAR FAZER FETCH DOS DADOS: ".$ex->getMessage());
        }
    }
    
    /**
     * Procedimento executa as operações no banco de dados
     * @return boolean true|false
     * @throws \PDOException
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
            throw new \PDOException("ERRO SMTP: ".$e->getMessage());
        }
    }
       
    /**
     * Retorna a Query formada a ser submetida a base de dados
     * @param int $operation Seleciona o tipo de retorno
     *                       self::SQL_STRING - 1 retornará uma \String com a query (default)
     *                       self::SQL_OBJECT - 2 retornará um \ArrayObject separado por procedimentos sql
     * @return \String|\ArrayObject
     * @throws \Exception
    */
    protected function getQuery($operation = self::SQL_STRING)
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
     * @throws \Exception
    */
    protected function getLastInsertId()
    {
        try
        {
            return (int) $this->lastInsertId;
        }
        catch(\Exception $ex)
        {
            throw new Exception('Ao ao tentar retornar último id inserido no banco de dados: '.$ex->getMessage());
        }
        
    }
        
    /**
     * Limpar as propriedades da Classe
     * @throws \Exception
    */
    protected function clear()
    {
        try
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
        catch(\Exception $ex)
        {
            throw new \Exception("ERRO AO LIMPAR BASE DE INFORMAÇÕES: ".$ex->getMessage()); 
        }
    }
    
    /**
     * Esta função constroe tada a query para execução no banco de dados
     * @return String Query construida para perpetuar select
     * @throws \Exception
    */
    private function constructQueryString()
    {
        try
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
        catch(\Exception $ex)
        {
            throw new \Exception("ERRO AO CONSTRUIR STRING SQL: ".$ex->getMessage()); 
        }
        
    }
    
    /**
     * Esta função constroe tada a query para execução no banco de dados
     * @return String Query construida para perpetuar select
     * @throws \Exception
    */
    private function constructQueryObject()
    {
        try
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
        catch(\Exception $ex)
        {
            throw new \Exception("ERRO AO CONSTRUIR OBJETO SQL: ".$ex->getMessage()); 
        }
    }
    
    /**
     * submitNickname (apelidos) a serem usados nas entidades objetos de pesquisa
     * @param String $table Nome da tabela (entidade)
     * @return String
     * @throws \Exception
    */
    private function setFrom($table)
    {
        try
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
        catch(\Exception $ex)
        {
            throw new \Exception('ERRO AO EXECUTAR MÉTODO ->setFrom($table): '.$ex->getMessage()); 
        }
    }
    
    /**
     * Recupera o nickname da tabela dos apelidos contanstes no array $this->$nickname
     * @param String $tableName Nome da tabela a ser resgatado o nickname, apelido
     * @return Array->ArrayObject com nickname da tabela na consulta
     * @throws \Exception
    */
    private function getFrom($table)
    {
        try
        {
            $filter = new Filters\FilterArrayObject(new \ArrayObject($this->from), new \ArrayIterator(array('table')), $table);
            return $filter->getObjFiltered();
        }
        catch (\Exception $ex)
        {
            throw new \Exception('ERRO AO EXECUTAR MÉTODO ->getFrom($table): '.$ex->getMessage()); 
        }
    }
    
    /**
     * Set os valores para serem feitos os  BindValues
     * @param Array $args array(':campo' => 'valor')
     * @throws \Exception
    */
    private function setToPrepare(array $args)
    {
        try
        {
            if(!empty($args)):
            
                foreach ($args as $prepare => $value):

                    array_push($this->toPrepare, array($prepare => $value));
                endforeach;
            endif;
        }
        catch (\Exception $ex)
        {
            throw new \Exception('ERRO AO EXECUTAR MÉTODO ->setToPrepare(array $args): '.$ex->getMessage()); 
        }
    }
    
    /**
     * Retorna uma string sem os marcadores bindValue (Ex.: :campos), ao invés disso retorna seus valores
     * @param String $query String a ser convertida para substituição de dos :campos por seus valores
     * @return String $query String tratada para exibição
     * @throws \Exception
    */
    private function getToPrepare($query)
    {
        try{
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
                return $query;
            else:
                
                throw new \Exception('É necessario estarem setados o objeto para prepare bem como a query a ser executada.'); 
            endif;
        }catch (\Exception $ex)
        {
            throw new \Exception('ERRO AO EXECUTAR MÉTODO ->getToPrepare($query): '.$ex->getMessage()); 
        }
    }
    
    /**
     * Função registras os values para serem realizados no banco de dados
     * @throws \PDOException
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
            else:
                
                throw new \PDOException('É necessario estar inicializada os valores para prepare e o objeto $this->stmt ser uma instancia de \PDOStatement: ');
            endif;
        } 
        catch(\PDOException $ex)
        {
            throw new \PDOException("ERRO AO REALIZAR OS BINDVALUE: ".$ex->getMessage());
        }
    }
    
    /**
     * Set and Get de novas tabelas
     * @param String $table String com nome da tabela
     * @return String $nickname O retorno será o nick da tabela ou o proprio nome da tabela
     * @throws \Exception
    */
    private function makeTableFrom($table)
    {
        try{
            $tableExplode = explode(" ", $table);
            $nickname = $this->getFrom($tableExplode[0]);
            if(empty($nickname)):

                $this->setFrom($table);
                $nickname = $this->getFrom($tableExplode[0]);
            endif;
            return ($nickname[0]['nickname'] !== null) ? $nickname[0]['nickname'] : $tableExplode[0];  
        }
        catch (\Exception $ex)
        {
            throw new \Exception('ERRO AO REALIZAR MÉTODO ->makeTableFrom($table): '.$ex->getMessage());
        }
    }
    
    /**
     * Set and Get de novas tabelas
     * @param String $table String com nome da tabela
     * @return String $nickname O retorno será o nick da tabela ou o proprio nome da tabela
     * @throws \Exception
    */
    private function makeTableJoin($table)
    {
        try{
            $tableExplode = explode(" ", $table);
            return (isset($tableExplode[1]) && $tableExplode[1] !== null) ? $tableExplode[1] : $tableExplode[0];  
        }
        catch (\Exception $ex)
        {
            throw new \Exception('ERRO AO REALIZAR MÉTODO ->makeTableJoin($table): '.$ex->getMessage());
        }
    }
    
    /**
     * Este método cria, de acordo com os parametros, e seta na variável $this->joins os JOINS no que serão realizados na pesquisa
     * @param array $args Array com tabela e condição para criação de joins
     * @param array $fields Array com campos que serão retornados da tabela join
     * @param type $type Tipo de JOIN podendo ser <b>INNER JOIN, LEFT JOIN ou RIGHT JOIN</b>
     * @throws \Exception
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
            else:
                
                throw new \Exception("ERRO DE CONSTRUÇÃO DE SQL({$type}), parametrização obrigatória");
            endif;
        }
        catch(\Exception $ex)
        {
            throw new \Exception("ERRO DE CONSTRUÇÃO DE SQL({$type}): ".$ex->getMessage());
        }
    }
}