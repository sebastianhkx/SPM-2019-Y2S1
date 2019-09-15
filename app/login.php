<?php
require_once 'include/common.php';

$error = '';

if ( isset($_GET['error']) ) {
    $error = $_GET['error'];
} elseif ( isset($_POST['username']) && isset($_POST['password']) ) {
    $username = $_POST['username'];
    $password = $_POST['password'];

<<<<<<< HEAD
    $dao= new StudentDAO();
    $student=$dao->retrieve($userid);
=======
    $dao= new Student();
    $student=$dao->retrieve($username);
>>>>>>> parent of 66ed3dd... fix

    if ( $user != null && $user->authenticate($password) ) {
        $_SESSION['username'] = $username; 
        header("Location: DisplayBids.php");
        return;

    } else {
        $error = 'Incorrect username or password!';
    }


}
?>
<html>
    <head>
        <!--<link rel="stylesheet" type="text/css" href="include/style.css">-->
    </head>
    <body>
        <h1>Login</h1>
        <form method='POST' action='login.php'>
            <table border='0'>
                <tr>
                    <td>Username</td>
                    <td>
                        <input name='username' />
                    </td>
                </tr>
                <tr>
                    <td>Password</td>
                    <td>
                        <input name='password' type='password' />
                    </td>
                </tr>
                <tr>
                    <td colspan='2'>
                        <input name='Login' type='submit' />
                    </td>
                </tr>
            </table>             
        </form>

        </p>
            <?=$error?>
        </p>
        
    </body>
</html>

