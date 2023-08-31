<?php

require_once './src/App.php';

$user = new User();
$user->logout();
Redirect::to('index.php');
