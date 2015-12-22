<?php

namespace EpClasses\Helpers\Read;

/**
 * <b>ReadYml:</b> Utilizado na leitura de arquivos YML|YAML
 * @author tom
 */
class ReadXml
{
    /**
     * Tranformara o arquivo reader solicitado em array php
     * @param String $file Local do arquivo .xml a ser read e retornado como Array;
     */
    public function getArrayFromXml($file)
    {
        try
        {
            if(file_exists($file)):
                
                $xmlString = file_get_contents($file);
                $dom = new \DOMDocument();
                $dom->loadXML($xmlString);        
                $xml = simplexml_import_dom($dom);
                return $xml;
            endif;
            return null;
        } 
        catch (\Exception $ex) {
            echo "ERRO DE CONVERSÃO XML: ".$ex->getMessage();
            return null;
        }
    }
}