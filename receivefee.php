<?php
require_once 'private/autoload.php';
$_SESSION['url']=$_SERVER['REQUEST_URI'];
$num_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$course = isset($_GET['course']) ? $_GET['course'] : '';
$batch = isset($_GET['batch']) ? $_GET['batch'] : '';
$student=isset($_GET['student'])?$_GET['student']:'';
$start_from = ($page - 1) * $num_per_page;
$err = [];
if (isset($_GET['btnstudent'])) {
    if (isset($_GET['course']) && !empty($_GET['course']) && trim($_GET['course'])) {
        $course = $_GET['course'];
    } else {
        $err['course'] = 'Select course';
    }
    if (isset($_GET['batch']) && !empty($_GET['batch'])) {
        $batch = $_GET['batch'];
    } else {
        $err['batch'] = 'Select Batch';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Fee Receive</title>
    <link rel="stylesheet" href="css/table.css">
    <link rel="stylesheet" href="css/getstudent.css">
</head>
<body>
    <?php include_once 'menu.php'; ?>
    <div class='container'>
        <div class='header'>
            <div class='header-left'>
                <div class='detail'>Fee Receive</div>
            </div>
        </div>
        <div class='Form'>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
                <?php include_once 'search.php';?>
                <span class='search-row'>
                    <label for="student">Student</label>
                    <input type="text" value="<?php echo ($student); ?>" placeholder='name/rollno' name="student" class='search-bar'>
                </span>
                <span class='search-row'>
                    <input type="submit" name='btnstudent' value='Search'>
                </span>
            </form>
        </div>
        <?php 
        getmessage();
        if (count($err) == 0 && !empty($course) && !empty($batch)) { ?>
            <div class='content'>
                <div class='content-center'>
                    <div class='table-container'>
                        <table id="Searchreceive">
                            <tr>
                                <th>Roll No</th>
                                <th>Student Name</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                            <?php
                            $searchCondition = isset($student) ? "AND (s.name LIKE '%$student%' OR s.roll_no LIKE '%$student%')" : '';
                            $sql = "SELECT s.*,f.*,st.semester_name,pays.payment_id, pays.status AS stat FROM student_tb AS s
                            JOIN batch_tb b ON b.batch_id = s.batch_id
                            JOIN feestructure f ON f.batch_id = b.batch_id
                            JOIN semester_tb st ON st.semester_id=f.semester_id
                            JOIN course_tb c ON c.course_id=b.course_id
                            LEFT JOIN pays ON pays.student_id = s.student_id AND pays.fee_id = f.fee_id
                            WHERE b.batch_name='$batch' AND c.course_name='$course' 
                            $searchCondition ORDER BY f.fee_id,s.roll_no
                            LIMIT $start_from, $num_per_page";
                            $result = $connection->query($sql);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) { ?>
                                    <tr>

                                        <td><?php echo $row['roll_no']; ?></td>
                                        <td><?php echo $row['name']; ?></td>
                                        <td><?php echo $row['semester_name']; ?></td>
                                        <td><?php  $data = json_decode($row['description'], true);
                                        $formattedData = ""; 
                                        foreach ($data as $entry) {
                                            $formattedData .=  $entry['particulars'] . ": Rs" . number_format($entry['amount'], 2) . "<br />";
                                        } echo $formattedData;?></td>
                                        <td><?php echo $row['amount']; ?></td>
                                        <td>
                                            <?php if ($row['stat'] !== 'paid') { ?>
                                                <a href="paycash.php?id=<?php echo $row['fee_id']; ?>&student=<?php echo $row['student_id']; ?>">
                                                    <button class='edit-btn'>Pay</button>
                                                </a>
                                            <?php } else{ ?>
                                                <a href="viewpayment.php?id=<?php echo $row['payment_id'];?>" style='color:red'>Paid</a>
                                            <?php }?>
                                        </td>
                                    </tr>
                                <?php }
                            } else {
                                echo "<tr><td colspan='5' style='text-align:center;'>No entries found</td></tr>";
                            }
                            ?>
                        </table>
                        <div class='pagination'>
                            <?php
                            // Fetching total number of records to calculate total pages
                            $sql_count = "SELECT COUNT(DISTINCT s.student_id) AS count FROM student_tb s 
                            JOIN batch_tb b ON b.batch_id = s.batch_id
                            JOIN feestructure f ON f.batch_id = b.batch_id
                            JOIN semester_tb st ON st.semester_id=f.semester_id
                            JOIN course_tb c ON c.course_id=b.course_id
                            LEFT JOIN pays ON pays.student_id = s.student_id 
                            AND pays.fee_id = f.fee_id
                            WHERE b.batch_name='$batch' AND c.course_name='$course' 
                            $searchCondition";
                            $result_count = $connection->query($sql_count);
                            $row_count = $result_count->fetch_assoc();
                            $total_records = $row_count['count'];
                            $total_pages = ceil($total_records / $num_per_page);
                            $visible_pages = 5; // Number of visible pages around the current page
                            
                            // Ensure current page is within valid range
                            $current_page = max(1, min($total_pages, isset($_GET['page']) ? (int)$_GET['page'] : 1));

                            // Determining start and end of pagination links
                            $half_visible = floor($visible_pages / 2);
                            $start = max(1, $current_page - $half_visible);
                            $end = min($total_pages, $current_page + $half_visible);

                            if ($start > 1) {
                                echo '<button><a href="receivefee.php?page=1&course=' . $course . '&batch=' . $batch . '">1</a></button>';
                                if ($start > 2) {
                                    echo '<span>...</span>';
                                }
                            }
                            for ($btn = $start; $btn <= $end; $btn++) {
                                $url = "receivefee.php?page=$btn&course=$course&batch=$batch";
                                if (!empty($student)) {
                                    $url .= "&student=" . urlencode($student);
                                }
                                echo "<button" . ($btn == $current_page ? " class='active'" : "") . "><a href='$url'>$btn</a></button>";
                            }


                            if ($end < $total_pages) {
                                if ($end < $total_pages - 1) {
                                    echo '<span>...</span>';
                                }
                                echo '<button><a href="receivefee.php?page=' . $total_pages . '&course=' . $course . '&batch=' . $batch . '">' . $total_pages . '</a></button>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</body>
</html>