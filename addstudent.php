<?php
$name = $email = $batch =$roll= $phone=$gender=$address='';
require_once 'private/autoload.php';
 // Loading Composer's autoloader
 require __DIR__ . '/vendor/autoload.php';

 // Importing PHPMailer classes into the global namespace
 use PHPMailer\PHPMailer\PHPMailer;
 use PHPMailer\PHPMailer\Exception;
$loc=isset($_SESSION['url'])?$_SESSION['url']:"student.php";
if (isset($_POST['btnstudent'])) {
    $err = [];
    if (isset($_POST['name']) && !empty($_POST['name']) && trim($_POST['name'])) {
        $name = $_POST['name'];
    } else {
        $err['name'] = "Enter name";
    }
    if (isset($_POST['email']) && !empty($_POST['email']) && trim($_POST['email'])) {
        $email = $_POST['email'];
        if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
            $err['email'] = "Enter valid email";
        }
        $sql = "SELECT COUNT(*) AS count FROM student_tb WHERE email = '$email'";
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
    $phonepattern = "/^(97|98)\d{8}$/";
    if (isset($_POST['phone']) && !empty($_POST['phone']) && trim($_POST['phone'])) {
        $phone = $_POST['phone'];
        if (!preg_match($phonepattern, $phone)) {
            $err['phone'] = "Enter a valid phone number of 10 digits";
        }
    } else {
        $err['phone'] =  "Enter a phone number";
    }
    if(isset($_POST['batch']) && !empty($_POST['batch']) && trim($_POST['batch']))
    {
        $batch=$_POST['batch'];
    }
    else{
        $err['batch']="Select batch";
    }
    if(isset($_POST['gender']) && !empty($_POST['gender']) && trim($_POST['gender']))
    {
        $gender=$_POST['gender'];
    }
    else{
        $err['gender']="Select gender";
    }
    if(isset($_POST['roll']) && !empty($_POST['roll']) && trim($_POST['roll']))
    {
        $roll=$_POST['roll'];
    }
    else{
        $err['roll']="E";
    }
    if(isset($_POST['address']) && !empty($_POST['address']) && trim($_POST['address']))
    {
        $address=$_POST['address'];
    }
    if(isset($_POST['password'])&& !empty($_POST['password']) && trim($_POST['password']))
    {
        $passed=$_POST['password'];
    }else{
        $err['password']="Generate";
    }
    if (count($err) == 0) {
        $year=date('Y');
        $pass=md5($passed);
        $insert = "INSERT INTO student_tb(name, gender, roll_no, phone_number, email,address, password,batch_id,enrollment_date,status) 
        VALUES ('$name', '$gender','$roll', '$phone', '$email','$address','$pass','$batch',NOW(),1)";
        if ($connection->query($insert)) {
                $phpmailer = new PHPMailer(true);
                try {
                    //Server settings
                    $phpmailer->isSMTP();
                    $phpmailer->Host = 'sandbox.smtp.mailtrap.io';
                    $phpmailer->SMTPAuth = true;
                    $phpmailer->Port = 2525;
                    $phpmailer->Username = '46880d6ac6d7cb';
                    $phpmailer->Password = 'ec5e894030a269';
                    $phpmailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    //debuging
                    $phpmailer->SMTPDebug = 0;
                    // Email settings
                    $phpmailer->setFrom('noreply@hotm.com', 'Mailer');
                    $phpmailer->isHTML(true);
                    $phpmailer->Subject = 'Enrollment';
                    $phpmailer->Body    = 'Dear '.$name.' , <br>You have been successfully enrolled.<br>
                    Please use this '.$passed.' to pay fee further and check dues.<br>Thank you.';
                    $phpmailer->addAddress($email);
                    // Send email
                    $phpmailer->send();
                    // Clear recipients 
                    $phpmailer->clearAddresses();            
                    $_SESSION['success']="Mail sent";
                } catch (Exception $e) {
                    $_SESSION['success']= "Message could not be sent.";
                } 
                $_SESSION['success'] .= ' Student Added Successfully';
                header('location:addstudent.php');
        } else {
            $_SESSION['failure']= 'Student add failure';
            header('location:addstudent.php');
        }
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
    <title>Add Student</title>
    <link rel="stylesheet" href="css/add.css">

</head>
<body>
    <?php include_once 'menu.php';?>
    <div class='container'>
        <div class='header'>
            <div class='header-left'>
                <div class='detail'>Add Student</div>
            </div>
        </div>
        <div class="content">
            <div class='form-body-1'>
                <div class='form-body-r'>
                    <?php getmessage();?>
                    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method='post'>
                        <div class='form-row'>
                            <label for="batch">Batch</label>
                            <select name="batch" id="batch">
                                <option value=" ">Select batch</option>
                                <?php
                                $select="SELECT * FROM batch_tb b WHERE b.status='1';";
                                $result = $connection->query($select);
                                if($result->num_rows>0){
                                    while($row = $result->fetch_assoc()){
                                ?>
                                <option value="<?php echo $row['batch_id'];?>" <?php echo ($batch == $row['batch_name']) ? 'selected' : ''; ?> ><?php echo $row['batch_name'];?></option>

                                <?php }} ?>
                            </select>
                            <span class='err-msg'><?php echo (isset($err['batch']))?$err['batch']:''; ?></span>
                        </div>
                        <div class='form-row'>
                            <label for="roll">Roll</label>
                            <input type="text" name='roll' value="<?php echo $roll;?>" id="rollNumberInput" readonly>
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
                            <input type="number" name='phone' value="<?php echo $phone;?>">
                            <span class='err-msg'><?php echo (isset($err['phone']))?$err['phone']:''; ?></span>
                        </div>
                        <div class='form-row'>
                            <label for="gender">Gender</label>
                            <select name="gender" id="gender">
                                <option value="">Select Gender </option>
                                <option value="Male" <?php echo ($gender == "Male") ? 'selected' : ''; ?>>Male</option>
                                <option value="female" <?php echo ($gender == "female") ? 'selected' : ''; ?>>Female</option>
                            </select>
                            <span class='err-msg'><?php echo (isset($err['gender']))?$err['gender']:''; ?></span>
                        </div>
                        <div class='form-row'>
                            <label for="address">Address</label>
                            <input type="text" name='address' value="<?php echo $address;?>">
                            <span class='err-msg'><?php echo (isset($err['address']))?$err['address']:''; ?></span>
                        </div>
                        <div class="form-row">
                            <label for="password">Password</label>
                            <input type="password" id="passwordField" name="password" placeholder="Your password">
                            <button type="button" onclick="generatePassword()">Generate Password</button>
                            <span class="err-msg"><?php echo (isset($err['password'])) ? $err['password'] : ''; ?></span>
                        </div>
                        <div class='form-row'>
                            <input type="submit" name='btnstudent' value='Save'>
                            <input type="submit" name='cancel' value='Cancel'>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        function generatePassword() {
            const length = 8;
            const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+~`|}{[]:;?><,./-=";
            let password = "";

            for (let i = 0; i < length; i++) {
                const randomIndex = Math.floor(Math.random() * charset.length);
                password += charset.charAt(randomIndex);
            }
            document.getElementById("passwordField").value = password;
        }
    </script>
    <script src="jquery-3.7.1.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#batch').change(function() {
            var selectedBatch = $(this).val();
            $.ajax({
                url: 'load_roll.php',
                type: 'POST',
                data: { batch: selectedBatch },
                success: function(response) {
                    $('#rollNumberInput').val(response); 
                },
                error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                }
            });
        });
    });
    </script>
</body>
</html>