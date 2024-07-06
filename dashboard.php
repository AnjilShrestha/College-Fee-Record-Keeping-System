<?php
require_once 'private/autoload.php';
if (isset($_COOKIE['name'])) {
    $cookie_value = $_COOKIE['name'];
    if ($cookie_value) {
        // Cookie is valid, you can proceed with the request
        echo "Cookie is valid.";
    } else {
        // Invalid cookie value, redirect to login
        header("Location: logout.php");
        exit();
    }
} else {
    // Cookie is not set, redirect to login
    echo "Cookie is not valid.";
    header("Location: logout.php");
    exit();
}
if(!isset($_SESSION['user_type']) || $_SESSION['user_type']!=='admin' )
{
  header('location:login.php');
  exit;
}else{
    $name=$_SESSION['name'];
}
$_SESSION['url']=$_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/details.css" />
    <link rel="stylesheet" href="css/table.css" />
</head>
<body>
    <?php
    include_once 'menu.php';
    ?>
    <div class='container'>
        <div class='header'>
            <div class='header-left'>
                <div class='detail' style="height:25px;">Welcome <?php echo $name;?></div>
            </div>
        </div>
        <?php getmessage();?>
        <a href="admin.php" style='color:black;'>
            <div class='column'>
                <div class='card-content'>
                    <div class='content-data'>
                        <?php
                        $sql="SELECT COUNT(admin_id) AS number  FROM admin_tb";
                        $result = $connection->query($sql);
                        if($result->num_rows>0){
                            $row = $result->fetch_assoc();
                            echo $row['number'];
                        }
                        else{
                            echo '0';
                        }
                        ?>
                    </div>
                    <div class='content-heading'>Admin</div>
                </div>
            </div>
        </a>
        <a href="student.php" style='color:black;'>
            <div class='column'>
                <div class='card-content'>
                    <div class='content-data'>
                        <?php
                        $sql="SELECT COUNT(student_id) AS number  FROM student_tb";
                        $result = $connection->query($sql);
                        if($result->num_rows>0){
                            $row = $result->fetch_assoc();
                            echo $row['number'];
                        }
                        else{
                            echo '0';
                        }
                        ?>
                    </div>
                    <div class='content-heading'>Total Students</div>
                </div>
            </div>
        </a>
        <a href="student.php" style='color:black;'>
            <div class='column'>
                <div class='card-content'>
                    <div class='content-data'>
                        <?php
                        $sql="SELECT COUNT(student_id) AS number  FROM student_tb WHERE status='1'";
                        $result = $connection->query($sql);
                        if($result->num_rows>0){
                            $row = $result->fetch_assoc();
                            echo $row['number'];
                        }
                        else{
                            echo '0';
                        }
                        ?>
                    </div>
                    <div class='content-heading'>Active student</div>
                </div>
            </div>
        </a>
        <a href="student.php" style='color:black;'>
            <div class='column'>
                <div class='card-content'>
                    <div class='content-data'>
                        <?php
                        $sql="SELECT COUNT(student_id) AS number  FROM student_tb WHERE status='0'";
                        $result = $connection->query($sql);
                        if($result->num_rows>0){
                            $row = $result->fetch_assoc();
                            echo $row['number'];
                        }
                        else{
                            echo '0';
                        }
                        ?>
                    </div>
                    <div class='content-heading'>dropout student</div>
                </div>
            </div>
        </a>
        <a href="income.php">
            <div class='column'>
                <div class='card-content'>
                    <div class='content-data'>
                        <?php
                        $first_day_of_month = date('Y-m-01');
                        $last_day_of_month = date('Y-m-t');
                        $income = "SELECT SUM(amount) as total_income FROM pays 
                        WHERE payment_date >= '$first_day_of_month' AND payment_date <= '$last_day_of_month' AND status='paid'";
                        $result_income = $connection->query($income);
                        if($result_income->num_rows>0){
                            $row_income = $result_income->fetch_assoc();
                            echo $row_income['total_income'];
                        }
                        else{
                            echo '0';
                        }
                        ?>
                    </div>
                    <div class='content-heading'><?php echo date('M') .' income';?></div>
                </div>
            </div>
        </a>
    </div>
    <div class='container'>
        <div class='content'>
            <span>Payment</span>
            <div class='content-center'>
                <div class='table-container'>
                    <table style='float:none;'>
                        <tr>
                            <th>SN</th>
                            <th>Invoice no</th>
                            <th>Student Name</th>
                            <th>Roll no</th>
                            <th>Semester name</th>
                            <th>Payment Date</th>
                            <th>Payment Mode</th>
                            <th>Amount</th>
                            <th>Payment Status</th>
                            <th>Action</th>
                        </tr>
                        <?php
                        $sql = "SELECT pays.*, s.*,st.semester_name,fs.*,pays.status AS stat FROM pays
                        INNER JOIN feestructure fs ON fs.fee_id = pays.fee_id
                        INNER JOIN semester_tb st ON st.semester_id=fs.semester_id
                        INNER JOIN student_tb s ON s.student_id = pays.student_id
                        INNER JOIN batch_tb b ON s.batch_id = b.batch_id
                        INNER JOIN course_tb c ON c.course_id=b.course_id
                        WHERE pays.status = 'paid' ORDER BY pays.payment_date desc LIMIT 5";
                        $result = $connection->query($sql);
                        if($result->num_rows > 0){
                        $i=1;
                        while($row = $result->fetch_assoc()){
                            ?>
                            <tr>
                                <td><?php echo  $i++; ?></td>
                                <td><?php echo $row['invoice_no'];?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['roll_no']; ?></td>
                                <td><?php echo $row['semester_name']; ?></td>
                                <td><?php echo $row['payment_date']; ?></td>
                                <td><?php echo $row['payment_mode']; ?></td>
                                <td><?php echo $row['amount']; ?></td>
                                <td><?php echo $row['stat']; ?></td>
                                <td>
                                    <a href="viewpayment.php?id=<?php echo $row['payment_id'];?>" class='delete-a'><button id="delBtn" class='view-btn'>View</button></a>
                                </td>        
                            </tr>
                            <?php
                            }
                        }else{
                            ?>
                            <td colspan=10 style='text-align:center;'>No entries found</td>
                            <?php
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>