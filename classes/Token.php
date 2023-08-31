<?php

class Token {

    public static function generate()
    {
        return Session::put(Config::get('xSession/token_name'), md5(uniqid()));
    }

    public static function check($token)
    {
        $token_name = Config::get('xSession/token_name');
        if (Session::exists($token_name) && $token === Session::get($token_name))
        {
           Session::delete($token_name);
            return true;
        }
       return false;
    }

}
