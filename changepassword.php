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

        $valid = $validation->check($_POST, ['oldpassword' => ['required' => true, 'min' => 8, 'max' => 50],
            'password' => ['required' => true, 'min' => 8, 'max' => 50],
            'co-password' => ['required' => true, 'matches' => 'password']]);



        if ($validation->passed())
        {
            try {
                if (Hash::make(Incoming::get('oldpassword'), $user->data()->salt) !== $user->data()->password)
                {

                    Session::flash('Update', 'Your password is incorrect');
                    Redirect::to('changepassword.php');
                }
                else
                {


                    $salt = Hash::salt(32);
                    $password = Hash::make(Incoming::get('password'), $salt);



                    $userData = ['password' => $password, 'salt' => $salt];

//                    'lname' => Incoming::get('lastname')];
//
                    $user->update($userData, $user->data()->id);
//



                    Session::flash('Update', 'Your password updated');
                    Redirect::to('changepassword.php');
                }
            } catch (Exception $exc) {
//
                Session::flash('Update', 'Update Error');
                Redirect::to('changepassword.php');
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
            Redirect::to('changepassword.php');
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
            <label for="oldpassword"> Current Password
                <input type="text" name="oldpassword" id="oldpassword" value=""  autocomplete="off" />
            </label>
            <label for="password">New Password
                <input type="text" name="password" id="password" value=""  autocomplete="off" />
            </label>

            <label for="co-password">Confirm Password
                <input type="text" name="co-password" id="co-password" value=""  autocomplete="off" />
            </label>


            <input type="hidden" name="xToken"  id="xToken"  value="<?= Token::generate() ?>">
            <input type="submit" value="Update">
        </form>
    </body>
</html>
