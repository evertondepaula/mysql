<?php

namespace EpClasses\DataBase;

/**
 * <b>Adapter: </b> Realiza operações as em bando de dados
 * @author tom
 */
class Adapter extends Conection
{   
    /**
     * Esta Objeto será alocado dinamicamente dependendo do bando de dados a ser trabalhado
     * @var Object \MySqlConection| 
     */
    private $adapter = null;
        
    /**
     * Construtor chama a construtor da classe \Conection
     */
    public function __construct(){
        try 
        {
            if($this->adapter !== null):
                $this->adapter = parent::getConstruct();
            endif;
        }
        catch (Exception $ex)
        {
            $this->adapter = null;
            echo $ex;
        }
        
    }
    
    /**
     * Construção de metodo para selecão de dados
     * @param String $table Table, View a ser realizada consulta
     * @param array $args Lista de campos que retornaram da consulta
     */
    public function select($table, array $args = null)
    {
        $this->adapter->select($table, $args);
    }
    
    /**
     * Construção de metodo para impor condição join na seleção de dados
     * @param array $args campos a serem feitos join ex.: array('a.campo' => 'b.campo')
     */
    public function join(array $args)
    {
        $this->adapter->join($args);
    }
    
    
    /**
     * Construção de metodo para impor condição leftJoin na seleção de dados
     * @param array $args campos a serem feitos leftJoin ex.: array('a.campo' => 'b.campo')
     */
    public function leftJoin(array $args)
    {
        $this->adapter->leftJoin($args);
    }
    
    /**
     * Construção de metodo para impor condição rightJoin na seleção de dados
     * @param array $args campos a serem feitos rightJoin ex.: array('a.campo' => 'b.campo')
     */
    public function rightJoin(array $args)
    {
        $this->adapter->rightJoin($args);
    }
    
    /**
     * Construção de metodo para impor condição where na seleção de dados
     * @param array $args campos a serem feitos where
     *                    ex.: array('campo' => 'valor')
     */
    public function where(array $args)
    {
        $this->adapter->where($args);
    }
    
    /**
     * Construção de metodo para impor condição order na seleção de dados
     * @param array $args campos a serem feitos order ex.: array('campo' => 'asc')
     */
    function order(array $args)
    {
        $this->adapter->order($args);   
    }
    
    /**
     * Construção de metodo para impor condição group na seleção de dados
     * @param array $args campos a serem feitos grupo ex.: array('campo', 'campo')
     */
    function group(array $args)
    {
        $this->adapter->group($args);
    }
    
    /**
     * Construção de metodo para impor condição limit na seleção de dados
     * @param int $args Valor interiro a ser limitado o resultado da consulta
     */
    public function limit($args)
    {
        $this->adapter->limit($args);
    }
    
    /**
     * Construção de metodo para inserção de dados
     * @param String $table Tabela a ser feita inserção
     * @param array $args campos e valores a serem inseridos
     */
    public function insert($table, array $args)
    {
        return $this->adapter->insert($table, $args);
    }
    
    /**
     * Construção de metodo para delete de dados
     * @param String $table Tabela a ser feita inserção
     *                      Não esquecer de usar metodo <b>->where()</b> para limitar delete
    */
    public function delete($table)
    {
        return $this->adapter->delete($table);
    }
    
    /**
     * Construção de metodo para update de dados
     * @param String $table Tabela a ser feita inserção
     * @param Array $args Lista de campos e valores a serem feitos update
     *                    Não esquecer de usar metodo <b>->where()</b> para limitar delete
    */
    public function update($table, array $args)
    {
        return $this->adapter->update($table, $args);
    }
    
    /**
     * Serialização dos valores recebido de select, usar após já ter elaborado a cadeia de construção do método <b>->select()</b>
     * @param Object $class Indica a qual object o deseja-se transforma o retorno da consulta
     * @param String $type Tipo de fetch a ser realizado
     *                    fetchAll
     *                    fetchObject
     *                    fetchArray
     * @return Object|Array
     */
    public function fetch($class = null, $type = null)
    {
        return $this->adapter->fetch($class, $type);
    }
    
    
    /**
     * Metodo retorna a string a ser submetida no bando de dados
     * @return String
    */
    public function getStringQuery()
    {
        return $this->adapter->getStringQuery();
    }
    
    /**
     * Metodo retorna a Int com o último id inserido no banco de dados
     * @return Int
    */
    public function getlastInsertId()
    {
        return $this->adapter->getlastInsertId();
    }
}