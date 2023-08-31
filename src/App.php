<?php

//initial sessions for all classes
session_start();
define('HOST', '127.0.0.1');
define('DBNAME', 'base_db');
define('USER', 'root');
define('PASS', '');

$GLOBALS['config'] = ['xDB' => ['host' => HOST, 'db' => DBNAME, 'user' => USER, 'password' => PASS],
    'xRemember' => ['cookie_name' => 'hash', 'cookie_expiry' => 604800],
    'xSession' => ['session_name' => 'user', 'token_name' => 'token']];



require_once 'func/sanitize.php';
spl_autoload_register('autoLoader');


if (Cookies::exists(Config::get('xRemember/cookie_name')) && !Session::exists(Config::get('xSession/session_name')))
{
    $cId = Cookies::get(Config::get('xRemember/cookie_name'));
    $checkId = DB::getInstance()->get('users_session', ['hash', '=', $cId]);
    //echo $checkId;
    if ($checkId->count())
    {
        $user = new User($checkId->result()->user_id);
        $user->login();
    }
    
}


