<?php
require_once 'private/autoload.php';
$user=$name=$email=$password=$confirm='';
$loc=isset($_SESSION['url'])?$_SESSION['url']:"admin.php";
if (isset($_POST['save'])){
    $err = [];
    if(isset($_POST['email']) && !empty($_POST['email']) && trim($_POST['email'])){
        $email = $_POST['email'];
        $sql = "SELECT COUNT(*) AS count FROM admin_tb WHERE email = '$email'";
        $result = mysqli_query($connection, $sql);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $count = $row['count'];
            if ($count > 0) {
                $err['email'] = "Email is already taken";
            }
        }
        if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,3}$/", $email)) {
            $err['email'].= "Enter valid email";
        }
    } else {
        $err['email'] =  "Enter email";
    }
    if(isset($_POST['name']) && !empty($_POST['name']) && trim($_POST['name'])){
        $name= $_POST['name'];
    }else{
        $err['name']="Enter name";
    }
    if(isset($_POST['user']) && !empty($_POST['user']) && trim($_POST['user'])){
        $user= $_POST['user'];
        $sql = "SELECT COUNT(*) AS count FROM admin_tb WHERE username = '$user'";
        $result = mysqli_query($connection, $sql);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $count = $row['count'];
            if ($count > 0) {
                $err['user'] = "Username already taken";
            }
        }
    }else{
        $err['user']="Enter username";
    }
    if(isset($_POST['password']) && !empty($_POST['password'])){
        $password= $_POST['password'];
    }
    else{
            $err['password']='Enter password';
    }
    if(isset($_POST['confirm']) && !empty($_POST['confirm'])){
        $confirm= $_POST['confirm'];
        if($password===$confirm){
            $pass=($_POST['password']);
        }
        else{
            $err['confirm']='Password not matched';
        }

    }else{
        $err['confirm']="Confirm password";
    }
    $status=$_POST['status'];
    if(count($err)==0){
        $email=mysqli_real_escape_string($connection, $email);
        $insert = "INSERT INTO admin_tb(name,username, email, password,status) 
        VALUES ('$name','$user', '$email', '$pass','$status')";
        if ($connection->query($insert)) {
            $_SESSION['success'] = "Admin Added Successfully!";
        } else {
            $_SESSION['failure'] = "Failed to add admin";
        }  
        header('Location:'.$loc);
        exit();
    }
}
if(isset($_POST['cancel'])){
    header('Location: '.$loc);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Admin</title>
    <link rel="stylesheet" href="css/add.css">
</head>
<body>
    <?php include_once 'menu.php';?>
    <div class='container'>
        <div class='header'>
            <div class='header-left'>
                <div class='detail'>Add Admin </div>
            </div>
        </div>
        <div class='form-body-1'>
            <div class='form-body-r'>
                <form action="<?php echo $_SERVER['PHP_SELF'];?>" method='post'>
                    <div class='form-row'>
                        <label for="name">Name</label>
                        <input type="text" name='name' value="<?php echo $name;?>">
                        <span class='err-msg'><?php echo (isset($err['name']))?$err['name']:''; ?></span>
                    </div>
                    <div class='form-row'>
                        <label for="user">Username</label>
                        <input type="text" name='user' value="<?php echo $user;?>">
                        <span class='err-msg'><?php echo (isset($err['user']))?$err['user']:''; ?></span>
                    </div>
                    <div class='form-row'>
                        <label for="email">Email</label>
                        <input type="text" name='email' value="<?php echo $email;?>">
                        <span class='err-msg'><?php echo (isset($err['email']))?$err['email']:''; ?></span>
                    </div>
                    <div class='form-row'>
                        <label for="password">Password</label>
                        <input type="password" name='password'>
                        <span class='err-msg'><?php echo (isset($err['password']))?$err['password']:''; ?></span>
                    </div>
                    <div class='form-row'>
                        <label for="Confirm">Confirm Password</label>
                        <input type="password" name='confirm'>
                        <span class='err-msg'><?php echo (isset($err['confirm']))?$err['confirm']:''; ?></span>
                    </div>
                    <div class='form-row'>
                        <label for="status">Status:</label>             
                            <input type="radio" name="status" value='1' checked="">Active
                            <input type="radio" name="status" value='0'>Deactive
                    </div>
                    <div class='form-row'>
                        <input type="submit" name='save' value='Save'>
                        <input type="submit" name='cancel' value='Cancel'>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>