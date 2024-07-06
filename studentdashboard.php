<?php
require_once 'private/autoload.php';
$_SESSION['url']=$_SERVER['REQUEST_URI'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/details.css">
    <style>
 .heading {
    font-size: 24px;
    font-weight: bold;
}
    </style>
</head>
<body>
    <?php
    include_once 'studentmenu.php';
    ?>
    <div class="container">
        <div class='header'>
                <div class='header-left'>
                    <div class='detail'>
                        <div class="heading">
                        <p>Welcome <?php echo $name; ?></p>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</body>
</html>