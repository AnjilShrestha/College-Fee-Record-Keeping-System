<?php
require_once 'private/autoload.php';
$student_id=$_SESSION['id'];
$search=isset($_GET['search'])?$_GET['search']:'';
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$num_per_page = 5;
$start_from = ($page - 1) * $num_per_page;
$searchCondition = '';
if(isset($_GET['btnSearchpayment']) && isset($_GET['search'])){
    if(isset($_GET['search']) &&trim($_GET['search'])&& !empty($_GET['search'])){
        $search = ($_GET['search']);
    }
}
if(isset($search)){
    $searchCondition = " AND (pays.invoice_no LIKE '%$search%' 
    OR sem.semester_name LIKE '%$search%' OR c.course_name LIKE '%$search%'
    OR pays.status LIKE '%$search%' OR pays.amount LIKE '%$search%'OR pays.payment_mode LIKE '%$search%')";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Payment</title>
    <link rel="stylesheet" href="css/table.css">
</head>
<body>
    <?php include_once 'studentmenu.php';?>
    <div class='container'>
        <div class='header'>
            <div class='header-left'>
                <div class='detail'>List of Payment </div>
            </div>
        </div>
        <div class='content'>
            <div class='content-left'>
                <span class='content-right'>
                    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="get">
                        <input type="text" name="search" value="<?php echo $search;?>" class='search-bar' >
                        <input type="submit" value="Search" name="btnSearchpayment" class="Search">
                    </form>
                </span>
            </div>
            <div class='content-center'>
                <div class='table-container'>
                    <table>
                        <tr>
                            <th>SN</th>
                            <th>Invoice no</th>
                            <th>Course</th>
                            <th>Semester name</th>
                            <th>Description</th>
                            <th>Payment Date</th>
                            <th>Payment Mode</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>View</th>
                        </tr>
                        <?php
                            $sql = "SELECT pays.*,c.course_name,f.*,sem.semester_name,
                            pays.status AS stat FROM pays 
                            INNER JOIN feestructure f ON f.fee_id = pays.fee_id
                            INNER JOIN semester_tb sem ON sem.semester_id=f.semester_id
                            INNER JOIN student_tb s ON s.student_id = pays.student_id
                            INNER JOIN batch_tb b ON b.batch_id=s.batch_id
                            INNER JOIN course_tb c ON c.course_id=b.course_id
                            WHERE s.student_id=$student_id AND pays.status='paid'
                            $searchCondition;";
                            $result = $connection->query($sql);
                            if($result->num_rows > 0){
                                $i=1;
                                while($row = $result->fetch_assoc()){
                        ?>
                        <tr>
                            <td><?php echo  $i++; ?></td>
                            <td><?php echo $row['invoice_no'];?></td>
                            <td><?php echo $row['course_name']; ?></td>
                            <td><?php echo $row['semester_name']; ?></td>
                            <td><?php  
                            $data = json_decode($row['description'], true);
                            $formattedData = ""; 
                            foreach ($data as $entry) {
                                $formattedData .= $entry['particulars'] . ": Rs" . number_format($entry['amount'], 2) . "<br />";
                            } 
                            echo $formattedData;?>
                            </td>
                            <td><?php echo $row['payment_date'];?></td>
                            <td><?php echo $row['payment_mode'];?></td>
                            <td><?php echo $row['amount']; ?></td>
                            <td><?php echo $row['stat'];?></td>
                            <td>
                                <a href="viewfee.php?id=<?php echo $row['invoice_no'];?>" class='view-a'><button id="delBtn" class='view-btn'>View</button></a>
                            </td>        
                        </tr>
                        <?php }} 
                        else{ ?>
                            <tr>
                                <td colspan='10' style='text-align:center;'>No entries found</td>
                            </tr>
                        <?php  }?>
                    </table>
                    <div class='pagination'>
                        <?php
                        $sql_count = "SELECT COUNT(pays.payment_id) AS count FROM pays 
                        INNER JOIN feestructure f ON f.fee_id = pays.fee_id
                        INNER JOIN student_tb s ON s.student_id = pays.student_id
                        INNER JOIN semester_tb sem ON sem.semester_id=f.semester_id
                        INNER JOIN batch_tb b ON b.batch_id=s.batch_id
                        INNER JOIN course_tb c ON c.course_id=b.course_id
                        WHERE s.student_id=$student_id AND pays.status='paid'
                        $searchCondition";
                        $result_count = $connection->query($sql_count);
                        $row_count = $result_count->fetch_assoc();
                        $total_pages = ceil($row_count['count'] / $num_per_page);
                        $visible_pages = 3; 
                        $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
                        $start = max(1, $current_page - floor($visible_pages / 2));
                        $end = min($start + $visible_pages - 1, $total_pages);
                        if ($start > 1) {
                            echo '<button><a href="paidfee.php?page=1">1</a></button>';
                            if ($start > 2) {
                                echo '<span>...</span>';
                            }
                        }
                        for ($btn = $start; $btn <= $end; $btn++) {
                            $url = "paidfee.php?page=$btn";
                            if (!empty($search)) {
                                $url .= "&search=" . urlencode($search);
                            }
                            echo "<button><a href='$url'>$btn</a></button>";
                        }
                        if ($end < $total_pages) {
                            if ($end < $total_pages - 1) {
                                echo '<span>...</span>';
                            }
                            echo '<button><a href="paidfee.php?page=' . $total_pages .'">' . $total_pages . '</a></button>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>       
</body>
</html>