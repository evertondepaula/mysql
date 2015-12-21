<?php

namespace Epsoftware\Helpers\Filters;

/**
 * Filter Class filtra Iterators multidimensionais
 * * @author tom
 */
class FilterArrayObject extends \FilterIterator
{
    private $filter;
    private $key;

    /**
     * Método construtor parametrizado
     * @param \ArrayObject $arrayObject ArrayObject a ser filtrado
     * @param ArrayIterator $key Chaves do ArrayObject em que se aplicaram o filtro
     * @param String $filter Conteudo a ser filtrado
     */
    public function __construct(\ArrayObject $arrayObject, ArrayIterator $key, $filter)
    {
        parent::__construct($arrayObject->getIterator());
        $this->key = $key;
        $this->filter = $filter;
    }


    /**
     * Retorna em uma iteração apenas apenas os objetos iterators que possuem o filtro especificado
     * @return boolean
     */
    public function accept()
    {
        $current = $this->getInnerIterator()->current();
        foreach ($this->key as $key):
            
            if(strpos($current[$key], $this->filter) !== false):
                
                return true;
            endif;
        endforeach;
        return false;
    }


    /**
    * getObjFiltered retorna um Array de ArrayObject aplicando-se o filtro
    * @return array 
    */
    public function getObjFiltered()
    {
        $filtered = array();
        foreach ($this as $obj):
            
            array_push($filtered, new \ArrayObject($obj));
        endforeach;
        
        return $filtered;
    }
    
}