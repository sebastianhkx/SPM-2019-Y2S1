<?php
require_once 'include/common.php';
require_once 'include/token.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>BIOS Login</title>

  <!-- Custom fonts for this template-->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
    <div class="container">
      <a class="navbar-brand js-scroll-trigger" href="">Merlion University BIOS</a>
    </div>
</nav>

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
<body>
<div class="my-5">
    <div class="card-body p-5">
        <div class="p-5">
            <div class="container col-lg-3">

                <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4">Please log in</h1>
                </div>

                <form method='POST' action='login.php'>
                <div class="form-group">
                    <input name="userid" class="form-control form-control-user" placeholder="Enter User ID" value=<?= $wrong_userid ?>>
                </div>

                <div class="form-group">
                    <input name="password" type='password' class="form-control form-control-user" placeholder="Password">
                </div>

                <input name='Login' type='submit' class="btn btn-primary btn-user btn-block">
                </a>
                </form>

            </div>
        </div>
        <?php
    if ( isset($_GET['error']) ) {
        echo "<div class='text-center'><font color='red'>{$_GET['error']}</font></div>";
    }

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
                echo "<div class='text-center'><font color='red'>$error</font></div>";
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
                echo "<div class='text-center'><font color='red'>$error</font></div>";
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
?>

    </div>
</div>
  
</html>

<html>
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="js/sb-admin-2.min.js"></script>

</body>
</html>



    
    
