<?php
require_once 'private/autoload.php';
$_SESSION['url']=$_SERVER['REQUEST_URI'];
// search query and pagination parameters
$search=isset($_GET['search'])?$_GET['search']:'';
$searchCondition = '';
if(isset($_GET['search'])&& !empty($_GET['search']) && trim($_GET['search'])){
   $searchCondition = " WHERE (semester_name LIKE '%$search%' OR rank LIKE '%$search%')";
}
?>
<link rel="stylesheet" href="css/table.css">
    <?php include_once 'menu.php';?>
    <div class='container'>
        <div class='header'>
            <div class='header-left'>
                <div class='detail'>Semester </div>
            </div>
            <div class='header-right'>
                <a href="addsemester.php"><button class='add'>+Add Semester</button></a>
            </div>
        </div>
        <div class='content'>
            <div class='content-left'>
                <?php getmessage();?>
                <span class='content-right'>
                    <span>Search:</span>
                    <form action="semester.php" method="get">
                        <input type="text" value="<?php echo $search?>" name="search" class='search-bar' >
                        <input type="submit" value="Search" name="btnSearchSemester" class="Search">
                    </form>
                </span>
            </div>
            <div class='content-center'>
                <div class='table-container'>
                    <table id="SearchSemester">
                        <tr>
                            <th>SN</th>
                            <th>Name</th>
                            <th>Rank</th>
                            <th>Action</th>
                        </tr>
                        <?php
                            $sql = "SELECT * FROM semester_tb $searchCondition ORDER BY rank";
                            $result = $connection->query($sql);
                            if($result->num_rows > 0){
                                $i = 1;
                                while($row = $result->fetch_assoc()){
                        ?>
                        <tr>
                            <td><?php echo $i++;?></td>
                            <td><?php echo $row['semester_name']; ?></td>
                            <td><?php echo $row['rank']; ?></td>
                            <td>
                            <a href="editsemester.php?id=<?php echo $row['semester_id'];?>" class='edit-a'><button id="editBtn" class='edit-btn'>Edit</button></a>
                                <form action="delete.php" method="post" onsubmit="return confirm('Are you sure to delete <?php echo $row['semester_name'];?>?')">
                                    <input type="hidden" value="<?php echo $row['semester_id'];?>" name='semester'>
                                    <input type="submit" value="Delete" name="Delete" id="delBtn" class='delete-btn'/>
                                </form>
                            </td>        
                        </tr>
                        <?php
                                }
                            } else {
                                echo "<tr><td colspan='6' style='text-align:center;'>No entries found</td></tr>";
                            }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

