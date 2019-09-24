<?php
require_once 'include/common.php';


if ( isset($_POST['userid']) && isset($_POST['password']) ) {
    $userid = $_POST['userid'];
    $password = $_POST['password'];


    if ( $userid === "admin") {
        $dao = new AdminDAO();
        $admin = $dao->retrieve($userid);
    
        if ( $admin != null && $admin->authenticate($password) ) {
            $_SESSION['userid'] = $userid; 
            echo "yay";
        }
        header("Location: home_admin.php");
        exit;
    }

    else {
        $dao = new StudentDAO();
        $student = $dao->retrieve($userid);

        if ( $student != null && $student->authenticate($password) ) {
            $_SESSION['userid'] = $userid; 
        
        header("Location: home.php");
        exit;
        }
    }

    $error = 'Incorrect userid or password!';

    // echo "<form action='login.php' method='POST'>";
    echo "<input type='hidden' name='error' value='$error'/>";

    header("Location: login.php");
    exit;
}