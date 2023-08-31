<?php

class Session {

    public static function put($name, $value)
    {
        return $_SESSION[$name] = $value;
    }

    public static function get($value)
    {
        return $_SESSION[$value];
    }

    public static function exists($value)
    {
        return (isset($_SESSION[$value])) ? true : false;
    }

    public static function delete($value)
    {
        if (self::exists($value))
        {
            unset($_SESSION[$value]);
        }
    }

    public static function flash($status, $message = '')
    {
        if (self::exists($status))
        {
            $statusValue = self::get($status);
            self::delete($status);
            return $statusValue;
        }
        else
        {
            self::put($status, $message);
        }
    }

}
