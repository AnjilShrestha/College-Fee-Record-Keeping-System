<?php
include_once ('header.php');
require_once 'private/autoload.php';
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true) {
    if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'student'){
        header("Location: ./studentdashboard.php");
        exit;
    }else{
        header("Location: ./dashboard.php");
        exit;
    }
}?>
<?php
$email = $pass = '';
if (isset($_POST['submit'])) {
    $err = [];
    if (!empty($_POST['email']) && trim($_POST['email'])) {
        $email = $_POST['email'];
        if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
            $err['email']= "Enter valid email";
        }
    } else {
        $err['email'] = "Enter your email";
    }

    if (!empty($_POST['password'])) {
        $pass = md5($_POST['password']);
    } else {
        $err['password'] = "Enter password";
    }

    if (count($err) == 0) {
        //preventing the sql injection
        $email = mysqli_real_escape_string($connection, $email);
        $pass = mysqli_real_escape_string($connection, $pass);
        $select = "SELECT * FROM student_tb WHERE email='$email'";
        $result = mysqli_query($connection, $select);
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            
            if ($pass===$row['password']) {
                $_SESSION['name'] = $row['name'];
                $_SESSION['login_time'] = time();
                $_SESSION["loggedin"] = true;
                $_SESSION['id'] = $row['student_id'];
                $_SESSION['user_type'] ='student';
                if (isset($_POST['remember'])) {
                    setcookie('name', $row['name'], time() + 24 * 60 * 60);
                }
                header('Location: studentdashboard.php');
                exit;
            } else {
                $err['wrong'] = 'Incorrect password';
            }
        } else {
            $err['wrong'] = 'Email not found';
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
  <div class="main-login">
    <div class="left-login">
    <marquee><h1>Hi! Welcome to College Fee Manager</h1></marquee>
    </div>
    <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
        <div class="right-login">
            <div class="card-login">
              <h1>Student Login</h1>
              <span class='err-msg'><?php echo (isset($err['wrong'])) ? $err['wrong'] : ''; ?></span>
                <div class="textfield">
                    <label for="email">Enter Email</label>
                    <span class='err-msg'><?php echo (isset($err['email'])) ? $err['email'] : ''; ?></span>
                    <input type="text" name="email" placeholder="Email">
                </div>
                <div class="textfield">
                    <label for="password">Password</label>
                    <span class='err-msg'><?php echo (isset($err['password'])) ? $err['password'] : ''; ?></span>
                    <input type="password" name="password" placeholder="Password" id="password">
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
<script src='./javascript/pass.js'>
</script>
</body>
</html>
