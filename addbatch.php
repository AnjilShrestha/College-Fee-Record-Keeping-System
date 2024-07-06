<?php
require_once 'private/autoload.php';
$name=$course=$start='';
$loc=isset($_SESSION['url'])?$_SESSION['url']:"batch.php";
if (isset($_POST['btnbatch'])){
    $err = [];
    if(isset($_POST['course']) && !empty($_POST['course']) && trim($_POST['course'])){
        $course= $_POST['course'];
    }else{
        $err['course']="Select course";
    }
    if(isset($_POST['name']) && !empty($_POST['name']) && trim($_POST['name'])){
        $name= $_POST['name'];
        $select="SELECT COUNT(*) AS count FROM batch_tb WHERE batch_name='{$_POST['name']}'";
        $result=mysqli_query($connection, $select);
        if($result){
            $row = mysqli_fetch_assoc($result);
            $count = $row['count'];
            if($count>0)
            {
                $err['name']="batch already exists";
            }
        }
    }else{
        $err['name']="Enter name of batch";
    }
    if(isset($_POST['start']) && !empty(trim($_POST['start']))){
        $year=$_POST['start'];
        $current=date('Y');
        $maxYear = $current + 1; 
        if ($year==$current || $year == $maxYear) {
        } else {
            $err['start']='Batch start should be current year or next year only';
        }
    }else{
        $err['start']='Enter start year';
    }
    if(count($err)==0){
        $insert= "INSERT INTO batch_tb(batch_name,status,course_id,startyear) 
        VALUES ('$name','1','$course','$year')";
        if($connection->query($insert)){
            $_SESSION['success'] ='Batch added Successfully';
        }
        else{
            $_SESSION['failure']='Batch add failure';
        }   
        header('location:'. $loc);
        exit();
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
    <title>Add Batch</title>
    <link rel="stylesheet" href="css/add.css">
</head>
<body>
    <?php include_once 'menu.php';?>
    <div class='container'>
    <div class='header'>
            <div class='header-left'>
                <div class='detail'>Add Batch</div>
            </div>
        </div>
        <div class="content">
            <div class='form-body-1'>
                <div class='form-body-r'>
                    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method='post'>
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
                                <option value="<?php echo $row['course_id'];?>" <?php echo ($course == $row['course_id']) ? 'selected' : ''; ?> ><?php echo $row['course_name'];?></option>
                                <?php }} ?>
                            </select>
                            <span class='err-msg'><?php echo (isset($err['course']))?$err['course']:''; ?></span>
                        </div>
                        <div class='form-row'>
                            <label for="name">Batch Name</label>
                            <input type="text" name='name' value="<?php echo $name;?>" placeholder='course-year'>
                            <span class='err-msg'><?php echo (isset($err['name']))?$err['name']:''; ?></span>
                        </div>
                        <div class='form-row'>
                            <label for="start">Start Year</label>
                            <input type="text" name='start' value="<?php echo $start;?>" placeholder='Start Year'>
                            <span class='err-msg'><?php echo (isset($err['start']))?$err['start']:''; ?></span>
                        </div>
                        <div class='form-row'>
                            <input type="submit" name='btnbatch' value='Create'>
                            <input type="submit" name='cancel' value='Cancel'>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>