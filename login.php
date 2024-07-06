<?php
$user = $pass = '';
if (isset($_POST['submit'])) {
    $err = [];
    if (isset($_POST['user']) && !empty($_POST['user']) && trim($_POST['user'])) {
        $user = $_POST['user'];
    } else {
        $err['user'] = 'Enter username';
    }
    if (!empty($_POST['pass'])) {
        $pass = $_POST['pass'];
    } else {
        $err['pass'] = "Enter password";
    }
    if (count($err) == 0) {
        require_once 'private/autoload.php';
        $user = mysqli_real_escape_string($connection, $user);
        $pass = mysqli_real_escape_string($connection, $pass);
        $select = "SELECT * FROM admin_tb WHERE username='$user' AND status=1";
        $result = mysqli_query($connection, $select);
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

            if (($pass=== $row['password'])) {
                $_SESSION['name'] = $row['name'];
                $_SESSION['login_time'] = time();
                $_SESSION["loggedin"] = true;
                $_SESSION['id'] = $row['admin_id'];
                $_SESSION['user_type'] = 'admin';
                if (isset($_POST['remember'])) {
                    setcookie('name', $row['name'], time() + 2*24 * 60 * 60);
                }
                header('Location: dashboard.php');
                exit;
            } else {
                $err['wrong'] = 'Incorrect password';
            }
        } else {
            $err['wrong'] = 'Username not found';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="css/login.css">
  <title>Login</title>
</head>
<body>
    <?php include_once 'header.php'?>
  <div class="main-login">
    <div class="left-login">
    <marquee><h1>Hi! Welcome to College Fee Manager</h1></marquee>
    </div>
    <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
        <div class="right-login">
            <div class="card-login">
              <h1>Administrator Login</h1>
              <span class='err-msg'><?php echo (isset($err['wrong'])) ? $err['wrong'] : ''; ?></span>
                <div class="textfield">
                    <label for="user">Enter Username</label>
                    <span class='err-msg'><?php echo (isset($err['user'])) ? $err['user'] : ''; ?></span>
                    <input type="text" name="user" placeholder="Username" value="<?php echo $user; ?>">
                </div>
                <div class="textfield">
                    <label for="pass">Password</label>
                    <span class='err-msg'><?php echo (isset($err['pass'])) ? $err['pass'] : ''; ?></span>
                    <input type="password" name="pass" placeholder="Password" id="password">
                </div>
                <div class="textfield">
                    <input type="checkbox" class="check-pass" id="togglePassword">
                    <label for="togglePassword" class="check-password">Show Password</label>
                </div>
                <div class="textfield">
                    <input type="checkbox" name="remember" id="remember" value="remember" />Remember me
                </div>
                  <input type="submit" name="submit" value="Login Now" class="btn-login" />
              </div>
          </div>
        </div>
    </form>
</div>
<script src='./javascript/pass.js'></script>
</body>
</html>
