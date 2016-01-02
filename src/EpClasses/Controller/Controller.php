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
    protected $title;

    public function __construct($title) {
        $this->view = new \stdClass();
        $this->title = $title;
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
        if(file_exists("../App/Views/Templates/".$template.".phtml")):
            include_once "../App/Views/Templates/".$template.".phtml";
        else:
            $this->getContent();
        endif;
    }
    
    /**
     * Seleciona o caminho do arquivo a ser renderizado
     */
    public function getContent(){
        $atual = get_class($this);
        $singleClassName = strtolower(str_replace("App\\Controllers\\", "", $atual));
        include_once "../App/Views/".$singleClassName."/".$this->action."phtml";
    }   
}