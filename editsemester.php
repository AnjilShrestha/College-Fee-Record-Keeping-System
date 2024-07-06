<?php
$semester_name=$rank='';
require_once 'private/autoload.php';
$loc=isset($_SESSION['url'])?$_SESSION['url']:'semester.php';
if (!isset($_GET['id'])) {
    header('location:'.$loc);
} else {
    $id = $_GET['id'];
    $sql = "SELECT * FROM semester_tb WHERE semester_id='$id'";
    $result = mysqli_query($connection, $sql);
    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        extract($data);
        $semester_id = $data['semester_id'];
    } else {
        header('location:'.$loc);
    }
}
if (isset($_POST['btnsemester'])) {
    $err = [];
    if (isset($_POST['semester_name']) && !empty($_POST['semester_name']) && trim($_POST['semester_name'])) {
        $semester_name = $_POST['semester_name'];
        $select="SELECT COUNT(*) AS count FROM semester_tb WHERE semester_name='$semester_name' AND semester_id!=$id;";
        $result=mysqli_query($connection, $select);
        if($result){
            $row = mysqli_fetch_assoc($result);
            $count = $row['count'];
            if($count>0)
            {
                $err['semester_name']="Semester already exists";
            }
        }
    } else {
        $err['semester_name'] = "Enter name of semester";
    }
    if(isset($_POST['rank']) && !empty($_POST['rank']) && trim($_POST['rank'])){
        $rank= $_POST['rank'];
        if($rank<0)
        {
            $err['rank']="Enter correct rank"; 
        }
    }else{
        $err['rank']="Enter rank";
    }
    if (count($err) == 0) {
        $update = "UPDATE semester_tb SET semester_name='$semester_name',rank='$rank' WHERE semester_id=$id";
        $connection->query($update);
        if ($connection->affected_rows==1) {
            $_SESSION['success']='Update Success';
            header('location:'.$loc);
            exit();
        } else {
            $_SESSION['failure']='Data not Updated';
            header('location:'.$loc);
            exit();
        }
    }
}
if(isset($_POST['cancel'])){
    header('location:'.$loc);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Semester</title>
    <link rel="stylesheet" href="css/add.css">
</head>
<body>
    <?php include_once 'menu.php'; ?>
    <div class='container'>
        <div class='header'>
            <div class='header-left'>
                <div class='detail'>Edit Semester</div>
            </div>
        </div>
        <div class='form-body-1'>
            <div class='form-body-r'>
                <form action="<?php echo $_SERVER['PHP_SELF'];?>?id=<?php echo $id ?>" method='post'>
                    <div class='form-row'>
                        <label for="semester_name">Semester Name</label>
                        <input type="text" name='semester_name' value="<?php echo $semester_name; ?>">
                        <span class='err-msg'><?php echo (isset($err['semester_name'])) ? $err['semester_name'] : ''; ?></span>
                    </div>
                    <div class='form-row'>
                        <label for="rank">Rank</label>
                        <input type="text" name='rank' value="<?php echo $rank; ?>">
                        <span class='err-msg'><?php echo (isset($err['rank'])) ? $err['rank'] : ''; ?></span>
                    </div>
                    <div class='form-row'>
                        <input type="submit" name='btnsemester' value='Update'>
                        <input type="submit" name='cancel' value='Cancel'>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
