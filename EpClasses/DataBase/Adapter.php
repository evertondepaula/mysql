<?php

namespace EpClasses\DataBase;

/**
 * <b>Adapter: </b> Realiza operações no bando de dados.<br/>
 * Seu uso deve ser feito como extend da classe que se deseja trabalhar.<br/>
 * <b>Ex.: class Cliente extends Adapter</b><br/>
 * Desta maneira o operador <b>$this-></b> encontrará as funções necessárias para acesso aos dados no banco de dados.
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
                $this->adapter = parent::__construct();
            endif;
        }
        catch (Exception $ex)
        {
            $this->adapter = null;
            echo $ex;
        }
        
    }
    
    /**
     * Construção de método para selecão de dados
     * @param array $args Lista de tables (entidades) e campos que retornaram da consulta
     *                    <b>ex.: array(
     *                          'tablename t' =>
     *                           array(
     *                              'campo' => 'alias'
     *                           )
     *                         );</b>
     *                    O primeiro array deve conter o nome da tabela (entidade) que contém os campos a serém pesquisados<br>
     *                    O Array da 'tableName' deve conter os nomes das colunas, existe a opção de passar o nome alias do campos como valor da key do array, o qual contém o nome do campo da tabela<br>
     *                    O nome da tabela é obrigatório, já seu apelido é opcional mas recomendado, o uso dele nas clausulas where e inner ajudaram.<br>
     *                    Se desejá retornar todos os campos da tabela, coloque * na chave do array 'campo'. ex.: <b>array('tablename t' => array ('*'))</b><br>
     */ 
    protected function select(array $args)
    {
        $this->adapter->select($args);
        return $this;
    }
    
    /**
     * Construção de metodo para selecão de functions do banco de dados
     * Este método pode ser usado antes ou depois do método select, ele criara a sintaxe para se trabalhar com as tables necessarias
     * @param array $args Nome da função, tabelas, campos e valores fixos que serão utilizados como parâmetros
     *                    <b>ex.: array(<br/>
     *                               "functionName1" =><br/>
     *                                   array(<br/>
     *                                       "tabela1 t1" => array(<br/>
     *                                           "parametro1", "parametro2"<br/>
     *                                       ),<br/>
     *                                       "tabela2 t2" => array(<br/>
     *                                           "parametro3", "parametro4"<br/>
     *                                       ),<br/>
     *                                       array("? ou :parametro" => "valor_parametro")<br/>
     *                                   ),<br/>
     *                               "functionName2" =><br/>
     *                                   array(<br/>
     *                                       "tabela1" => array(<br/>
     *                                           "parametro1", "parametro2"<br/>
     *                                       ),<br/>
     *                                       "tabela2" => array(<br/>
     *                                           "parametro3", "parametro4"<br/>
     *                                       ),<br/>
     *                                       array("? ou :parametro" => "valor_parametro")<br/>
     *                                   ), "alias"<br/>
     *                               )</b><br/>
     *                    O primeiro array deve conter o nome da função<br/>
     *                    Após inserir array com nomes das tabelas que a função irá operar, os valores do array serão os parametros, que poderão ser campos da tabela ou não<br/>
     *                    Caso queira passar parametros fixos, não pode ser coloca o nome da tabela a ser trabalhada<br/>
     *                    Ao final o segundo item do array principal pode ser o alias da função, é recomendado o seu uso<br/>
     *                    O apelido da tabela é opcional, porém o nome da tabela é obrigatório, em casos de uso de seus campos<br/>
     */
    protected function functions(array $args)
    {
        $this->adapter->functions($args);
        return $this;
    }

    /**
     * Construção de método para impor condição INNER JOIN na seleção de dados<br/><b>Limitado a apenas uma tabela por método envocado join()</b>
     * @param Array $args Tabela para JOIN e Condição, Limitado a apenas uma tabela por método <b>join</b><br/>
     *                              <b>ex.: array('tableName c' => <br/>
     *                                             array('c.campo = u.campo')<br/>
     *                                           )</b><br/>
     *                              Lembrando que o o apelido (nickname) para a tabela é opcional, é preciso ficar atento, <br/>
     *                              Caso não use o apelido é necessário escrever no array de condição o nome completo da tabela . campo<br/>
     *                              Também é possível usar mais de uma condição, basta inserir mais valores no segundo array.
     * @param Array $fields Campos de retorno da Tabela Join, <b>Por default retornará todos os campos *</b><br/>
     *                              <b>ex. de uso: array("campo1" => "alias", "campo2", "campo3" => "alias3")</b>
     */
    protected function join(array $args, array $fields = null)
    {
        $this->adapter->join($args, $fields);
        return $this;
    }
    
    /**
     * Construção de método para impor condição LEFT JOIN na seleção de dados<br/><b>Limitado a apenas uma tabela por método envocado join()</b>
     * @param Array $args Tabela para JOIN e Condição, Limitado a apenas uma tabela por método <b>join</b><br/>
     *                              <b>ex.: array('tableName c' => <br/>
     *                                             array('c.campo = u.campo')<br/>
     *                                           )</b><br/>
     *                              Lembrando que o o apelido (nickname) para a tabela é opcional, é preciso ficar atento, <br/>
     *                              Caso não use o apelido é necessário escrever no array de condição o nome completo da tabela . campo<br/>
     *                              Também é possível usar mais de uma condição, basta inserir mais valores no segundo array.
     * @param Array $fields Campos de retorno da Tabela Join, <b>Por default retornará todos os campos *</b><br/>
     *                              <b>ex. de uso: array("campo1" => "alias", "campo2", "campo3" => "alias3")</b>
     */
    protected function leftJoin(array $args, array $fields = null)
    {
        $this->adapter->leftJoin($args);
        return $this;
    }
    
    /**
     * Construção de método para impor condição RIGHT JOIN na seleção de dados<br/><b>Limitado a apenas uma tabela por método envocado join()</b>
     * @param Array $args Tabela para JOIN e Condição, Limitado a apenas uma tabela por método <b>join</b><br/>
     *                              <b>ex.: array('tableName c' => <br/>
     *                                             array('c.campo = u.campo')<br/>
     *                                           )</b><br/>
     *                              Lembrando que o o apelido (nickname) para a tabela é opcional, é preciso ficar atento, <br/>
     *                              Caso não use o apelido é necessário escrever no array de condição o nome completo da tabela . campo<br/>
     *                              Também é possível usar mais de uma condição, basta inserir mais valores no segundo array.
     * @param Array $fields Campos de retorno da Tabela Join, <b>Por default retornará todos os campos *</b><br/>
     *                              <b>ex. de uso: array("campo1" => "alias", "campo2", "campo3" => "alias3")</b>
     */
    protected function rightJoin(array $args, array $fields = null)
    {
        $this->adapter->rightJoin($args);
        return $this;
    }
    
    /**
     * 
     */
    protected function having($terms, array $parameters = null)
    {
        return $this->adapter->having($terms, $parameters);
    }
    
    /**
     * Construção de método para impor condição where na seleção de dados
     * @param String $terms String contendo as condições where do select ex.:
     *                  <b>"tabela.campo1 = tabela.campo2 AND tabela.campo1 > ? ou ainda :parametro1"</b><br/>
     *                  O uso do nome da tabela é obrigatório caso não seja colocado seu apelido na método <b>select</b>.<br/>
     *                  <b>Observação: Você deve utilizar a anotação :parametro ou ?, se existir a mistura das notações um erro retornará do bind dos parâmetros.</b><br/>
     *@param Array $parameters Verifica no termo os pontos :parametro ou ? e substitui pelo parametros em array, no caso do uso de ? ordem do array deve ser a mesma da
     *                          inserção dos pontos ? na string $terms
     */
    protected function where($terms, array $parameters)
    {
        $this->adapter->where($terms, $parameters);
        return $this;
    }
    
    /**
     * Construção de metodo para impor condição order by na seleção de dados
     * @param array $args campos a serem feitos order by
     *  <b>ex.: array('apelido.campo' => 'ASC ou DESC') ou array('u.campo'), neste último caso o sistema ordenará automaticamente Ascendente (ASC)</b>
     */
    protected function order(array $args)
    {
        $this->adapter->order($args);   
        return $this;
    }
    
    /**
     * Construção de metodo para impor condição group by na seleção de dados
     * @param array $args campos a serem feitos grupo 
     *  <b>ex.:array('u.campo', 'table.campo')</b>
     */
    protected function group(array $args)
    {
        $this->adapter->group($args);
        return $this;
    }
    
    /**
     * Construção de metodo para impor condição limit na seleção de dados
     * @param int $args Valor interiro a ser limitado o resultado da consulta
     */
    protected function limit($args)
    {
        $this->adapter->limit($args);
        return $this;
    }
    
    /**
     * Construção de metodo para inserção de dados
     * @param String $table Tabela a ser feita inserção
     * @param array $args campos e valores a serem inseridos
     */
    protected function insert($table, array $args)
    {
        return $this->adapter->insert($table, $args);
    }
    
    /**
     * Construção de metodo para delete de dados
     * @param String $table Tabela a ser feita inserção
     *                      Não esquecer de usar metodo <b>->where()</b> para limitar delete
    */
    protected function delete($table)
    {
        return $this->adapter->delete($table);
    }
    
    /**
     * Construção de metodo para update de dados
     * @param String $table Tabela a ser feita inserção
     * @param Array $args Lista de campos e valores a serem feitos update
     *                    Não esquecer de usar metodo <b>->where()</b> para limitar delete
    */
    protected function update($table, array $args)
    {
        return $this->adapter->update($table, $args);
    }
    
    /**
     * Construção de método para procedures de dados
     * @param array $args Nome da procedure, valores fixos que serão utilizados como parâmetros<br/>
     *                    <b>ex.:array(<br/>
     *                          'procedureName' => <br/>
     *                              array('valor_do_parametro','valor_do_parametro2')<br/>
     *                               );<br/></b>
     *                    O primeiro array deve conter o nome da procedure<br/>
     *                    Este array aponta para um segundo que conterá os valores dos parametros,<br/>
     * @param Object $type Tipo de fetch a ser realizado<br/>
     *                    PDO::FETCH_ASSOC<br/>
     *                    PDO::FETCH_BOTH<br/>
     *                    PDO::FETCH_CLASS<br/>
     *                    e outros<br/>
     */
    protected function procedure(array $args, $type = null)
    {
        return $this->adapter->procedure($args, $type);
    }
    
    /**
     * Serialização dos valores recebido de select, usar após já ter elaborado a cadeia de construção do método <b>select</b><br/>
     * @param Object $type Tipo de fetch a ser realizado<br/>
     *                    PDO::FETCH_ASSOC<br/>
     *                    PDO::FETCH_BOTH<br/>
     *                    PDO::FETCH_CLASS<br/>
     *                    e outros<br/>
     * @param String $class Indica a qual object deseja-se transforma o retorno da consulta<br/>
     * @return Object|Array<br/>
     */
    protected function fetch($type = null, $class = null)
    {
        return $this->adapter->fetch($type, $class);
    }
    
    /**
     * Executa métodos no bando de dados, como delet por exemplo, retorna true ou false para à operação
     * @return boolean true|false
     */
    protected function execute()
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
    protected function getQuery($operation = 1)
    {
        return $this->adapter->getQuery($operation);
    }
    
    /**
     * Metodo retorna a Int com o último id inserido no banco de dados
     * @return Int
    */
    protected function getlastInsertId()
    {
        return $this->adapter->getlastInsertId();
    }
    
    /**
     * Metodo limpa todas as propriedades do \Adapter, deixando limpo para proxima consulta<br/>
     * <b>Este método é executado automaticamente após cada fetch() ou execute()</b>
    */
    protected function clear()
    {
        $this->adapter->clear();
        return this;
    }
}