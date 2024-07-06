<?php
require_once 'private/autoload.php';
$id=$_SESSION['id'];
$old=$new=$confirm='';
$loc=isset($_SESSION['url'])?$_SESSION['url']:"dashboard.php";
if(isset($_POST['change'])){
    $err=[];
    if(isset($_POST['old']) && !empty($_POST['old'])){
        $old =($_POST['old']);
    }
    else {
        $err['old'] =  "Enter old password";
    }
    $passwordpattern='/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{5,}$/';
   if(isset($_POST['new']) && !empty($_POST['new'])){
        $new = $_POST['new'];
        if($old===$new){
            $err['new']="New password must not be same as old";
        }
        if (!preg_match($passwordpattern, $new)) {
            $err['new'] = "Password is weak";
        }
    } else {
        $err['new'] =  "Enter new password";
    }
    if(isset($_POST['confirm']) && !empty($_POST['confirm'])){
        $confirm = $_POST['confirm'];
        if($new===$confirm){
            $pass=($_POST['new']);
        }
        else{
            $err['confirm']='Password not matched';
        }
   } else {
       $err['confirm'] =  "confirm new password";
   }
   if(count($err)==0){
        $check="SELECT password FROM admin_tb WHERE admin_id='$id'";
        $result = $connection->query($check);
        if($result->num_rows>0){
            $row = $result->fetch_assoc();
            $password=$row['password'];
            if($password===$old){
                $update_password= "UPDATE admin_tb SET password='$pass' WHERE admin_id=$id";
                $connection->query($update_password);
                if($connection->affected_rows ==1 ){
                    $_SESSION['success'] ="Password Update Success";
                }
                else{
                    $_SESSION['failure'] = "Password Update Failed";
                }
                header('location:'.$loc);
            }
            else{
                $err['old']="Old Password not matched";
            }
        }
    }
}
if(isset($_POST['cancel'])){
    header('location:'.$loc);
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
        include_once './menu.php';
    ?>
    <div class='container'>
        <div class='row-c1'>
            <div class="heading">
                <h1>My Account-Change password</h1>
                </div>
                <div class="change">
                    <div class="change-row">
                        <h3>Change Password</h3>
                        <form method="post" action=''>
                        <div style='color:blue'><?php echo isset($success)?$success:'';?></div>
                        <div >
                            <span class='error-msg'><?php echo (isset($err['msg']))?$err['msg']:''; ?></span>
                            <label for='old'>Old Password*</label>
                            <input type="password" name='old'/>
                            <span class='error-msg'><?php echo (isset($err['old']))?$err['old']:''; ?></span>
                        </div>
                        <div>
                            <label for='new'>New Password*</label>
                            <input type="Password" name='new'/>
                            <span class='error-msg'><?php echo (isset($err['new']))?$err['new']:''; ?></span>
                        </div>
                        <div>
                            <label for='confirm'>Confirm New Password*</label>
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