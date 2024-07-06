<?php
require_once 'private/autoload.php';
$_SESSION['url']=$_SERVER['REQUEST_URI'];
// search query and pagination parameters
$search=isset($_GET['search'])?$_GET['search']:'';
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$num_per_page =$visible_pages= 5;
$start_from = ($page - 1) * $num_per_page;
$searchCondition = '';
if(isset($_GET['search'])&& !empty($_GET['search']) && trim($_GET['search'])){
   $searchCondition = " WHERE (name LIKE '%$search%' OR username LIKE '%$search%' OR email LIKE '%$search%')";
}
?>
<link rel="stylesheet" href="css/table.css">
    <?php include_once 'menu.php';?>
    <div class='container'>
        <div class='header'>
            <div class='header-left'>
                <div class='detail'>Admin </div>
            </div>
            <div class='header-right'>
                <a href="addadmin.php"><button class='add'>+Add Admin</button></a>
            </div>
        </div>
        <div class='content'>
            <div class='content-left'>
                <?php getmessage();?>
                <span class='content-right'>
                    <span>Search:</span>
                    <form action="admin.php" method="get">
                        <input type="text" value="<?php echo $search?>" name="search" class='search-bar' >
                        <input type="submit" value="Search" name="btnSearchAdmin" class="Search">
                    </form>
                </span>
            </div>
            <div class='content-center'>
                <div class='table-container'>
                    <table id="SearchAdmin">
                        <tr>
                            <th>SN</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        <?php
                            $sql = "SELECT * FROM admin_tb $searchCondition";
                            $result = $connection->query($sql);
                            if($result->num_rows > 0){
                                $i = 1;
                                while($row = $result->fetch_assoc()){
                        ?>
                        <tr>
                            <td><?php echo $i++;?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php  if($row['status']==1) { 
                                    echo 'Active';
                                }else{ 
                                    echo'Inactive';
                                }?>
                            </td>
                            <td>
                            <a href="editadmin.php?id=<?php echo $row['admin_id'];?>" class='edit-a'><button id="editBtn" class='edit-btn'>Edit</button></a>
                                <form action="delete.php" method="post" onsubmit="return confirm('Are you sure to delete <?php echo $row['name'];?>?')">
                                    <input type="hidden" value="<?php echo $row['admin_id'];?>" name='admin'>
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

