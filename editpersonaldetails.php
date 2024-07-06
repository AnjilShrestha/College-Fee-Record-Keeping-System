<?php
@session_start();
require_once 'private/autoload.php';
$student_id=$_SESSION['id'];
$details="SELECT * FROM student_tb s WHERE s.student_id=$student_id";
$retrieve_details = mysqli_query($connection, $details);
if(mysqli_num_rows($retrieve_details) > 0){
    $data = mysqli_fetch_assoc($retrieve_details);
    extract($data);
} else {
    header('location:personaldetails.php');
    exit();
}
if (isset($_POST['btnedit'])){
    $err = [];
    if(isset($_POST['name']) && !empty($_POST['name']) && trim($_POST['name'])){
        $name= $_POST['name'];
    }else{
        $err['name']="Enter name";
    }
    if (isset($_POST['email']) && !empty($_POST['email']) && trim($_POST['email'])) {
        $email = $_POST['email'];
        if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
            $err['email'] = "Enter valid email";
        }else{
            $sql = "SELECT COUNT(*) AS count FROM student_tb WHERE email = '$email' AND student_id!='$student_id'";
            $result = mysqli_query($connection, $sql);
            if ($result) {
                $row = mysqli_fetch_assoc($result);
                $count = $row['count'];
                if ($count > 0) {
                    $err['email']="Email is already taken";
                }
            }
        }
    } else {
        $err['email'] =  "Enter email";
    }
    if(isset($_POST['phone']) && !empty($_POST['phone']) && trim($_POST['phone'])){
        $phone= $_POST['phone'];
    }else {
        $err['phone']="Enter phone number";
    }
    if(isset($_POST['address']) && !empty($_POST['address']) && trim($_POST['address']))
    {
        $address=$_POST['address'];
    }
    else{
        $err['address']="Enter address";
    }
    if(isset($_POST['gender']) && !empty($_POST['gender']) && trim($_POST['gender']))
    {
        $gender=$_POST['gender'];
    }
    else{
        $err['gender']="Choose gender";
    }
    $new_img_name=$data['image'];
    if (isset($_FILES['pp']['name']) && !empty($_FILES['pp']['name'])) {
        $img_name = $_FILES['pp']['name'];
        $tmp_name = $_FILES['pp']['tmp_name'];
        $img_size = $_FILES['pp']['size'];
        $error = $_FILES['pp']['error'];

        if ($error === 0) {
            if ($img_size < 500000) {
                $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
                $img_ex_lc = strtolower($img_ex);
                $allowed_types = ['jpg', 'jpeg', 'png'];
                if (in_array($img_ex_lc, $allowed_types)) {
                    $new_img_name =base64_encode(uniqid() . '_' . $_FILES['pp']['name']);
                    $img_upload_path = './upload/' . $new_img_name;
                    if(!empty($_FILES['pp']['name'])){
                        if (!empty($data['image']) && file_exists("./upload/" . $data['image'])) {
                            unlink("./upload/" . $data['image']);
                        }
                        move_uploaded_file($tmp_name, $img_upload_path);
                    }
                } else {
                    $err['image'] = "File type must be jpg, jpeg, png";
                }
            } else {
                $err['image'] = "File size must be below 500KB";
            }
        } else {
            $err['image'] = "File upload error";
        }
    }
    if(count($err)==0){
        $update = "UPDATE student_tb SET name='$name', email='$email', phone_number='$phone', image='$new_img_name',gender='$gender',
        address='$address' WHERE student_id='$student_id'";
        $update_result = mysqli_query($connection, $update);
        if (mysqli_affected_rows($connection) == 1) {
            $_SESSION['success'] = 'Personal details updated';
        } else {
            $_SESSION['failure'] = 'Personal details not updated';
        }
        header('location:personaldetails.php');
        exit();
    }
}
if(isset($_POST['cancel'])){
    header('location:personaldetails.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Personal Details</title>
    <link rel="stylesheet" href="css/add.css">
    <style>
         <style>
        label[for='pp'] {
            font-weight: bold;
            color: #333;
            font-size: 1.2em;
            margin-bottom: 10px;
            display: block;
        }

        input[type="file"] {
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 5px;
            display: block;
            margin-bottom: 15px;
            width: 100%;
            max-width: 300px;
        }

        img.rounded-circle {
            display: block;
            margin: 10px auto;
            height: 70px;
            border-radius: 50%;
            border: 2px solid #007bff;
            object-fit: cover;
        }

        input[type="text"][hidden] {
            display: none;
        }
    </style>
    </style>
</head>
<body>
<?php include_once 'studentmenu.php';?>
<div class='container'>
        <div class='header'>
            <div class='header-left'>
                <div class='detail'>Personal Details Update of <?php echo $name;?></div>
            </div>
        </div>
        <div class='form-body-1'>
            <div class='form-body-r'>
                <form action="<?php echo $_SERVER['PHP_SELF'];?>" method='post' enctype='multipart/form-data'>
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
                    <div class="form-row">
                        <img src="./upload/<?php echo ($data['image']); ?>" class="rounded-circle" style="width: 70px">
                        <label for='pp'>Profile Picture</label>
                        <input type="file" name="pp" placeholder='Choose profile picture'>
                        <input type="text" hidden="hidden" name="old_pp" value="<?php echo ($data['image']); ?>">
                        <span class='err-msg'><?php echo isset($err['image']) ? $err['image'] : ''; ?></span>
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
                            <option value="female" <?php echo ($gender == "female") ? 'selected' : ''; ?>>Female</option>
                        </select>
                        <span class='err-msg'><?php echo (isset($err['gender']))?$err['gender']:''; ?></span>
                    </div>
                    <div class='form-row'>
                        <label for="address">Address</label>
                        <input type="text" name='address' value="<?php echo $address;?>">
                        <span class='err-msg'><?php echo (isset($err['address']))?$err['address']:''; ?></span>
                    </div>
                    <div class='form-row'>
                        <input type="submit" name='btnedit' value='Update'>
                        <input type="submit" name='cancel' value='Cancel'>
                    </div>
                </form>
            </div>
        </div>
</body>
</html>