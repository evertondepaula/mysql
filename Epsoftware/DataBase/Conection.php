<?php

namespace Epsoftware\DataBase;

/**
 * <b>Conection: </b> Classe abstrata para cordenar a conexao, bem como os metodos de manipulação de dados
 * @author tom
 */
abstract class Conection implements Epsoftware\Interfaces\InterfaceConection
{
    /** @var string Tipo de sgbd utilizado */
    private $sgbd = "mysql";
    
    /** @var string Local do bando de dados */
    private $host = "localhost";
    
    /** @var Nome da base de dados a ser explorada */
    private $database = "";
    
    /** @var Porta de conexao do bando de dados */
    private $port = "3306";
    
    /** @var Usuário registrado no bando de dados para manipulacão de dados */
    private $user = "root";
    
    /** @var Senha do usuário registrado no bando de dados*/
    private $pass = "root";

    /**
     * @var  Objeto \PDO de conexao com o bando de dados
     */
    protected $dbInstance = null;
    
    
    /**
     * Ao construir a classe é feita a tentativa de estabelecer conexao com a base de dados, bem como determinar qual o bando de dados que será trabalhado
     * @return Object MySqlConection|
     * @throws Exception ERRO de conexao
     */
    protected function __construct()
    {
        try
        {
            if($this->dbInstance === null):
                return $this->getDbInstance();
            endif;
        }
        catch(\PDOException $e)
        {
            throw new Exception("ERRO DE CONEXAO: ".$e->getMessage());
        }
    }
    
    /**
     * Cria a instancia de conexao e retorna a clases ne manipulação do bando dados apropriada
     * @return Object \MySqlConection | Outros
     */
    private function getDbInstance()
    {
        $this->readConfig();
        switch ($this->sgbd):
                
            case "mysql":
                $this->dbInstance = new \PDO("mysql:host={$this->host};dbname={$this->database}", $this->user, $this->pass);
                return new \MySqlConection;

            default :
                return null;
        endswitch;
    }

    /**
     * Esta função é resposavel por lear as configurações do arquivo app/config/databse.yml
     */
    private function readConfig(){
    
    }
    
    /**
     * Obriga a implentação de metodo para realizar select no bando de dados
     */
    abstract public function select($table, array $args = null);
    
    /**
     * Obriga a implentação de metodo para implementar condição join no select ao no bando de dados
     */
    abstract public function join(array $args = null);
    
    /**
     * Obriga a implentação de metodo para implementar condição leftJoin no select ao no bando de dados
     */
    abstract public function leftJoin(array $args = null);
    
    /**
     * Obriga a implentação de metodo para implementar condição rightJoin no select ao bando de dados
     */
    abstract public function rightJoin(array $args = null);
    
    /**
     * Obriga a implentação de metodo para implementar condição where no select ao bando de dados
     */
    abstract public function where(array $args = null);
    
    /**
     * Obriga a implentação de metodo para implementar condição order no select ao bando de dados
     */
    abstract public function order(array $args = null);
    
    /**
     * Obriga a implentação de metodo para implementar condição group no select ao bando de dados
     */
    abstract public function group(array $args = null);
    
    /**
     * Obriga a implentação de metodo para implementar condição limit no select ao bando de dados
     */
    abstract public function limit(array $args = null);
    
    /**
     * Obriga a implentação de metodo para realizar inserts ao bando de dados
     */
    abstract public function insert($table, array $args = null);
    
    /**
     * Obriga a implentação de metodo para realizar deletes ao bando de dados
     */
    abstract public function delete($table);
    
    /**
     * Obriga a implentação de metodo para realizar updates ao bando de dados
     */
    abstract public function update($table, array $args = null);
    
    /**
     * Obriga a implentação de metodo para realizar fetchs de dados(consutlas)
     */
    abstract public function fetch($class = null, $type = null);
    
    /**
     *  Obriga a implentação de metodo para conseguir a string da query formada pela objeto de execução da Query
     */
    abstract public function getStringQuery();
    
    /**
     * Obriga a implentação de metodo para conseguir o ultimo id inserido no bando de dados
     */
    abstract public function getlastInsertId();
}