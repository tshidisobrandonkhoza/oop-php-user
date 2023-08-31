<!--<!DOCTYPE html>-->
<?php
require_once 'src/App.php';
if (Incoming::exists())
{

    if (Token::check(Incoming::get('xToken')))
    {
        $validation = new Validation();
        $valid = $validation->check($_POST, ['username' => ['required' => true, 'min' => 2, 'max' => 20, 'unique' => 'users'],
            'firstname' => ['required' => true, 'min' => 2, 'max' => 50],
            'lastname' => ['required' => true, 'min' => 2, 'max' => 50],
            'password' => ['required' => true, 'min' => 8, 'max' => 50],
            'co-password' => ['required' => true, 'matches' => 'password']]);

        if ($validation->passed())
        {

            $user = new User();

            try {
                $salt = Hash::salt(32);
                $password = Hash::make(Incoming::get('password'), $salt);

                $userData = ['username' => Incoming::get('username'),
                    'fname' => Incoming::get('firstname'),
                    'lname' => Incoming::get('lastname'),
                    'password' => $password,
                    'salt' => $salt,
                    'group_id' => 1];

                $user->create($userData);

                Session::flash('Logout', 'Succesfully Registered, Please login');
                Redirect::to('login.php');
            } catch (Exception $exc) {

                Session::flash('Registration', 'Registration couldnt complete');
                Redirect::to('signup.php');
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
            Session::flash('Registration', $err);
            Redirect::to('signup.php');
        }
    }
}
if (Session::exists('Registration'))
{
    echo Session::flash('Registration');
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
        <form name="register" method="POST">
            <label for="username"> Username
                <input type="text" name="username" id="username" value="<?= escape(Incoming::get('username')) ?>"  autocomplete="off" />
            </label>
            <label for="username"> First Name
                <input type="text" name="firstname" id="firstname" value="<?= escape(Incoming::get('firstname')) ?>"  autocomplete="off" />
            </label>
            <label for="username">Last Name
                <input type="text" name="lastname" id="lastname" value="<?= escape(Incoming::get('lastname')) ?>"  autocomplete="off" />
            </label>
            <label for="username">Password
                <input type="text" name="password" id="password" value="<?= escape(Incoming::get('password')) ?>"  autocomplete="off" />
            </label>
            <label for="username">Confirm Passsword
                <input type="text" name="co-password" id="co-password" value="<?= escape(Incoming::get('co-password')) ?>"  autocomplete="off" />
            </label>

            <input type="hidden" name="xToken"  id="xToken"  value="<?= Token::generate() ?>">
            <input type="submit" value="Register">
        </form>
    </body>
</html>


