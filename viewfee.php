<?php
if (!isset($_GET['id'])) {
   header('location:paidfee.php');
} else {
    $id = $_GET['id'];
    require_once 'private/autoload.php';
    $student_id=$_SESSION['id'];
    $sql = "SELECT *,p.status AS stat FROM pays AS p
    INNER JOIN feestructure f ON f.fee_id = p.fee_id
    INNER JOIN semester_tb sem ON sem.semester_id=f.semester_id
    INNER JOIN student_tb s ON s.student_id = p.student_id
    INNER JOIN batch_tb b ON b.batch_id=s.batch_id
    INNER JOIN course_tb c ON c.course_id=b.course_id
    WHERE invoice_no='$id' AND p.student_id='$student_id'";
    $retrieve_payment = mysqli_query($connection, $sql);
    if (mysqli_num_rows($retrieve_payment) > 0) {
        $payment = mysqli_fetch_assoc($retrieve_payment);
        extract($payment);
    } else {
        header('location:paidfee.php');
        exit;
    }
}
if(isset($_POST['back'])){
    header('location:paidfee.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paid fee </title>
    <link rel="stylesheet" href="css/add.css">
    <link rel="stylesheet" href="css/view.css">
</head>
<body>
    <?php include_once 'studentmenu.php';?>
    <div class='container'>
        <div class='header'>
            <div class='header-right'>
                <div class='detail'>Payment Details </div>
            </div>
        </div>
        <div class='content'>
            <div class='content-center' id='cnt'>
               <table>
                <tr>
                    <th colspan='2'></th>
                </tr>
                <tr>
                    <th>Course:</th>
                    <td><?php echo $course_name;?></td>
                </tr>
                <tr>
                    <th>Batch:</th>
                    <td><?php echo $batch_name;?></td>
                </tr>
                <tr>
                    <th>Semester:</th>
                    <td><?php echo $semester_name?></td>
                </tr>
                <tr>
                    <th>Payment Date:</th>
                    <td><?php echo $payment_date;?></td>
                </tr>
                <tr>
                    <th>Payment Amount:</th>
                    <td><?php echo $amount;?></td>
                </tr>
                <tr>
                    <th>Payment Mode:</th>
                    <td><?php echo $payment_mode;?></td>
                </tr>
                <tr>
                    <th>Payment Status:</th>
                    <td><?php echo $stat;?></td>
                </tr>
                <tr>
                    <th colspan="2">
                    <button class="print-button" onclick="printExternalPage(<?php echo $invoice_no;?>)">Print</button>
                        <a href="paidfee.php"><button>back</button></a>
                    </th>
                </tr>
               </table>
            </div>
        </div>
    </div> 

    <script>
        function printExternalPage(invoice) {
            // Open a new window and load the print page
            var printWindow = window.open('print.php?id='+ invoice, '_blank');

            // Add an event listener to trigger print dialog when the page is fully loaded
            printWindow.onload = function() {
                printWindow.print();
            };
        }
    </script>
</body>
</html>
