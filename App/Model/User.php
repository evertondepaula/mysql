<?php

namespace App\Model;

use EpClasses\DataBase\Adapter;

/**
 * Usuario
 * @author tom
 */
class User extends Adapter
{
    
    private $table = "users";
            
    public function getUsers()
    {
        return $this->select(array($this->table => array("nome", "sobrenome", "email", "dtCadastro")))->fetch(\PDO::FETCH_NUM);
    }
    
    public function newUser()
    {
        $dt = new \DateTime();
        $dt = $dt->format("Y-m-d H:i:s");
        $this->insert($this->table, array('nome', 'sobrenome', 'email', 'dtCadastro'),                               
                                array(
                                    array("Carlos", "Manuel", "chubulubi@gmail.com",$dt),
                                    array("Mano", "Jow", "jow@gmail.com",$dt)
                                )
                         );
        return $this->getlastInsertId();
    }
    
    public function updateUser()
    {
        return $this->update($this->table,
                            array(
                                "nome" => "Everton Jose de",
                                "sobrenome" => "Paula"
                            ), 
                            array(
                                "{$this->table}.idUsers = ?" => array(1)
                            )
                      );
    }
    
    public function deleteUser()
    {
        return $this->delete($this->table, 
                        array(
                           "{$this->table}.idUsers > ?" => array(1)
                        )
                      );
    }
}
