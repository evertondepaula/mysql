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
    
    /** @var Objeto \PDOConection criado para exercer a ponte de conexao com o bando de dados */
    private $dbInstance = null;
    
    
    
    /**
     * Método construtor para conexao com banco de dados
     * @param \PDO $conection Conexao estabelcida com PDO com o bando de dados
     */
    protected function __construct(\PDO $conection)
    {
        if($this->dbInstance === null):
            $this->dbInstance = $conection;
            $this->dbInstance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->dbInstance->setAttribute(\PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8");
        endif;
    }
    
    /**
     * Select de dados em bando de dados MySql
     * @param String $table Tabela, View a ser trabalhada no banco de dados
     * @param array $args Lista de campos que deveram retornar da consulta
     */
    public function select($table, array $args = null)
    {
         if($args === null):
            
            $this->query = "SELECT * FROM {$table}";
            $this->queryDebug = "SELECT * FROM {$table}";
        else:
                
        endif;
        
        return $this;
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
     * @param Object $type Tipo de fetch a ser realizado
     *                    PDO::FETCH_ASSOC
     *                    PDO::FETCH_BOTH
     *                    PDO::FETCH_CLASS
     *                    e outros
     * @param String $class Indica a qual object deseja-se transforma o retorno da consulta
     * @return Object|Array
     */
    public function fetch($type = null , $class = null)
    {
        if($this->query !== null):
            
            $callback = null;
            $this->stmt = $this->dbInstance->prepare($this->query);

            if($this->stmt->execute()):

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
    
    /**
     * Procedimento executa as operações no bando de dados
     * @return boolean true|false
     * @throws \Exception Erro na tentiva de execução junto ao bando de dados
     */
    public function execute()
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
    
    /**
     * Limpar as propriedades da Classe
     */
    private function clear()
    {
        $this->query = null;
        $this->queryDebug = null;
        $this->stmt = null;
    }
}