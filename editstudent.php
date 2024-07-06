<?php
$pass=$loc='';
@session_start();
require_once 'private/autoload.php';
$loc=isset($_SESSION['url'])?$_SESSION['url']:"student.php";
if(!isset($_GET['id'])){
    header('location:' . $loc);
    exit();
} else {
    $id =$_GET['id'];
    $sql = "SELECT s.*,b.*,s.status AS stat FROM student_tb s
    INNER JOIN batch_tb b ON s.batch_id = b.batch_id
    INNER JOIN course_tb c ON c.course_id=b.course_id 
    WHERE s.student_id='$id'";
    $retrieve_student = mysqli_query($connection, $sql);
    if(mysqli_num_rows($retrieve_student) > 0){
        $data = mysqli_fetch_assoc($retrieve_student);
        extract($data);
        $batch = $data['batch_name'];
        $email=$data['email'];
    } else {
        header('location:'. $loc);
        exit();
    }
}
?>
<?php
if (isset($_POST['btnedit'])){
    $err = [];
    if(isset($_POST['name']) && !empty($_POST['name']) && trim($_POST['name'])){
        $name= $_POST['name'];
    }else{
        $err['name']="Enter name";
    }
    if(isset($_POST['gender']) && !empty($_POST['gender']) && trim($_POST['gender']))
    {
        $gender=$_POST['gender'];
    }
    else{
        $err['gender']="Select gender";
    }
    $phonepattern = "/^[97-98]+[0-9]{8}+$/";
    if (isset($_POST['phone']) && !empty($_POST['phone']) && trim($_POST['phone'])) {
        $phone = $_POST['phone'];
        if (!preg_match($phonepattern, $phone)) {
            $err['phone'] = "Enter valid phone number";
        }
    } else {
        $err['phone'] =  "Enter phone number";
    }
    if (isset($_POST['email']) && !empty($_POST['email']) && trim($_POST['email'])) {
        $email = $_POST['email'];
        if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
            $err['email'] = "Enter valid email";
        }
        $sql = "SELECT COUNT(email) AS count FROM student_tb WHERE email = '$email' AND student_id!=$id";
        $result = mysqli_query($connection, $sql);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $count = $row['count'];
            if ($count > 0) {
                $err['email']="Email is already taken";
            }
        }
    } else {
        $err['email'] =  "Enter email";
    }
    if(isset($_POST['pass']) && !empty($_POST['pass']) && trim($_POST['pass'])){
        $pass= $_POST['pass'];
    }
    if(isset($_POST['address']) && !empty($_POST['address']) && trim($_POST['address']))
    {
        $address=$_POST['address'];
    }
    else{
        $err['address']="Enter address";
    }
    $status=$_POST['status'];
    if(count($err)==0){
        if(!empty($pass)) {
            $pass1 = md5($pass);
            $update = "UPDATE student_tb SET name='$name', email='$email', address='$address',
            gender='$gender', phone_number=$phone, status='$status', password='$pass1' WHERE student_id=$id";
        } else {
            $update = "UPDATE student_tb SET name='$name', email='$email', address='$address',
            gender='$gender', phone_number=$phone, status='$status' WHERE student_id=$id";
        }
        $connection->query($update);
        if ($connection->affected_rows==1) {
           $_SEEION['success']= 'Update Success';
        } else {
            $_SESSION['failure']='Failed to Update try again later';
        }
        header('location:'.$loc);
        exit();
    }
}
if(isset($_POST['cancel'])){
    header('location:'. $loc);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Update Student</title>
<link rel="stylesheet" href="css/add.css">
</head>
<body>
<?php include_once 'menu.php';?>
    <div class='container'>
        <div class='header'>
            <div class='header-left'>
                <div class='detail'>Edit Student</div>
            </div>
        </div>
        <div class="content">
            <div class='form-body-1'>
                <div class='form-body-r'>
                    <form action="<?php echo $_SERVER['PHP_SELF'];?>?id=<?php echo $id ?>" method='post'>
                        <div class='form-row'>
                            <label for="batch">Batch</label>
                            <input type="text" name='batch' value="<?php echo $batch_name;?>" readonly>
                        </div>
                        <div class='form-row'>
                            <label for="roll">Roll</label>
                            <input type="text" name='roll' value="<?php echo $roll_no;?>" id="rollNumberInput" readonly>
                        </div>
                        <div class='form-row'>
                            <label for="name">Name</label>
                            <input type="text" name='name' value="<?php echo $name;?>">
                            <span class='err-msg'><?php echo (isset($err['name']))?$err['name']:''; ?></span>
                        </div>
                        <div class='form-row'>
                            <label for="email">Email</label>
                            <input type="text" name='email' value="<?php echo $email;?>">
                            <span class='err-msg'><?php echo (isset($err['email']))?$err['email']:''; ?></span>
                        </div>
                        <div class='form-row'>
                            <label for="phone">Phone</label>
                            <input type="number" name='phone' value="<?php echo $phone_number;?>">
                            <span class='err-msg'><?php echo (isset($err['phone']))?$err['phone']:''; ?></span>
                        </div>
                        <div class='form-row'>
                            <label for="gender">Gender</label>
                            <select name="gender" id="gender">
                                <option value="">Select Gender </option>
                                <option value="Male" <?php echo ($gender == "male") ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo ($gender == "female") ? 'selected' : ''; ?>>Female</option>
                            </select>
                            <span class='err-msg'><?php echo (isset($err['gender']))?$err['gender']:''; ?></span>
                        </div>
                        <div class='form-row'>
                            <label for="address">Address</label>
                            <input type="text" name='address' value="<?php echo $address;?>">
                            <span class='err-msg'><?php echo (isset($err['address']))?$err['address']:''; ?></span>
                        </div>
                        <div class='form-row'>
                        <label for="status">Status:</label>
                        <?php if($data['stat']==1){?>
                            <input type="radio" name="status" value='1' checked="">Active
                            <input type="radio" name="status" value='0'>Dropout
                        <?php }else{ ?>
                            <input type="radio" name="status" value='1'>Active
                            <input type="radio" name="status" value='0' checked="">Dropout
                            <?php } ?>
                    </div>
                        <div class='form-row'>
                            <label for="pass">Password</label>
                            <input type="password" name='pass' value="<?php echo $pass;?>">
                            Leave this empty if u dont want to change password
                        </div>
                        <div class='form-row'>
                            <input type="submit" name='btnedit' value='Update'>
                            <input type="submit" name='cancel' value='Cancel'>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>