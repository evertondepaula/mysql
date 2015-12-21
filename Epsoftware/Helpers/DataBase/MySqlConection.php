<?php

namespace Epsoftware\Helpers\DataBase;

/**
 * <b>MySqlConection: </b> Esta Classe realiza os comandos em banco de dados MySql
 *
 * @author tom
 */
class MySqlConection extends \Conection{
    
    /** @var String Query montada para submissao a base de dados */
    private $query = null;
    
    /** @var String Query para DEBUG, $var mostrará os procedimentos a serem submetidos no bando de dados */
    private $queryDebug = null;
    
    /** @var Int Último Id inserido no banco de dados*/
    private $lastInsertId = null;
    
    /** @var Objeto \PDOStatement criado para execução de comandos na base de dados */
    private $stmt = null;
    
    public function select($table, array $args = null);
    public function join(array $args = null);
    public function leftJoin(array $args = null);
    public function rightJoin(array $args = null);
    public function where(array $args = null);
    public function order(array $args = null);
    public function group(array $args = null);
    public function limit(array $args = null);
    public function insert($table, array $args = null);
    public function delete($table, array $args = null);
    public function update($table, array $args = null);
    public function fetch($class = null, $type = null);
    
    
    /**
     * Procedimento executa as operações no bando de dados
     * @return boolean
     * @throws \Exception Erro na tentiva de execução junto ao bando de dados
     */
    private function execute(){
        try{
            if($this->stmt instanceof \PDOStatement):
                $retorno = $this->stmt->execute();
                $this->stmt = null;
                $this->query = null;
                $this->queryDebug = null;
                return $retorno;
            endif;
        }  catch (\PDOException $e){
            throw new \Exception("ERRO SMTP: ".$e->getMessage());
        }
    }
       
    /**
     *  Retorna a Query formada a ser submetida a base de dados
     * @return String 
     */
    public function getStringQuery(){
        return $this->queryDebug;
    }
    
    
    /**
     * Retorna o último Id inserido
     * @return Int
     */
    public function getLastInsertId(){
        return (int) $this->lastInsertId;
    }
    
}