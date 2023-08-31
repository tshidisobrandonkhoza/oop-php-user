<!--<!DOCTYPE html>-->
<?php
require_once 'src/App.php';

if (Session::exists('Success'))
{

    echo Session::flash('Success');
}

$user = new User();
if ($user->isLoggedIn())
{

    if ($user->hasPermission('admin'))
    {
        echo '<br/>Admin :: ';
    }

    echo '<br/> Hello ' . $user->data()->username;
    echo '<br/><a href="logout.php">Logout</a>';
    echo '<br/><a href="profile.php?user='. escape($user->data()->username) .'">Profile</a>';
    echo '<br/><a href="update.php">Update</a>';
    echo '<br/><a href="changepassword.php">Update Password</a>';
    ?>

    <?php
}
else
{
    echo '<a href="login.php">Login</a>';
    echo '<a href="signup.php">Register</a>';
}
?>
 