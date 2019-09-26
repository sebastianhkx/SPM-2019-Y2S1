<?php
require_once 'include/common.php';
session_start();

if ( !isset($_SESSION['userid']) ) {

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
    if ( isset($_POST['userid']) && isset($_POST['password']) ) {
        $userid = $_POST['userid'];
        $password = $_POST['password'];
    
        # if admin, log into admin home page
        if ( $userid === "admin") {
            $dao = new AdminDAO();
            $admin = $dao->retrieve($userid);
        
            if ( $admin != null && $admin->authenticate($password) ) {
                $_SESSION['userid'] = $userid; 
    
                header("Location: home_admin.php");
                exit;
            }
            else {
                $error = 'Incorrect userid or password!';
                echo $error;
            }
        }
        
        # if student, log into student home page
        else {
            $dao = new StudentDAO();
            $student = $dao->retrieve($userid);
    
            if ( $student != null && $student->authenticate($password) ) {
                $_SESSION['userid'] = $userid; 
            
                header("Location: home.php");
                exit;
            }
            else {
                $error = 'Incorrect userid or password!';
                echo $error;
            }
        }
    }
}

# if session key exits, redirect to respective home page
else {
    if ($_SESSION['userid'] == "admin") {
    header("Location: home_admin.php");
    exit;
    }

    else {
        header("Location: home.php");
        exit;
    }
}







    
    
