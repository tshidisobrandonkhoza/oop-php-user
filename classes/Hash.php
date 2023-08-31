<?php

class Hash {

    public static function make($param, $salt = '')
    {
        return hash('sha256', $param . $salt);
    }

//improve security of hash
    public static function salt($size)
    {
        return mcrypt_create_iv($size);
    }

    public static function unique()
    {
        return self::make(uniqid());
    }

}
