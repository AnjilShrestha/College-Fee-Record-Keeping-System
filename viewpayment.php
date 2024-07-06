<?php
require_once 'private/autoload.php';
$loc=isset($_SESSION['url'])?$_SESSION['url']:"payment.php";
if (!isset($_GET['id'])) {
    header('location:'.$loc);
} else {
    $id = $_GET['id'];
    require_once 'private/autoload.php';
    $sql = "SELECT *,p.status AS stat FROM pays AS p
    INNER JOIN feestructure f ON f.fee_id = p.fee_id
    INNER JOIN semester_tb sem ON sem.semester_id=f.semester_id
    INNER JOIN student_tb s ON s.student_id = p.student_id
    INNER JOIN batch_tb b ON b.batch_id=s.batch_id
    INNER JOIN course_tb c ON c.course_id=b.course_id
    WHERE p.payment_id=$id";
    $retrieve_fee = mysqli_query($connection, $sql);
    if (mysqli_num_rows($retrieve_fee) > 0) {
        $data = mysqli_fetch_assoc($retrieve_fee);
        extract($data);
    } else {
        header('location:'.$loc);
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment details</title>
    <link rel="stylesheet" href="css/add.css">
    <link rel="stylesheet" href="css/view.css">
</head>
<body>
    <?php include_once'menu.php'?>
    <div class='container'>
        <div class='header'>
            <div class='header-left'>
                <div class='detail'>View Payment</div>
            </div>
        </div>
        <div class='content'>
            <div class='content-center'>
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
                    <th>Student name:</th>
                    <td><?php echo $name?></td>
                </tr>
                <tr>
                    <th>Roll no:</th>
                    <td><?php echo $roll_no?></td>
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
                        <a href="printpay.php?id=<?php echo $payment_id?>"><button>Print</button></a>
                        <a href="<?php echo $loc;?>"><button>back</button></a>
                    </th>
                </tr>
               </table>
            </div>
        </div>
    </div>
</body>
</html>