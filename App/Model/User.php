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
        return $this->select(array("Users" => 
                                        array("*")
                                    )
                              )
                    ->fetch();
    }
}
