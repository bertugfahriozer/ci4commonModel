<?php

namespace App\Libraries;

class CommonLibrary
{
    public function setPassword($users,string $password)
    {
        if (isset($users->password)) {
            $users->password = password_hash($password, PASSWORD_BCRYPT);
            $users->updated_at = date('Y-m-d H:i:s');
            return (array)$users;
        }
    }
}