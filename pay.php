<?php
require_once 'private/autoload.php';
@session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student = $_SESSION['id'];
    $fee_id = $_POST['fee_id'];
    $student = mysqli_real_escape_string($connection, $student);
    $fee_id = mysqli_real_escape_string($connection, $fee_id);
    $sql = "SELECT * FROM feestructure AS f
    INNER JOIN student_tb s ON s.batch_id = f.batch_id
    INNER JOIN semester_tb st ON st.semester_id = f.semester_id
    WHERE f.fee_id = '$fee_id' AND s.student_id = '$student'";

    $result = mysqli_query($connection, $sql);
    if ($result && mysqli_num_rows($result) == 1) {
        $fee = mysqli_fetch_assoc($result);
        $invoice_no = $fee['fee_id'] . $student . time();
        $total = $fee['amount'];

        $select = "SELECT payment_id FROM pays WHERE student_id = '$student' 
        AND fee_id = '$fee_id' AND status = 'pending' ";
        $paymentrecord = mysqli_query($connection, $select);
        if ($paymentrecord && mysqli_num_rows($paymentrecord) > 0) {
            $row = mysqli_fetch_assoc($paymentrecord);
            $payid = $row['payment_id'];

            $update = "UPDATE pays SET invoice_no = '$invoice_no' WHERE payment_id = '$payid'";

            if (mysqli_query($connection, $update)) {
                $_SESSION['esewat'] = $payid;
            } else {
				$_SESSION['failure']='We are sorry';
				header('Location: ../studentfee.php');
				exit();
            }
        } else {
            $query = "INSERT INTO pays (fee_id, student_id, invoice_no, amount, payment_mode, status) 
            VALUES ('$fee_id', '$student', '$invoice_no', '$total', '', 'pending')";

            if (mysqli_query($connection, $query)) {
                $lastid = mysqli_insert_id($connection);
                $_SESSION['esewat'] = $lastid;
            } else {
				$_SESSION['failure']='Please try again later';
				header('Location: ../studentfee.php');
				exit();
            }
        }
    } else {
		$_SESSION['failure']='Please try again later';
        header('Location: ../studentfee.php');
    	exit();
    }
} else {
    header('Location: ../studentfee.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Procced Payment</title>
		<style>
			table {
				margin: 20px;
				width: 50%; 
				border-collapse: collapse; 
			}
			th, td {
				padding: 10px; 
				border: 1px solid #ddd; 
			}
			th {
				text-align: left; 
			}
			input[type="submit"] {
				background-color: #007bff; 
				color: fff; 
				padding: 10px 20px;
				border: none; 
				cursor: pointer; 
			}
			input[type="submit"]:hover {
				background-color: grey; 
			}
		</style>
	</head>
	<body>
		<?php include_once './studentmenu.php' ?>
		<div class='container'>
			<div class='header'>
				<div class='header-left'>
					<div class='detail'>Payment Details </div>
				</div>
			</div>
			<div class="card-center">
				<table>
					<tr><th colspan=2 style='background-color:grey;'></th></tr>
					<tr>
						<th>Semester:</th>
						<td><?php echo $fee['semester_name'];?></td>
					</tr>
					<tr>
						<th>Particulars</th>
						<th>Amount</th>
					</tr>
					<?php
					$data = json_decode($fee['description'], true);
            		$formattedData = ""; 
            		foreach ($data as $entry) {
						$particulars = $entry['particulars']; 
                		$amount = number_format($entry['amount'], 2); 
						?>
						<tr>
							<td><?php echo $particulars?></td>
							<td><?php echo $amount?></td>         
						</tr>
            		<?php }?> 
					<tr>
						<th>Total amount:</th>
						<td><?php echo $fee['amount'];?></td>
					</tr>
					<tr>
						<td colspan=2>Procced To Pay: Rs.<?php echo $fee['amount'];?></td>
					</tr>
					<tr>
						<td>
							<form action="khalti-payment/payment-request.php" method="POST">
                                <input type="hidden"  name="PurchasedOrderId" value='<?php echo $invoice_no;?>'>
								<input type="hidden" value='<?php echo $total;?>'  name="Amount">
								<input type="hidden" value='<?php echo $semester_name;?>' name="PurchasedOrderName">
								<input type="hidden" name="Name" value='<?php echo $name;?>'>
								<input type="hidden" name="Email" value='<?php echo $email;?>'>
								<input type="hidden" name="Phone" value='<?php echo $phone_number;?>'>
                                <input type="submit" name="submit" value='Pay with khalti'>
							</form>
						</td>
						<td>
							<form action="https://uat.esewa.com.np/epay/main" method="POST">
								<input value="<?php echo $total;?>" name="tAmt" type="hidden">
								<input value="<?php echo $total;?>" name="amt" type="hidden">
								<input value="0" name="txAmt" type="hidden">
								<input value="0" name="psc" type="hidden">
								<input value="0" name="pdc" type="hidden">
								<input value="epay_payment" name="scd" type="hidden">
								<input value="<?php echo $invoice_no;?>" name="pid" type="hidden">
								<input value="http://127.0.0.1/cfm/esewa/success.php?q=su" type="hidden" name="su">
								<input value="http://127.0.0.1/cfm/esewa/failure.php?q=fu" type="hidden" name="fu">
								<input type="submit" value="Pay with Esewa">
							</form>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</body>
</html>
