<?php
require_once 'include/common.php';

$error = "";
if ( !isset($_SESSION['userid']) ) {
    if ( isset($_POST['error']) ) {
        $error = $_POST['error'];
        } 
    ?>
    <html>
        <head>
            <!--<link rel="stylesheet" type="text/css" href="include/style.css">-->
        </head>
        <body>
            <h1>Login</h1>
            <form method='POST' action='login_process.php'>
                <table border='0'>
                    <tr>
                        <td>User ID</td>
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
    <?php
}

else {
    header("Location: home.php");
    exit;
}







    
    
