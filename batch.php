<?php
require_once 'private/autoload.php';
// search query and pagination parameters
$search=isset($_GET['search'])?$_GET['search']:'';
$searchCondition = '';
if (!empty($search)) {
    $searchCondition = "AND (b.batch_name LIKE '%$search%' OR c.course_name LIKE '%$search%' OR b.startyear LIKE '%$search%')";
}
// fetching batch data with pagination and search
$sql = "SELECT b.*,c.course_name, IFNULL(COUNT(DISTINCT s.student_id), 0) AS student FROM batch_tb AS b 
INNER JOIN course_tb AS c ON c.course_id=b.course_id LEFT JOIN student_tb AS s ON s.batch_id = b.batch_id 
WHERE 1=1 $searchCondition GROUP BY b.status, b.batch_id";
$result = $connection->query($sql);
$_SESSION['url']=$_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batch</title>
    <link rel="stylesheet" href="css/table.css">
</head>
<body>
<?php include_once 'menu.php'; ?>
<div class='container'>
    <div class='header'>
        <div class='header-left'>
            <div class='detail'>Batch</div>
        </div>
        <div class='header-right'>
            <button class='add'><a href="addbatch.php">+ Add</a></button>
        </div>
    </div>
    <div class='content'>
        <div class='content-left'>
            <?php getmessage();?>
            <span class='content-right'>
                <span>Search:</span>
                <form action="batch.php" method="GET">
                    <input type="text" value="<?php echo ($search); ?>" name="search" class='search-bar'>
                    <input type="submit" value="Search" name="btnSearchbatch" class="Search">
                </form>
            </span>
        </div>
        <div class='content-center'>
            <div class='table-container'>
                <table id="batchSearch">
                    <tr>
                        <th>SN</th>
                        <th>Course Name</th>
                        <th>Batch Name</th>
                        <th>Start Year</th>
                        <th>Student</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    <?php
                    if ($result->num_rows > 0) {
                        $i=1;
                        while ($row = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $row['course_name']; ?></td>
                        <td><?php echo $row['batch_name']; ?></td>
                        <td><?php echo $row['startyear'];?></td>
                        <td><?php echo $row['student']; ?></td>
                        <td><?php echo ($row['status']==1)?'running':'completed';?></td>
                        <td>
                            <a href="editbatch.php?id=<?php echo $row['batch_id'];?>" class='edit-a'><button id="editBtn" class='edit-btn'>Edit</button></a>
                            <form action="delete.php" method="post" onsubmit="return confirm('Are you sure to delete <?php echo $row['batch_name'];?>?')">
                                <input type="hidden" value="<?php echo $row['batch_id'];?>" name='batch'>
                                <input type="submit" value="Delete" name="Delete" id="delBtn" class='delete-btn'/>
                            </form>
                        </td>        
                    </tr>
                    <?php } } else { ?>
                    <tr>
                        <td colspan='7' style='text-align:center;'>No entries found</td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>