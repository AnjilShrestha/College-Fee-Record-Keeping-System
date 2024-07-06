<?php
require_once 'private/autoload.php';
$loc=isset($_SESSION['url'])?$_SESSION['url']:"batch.php";
if (!isset($_GET['id'])) {
    header('location:'.$loc);
} else {
    $id = $_GET['id'];
    $sql = "SELECT * FROM batch_tb WHERE batch_id='$id'";
    $result = mysqli_query($connection, $sql);
    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        extract($data);
        $status=$data['status'];
    } else {
        header('location:'.$loc);
    }
}

if (isset($_POST['btnbatch'])) {
    $err = [];
    if(isset($_POST['course']) && !empty($_POST['course']) && trim($_POST['course'])){
        $course= $_POST['course'];
    }else{
        $err['course']="Select course";
    }
    if (isset($_POST['batch_name']) && !empty($_POST['batch_name']) && trim($_POST['batch_name'])) {
        $name = $_POST['batch_name'];
        $select="SELECT COUNT(*) AS count FROM batch_tb WHERE course_id='$course' 
        AND batch_name='$name' AND batch_id!=$id;";
        $result=mysqli_query($connection, $select);
        if($result){
            $row = mysqli_fetch_assoc($result);
            $count = $row['count'];
            if($count>0)
            {
                $err['batch_name']="batch already exists";
            }
        }
    } else {
        $err['name'] = "Enter name of batch";
    }
    if(isset($_POST['start']) && !empty(trim($_POST['start']))){
        $start=$_POST['start'];
    }else{
        $err['start']='Enter start year';
    }
    $status=$_POST['status'];
    if (count($err) == 0) {
        $update = "UPDATE batch_tb SET batch_name='$name', course_id='$course_id',
        status='$status', startyear='$start' WHERE batch_id=$id";
        $connection->query($update);
        if ($connection->affected_rows==1) {
            $_SESSION['success']='Update Success';
        } else {
            $_SESSION['failure']='Not Updated';
        }
        header('location:'.$loc);
        exit();
    }
}
if(isset($_POST['cancel'])){
    header('Location:'.$loc);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Batch</title>
    <link rel="stylesheet" href="css/add.css">
</head>
<body>
    <?php include_once 'menu.php'; ?>
    <div class='container'>
        <div class='header'>
            <div class='header-left'>
                <div class='detail'>Edit Batch</div>
            </div>
        </div>
        <div class='form-body-1'>
            <div class='form-body-r'>
                <form action="<?php echo $_SERVER['PHP_SELF'];?>?id=<?php echo $id ?>" method='post'>
                    <div class='form-row'>
                        <label for="course">Course Name</label>
                        <select name="course">
                            <option value="0">Select course</option>
                            <?php
                            $select="SELECT * FROM course_tb";
                            $result = $connection->query($select);
                            if($result->num_rows>0){
                                while($row = $result->fetch_assoc()){
                            ?>
                            <option value="<?php echo $row['course_id'];?>" <?php echo ($course_id == $row['course_id']) ? 'selected' : ''; ?> ><?php echo $row['course_name'];?></option>
                            <?php }} ?>
                        </select>
                        <span class='err-msg'><?php echo (isset($err['course']))?$err['course']:''; ?></span>
                    </div>
                    <div class='form-row'>
                        <label for="batch_name">Batch Name</label>
                        <input type="text" name='batch_name' value="<?php echo $batch_name; ?>">
                        <span class='err-msg'><?php echo (isset($err['batch_name'])) ? $err['batch_name'] : ''; ?></span>
                    </div>
                    <div class='form-row'>
                        <label for="start">start year</label>
                        <input type="text" name='start' value="<?php echo $startyear; ?>">
                        <span class='err-msg'><?php echo (isset($err['start'])) ? $err['start'] : ''; ?></span>
                    </div>
                    <div class='form-row'>
                        <label for="status">Status:</label>
                        <?php if($data['status']==1){?>
                            <input type="radio" name="status" value='1' checked="">Running
                            <input type="radio" name="status" value='0'>Completed
                        <?php }else{ ?>
                            <input type="radio" name="status" value='1'>Running
                            <input type="radio" name="status" value='0' checked="">Completed
                            <?php } ?>
                    </div>
                    <div class='form-row'>
                        <input type="submit" name='btnbatch' value='Update'>
                        <input type="submit" name='cancel' value='Cancel'>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
