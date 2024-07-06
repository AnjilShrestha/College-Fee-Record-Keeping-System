<?php
require_once 'private/autoload.php';
$loc=isset($_SESSION['url'])?$_SESSION['url']:"admin.php";
if (!isset($_GET['id'])) {
    header('location:'.$loc);
} else {
    $id = $_GET['id'];
    $sql = "SELECT * FROM admin_tb WHERE admin_id='$id'";
    $retrieve_admin = mysqli_query($connection, $sql);
    if (mysqli_num_rows($retrieve_admin) > 0) {
        $data = mysqli_fetch_assoc($retrieve_admin);
        extract($data);
    } else {
        header('location:'.$loc);
        exit();
    }
}
if(isset($_POST['cancel'])){
    header('Location:'.$loc);
}
if (isset($_POST['btneditadmin'])) {
    $err = [];
    if (isset($_POST['email']) && !empty($_POST['email']) && trim($_POST['email'])) {
        $email = $_POST['email'];
        if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
            $err['email'] = "Enter valid email";
        }
        $sql = "SELECT COUNT(*) AS count FROM admin_tb WHERE email = '$email' AND admin_id!=$id";
        $result = mysqli_query($connection, $sql);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $count = $row['count'];
            if ($count > 0) {
                $err['email']="Email is already taken";
            }
        }
    } else {
        $err['email'] = "Enter email";
    }

    if (isset($_POST['name']) && !empty($_POST['name']) && trim($_POST['name'])) {
        $name = $_POST['name'];
    } else {
        $err['name'] = "Enter name";
    }
    if (isset($_POST['username']) && !empty($_POST['username']) && trim($_POST['username'])) {
        $username = $_POST['username'];
        $sql = "SELECT COUNT(*) AS count FROM admin_tb WHERE username = '$username' AND admin_id!=$id";
        $result = mysqli_query($connection, $sql);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $count = $row['count'];
            if ($count > 0) {
                $err['user'] = "Username already taken";
            }
        }
    } else {
        $err['username'] = "Enter name";
    }
    if (isset($_POST['password']) && !empty($_POST['password']) && trim($_POST['password'])) {
        $password = ($_POST['password']);
    }
    $status=$_POST['status'];
    if (count($err) == 0) {
        $user=$username;
        if (!empty($_POST['password'])) {
            $update = "UPDATE  admin_tb SET name='$name', username='$user', email='$email', password='$password',status='$status' 
            WHERE admin_id=$id";
        } else {
            $update = "UPDATE  admin_tb SET name='$name',username='$user', email='$email',status='$status' 
            WHERE admin_id=$id";
        }
        $connection->query($update);
        if ($connection->affected_rows==1) {
            $_SESSION['success'] = "Data successfully updated!";
            header('location:'.$loc);
            exit();
        } else {
            $_SESSION['failure']="Data not updated";
            header('location:'.$loc);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Admin</title>
    <link rel="stylesheet" href="css/add.css">
</head>
<body>
    <?php include_once 'menu.php';?>
    <div class="container">
        <div class='header'>
            <div class='header-right'>
                <div class='detail'>Edit Admin </div>
            </div>
        </div>
        <div class='form-body-1'>
            <div class='form-body-r'>
                <form action="<?php echo $_SERVER['PHP_SELF'];?>?id=<?php echo $id ?>" method='post'>
                    <div class='form-row'>
                        <label for="name">Name</label>
                        <input type="text" name='name' value="<?php echo $name;?>">
                        <span class='err-msg'><?php echo (isset($err['name']))?$err['name']:''; ?></span>
                    </div>
                    <div class='form-row'>
                        <label for="username">Username</label>
                        <input type="text" name='username' value="<?php echo $username;?>">
                        <span class='err-msg'><?php echo (isset($err['username']))?$err['username']:''; ?></span>
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
                        <label for="status">Status:</label>
                        <?php if($status==1){?>
                            <input type="radio" name="status" value='1' checked="">Active
                            <input type="radio" name="status" value='0'>Deactive
                        <?php }else{ ?>
                            <input type="radio" name="status" value='1'>Active
                            <input type="radio" name="status" value='0' checked="">Deactive
                            <?php } ?>
                    </div>
                    <div class='form-row'>
                        <input type="submit" name='btneditadmin' value='Update'>
                        <input type="submit" name='cancel' value='Cancel'>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
