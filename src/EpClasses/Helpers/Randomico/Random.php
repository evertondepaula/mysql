<?php

namespace EpClasses\Helpers\Randomico;

/**
 * <b>Random:</b> Classe utilizada para geração randômica de valores
 * String | Int
 * @author tom
 */
class Random {
    
    /* Constantes utilizadas como parâmetros */
    const LOWERCASE = 1;
    const UPPERCASE = 2;
    const ALPHABET_LOWERCASE = "abcdefghijklmnopqrstuvxwyz";
    const ALPHABET_UPPERCASE = "ABCDEFGHIJKLMNOPQRSTUVXWYZ";
    
    /**
     * <b>getRandomLetters: </b> retorna uma sequência de caracteres de [a-zA-Z]
     * @param Int $limit Quantidade limite de caracteres na sequência de letras a serem usadas.
     *                   valor default = 10
     * @param Int $args Determina o tipo de Random
     *                  null - Retornará sequência lowercase e uppercase -default
     *                  1 | ::LOWERCASE - Retornará sequência lowercase
     *                  2 | ::UPPERCASE - Retornará sequência uppercase
     * @return String Sequência de caracteres randômicos, nos casos de parâmetros inválidos o retorno será null.
     */
    public static function getRandomLetters($limit = 10, $args = null)
    {
        if($args === null):
            
            $alphabet = self::ALPHABET_LOWERCASE . self::ALPHABET_UPPERCASE;
        elseif($args === self::LOWERCASE):
            
            $alphabet = self::ALPHABET_LOWERCASE;
        elseif($args === self::UPPERCASE):
            
            $alphabet = self::ALPHABET_UPPERCASE;
        else:
            
            return null;
        endif;
        
        $min = 0;
        $max = strlen($alphabet) - 1;
        $randomLetters = "";
        
        while($limit > 0):
            
            $randomLetters .= $alphabet[rand($min, $max)];
            $limit--;
        endwhile;
        
        return $randomLetters;
    }
}