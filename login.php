<!--<!DOCTYPE html>-->
<?php
require_once 'src/App.php';
if (Incoming::exists())
{
    if (Token::check(Incoming::get('xToken')))
    {
        $validation = new Validation();
        $valid = $validation->check($_POST, ['username' => ['required' => true],
            'password' => ['required' => true]
        ]);
        if ($validation->passed())
        {
            $user = new User();
            try {

                $remember = Incoming::get('remember') === 'on' ? true : false;

                $login = $user->login(Incoming::get('username'), Incoming::get('password'), $remember);
                if ($login)
                {
                    Session::flash('Success', 'Successfully Logged In');
                    Redirect::to('index.php');
                }
                else
                {

                    Session::flash('Logout', 'Failed to Log In');
                    Redirect::to('login.php');
                }
            } catch (Exception $exc) {

                Session::flash('Logout', 'Error with Loggin In');
                Redirect::to('login.php');
            }
        }
        else
        {
            $errors = $valid->errors();
            $err = '';
            foreach ($errors as $e)
            {
                (empty($err)) ? $err = $e . '<br/>' : $err .= $e . '<br/>';
            }
            Session::flash('Logout', $err);
            Redirect::to('login.php');
        }
    }
}

if (Session::exists('Logout'))
{
    echo Session::flash('Logout');
}


$user = new User();
if ($user->isLoggedIn())
{
    //echo $user->data()->username;
}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <style type="text/css" >
            form label{
                display:flex;
                flex-direction: row;
            }
        </style>
    </head>
    <body>
        <a href="index.php">Home</a>
        <form name="login" method="POST">
            <label for="username"> Username
                <input type="text" name="username" id="username" value="<?= escape(Incoming::get('username')) ?>"  autocomplete="off" />
            </label>
            <label for="username">Password
                <input type="text" name="password" id="password" value="<?= escape(Incoming::get('password  ')) ?>"  autocomplete="off" />
            </label>


            <label for="remember">
                <input type="checkbox" name="remember" id="remember"  />

                remember me
            </label>

            <input type="hidden" name="xToken"  id="xToken"  value="<?= Token::generate() ?>">
            <input type="submit" value="Login">
        </form>
    </body>
</html>


