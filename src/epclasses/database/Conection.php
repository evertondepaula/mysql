<?php

namespace epclasses\database;

use epclasses\helpers\read;

/**
 * <b>Conection: </b> Classe abstrata para cordenar a conexao, bem como os metodos de manipulação de dados
 * @author tom
 */
abstract class Conection implements \interfaces\InterfaceConection
{
    /**
     * Constante para arquivo de configuração
     */
    const FILE_CONFIG = __DIR__.'/../../../app/config/database.xml';
    
    /** @var string Tipo de sgbd utilizado */
    private $drive = null;
    
    /** @var string Local do bando de dados */
    private $host = null;
    
    /** @var Nome da base de dados a ser explorada */
    private $dbname = null;
    
    /** @var Porta de conexao do bando de dados */
    private $port = null;
    
    /** @var Usuário registrado no bando de dados para manipulacão de dados */
    private $user = null;
    
    /** @var Senha do usuário registrado no bando de dados*/
    private $password = null;

    /**
     * @var  Objeto \PDO de conexao com o bando de dados
     */
    protected $dbInstance = null;
    
    
    /**
     * Ao construir a classe é feita a tentativa de estabelecer conexão com a base de dados, bem como determinar qual o banco de dados que será trabalhado
     * @return Object MySqlConection|
     * @throws Exception ERRO de conexao
     */
    protected static function getConstruct()
    {
        try
        {
            if($this->dbInstance === null):
                return $this->getDbInstance();
            endif;
        }
        catch(\PDOException $e)
        {
            throw Exception("ERRO DE CONEXAO: ".$e->getMessage());
        }
    }
    
    /**
     * Cria a instancia de conexao e retorna a clases ne manipulação do bando dados apropriada
     * @return Object \MySqlConection | Outros
     */
    private function getDbInstance()
    {
        $this->readConfig();
        switch ($this->drive):
            case "pdo_mysql":
                $this->dbInstance = new \PDO("mysql:host={$this->host};dbname={$this->dbname};port={$this->port}", $this->user);
                return new \MySqlConection;

            default :
                return null;
        endswitch;
    }

    /**
     * Esta função é responsável por ler as configurações do arquivo app/config/config.* xml | yml
     */
    private function readConfig()
    {
        $ext = pathinfo(self::FILE_CONFIG, PATHINFO_EXTENSION);
        switch ($ext):
            
            case "yml":
                return null;
                
            default :
                return $this->readConfigXML();
        endswitch;
   }
    
    /**
     * Esta função é reponsável por ler configurações de arquivos XML
     */
    private function readConfigXML()
    {
        $read = new read\ReadXml();
        $xml = $read->getArrayFromXml(self::FILE_CONFIG);
        $this->drive = $xml->database->drive;
        $this->host = $xml->database->host;
        $this->port = $xml->database->port;
        $this->user = $xml->database->user;
        $this->password = $xml->database->password;
    }
    
    /**
     * Obriga a implentação de método para realizar select no bando de dados
     */
    abstract public function select($table, array $args = null);
    
    /**
     * Obriga a implentação de método para implementar condição join no select ao no bando de dados
     */
    abstract public function join(array $args = null);
    
    /**
     * Obriga a implentação de método para implementar condição leftJoin no select ao no bando de dados
     */
    abstract public function leftJoin(array $args = null);
    
    /**
     * Obriga a implentação de método para implementar condição rightJoin no select ao bando de dados
     */
    abstract public function rightJoin(array $args = null);
    
    /**
     * Obriga a implentação de método para implementar condição where no select ao bando de dados
     */
    abstract public function where(array $args = null);
    
    /**
     * Obriga a implentação de método para implementar condição order no select ao bando de dados
     */
    abstract public function order(array $args = null);
    
    /**
     * Obriga a implentação de método para implementar condição group no select ao bando de dados
     */
    abstract public function group(array $args = null);
    
    /**
     * Obriga a implentação de método para implementar condição limit no select ao bando de dados
     */
    abstract public function limit(array $args = null);
    
    /**
     * Obriga a implentação de método para realizar inserts ao bando de dados
     */
    abstract public function insert($table, array $args = null);
    
    /**
     * Obriga a implentação de método para realizar deletes ao bando de dados
     */
    abstract public function delete($table);
    
    /**
     * Obriga a implentação de método para realizar updates ao bando de dados
     */
    abstract public function update($table, array $args = null);
    
    /**
     * Obriga a implentação de método para realizar fetchs de dados(consutlas)
     */
    abstract public function fetch($class = null, $type = null);
    
    /**
     *  Obriga a implentação de método para conseguir a string da query formada pela objeto de execução da Query
     */
    abstract public function getStringQuery();
    
    /**
     * Obriga a implentação de método para conseguir o último id inserido no bando de dados
     */
    abstract public function getlastInsertId();
}