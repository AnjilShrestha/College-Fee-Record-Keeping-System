<?php
require_once 'private/autoload.php';
$search=isset($_GET['search'])?$_GET['search']:'';
$_SESSION['url']=$_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course</title>
    <link rel="stylesheet" href="css/table.css">
</head>
<body>
    <?php include_once 'menu.php'; ?>
    <div class='container'>
        <div class='header'>
            <div class='header-left'>
                <div class='detail'>Courses</div>
            </div>
            <div class='header-right'>
                <button class='add'><a href="addcourse.php">+ Add</a></button>
            </div>
        </div>
        <div class='content'>
            <div class='content-left'>
                <?php getmessage();?>
                <span class='content-right'>
                    <span>Search:</span>
                    <form action="course.php" method="get">
                        <input type="text" value="<?php echo $search?>" name="search" class='search-bar' >
                        <input type="submit" value="Search" name="btnSearchprogram" class="Search">
                    </form>
                </span>
            </div>
            <div class='content-center'>
                <div class='table-container'>
                    <table id="batchSearch">
                        <tr>
                            <th>SN</th>
                            <th>Course name</th>
                            <th>Field</th>
                            <th>Duration</th>
                            <th>Action</th>
                        </tr>
                        <?php
                        $searchCondition = '';
                        if(isset($_GET['search']) && !empty($_GET['search']) && trim($_GET['search'])){
                            $searchCondition = " WHERE course_name LIKE '%$search%' OR field LIKE'%$search%'
                            OR durationyears LIKE'%$search%'";
                        }
                        $sql ="SELECT * FROM course_tb $searchCondition";
                        $result = $connection->query($sql);
                        if($result->num_rows > 0){
                            $i = 1;
                            while($row = $result->fetch_assoc()){
                        ?>
                        <tr>
                            <td><?php echo  $i++; ?></td>
                            <td><?php echo $row['course_name']; ?></td>
                            <td><?php echo $row['field']; ?></td>
                            <td><?php echo $row['durationyears'] .' year';?></td>
                            <td>
                            <a href="editcourse.php?id=<?php echo $row['course_id'];?>" class='edit-a'><button id="editBtn" class='edit-btn'>Edit</button></a>
                                <form action="delete.php" method="post" onsubmit="return confirm('Are you sure to delete <?php echo $row['course_name'];?>?')">
                                    <input type="hidden" value="<?php echo $row['course_id'];?>" name='course'>
                                    <input type="submit" value="Delete" name="Delete" id="delBtn" class='delete-btn'/>
                                </form>
                            </td>        
                        </tr>
                        <?php }} 
                        else{ ?>
                            <tr>
                                <td colspan='6' style='text-align:center;'>No entries found</td>
                            </tr>
                        <?php  }?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
