<?php
   $pa='';
   @session_start();
   $loc=isset($_SESSION['url'])?$_SESSION['url']:"receivefee.php";
   $invoice_no='';
if (!isset($_GET['id']) && !isset($_GET['student'])) {
    header('location:'.$loc);
    exit;
} else {
    $fee_id = $_GET['id'];
    $student_id=$_GET['student'];
    require_once 'private/autoload.php';
    $sql = "SELECT  f.*,s.*,b.batch_name,st.semester_name FROM feestructure AS f
    JOIN semester_tb st ON st.semester_id=f.semester_id
    LEFT JOIN student_tb s ON f.batch_id=s.batch_id 
    INNER JOIN batch_tb b ON b.batch_id=f.batch_id
    INNER JOIN course_tb c ON c.course_id=b.course_id
    WHERE f.fee_id='$fee_id' AND s.student_id='$student_id'";
	$result = mysqli_query($connection, $sql);
	if($result)
	{
		if ( mysqli_num_rows($result) == 1)
		{
			$fee = mysqli_fetch_assoc($result);
            $invoice_no= $fee['fee_id'].$fee['fee_id']. time().mt_rand(1000,9999);
            extract($fee);
		}
	}
}
if (isset($_POST['btnpay'])) {
    $err = [];
    if(isset($_POST['invoice'])){
        $invoice_no=$_POST['invoice'];
    }
    if (isset($_POST["pa"]) && is_numeric($_POST["pa"])) {
        $pa = $_POST["pa"];
        if ($pa!= $amount) {
            $err['pa'] = "Full payment only";
        }
    } else {
        $err['pa'] = "Paid amount is required";
    }

    if (count($err) == 0) {
        $query = "INSERT INTO pays(fee_id,student_id,invoice_no, amount, status, payment_date, payment_mode )
			VALUES( '$fee_id','$student_id','$invoice_no', '$pa', 'paid', NOW(),'cash')";
			if( !mysqli_query($connection, $query))
			{
				die('Error!');
			}
            else{
                $_SESSION['success']= 'Payment Successfull';
                header('Location:'.$loc);
                exit;
            }
    }
}

if (isset($_POST['cancel'])) {
    header('Location:'.$loc);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receive Payment</title>
    <link rel="stylesheet" href="css/add.css">
</head>
<body>
    <?php include_once 'menu.php';?>
    <div class='container'>
        <div class='header'>
            <div class='header-left'>
                <div class='detail'>Add Payment</div>
            </div>
        </div>
        <div class="content">
            <div class='form-body-1'>
                <div class='form-body-r'>
                <form action="<?php echo $_SERVER['PHP_SELF'];?>?id=<?php echo $fee_id ?>&student=<?php echo $student_id ?>" method='post'>
                        <div class='form-row'>
                            <label for="invoice">Invoice</label>
                            <input type="text" name="invoice" value="<?php echo $invoice_no;?>" readonly>
                        </div>
                        <div class='form-row'>
                            <label for="batch">Batch</label>
                            <input type="text" name="batch" value="<?php echo $batch_name;?>" readonly>
                        </div>
                        <div class='form-row'>
                            <label for="roll">Roll no</label>
                            <input type="text" name="roll" value="<?php echo $roll_no;?>" readonly>
                        </div>
                        <div class='form-row'>
                            <label for="student">Student</label>
                            <input type="text" name="student" value="<?php echo $name;?>" readonly>
                        </div>
                        <div class='form-row'>
                            <label for="semester">Semester Name</label>
                            <input type="text" name="semester" value="<?php echo $semester_name;?>" readonly>
                        </div>
                        <div class='form-row'>
                            <label for="amount">Fee Amount</label>
                            <input type="number" id='amount'name="amount" value="<?php echo $amount;?>" readonly>
                        </div>
                        <div class='form-row'>
                            <label for="pa">Paid Amount</label>
                            <input type="number" id='paid' name='pa' value="<?php echo $pa;?>">
                            <span class='err-msg'><?php echo (isset($err['pa'])) ? $err['pa'] : ''; ?></span>
                        </div>
                        <div class='form-row'>
                            <input type="submit" name='btnpay' value='Pay'>
                            <input type="submit" name='cancel' value='Cancel'>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
