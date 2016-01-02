<?php

namespace EpClasses\Controller;

/**
 * Métodos para controllers
 *  Inclui renderização de paginas
 *
 * @author tom
 */
abstract class Controller
{

    protected $view;
    protected $action;
    protected $title = null;

    public function __construct() {
        $this->view = new \stdClass();
    }
    
    /**
     * Setar titulo da pagina
     * <b>Para uso no template ou layout com $this->title</b>
     * @param type $title Titulo da pagina
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

        /**
     * Renderização da pagina a ser chamada
     * @param type $action Nome do pagina a ser renderizada
     * @param type $template Para uso do template como base 
     * <b>Não é Necessário uso da extensão</b><br/>
     * <b>Trabalha com renderização de arquivos .phtml</b><br/>
     *                                  
     */
    protected function render($action, $template = null)
    {
        $this->action = $action;
        
        if($template !== null && file_exists("../App/Views/Templates/".$template.".phtml")):
            require_once "../App/Views/Templates/".$template.".phtml";
        else:
            $this->getContent();
        endif;
    }
    
    /**
     * Seleciona o caminho do arquivo a ser renderizado
     */
    protected function getContent()
    {
        $atual = get_class($this);
        $singleClassName = str_replace("App\\Controllers\\", "", $atual);
        require_once "../App/Views/".$singleClassName."/".$this->action.".phtml";
    }   
    
    /**
     * Recupera posts
     * @param type $key Chave nome do post
     * @return O conteudo do post
     */
    protected function post($key)
    {
        return filter_input(INPUT_POST, $key);
    }
    
    /**
     * Retorna todos os post em um array
     * @return Array 
     */
    protected function postAll()
    {
        return filter_input_array(INPUT_POST);
    }
    
    /**
     * Recupera gets
     * @param type $key Chave nome do get
     * @return O conteudo do get
     */
    protected function get($key)
    {
        return filter_input(INPUT_GET, $key);
    }
    
    /**
     * Retorna todos os gets em um array
     * @return type Array
     */
    protected function getAll()
    {
        return filter_input_array(INPUT_GET);
    }
}