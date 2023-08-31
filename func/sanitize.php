<?php

function escape($string)
{
    $escape = htmlentities($string, ENT_QUOTES, 'utf-8');
    return $escape;
}

function autoLoader($class)
{
    require_once 'classes/' . $class . '.php';
}
