<?php
require_once 'private/autoload.php';
$loc=isset($_SESSION['url'])?$_SESSION['url']:"semester.php";
if (!isset($_GET['id']) && !isset($_GET['status'])) {
    header('location:'.$loc);
} else {
    $id = $_GET['id'];
    $id=mysqli_real_escape_string($connection,$id);
    $status=$_GET['status'];
    $stat=mysqli_real_escape_string($connection,$status);
    if($stat=='paid'){
        $sql = "SELECT DISTINCT s.* , pays.* FROM pays 
        INNER JOIN student_tb s ON s.student_id=pays.student_id 
        WHERE fee_id='$id'AND pays.status='paid' ";
    }else if($stat=='unpaid'){
        $sql = "SELECT s.*,fs.* FROM student_tb s
        LEFT JOIN pays ON s.student_id = pays.student_id AND pays.fee_id = '$id'
        INNER JOIN batch_tb b ON b.batch_id=s.batch_id
        INNER JOIN feestructure AS fs ON fs.batch_id=b.batch_id AND fs.fee_id='$id'
        WHERE pays.fee_id IS NULL;";
    }
    $result = mysqli_query($connection, $sql);
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paid List </title>
    <link rel="stylesheet" href="css/add.css">
    <link rel="stylesheet" href="css/table.css">
</head>
<body>
    <?php include_once 'menu.php'; ?>
    <div class='container'>
        <div class='header'>
            <div class='header-left'>
                <div class='detail'>View <?php echo $status;?> Student</div>
            </div>
        </div>
        <div class='content'>
            <div class='content-center'>
                <table style='width:50%;'>
                    <tr>
                        <th>S.No.</th>
                        <th>Roll no</th>
                        <th>Name</th>
                        <th>Amount</th>
                    </tr>
                        <?php
                        $amt=0;
                        if (mysqli_num_rows($result) > 0) {
                            $i=1;
                            while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo $row['roll_no']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['amount']; $amt+=$row['amount'];?></td>
                        </tr>
                        <?php
                            }
                            ?>
                            <tr>
                                <td colspan=3>Total</td>
                                <td><?php echo $amt;?></td>
                            </tr>
                            <?php
                            } else {
                                ?>
                                <td colspan='4'>No student paid fee</td>
                                <?php
                                }
                            }
                            ?>
                            <tr>
                                <th colspan=4>
                                    <a href="<?php echo $loc ?>">
                                        <button>back</button>
                                    </a>
                                </th>
                            </tr>
                        </table>
                    </div> 
                </div>
            </div>
        </div>
    </body>
</html>
