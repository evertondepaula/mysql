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
            if($this->adapter === null):
                $this->adapter = parent::getConstructForAdapter();
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
     * @param array $args Lista de tables (entidades) e campos que retornaram da consulta
     *                    <b>ex.: array(
     *                          'tablename' =>
     *                           array(
     *                              'campo' => 'alias'
     *                           )
     *                         );</b>
     *                    O primeiro array deve conter o nome da table (entidade) que contém os campos serem pesquisados
     *                    O Array da 'tableName' deve conter os nomes das colunas e existe a opção de passar o nome alias do campos como valor da key do array que contém o nome do campo da table
     */
    public function select(array $args)
    {
        $this->adapter->select($args);
        return $this;
    }
    
    /**
     * Construção de metodo para selecão de functions do banco de dados
     * Este método pode ser usado antes ou depois do método selet, ele criara a sintaxe para se trabalhar com as tables necessarias
     * @param array $args Nome da função, tabelas, campos e valores fixos que serão utilizados como parâmetros
     *                    <b>ex.: array(
     *                               "functionName1" =>
     *                                   array(
     *                                       "tabela1" => array(
     *                                           "parametro1", "parametro2"
     *                                       ),
     *                                       "tabela2" => array(
     *                                           "parametro3", "parametro4"
     *                                       ),
     *                                       array("parametro_sem_tabela")
     *                                   ),
     *                               "functionName2" =>
     *                                   array(
     *                                       "tabela1" => array(
     *                                           "parametro1", "parametro2"
     *                                       ),
     *                                       "tabela2" => array(
     *                                           "parametro3", "parametro4"
     *                                       ),
     *                                       array("parametro_sem_tabela")
     *                                   ), "alias"
     *                               )</b>
     *                    O primeiro array deve conter o nome da função
     *                    Após inserir array com nomes das tabelas que a função irá operar, os valores do array serão os parametros, que poderão ser campos da table
     *                    Caso queira passar parametros fixos, não pode ser coloca o nome da table a ser trabalhada
     *                    Ao final o segundo item do array principal pode ser o alias da função, é recomendado o seu uso
     */
    public function functions(array $args)
    {
        $this->adapter->functions($args);
        return $this;
    }

    /**
     * Construção de metodo para impor condição join na seleção de dados
     * @param array $args campos a serem feitos join ex.: array('a.campo' => 'b.campo')
     */
    public function join(array $args)
    {
        return $this->adapter->join($args);
    }
    
    /**
     * Construção de metodo para impor condição leftJoin na seleção de dados
     * @param array $args campos a serem feitos leftJoin ex.: array('a.campo' => 'b.campo')
     */
    public function leftJoin(array $args)
    {
        return $this->adapter->leftJoin($args);
    }
    
    /**
     * Construção de metodo para impor condição rightJoin na seleção de dados
     * @param array $args campos a serem feitos rightJoin ex.: array('a.campo' => 'b.campo')
     */
    public function rightJoin(array $args)
    {
        return $this->adapter->rightJoin($args);
    }
    
    /**
     * Condição having em banco de dados MySql
     * @param array $args Lista de campos a serem feitos having
     */
    public function having(array $args)
    {
        return $this->adapter->having($args);
    }
    
    /**
     * Construção de método para impor condição where na seleção de dados
     * @param String $terms String contendo as condições where do select ex.:
     *                  <b>"tabela.campo1 = tabela.campo2 AND tabela.campo1 > ? ou ainda :parametro1"</b>
     *                  O uso do nome da tabela é obrigatório para o bom funionamento da consulta
     *@param Array $parameters Verifica no termo os pontos :parametro ou ? e substitui pelo parametros em array, no caso do uso de ? ordem do array deve ser a mesma da
     *                          inserção dos pontos ? na strign $terms
     */
    public function where($terms, array $parameters)
    {
        $this->adapter->where($terms, $parameters);
        return $this;
    }
    
    /**
     * Construção de metodo para impor condição order na seleção de dados
     * @param array $args campos a serem feitos order ex.: array('campo' => 'asc')
     */
    function order(array $args)
    {
        return $this->adapter->order($args);   
    }
    
    /**
     * Construção de metodo para impor condição group na seleção de dados
     * @param array $args campos a serem feitos grupo ex.: array('campo', 'campo')
     */
    function group(array $args)
    {
        return $this->adapter->group($args);
    }
    
    /**
     * Construção de metodo para impor condição limit na seleção de dados
     * @param int $args Valor interiro a ser limitado o resultado da consulta
     */
    public function limit($args)
    {
        $this->adapter->limit($args);
        return $this;
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
     * Construção de metodo para procedures de dados
     * @param array $args Nome da procedure, valores fixos que serão utilizados como parâmetros
     *                    <b>ex.: array(
     *                          'procedureName' => array(
     *                                  'parametro1', 'parametro2', '...'
     *                              )
     *                         );</b>
     *                    O primeiro array deve conter o nome da procedure
     *                    Este array aponta para um segundo que conterá os valores dos parametros
     */
    public function procedure(array $args)
    {
        return $this->adapter->procedure($args);
    }
    
    /**
     * Serialização dos valores recebido de select, usar após já ter elaborado a cadeia de construção do método <b>->select()</b>
     * @param Object $type Tipo de fetch a ser realizado
     *                    PDO::FETCH_ASSOC
     *                    PDO::FETCH_BOTH
     *                    PDO::FETCH_CLASS
     *                    e outros
     * @param String $class Indica a qual object deseja-se transforma o retorno da consulta
     * @return Object|Array
     */
    public function fetch($type = null, $class = null)
    {
        return $this->adapter->fetch($class, $type);
    }
    
    /**
     * Executa métodos no bando de dados, como delet por exemplo, retorna true ou false para à operação
     * @return boolean true|false
     */
    public function execute()
    {
        return $this->adapter->execute();
    }
    
    /**
     * Retorna a Query formada a ser submetida a base de dados
     * @param int $operation Selecionará o tipo de retorno
     *                       ::SQL_STRING - 1 retornará uma \String com a query (default)
     *                       ::SQL_OBJECT - 2 retornará um \ArrayObject separado por procedimentos sql
     * @return \String|\ArrayObject 
    */
    public function getQuery($operation = 1)
    {
        return $this->adapter->getQuery($operation);
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