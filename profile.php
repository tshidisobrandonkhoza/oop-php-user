<!--<!DOCTYPE html>-->
<?php
require './src/App.php';


$user = new User();
if (!$user->isLoggedIn())
{
    Redirect::to('index.php');
}

if (!$username = Incoming::get('user'))
{
    Redirect::to('index.php');
}
else
{
    $user = new User($username);

    if (!$user->exists())
    {
        Redirect::to(404);
    }
    else
    {
        $data = $user->data();
    }
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
        
 <?php
  echo "<h1>" . $data->username. "</h1>";
 echo "<p>Full Name: " . $data->fname ." ". $data->lname . "</p>";
 ?>
    </body>
</html>
