<?php

class Config {

    private static $_config = null;

    public static function get($path = null)
    {
        self::setConfig();
        if ($path)
        {
            $config = self::$_config;
            $path = explode('/', $path);
            foreach ($path as $p)
            {
                if (isset($config[$p]))
                {
                    $config = $config[$p];
                }
            }
            return $config;
        }
        else
        {
            return false;
        }
    }

    private static function setConfig()
    {
        return self::$_config = $GLOBALS['config'];
    }

}
