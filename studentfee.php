<?php
require_once 'private/autoload.php';
if(isset($_SESSION['esewat'])){
    $id = $_SESSION['esewat'];
    $sql = "DELETE FROM pays where payment_id='$id'";
    if(mysqli_query($connection,$sql)){
    }
    unset($_SESSION['esewat']);
}
$search = isset($_GET['search']) ? $_GET['search'] : '';
$student_id=$_SESSION['id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Details</title>
    <link rel="stylesheet" href="css/table.css">
</head>
<body>
    <?php include_once 'studentmenu.php';?>
    <div class='container'>
        <div class='header'>
            <div class='header-left'>
                <div class='detail'>Fee</div>
            </div>
        </div>
        <div class='content'>
            <div class='content-left'>
                <span class='content-right'>
                    <span>Search:</span>
                    <form action="studentfee.php" method="get">
                        <input type="text" value="<?php echo $search;?>" name="search" class='search-bar'>
                        <input type="submit" value="Search" name="btnSearchfee" class="Search">
                    </form>
                </span>
            </div>
            <div class='content-center'>
                <div class='table-container'>
                    <table>
                        <tr>
                            <th>SN</th>
                            <th>Semester</th>
                            <th>Description</th>
                            <th>Due date</th>
                            <th>Fee Amount</th>
                            <th>Action</th>
                        </tr>
                        <?php 
                        $searchCondition = '';
                        if(isset($_GET['btnSearchfee'])) {
                            $search = $_GET['search'];
                            $searchCondition = "AND (st.semester_name LIKE '%$search%' OR f.amount LIKE '%$search%')";
                        }
                        $sql = "SELECT f.* , IFNULL(p.status, 'unpaid') AS stat,st.semester_name FROM feestructure f
                        INNER JOIN student_tb s ON s.batch_id = f.batch_id
                        INNER JOIN semester_tb st ON st.semester_id=f.semester_id
                        LEFT JOIN pays p ON p.fee_id = f.fee_id AND p.student_id = s.student_id
                        WHERE s.student_id = '$student_id' AND f.due_date IS NOT NULL
                        $searchCondition 
                        ORDER BY f.rank";
                        $result = mysqli_query($connection, $sql);
                        if($result->num_rows > 0) {
                            $i = 1;
                            while($row = $result->fetch_assoc()) {
                                ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo $row['semester_name'];?></td>
                                    <td>
                                        <?php  $data = json_decode($row['description'], true);
                                        $formattedData = ""; 
                                        foreach ($data as $entry) {
                                            $formattedData .= $entry['particulars'] . ": Rs" . number_format($entry['amount'], 2) . "<br />";
                                        } echo $formattedData;?>
                                    </td>
                                    <td><?php echo $row['due_date'];?></td>
                                    <td><?php echo $row['amount'];?></td>
                                    <td>
                                    <?php
                                        if($row['stat']!=='paid'){ ?>
                                            <form method="post" action="pay.php">
                                                <input type="hidden" name="fee_id" value="<?php echo $row['fee_id'];?>">
                                                <input type="submit" name="submit" value="Pay" class='edit-btn'>
                                            </form>
                                        <?php 
                                    }else{
                                        echo 'paid';
                                    } 
                                    ?>
                                    </td>
                                </tr>
                                <?php 
                            }
                        } else { ?>
                            <tr>
                                <td colspan='10' style='text-align:center;'>No entries found</td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
