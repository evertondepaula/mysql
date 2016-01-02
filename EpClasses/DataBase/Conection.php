<?php

namespace EpClasses\DataBase;

use EpClasses\Interfaces\DataBase\InterfaceConection;
use EpClasses\Helpers\Read;

/**
 * <b>Conection: </b> Classe abstrata para cordenar a conexao, bem como os metodos de manipulação de dados
 * @author tom
 */
abstract class Conection implements InterfaceConection
{
    /**
     * Constante para arquivo de configuração
     */
    const FILE_CONFIG = "database.xml";
    
    /**
     * Ao construir a classe é feita a tentativa de estabelecer conexão com a base de dados, bem como determinar qual o banco de dados que será trabalhado
     * @return Object MySqlConection|
     * @throws Exception ERRO de conexao
     */
    protected function __construct(\PDO $conection = null)
    {
        try
        {
            return $this->getDbInstance();
        }
        catch(\PDOException $e)
        {
            throw new \PDOException("ERRO DE CONEXAO: ".$e->getMessage());
        }
    }
    
    /**
     * Cria a instancia de conexao e retorna a clases ne manipulação do bando dados apropriada
     * @return Object \MySqlConection | Outros
     */
    private function getDbInstance()
    {
        $config = $this->readConfig();
        switch ($config->drive):
            case "pdo_mysql":

                return new MySqlConection(new \PDO("mysql:host={$config->host};dbname={$config->dbname};port={$config->port}", $config->user, $config->password));
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
        try{
            $read = new Read\ReadXml();
            return $read->getArrayFromXml($_SERVER['DOCUMENT_ROOT']."/../App/config/".self::FILE_CONFIG);
        }  
        catch (\Exception $ex)
        {
            echo "ERRO AO TENTAR LER ARQUIVO DE CONFIGURAÇÃO .XML: ". $ex->getMessage();
        }
    }
    
    /**
     * Obriga a implentação de método para realizar select no bando de dados
     */
    abstract protected function select(array $args);
    
    /**
     * Obriga a implentação de método para realizar functions no bando de dados
     */
    abstract protected function functions(array $args);
    
    /**
     * Obriga a implentação de método para implementar condição join no select ao no bando de dados
     */
    abstract protected function join(array $args, array $fields = null);
    
    /**
     * Obriga a implentação de método para implementar condição leftJoin no select ao no bando de dados
     */
    abstract protected function leftJoin(array $args, array $fields = null);
    
    /**
     * Obriga a implentação de método para implementar condição rightJoin no select ao bando de dados
     */
    abstract protected function rightJoin(array $args, array $fields = null);
    
    /**
     * Obriga a implentação de método para implementar condição having no select ao bando de dados
     */
    abstract protected function having($terms, array $parameters = null);
    
    /**
     * Obriga a implentação de método para implementar condição where no select ao bando de dados
     */
    abstract protected function where($terms, array $parameters);
    
    /**
     * Obriga a implentação de método para implementar condição order no select ao bando de dados
     */
    abstract protected function order(array $args);
    
    /**
     * Obriga a implentação de método para implementar condição group no select ao bando de dados
     */
    abstract protected function group(array $args);
    
    /**
     * Obriga a implentação de método para implementar condição limit no select ao bando de dados
     */
    abstract protected function limit($args);
    
    /**
     * Obriga a implentação de método para realizar inserts ao bando de dados
     */
    abstract protected function insert($table, array $args);
    
    /**
     * Obriga a implentação de método para realizar deletes ao bando de dados
     */
    abstract protected function delete($table);
    
    /**
     * Obriga a implentação de método para realizar updates ao bando de dados
     */
    abstract protected function update($table, array $args);
    
    /**
     * Obriga a implentação de método para realizar procedures no bando de dados
     */
    abstract protected function procedure(array $args, $type = null);
    
    /**
     * Obriga a implentação de método para realizar fetchs de dados(consutlas)
     */
    abstract protected function fetch($type = null, $class = null);
    
    /**
     *  Obriga a implentação de método para conseguir a string da query formada pela objeto de execução da Query
     */
    abstract protected function getQuery($operation = 1);
    
    /**
     * Obriga a implentação de método para conseguir o último id inserido no bando de dados
     */
    abstract protected function getlastInsertId();
    
    /**
     * Obriga a implentação de método para limpar todos as propriedades de consultadas já elaborardas
     */
    abstract protected function clear();
}