<?php
// Load Composer's autoloader
require __DIR__ . '/vendor/autoload.php';

// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
@session_start();
$loc=isset($_SESSION['url'])?$_SESSION['url']:"fee.php";
$name = '';
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    require_once 'private/autoload.php';
    @session_start();
    $sql = "SELECT f.*,s.semester_name,b.batch_name FROM feestructure AS f
    INNER JOIN semester_tb AS s ON s.semester_id=f.semester_id
    INNER JOIN batch_tb AS b ON f.batch_id = b.batch_id
    WHERE f.fee_id = '$id'";
    $result = mysqli_query($connection, $sql);
    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        extract($data);
    } else {
        header('Location:'.$loc);
        exit();
    }
} else {
    header('Location:'.$loc);
    exit();
}

if (isset($_POST['btnUpdate'])) {
    $err = [];
    if (isset($_POST['amount']) && !empty($_POST['amount']) && trim($_POST['amount'])) {
        $amount = $_POST['amount'];   
    }

    if (isset($_POST['date']) && !empty($_POST['date'])) {
        $selectedDate = $_POST['date'];
        $today = date('Y-m-d');
        if ($selectedDate < $today) {
            $err['date'] = "Please select a future date.";
        }
    } else {
        $err['date'] = "Please select a date.";
    }
    if(isset($_POST['msg'])){
        $msg=$_POST['msg'];
    }else{
        $err['msg']='Choose';
    }
    if (count($err) == 0) {
        $update = "UPDATE feestructure SET amount = '$amount', due_date = '$selectedDate' WHERE fee_id = $id";
        if ($connection->query($update)) {
            if($msg==0){
                $selectEmails = "SELECT DISTINCT(s.email) FROM student_tb s
                LEFT JOIN pays ON s.student_id = pays.student_id AND pays.fee_id = '$id'
                INNER JOIN batch_tb b ON b.batch_id=s.batch_id
                INNER JOIN feestructure AS f ON f.batch_id=b.batch_id AND f.fee_id='$id'
                WHERE pays.payment_mode IS NULL";
                $resultEmails = $connection->query($selectEmails);
                if ($resultEmails->num_rows > 0) {
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
                        $phpmailer->setFrom('noreply@hot.com', 'Mailer');
                        $phpmailer->isHTML(true);
                        $phpmailer->Subject = 'Please pay your fee';
                        $phpmailer->Body = 'Dear Student<br />
                        The fee amount is ' . $amount . ' and the due date is ' . $selectedDate . '.<br />
                        Please pay your fee as soon as possible.<br />Thank you.<br />College Department';
                    
                        // Add recipients
                        while ($row = $resultEmails->fetch_assoc()) {
                            $phpmailer->addAddress($row["email"]);
                        }
                        // Send the email
                        $phpmailer->send();
                        // Clear all recipients after sending
                        $phpmailer->clearAddresses();
                        $_SESSION['success'] = 'Mail sent';
                        header('location:'.$loc);
                    } catch (Exception $e) {
                        $_SESSION['failure'] = "Message could not be sent.";
                    }
                }else{
                    header('location:'.$loc);
                }
            }else{
                header('location:'.$loc);
            }
        }
    }
}

if (isset($_POST['cancel'])) {
    header('Location:'.$loc);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Semester</title>
    <link rel="stylesheet" href="css/add.css">
</head>
<body>
    <?php include_once 'menu.php'?>
    <div class='container'>
        <div class='form-body-1'>
            <div class='form-body-r'>
                <form action="<?php echo $_SERVER['PHP_SELF'] ?>?id=<?php echo $id ?>" method="post">
                    <div class='form-row'>
                        <label for="batch">Batch</label>
                        <input type="text"  name='batch'value='<?php echo $batch_name ?>' readonly>
                    </div>
                    <div class='form-row'>
                        <label for="semester">Semester</label> 
                        <input type="text " name="semester" value="<?php echo $semester_name ?>" readonly>
                    </div>
                    <div class='form-row'>
                        <label for="date">Due Date</label>
                        <input type="date" name='date' value="<?php echo $due_date; ?>">
                        <span class='err-msg'><?php echo (isset($err['date']))?$err['date']:''; ?></span>
                    </div>
                    <div class='form-row'>
                        <label for="amount">Amount</label> 
                        <input type="number" name="amount"  value="<?php echo $amount ?>" readonly/>
                    </div>
                    <div class='form-row'>
                        <label for="msg">Mail</label>
                        <input type="radio" value=0 name='msg'>Sent
                        <input type="radio" value=1 name='msg'>Don't sent
                        <span class='err-msg'><?php echo isset($err['msg'])?$err['msg']:''; ?></span>
                    </div>
                    <div class='form-row'>
                        <input type="submit" name='btnUpdate' value='Update'>
                        <input type="submit" name='cancel' value='Cancel'>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
