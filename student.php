<?php
require_once 'private/autoload.php';

$num_per_page = 5;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$course=isset($_GET['course'])?$_GET['course']:'';
$batch=isset($_GET['batch'])?$_GET['batch']:'';
$start_from = ($page - 1) * $num_per_page;
$err=[];
if (isset($_GET['btnstudent'])) {
    if (empty($course)) {
        $err['course'] = '*';
    }
    if (empty($batch)) {
        $err['batch'] = '*';
    }
}
if(isset($_GET['student']) && trim($_GET['student'] ) && !empty($_GET['student'])){
    $student=$_GET['student'];
}else{
    $student='';
}
$searchCondition=!empty($student)?"AND (name LIKE '%$student%' OR roll_no LIKE '%$student%') ":'';
$_SESSION['url']=$_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student</title>
    <link rel="stylesheet" href="css/table.css">
    <link rel="stylesheet" href="css/getstudent.css">
</head>

<body>
    <?php include_once 'menu.php';?>
    <div class='container'>
        <div class='header'>
            <div class='header-left'>
                <div class='detail'>Student </div>
            </div>
            <div class='header-right'>
                <button class='add'><a href="addstudent.php">+ Add Student</a></button>
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
        <?php getmessage();?>
        <?php
        if (count($err) == 0 && !empty($course) && !empty($batch)) {
            ?>
            <div class='content'>
                <div class='content-center'>
                    <div class='table-container'>
                        <table id="SearchStudent">
                            <tr>
                                <th>Roll No</th>
                                <th>Profile picture</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Gender</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Enrollment date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            <?php
                            $sql = "SELECT s.*,s.roll_no AS roll FROM student_tb AS s
                            INNER JOIN batch_tb b ON b.batch_id=s.batch_id
                            INNER JOIN course_tb c ON c.course_id=b.course_id 
                            WHERE b.batch_name='$batch' AND c.course_name='$course' 
                            $searchCondition 
                            LIMIT $start_from,$num_per_page";
                            $result = $connection->query($sql);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td><?php echo $row['roll']; ?></td>
                                        <td><img src="./upload/<?php echo isset($row['image'])?$row['image']:'noprofil.jpg'?>" width='100px'></td>
                                        <td><?php echo $row['name']; ?></td>
                                        <td><?php echo $row['email']; ?></td>
                                        <td><?php echo $row['gender']; ?></td>
                                        <td><?php echo $row['phone_number']; ?></td>
                                        <td><?php echo $row['address']; ?></td>
                                        <td><?php echo $row['enrollment_date']; ?></td>
                                        <td><?php echo ($row['status']==1)?'Active':'Dropout'; ?></td>
                                        <td>
                                        <a href="editstudent.php?id=<?php echo $row['student_id'];?>" class='edit-a'><button id="editBtn" class='edit-btn'>Edit</button></a>
                                            <form action="delete.php" method="post" onsubmit="return confirm('Are you sure to delete <?php echo $row['name'];?>?')">
                                            <input type="hidden" value="<?php echo $row['student_id'];?>" name='student'>
                                            <input type="submit" value="Delete" name="Delete" id="delBtn" class='delete-btn' style="background-color:red;"/>
                                        </form>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                ?>
                                <td colspan=10 style='text-align:center;'>No entries found</td>
                                <?php
                            }
                            ?>
                        </table>
                        <div class='pagination'>
                            <?php
                            $sql_count = "SELECT COUNT(*) AS count FROM student_tb AS s
                            INNER JOIN batch_tb b ON b.batch_id=s.batch_id 
                            INNER JOIN course_tb c ON c.course_id=b.course_id
                            WHERE b.batch_name='$batch' AND c.course_name='$course'
                            $searchCondition";
                            $result_count = $connection->query($sql_count);
                            $row_count = $result_count->fetch_assoc();
                            $total_pages = ceil($row_count['count'] / $num_per_page);
                            
                            $visible_pages = 3; 
                            $current_page = isset($_GET['page']) ? $_GET['page'] : 1;

                            $start = max(1, $current_page - floor($visible_pages / 2));
                            $end = min($start + $visible_pages - 1, $total_pages);

                            if ($start > 1) {
                                echo '<button><a href="student.php?page=1&course=' . $course . '&batch=' . $batch . '">1</a></button>';
                                if ($start > 2) {
                                    echo '<span>...</span>';
                                }
                            }
                            for ($btn = $start; $btn <= $end; $btn++) {
                                $url = "student.php?page=$btn&course=$course&batch=$batch";
                                if (!empty($student)) {
                                    $url .= "&student=" . urlencode($student);
                                }
                                echo "<button><a href='$url'>$btn</a></button>";
                            }
                            if ($end < $total_pages) {
                                if ($end < $total_pages - 1) {
                                    echo '<span>...</span>';
                                }
                                echo '<button><a href="student.php?page=' . $total_pages . '&course=' . $course . '&batch=' . $batch . '">' . $total_pages . '</a></button>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }else{?>
            <div class='content'>
            <div class='content-center'>
                <div class='table-container'>
                    <table id="SearchStudent">
                        <tr>
                            <th>Batch</th>
                            <th>Roll No</th>
                            <th>Profile Picture</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Gender</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Enrollment date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        <?php
                        $sql = "SELECT s.*,s.roll_no AS roll,b.batch_name FROM student_tb AS s
                        INNER JOIN batch_tb b ON b.batch_id=s.batch_id 
                        INNER JOIN course_tb c ON c.course_id=b.course_id 
                        WHERE 1=1 ORDER BY s.student_id desc LIMIT 5";
                        $result = $connection->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                ?>
                                <tr>
                                    <td><?php echo $row['batch_name']; ?></td>
                                    <td><?php echo $row['roll']; ?></td>
                                    <td><img src="./upload/<?php echo isset($row['image'])?$row['image']:'noprofil.jpg'?>" width='100px'></td>
                                    <td><?php echo $row['name']; ?></td>
                                    <td><?php echo $row['email']; ?></td>
                                    <td><?php echo $row['gender']; ?></td>
                                    <td><?php echo $row['phone_number']; ?></td>
                                    <td><?php echo $row['address']; ?></td>
                                    <td><?php echo $row['enrollment_date']; ?></td>
                                    <td><?php echo ($row['status']==1)?'Active':'Dropout'; ?></td>
                                    <td>
                                        <a href="editstudent.php?id=<?php echo $row['student_id'];?>" class='edit-a'><button id="editBtn" class='edit-btn'>Edit</button></a>
                                        <form action="delete.php" method="post" onsubmit="return confirm('Are you sure to delete <?php echo $row['name'];?>?')">
                                            <input type="hidden" value="<?php echo $row['student_id'];?>" name='student'>
                                            <input type="submit" value="Delete" name="Delete" id="delBtn" class='delete-btn' style="background-color:red;"/>
                                        </form>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <td colspan=10 style='text-align:center;'>No entries found</td>
                            <?php
                        }
                        ?>
                    </table> 
       <?php }
        ?>
    </div>
</body>

</html>
