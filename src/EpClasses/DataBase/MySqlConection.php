<?php

namespace EpClasses\DataBase;

/**
 * <b>MySqlConection: </b> Esta Classe realiza os comandos em banco de dados MySql
 * @author tom
 */
class MySqlConection extends Conection
{
    /** @var String Query montada para submissao a base de dados */
    private $query = null;
    
    /** @var String Query para DEBUG, $var mostrará os procedimentos a serem submetidos no bando de dados */
    private $queryDebug = null;
    
    /** @var Int Último Id inserido no banco de dados*/
    private $lastInsertId = null;
    
    /** @var Objeto \PDOStatement criado para execução de comandos na base de dados */
    private $stmt = null;
    
    /**
     * Select de dados em bando de dados MySql
     * @param String $table Tabela, View a ser trabalhada no banco de dados
     * @param array $args Lista de campos que deveram retornar da consulta
     */
    public function select($table, array $args = null)
    {
        
    }
    
    /**
     * Condição Join em bando de dados MySql
     * @param array $args Lista de campos a serem feito join
    */
    public function join(array $args)
    {
        
    }
    
    /**
     * Condição leftJoin em bando de dados MySql
     * @param array $args Lista de campos a serem feito leftJoin
    */
    public function leftJoin(array $args)
    {
        
    }
    
    /**
     * Condição rightJoin em bando de dados MySql
     * @param array $args Lista de campos a serem feitos rightJoin
    */
    public function rightJoin(array $args)
    {
        
    }
    
    /**
     * Condição where em bando de dados MySql
     * @param array $args Lista de condições WHERE da consulta
    */
    public function where(array $args)
    {
        
    }
    
    /**
     * Condição order em bando de dados MySql
     * @param array $args Lista de condições ORDER da consulta
    */
    public function order(array $args)
    {
        
    }
    
    /**
     * Condição group em bando de dados MySql
     * @param array $args Lista de condições GROUP da consulta
    */
    public function group(array $args)
    {
        
    }
    
    /**
     * Condição limit em bando de dados MySql
     * @param array $args Lista de condições LIMIT da consulta
    */
    public function limit($args)
    {
        
    }
    
    /**
     * Insert de dados em bando de dados MySql
     * @param type $table Tabela, View a ser feito insert no bando de dados
     * @param array $args Lista de campos e valores a serem inseridos
     * @return boolean true|false
     */
    public function insert($table, array $args)
    {
        
    }
    
    /**
     * Delete de dados em bando de dados MySql
     * @param type $table Tabela a ser feito delete no bando de dados
     * @return boolean true|false
     */
    public function delete($table)
    {
        
    }
    
    /**
     * Update de dados em bando de dados MySql
     * @param type $table Tabela, View a ser feito insert no bando de dados
     * @param array $args Lista de campos e valores a serem feitos atualização
     * @return boolean true|false
     */
    public function update($table, array $args = null)
    {
        
    }
    
    /**
     * Serialização dos valores recebido de consulta.
     * @param Object $class Indica a qual object o deseja-se transforma o retorno da consulta
     * @param String $type Tipo de fetch a ser realizado
     *                    fetchAll
     *                    fetchObject
     *                    fetchArray
     * @return Object|Array
     */
    public function fetch($class = null, $type = null)
    {
        
    }
    
    /**
     * Procedimento executa as operações no bando de dados
     * @return boolean true|false
     * @throws \Exception Erro na tentiva de execução junto ao bando de dados
     */
    private function execute()
    {
        try
        {
            if($this->stmt instanceof \PDOStatement):
                
                $retorno = $this->stmt->execute();
                $this->stmt = null;
                $this->query = null;
                $this->queryDebug = null;
                return $retorno;
            endif;
        }
        catch (\PDOException $e)
        {
            throw new \Exception("ERRO SMTP: ".$e->getMessage());
        }
    }
       
    /**
     * Retorna a Query formada a ser submetida a base de dados
     * @return String 
     */
    public function getStringQuery()
    {
        return $this->queryDebug;
    }
    
    
    /**
     * Retorna o último Id inserido
     * @return Int
     */
    public function getLastInsertId()
    {
        return (int) $this->lastInsertId;
    }
}