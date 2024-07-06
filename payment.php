<?php
require_once 'private/autoload.php';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$course = isset($_GET['course']) ? $_GET['course'] : '';
$batch = isset($_GET['batch']) ? $_GET['batch'] : '';
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$student=isset($_GET['student']) ? $_GET['student'] : '';
$num_per_page = 4;
$start_from = ($page - 1) * $num_per_page;
$err = [];
if (isset($_GET['btnstudent'])) {
    if (isset($_GET['course']) && !empty($_GET['course'])) {
        $course = $_GET['course'];
    } else {
        $err['course'] = 'Select course';
    }
    if (isset($_GET['batch']) && !empty($_GET['batch'])) {
        $batch = $_GET['batch'];
    } else {
        $err['batch'] = 'Select batch';
    }
    if (isset($_GET['student']) && !empty($_GET['student'])) {
        $student = $_GET['student'];
    }
}
if(isset($student)){
    $searchCondition = "AND (s.name LIKE '%$student%' OR s.roll_no LIKE '%$student%' OR pays.invoice_no LIKE '%$student%'
    OR pays.payment_mode LIKE '%$student%' OR pays.status LIKE '%$student%') ";
}else{
    $searchCondition='';
}
$_SESSION['url']=$_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="css/table.css">
    <link rel="stylesheet" href="css/getstudent.css">
</head>
<body>
    <?php include_once 'menu.php';
    ?>
    <div class='container'>
        <div class='header'>
            <div class='header-left'>
                <div class='detail'>List of Payment </div>
            </div>
        </div>
        <div class='content'>
            <div class='Form'>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
                    <?php include_once 'search.php';?>
                    <span class='search-row'>
                        <label for="student">Search</label>
                        <input type="text" value="<?php echo ($student); ?>" placeholder='name/rollno/payment mode' name="student" class='search-bar'>
                    </span>
                    <span class='search-row'>
                        <input type="submit" name='btnstudent' value='Search'>
                    </span>
                </form>
            </div>
            <?php
            getmessage();
            if (count($err) == 0 && !empty($course) && !empty($batch)) {
            ?>
            <div class='content'>
            <div class='content-center'>
                <div class='table-container'>
                    <table>
                        <tr>
                            <th>SN</th>
                            <th>Invoice no</th>
                            <th>Student Name</th>
                            <th>Roll no</th>
                            <th>Semester</th>
                            <th>Payment Date</th>
                            <th>Payment Mode</th>
                            <th>Amount</th>
                            <th>Payment Status</th>
                            <th>Action</th>
                        </tr>
                            <?php
                            $sql = "SELECT pays.*, s.*, st.semester_name AS semester, 
                            pays.status AS stat FROM pays
                            INNER JOIN feestructure sf ON sf.fee_id = pays.fee_id
                            INNER JOIN semester_tb st ON st.semester_id=sf.semester_id
                            INNER JOIN student_tb s ON s.student_id = pays.student_id
                            INNER JOIN batch_tb  b ON s.batch_id=b.batch_id
                            INNER JOIN course_tb c  ON c.course_id=b.course_id
                            WHERE pays.status='paid'
                            AND b.batch_name='$batch' AND c.course_name='$course' 
                            $searchCondition 
                            ORDER BY payment_date
                            LIMIT $start_from, $num_per_page
                            ";
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
                                        <td><?php echo $row['semester']; ?></td>
                                        <td><?php echo $row['payment_date']; ?></td>
                                        <td><?php echo $row['payment_mode']; ?></td>
                                        <td><?php echo $row['amount']; ?></td>
                                        <td><?php echo $row['stat']; ?></td>
                                        <td>
                                            <form action="delete.php" method="post" onsubmit="return confirm('Are you sure to delete <?php echo $row['invoice_no'];?> transaction?')">
                                                <input type="hidden" value="<?php echo $row['payment_id'];?>" name='payment'>
                                                <input type="submit" value="Delete" name="Delete" id="delBtn" class='delete-btn'/>
                                            </form>
                                            <a href="viewpayment.php?id=<?php echo $row['payment_id'];?>" class='view-a'><button id="viewbtn" class='view-btn'>View</button></a>
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
                        <div class='pagination'>
                            <?php
                            $sql_count = "SELECT count(*) AS count FROM pays
                            INNER JOIN feestructure sf ON sf.fee_id = pays.fee_id
                            INNER JOIN semester_tb st ON st.semester_id=sf.semester_id
                            INNER JOIN student_tb s ON s.student_id = pays.student_id
                            INNER JOIN batch_tb  b ON s.batch_id=b.batch_id
                            INNER JOIN course_tb c  ON c.course_id=b.course_id
                            WHERE pays.status='paid'
                            AND b.batch_name='$batch' AND c.course_name='$course' 
                            $searchCondition";
                            $result_count = $connection->query($sql_count);
                            $row_count = $result_count->fetch_assoc();
                            $total_pages = ceil($row_count['count'] / $num_per_page);
                            $visible_pages = 3; 
                            $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
                            $start = max(1, $current_page - floor($visible_pages / 2));
                            $end = min($start + $visible_pages - 1, $total_pages);
                            if ($start > 1) {
                                echo '<button><a href="receivefee.php?page=1&course=' . $course . '&batch=' . $batch . '">1</a></button>';
                                if ($start > 2) {
                                    echo '<span>...</span>';
                                }
                            }
                            for ($btn = $start; $btn <= $end; $btn++) {
                                $url = "payment.php?page=$btn&course=$course&batch=$batch";
                                if (!empty($student)) {
                                    $url .= "&student=" . urlencode($student);
                                }
                                echo "<button><a href='$url'>$btn</a></button>";
                            }
                            if ($end < $total_pages) {
                                if ($end < $total_pages - 1) {
                                    echo '<span>...</span>';
                                }
                                echo '<button><a href="payment.php?page=' . $total_pages . '&course=' . $course . '&batch=' . $batch . '">' . $total_pages . '</a></button>';
                            }
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
