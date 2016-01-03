<?php

namespace App\Model;

use EpClasses\DataBase\Adapter;

/**
 * Usuario
 * @author tom
 */
class User extends Adapter
{
    public function getUsers()
    {
        return $this->select(array("users u" => array("nome", "sobrenome", "email", "dtCadastro")))->fetch(\PDO::FETCH_NUM);
    }
    
    public function newUser()
    {
        $dt = new \DateTime();
        $dt = $dt->format("Y-m-d H:i:s");
        $this->insert('users', array('nome', 'sobrenome', 'email', 'dtCadastro'),                               
                                array(
                                    array("Carlos", "Manuel", "chubulubi@gmail.com",$dt),
                                    array("Mano", "Jow", "jow@gmail.com",$dt)
                                )
                         );
        return $this->getlastInsertId();
    }
}
