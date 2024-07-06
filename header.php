<?php
require_once 'private/autoload.php';
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true) {
    if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'student'){
        header("Location: ./studentdashboard.php");
        exit;
    }else{
        header("Location: ./dashboard.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
<nav class="nav">
        <header class="header">
            <div class="side">
                <a class="side-heading" href="index.php">College Fee Manager</a>
            </div>
            <ul class="nav-list">
                <li class="nav-item"><a href="index.php">Home</a></li>
                <li class="nav-item"><a href="studentlogin.php">Student Login</a></li>
                <li class="nav-item"><a href="login.php">Admin Login</a></li>
            </ul>
        </header>
    </nav>
</body>
</html>
