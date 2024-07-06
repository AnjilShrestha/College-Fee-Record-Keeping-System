<?php
require_once 'private/autoload.php';
$course = isset($_GET['course']) ? $_GET['course'] : '';
$batch = isset($_GET['batch']) ? $_GET['batch'] : '';
$err = [];
if (isset($_GET['btnfee'])) {
    if (isset($_GET['course']) && !empty($_GET['course']) && trim($_GET['course'])) {
        $course = $_GET['course'];
    } else {
        $err['course'] = 'Select Course';
    }
    if (isset($_GET['batch']) && !empty($_GET['batch'])) {
        $batch = $_GET['batch'];
    } else {
        $err['batch'] = 'Select batch';
    }
}
$_SESSION['url']=$_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Fee</title>
    <link rel="stylesheet" href="css/table.css">
    <link rel="stylesheet" href="css/getstudent.css">
</head>
<body>
    <?php include_once 'menu.php';
    ?>
    <div class='container'>
        <div class='header'>
            <div class='header-left'>
                <div class='detail'> Fee</div>
            </div>
            <div class='header-right'>
                <a href="addfee.php"><button class='add'>+ Create</button></a>
            </div>
        </div>
        <div class='Form'>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
                <?php include_once 'search.php';?>
            <span class='search-row'>
                <input type="submit" name='btnfee' value='Search'>
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
                        <table id="Searchfee">
                            <tr>
                                <th>SN</th>
                                <th>Semester</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Paid</th>
                                <th>Unpaid</th>
                                <th>Total Students</th>
                                <th>Due date</th>
                                <th>Notify</th>
                                <th>Action</th>
                            </tr>
                            <?php
                            $sql = "SELECT s.*,sem.semester_name,
                            COUNT(DISTINCT CASE WHEN p.student_id IS NOT NULL THEN st.student_id END) AS paid,
                            COUNT(DISTINCT st.student_id) AS total  FROM feestructure s
                            INNER JOIN batch_tb b ON s.batch_id = b.batch_id
                            INNER JOIN semester_tb AS sem ON sem.semester_id=s.semester_id
                            LEFT JOIN student_tb st ON st.batch_id = b.batch_id
                            INNER JOIN course_tb c ON c.course_id=b.course_id 
                            LEFT JOIN pays p ON s.fee_id = p.fee_id AND p.student_id = st.student_id
                            WHERE b.batch_name='$batch' AND c.course_name='$course'
                            GROUP BY s.rank,s.fee_id asc";
                            $result = $connection->query($sql);
                            if ($result->num_rows > 0) {
                                $i=1;
                                ?>
                                <?php
                                while ($row = $result->fetch_assoc()) {
                                    $unpaid = $row['total'] - $row['paid'];
                                    ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo $row['semester_name'];?></td>
                                        <td><?php  $data = json_decode($row['description'], true);
                                        $formattedData = ""; 
                                        foreach ($data as $entry) {
                                            $formattedData .=  $entry['particulars'] . ": Rs" . number_format($entry['amount'], 2) . "<br />";
                                        } echo $formattedData;?></td>
                                        <td><?php echo $row['amount'];?></td>
                                        <td><a href="paidstudent.php?id=<?php echo $row['fee_id']; ?>&status=<?php echo urlencode('paid'); ?>" style="color: black;"><?php echo $row['paid']; ?></a></td>
                                        <td><a href="paidstudent.php?id=<?php echo $row['fee_id']; ?>&status=<?php echo urlencode('unpaid'); ?>" style='color:black;'><?php echo $unpaid; ?></a></td>
                                        <td><?php echo $row['total'];?></td>
                                        <td><?php echo $row['due_date'];?></td>
                                        <td>
                                            <a href="notify.php?id=<?php echo $row['fee_id'];?>" class='edit-a'>
                                                <button id="editBtn" class='edit-btn'>notify</button>
                                            </a> 
                                        </td>
                                        <td>
                                            <a href="editfee.php?id=<?php echo $row['fee_id'];?>" class='edit-a'>
                                                <button id="editBtn" class='edit-btn'>Edit</button>
                                            </a> 
                                            <form action="delete.php" method="post" onsubmit="return confirm('Are you sure to delete <?php echo $row['semester_name'];?> fee?')">
                                                <input type="hidden" value="<?php echo $row['fee_id'];?>" name='fee'>
                                                <input type="submit" value="Delete" name="Delete" id="delBtn" class='delete-btn' style='background-color:red;' />
                                            </form> 
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
                <?php
            }
            ?>
        </div>
     </div>
</body>
</html>