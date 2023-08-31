<!--<!DOCTYPE html>-->
<?php
require './src/App.php';


$user = new User();
if (!$user->isLoggedIn())
{
    Redirect::to('index.php');
}


if (Incoming::exists())
{
    if (Token::check(Incoming::get('xToken')))
    {
        $validation = new Validation();

        $vald = $validation->check($_POST, [
            'firstname' => ['required' => true, 'min' => 2, 'max' => 50],
            'lastname' => ['required' => true, 'min' => 2, 'max' => 50]]);
        if ($validation->passed())
        {
            try {
                $userData = ['fname' => Incoming::get('firstname'),
                    'lname' => Incoming::get('lastname')];

                $user->update($userData, $user->data()->id);

                Session::flash('Update', 'Sucessfully updated user profile');
                Redirect::to('update.php');
            } catch (Exception $exc) {

                Session::flash('Update', 'Update Error');
                Redirect::to('update.php');
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
            Session::flash('Update', $err);
            Redirect::to('update.php');
        }
    }
}

if (Session::exists('Update'))
{
    echo Session::flash('Update');
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
        <form name="" method="POST">
            <label for="username"> First Name
                <input type="text" name="firstname" id="fname" value="<?= escape($user->data()->fname) ?>"  autocomplete="off" />
            </label>
            <label for="username">Last Name
                <input type="text" name="lastname" id="lname" value="<?= escape($user->data()->lname) ?>"  autocomplete="off" />
            </label>
            <input type="hidden" name="xToken"  id="xToken"  value="<?= Token::generate() ?>">
            <input type="submit" value="Update">
        </form>
    </body>
</html>
