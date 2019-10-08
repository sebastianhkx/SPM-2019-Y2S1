<?php
require_once 'include/common.php';
require_once 'include/token.php';

?>
<html>
<head>
  <title>BIOS Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
</head>
<body>

<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand">BIOS</a>
</nav>

<style>
table, th, td {
  border: 0px solid black;
}

th, td {
  padding: 10px;
}
</style>
</head>

<?php
if ( !isset($_SESSION['userid']) ) {
    if (isset ($_POST['userid'])) {
    $wrong_userid = $_POST['userid'];
    }
    else {
        $wrong_userid = "";
    }
    ?>
    <html>
        <head>
            <!--<link rel="stylesheet" type="text/css" href="include/style.css">-->
        </head>
        <body>
        <div class="container">
            <h1>Login</h1>
            <form method='POST' action='login.php'>
                <table border='0'>
                    <tr>
                        <td width="100">User ID</td>
                        <td>
                            <input name='userid' value=<?= $wrong_userid ?> >
                        </td>   
                    </tr>
                    <tr>
                        <td>Password</td>
                        <td>
                            <input name='password' type='password' />
                        </td>
                    </tr>
                    <tr>
                        <td colspan='2' style="text-align:right">
                            <input name='Login' type='submit' />
                        </td>
                    </tr>
                </table>             
            </form>
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
                echo "<font color='red'>$error</font>";
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
                echo "<font color='red'>$error</font>";
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







    
    
