<?php
require_once 'include/common.php';

$error = '';

if ( isset($_GET['error']) ) {
    $error = $_GET['error'];
} elseif ( isset($_POST['userid']) && isset($_POST['password']) ) {
    $userid = $_POST['userid'];
    $password = $_POST['password'];

    $dao= new Student();
    $student=$dao->retrieve($userid);

    if ( $student != null && $student->authenticate($password) ) {
        $_SESSION['userid'] = $userid; 
        header("Location: DisplayBids.php");
        return;

    } else {
        $error = 'Incorrect userid or password!';
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
                    <td>Userid</td>
                    <td>
                        <input name='userid' />
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

