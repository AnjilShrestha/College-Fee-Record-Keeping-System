<?php
require_once 'private/autoload.php';
$loc=isset($_SESSION['url'])?$_SESSION['url']:"studentdashboard.php";
if(isset($_POST['change'])){
    $err=[];
    if(isset($_POST['old']) && !empty($_POST['old'])){
        $old =md5($_POST['old']);
   } else {
       $err['old'] =  "Enter old password";
   }
   if(isset($_POST['new']) && !empty($_POST['new'])){
    $new = $_POST['new'];
    } else {
        $err['new'] =  "Enter password";
    }
    if(isset($_POST['confirm']) && !empty($_POST['confirm'])){
        $confirm = $_POST['confirm'];
        if($new===$confirm){
            $pass=md5($_POST['new']);
        }
        else{
            $err['msg']='Password not matched';
        }
   } else {
       $err['confirm'] =  "Enter password";
   }
   if(count($err)==0){
        $id=$_SESSION['id'];
        $pass=my_sqli_real_escape_string($connection,$pass);
        $id=my_sqli_real_escape_string($connection,$id);
        $check="SELECT password FROM student_tb WHERE student_id='$id'";
        $result = $connection->query($check);
        if($result->num_rows>0){
            $row = $result->fetch_assoc();
            $password=$row['password'];
            if($password===$old){
                $update_password= "UPDATE student_tb SET password='$pass' WHERE student_id=$id";
                $connection->query($update_password);
                if($connection->affected_rows ==1 ){
                    $_SESSION['success'] ="Password Update Success";
                    header('location:'.$loc);
                }
                else{
                    $_SESSION['failure'] = "Password Update Failed.Try again Later!";
                    header('location:'.$loc);
                }
            }else{
                $err['msg']="Old Password not matched";
            }
        }
    }
}
if(isset($_POST['cancel'])){
    header('location:studentdashboard.php');
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/change.css">
	<title></title>
</head>
<body>
	<?php
        include_once './studentmenu.php';
    ?>
    <div class='container'>
        <div class='row-c1'>
            <div class="heading">
                <h1>My Account-Change password</h1>
            </div>
            <div class="change">
                <div class="change-row">
                    <h3>Change Password</h3>
                    <?php if(isset($success)){echo $success;}if(isset($failure)){echo $failure;}?>
                    <form method="post" action=''>
                        <div>
                            <span class='error-msg'><?php echo (isset($err['msg']))?$err['msg']:''; ?></span>
                            <label for='old'>Old Password</label>
                            <input type="password" name='old'/>
                            <span class='error-msg'><?php echo (isset($err['old']))?$err['old']:''; ?></span>
                        </div>
                        <div>
                            <label for='new'>New Password</label>
                            <input type="Password" name='new'/>
                            <span class='error-msg'><?php echo (isset($err['new']))?$err['new']:''; ?></span>
                        </div>
                        <div>
                            <label for='confirm'>Confirm Password</label>
                            <input type="Password" name='confirm'/>
                            <span class='error-msg'><?php echo (isset($err['confirm']))?$err['confirm']:''; ?></span>
                        </div>
                        <div>
                            <input type="submit" name='change' value='update'/>
                            <input type="submit" name='cancel' value='cancel'/>
                        </div>
                    </form>
                </div>
            </div>
        </div>  
    </div>
</body>
</html>