<?php

namespace Epsoftware\Helpers\DataBase;

/**
 * <b>Conection: </b> Classe abstrata para cordenar a conexao, bem como os metodos de manipulação de dados
 * * @author tom
 */
abstract class Conection implements Interfaces\InterfaceConection{
    
    /** @var string Tipo de sgbd utilizado */
    private $sgbd = "mysql";
    
    /** @var string Local do bando de dados */
    private $host = "localhost";
    
    /** @var Nome da base de dados a ser explorada */
    private $database = "";
    
    /** @var Porta de conexao do bando de dados */
    private $port = "3306";
    
    /** @var Usuário registrado no bando de dados para manipulacao de dados */
    private $user = "root";
    
    /** @var Senha do usuário registrado no bando de dados*/
    private $pass = "root";

    /**
     * @var  Objeto \PDO de conexao com o bando de dados
     */
    protected $dbInstance = null;
    
    private function __construct() {
        
        try
        {
            if($this->dbInstance === null):
                switch ($this->sgbd):
                
                    case "mysql":
                        $this->dbInstance = new \PDO("mysql:host={$this->host};dbname={$this->database}", $this->user, $this->pass);
                        break;
                    
                    default :
                        throw new Exception("SGBD NÃO SUPORTADO.");
                endswitch;
            endif;
        }
        catch(\PDOException $e){
            throw new Exception("ERRO DE CONEXAO: ".$e->getMessage());
        }
        
    }
    
    private function __destruct() {
        
        try
        {
            if($this->dbInstance !== null):
                $this->dbInstance = null;
            endif;
        }
        catch(\Exception $e){
            throw new Exception("ERRO DE DESCONEXAO: ".$e->getMessage());
        }
        
    }
    
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
    
    abstract public function getStringQuery();
    
    abstract public function getlastInsertId();
    
    
}


