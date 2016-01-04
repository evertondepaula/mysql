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
     * Construção de método para impor condição having na seleção de dados do group by
     * @param String $terms String contendo as condições having do select ex.:
     *                  <b>"tabela.campo1 = tabela.campo2 AND tabela.campo1 > ? ou ainda :parametro1"</b><br/>
     *                  O uso do nome da tabela é obrigatório caso não seja colocado seu apelido na método <b>select</b>.<br/>
     *                  <b>Observação: Você deve utilizar a anotação :parametro ou ?, se existir a mistura das notações um erro retornará do bind dos parâmetros.</b><br/>
     *@param Array $parameters Verifica no termo os pontos :parametro ou ? e substitui pelo parametros em array, no caso do uso de ? ordem do array deve ser a mesma da
     *                          inserção dos pontos ? na string $terms
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
    protected function where($terms, array $parameters = null)
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
     * Inserir dados no database<br/>
     * <b>Dica: </b>Pode utilizar-se do método select->()->fecth(\PDO::NUM) para retornar um array de valores que serão inseridos no banco de dados
     * @param string $table Nome da tabela de inserção
     * @param array $fields array campos a serem inseridos valores
     * @param array $args array de valores a serem inseridos,<br/>
     * <b>Este array pode ser bidimensional, ou seja você pode passar varios arrays de valores a serem inseridos<br/>
     *  ex.: array('valor', 'valor2', 'valor3') ou <br/>
     *       array(<br/>
     *          array('valor', 'valor2', 'valor3')<br/>
     *          array('valor', 'valor2', 'valor3')<br/>
     *        )</b><br/>
     * @param boolean $getQueryString default FALSE<br/>
     * <b>TRUE evita o execução da query e retorna a string formada para execução no banco de dados</b><br/>
     * <b>FALSE executa o procedimento no banco de dados</b><br/>
     * @return boolean TRUE|FALSE|String <br/>
     * TRUE procedimento realizado com sucesso<br/>
     * FALSE falha ao executar procedimento de inserção
     * String da query formada para submit no banco de dados
     */
    protected function insert($table, array $fields, array $args, $getQueryString = false)
    {
        return $this->adapter->insert($table, $fields, $args, $getQueryString);
    }
    
    /**
     * Método update<br/>
     * <b>Dica: </b>Fique atento ao parametro where, sob o risco de <b>deletar</b> toda sua base de dados.
     * @param String $table Nome da tabela
     * @param Array $where este array contera os termos para delete dos dados<br/>
     * <b>
     * ex.: array( <br/>
     *  'tabela.campo = ?' => array( <br/>
     *      'valor do ?', '...' <br/>
     *  )<br/>
     * )<br/>
     * Dica: * ? representa o valor a ser feito bind obrigatório o uso de ? para parametrização.
     * </b>
     * @param boolean $getQueryString default FALSE<br/>
     * <b>TRUE evita o execução da query e retorna a string formada para execução no banco de dados</b><br/>
     * <b>FALSE executa o procedimento no banco de dados</b><br/>
     * @return boolean TRUE|FALSE|String <br/>
     * TRUE procedimento realizado com sucesso<br/>
     * FALSE falha ao executar procedimento de inserção
     * String da query formada para submit no banco de dados
     * 
    */
    protected function delete($table, $where = null, $getQueryString = false)
    {
        return $this->adapter->delete($table, $where, $getQueryString);
    }
    
    /**
     * Método update<br/>
     * <b>Dica: </b>Fique atento ao parametro where, sob o risco de modificar toda sua base de dados.
     * @param String $table Nome da tabela
     * @param array $args array contendo nomes e valores dos campos a serem atualizados<br/>
     * <b>
     * array(<br/>
     *   'campo1' => 'valor',<br/>
     *   'campo2' => 'valor'<br/>
     *   ...<br/>
     * )<br/>
     * </b>
     * @param Array $where este array contera os termos para atualização dos dados<br/>
     * <b>
     * ex.: array( <br/>
     *  'tabela.campo = ?' => array( <br/>
     *      'valor do ?', '...' <br/>
     *  )<br/>
     * )<br/>
     * Dica: * ? representa o valor a ser feito bind obrigatório o uso de ? para parametrização.
     * </b>
     * @param boolean $getQueryString default FALSE<br/>
     * <b>TRUE evita o execução da query e retorna a string formada para execução no banco de dados</b><br/>
     * <b>FALSE executa o procedimento no banco de dados</b><br/>
     * @return boolean TRUE|FALSE|String <br/>
     * TRUE procedimento realizado com sucesso<br/>
     * FALSE falha ao executar procedimento de inserção
     * String da query formada para submit no banco de dados
     * 
    */
    protected function update($table, array $args, $where = null, $getQueryString = false)
    {
        return $this->adapter->update($table, $args, $where, $getQueryString);
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
     *                    PDO::FETCH_NUM<br/>
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
     *                    PDO::FETCH_NUM<br/>
     *                    e outros<br/>
     * @param String $class Indica a qual object deseja-se transforma o retorno da consulta<br/>
     * @return Object|Array<br/>
     */
    protected function fetch($type = null, $class = null)
    {
        return $this->adapter->fetch($type, $class);
    }
    
    /**
     * Retorna a Query formada a ser submetida a base de dados
     * @param int $operation Selecionará o tipo de retorno<br/>
     * <b>
     * self::SQL_STRING - 1 retornará uma \String com a query (default)<br/>
     * self::SQL_OBJECT - 2 retornará um \ArrayObject separado por procedimentos sql
     * </b>
     * @return String|\ArrayObject 
    */
    protected function getQuery($operation = self::SQL_STRING)
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