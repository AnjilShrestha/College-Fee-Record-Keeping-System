<?php
require_once 'private/autoload.php';
$name=$field=$duration=$msg='';
$loc=isset($_SESSION['url'])?$_SESSION['url']:"course.php";
if (isset($_POST['btncourse'])){
    $err = [];
    if(isset($_POST['name']) && !empty($_POST['name']) && trim($_POST['name'])){
        $name= $_POST['name'];
        $select="SELECT COUNT(*) AS count FROM course_tb WHERE course_name='$name';";
        $result=mysqli_query($connection, $select);
        if($result){
            $row = mysqli_fetch_assoc($result);
            $count = $row['count'];
            if($count>0)
            {
                $err['name']="Course already exists";
            }
        }
    }else{
        $err['name']="Enter name of Course";
    }
    if(isset($_POST['field']) && !empty($_POST['field']) && trim($_POST['field'])){
        $field= $_POST['field'];
    }else{
        $err['field']="Enter course field";
    }
    if(isset($_POST['duration']) && !empty($_POST['duration']) && trim($_POST['duration'])){
        $duration= $_POST['duration'];
        if($duration<0)
        {
            $err['duration']="Enter correct duration period"; 
        }
    }else{
        $err['duration']="Enter duration in year";
    }
    if(count($err)==0){
        $insert= "INSERT INTO course_tb(course_name,field,durationyears) 
        VALUES ('$name','$field','$duration')";
        if($connection->query($insert)){
            $_SESSION['success'] ='Course added Successfully';
            header('Location:'.$loc);
            exit();
        }
        else{
            $msg ='Course add failure';
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
    <title>Add Course</title>
    <link rel="stylesheet" href="css/add.css">
</head>
<body>
    <?php include_once 'menu.php';?>
    <div class='container'>
    <div class='header'>
            <div class='header-left'>
                <div class='detail'>Add Course</div>
            </div>
        </div>
        <div class="content">
            <div class='msg'>
                <p> <?php echo $msg;?></p>
            </div>
            <div class='form-body-1'>
                <div class='form-body-r'>
                    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method='post'>
                        <div class='form-row'>
                            <label for="name">Course Name</label>
                            <input type="text" name='name' value="<?php echo $name;?>">
                            <span class='err-msg'><?php echo (isset($err['name']))?$err['name']:''; ?></span>
                        </div>
                        <div class='form-row'>
                            <label for="field">Field</label>
                            <input type="text" name='field' value="<?php echo $field;?>">
                            <span class='err-msg'><?php echo (isset($err['field']))?$err['field']:''; ?></span>
                        </div>
                        <div class='form-row'>
                            <label for="duration">Course Duration</label>
                            <input type="number" name='duration' value="<?php echo $duration;?>">-year
                            <span class='err-msg'><?php echo (isset($err['duration']))?$err['duration']:''; ?></span>
                        </div>
                        <div class='form-row'>
                            <input type="submit" name='btncourse' value='Create'>
                            <input type="submit" name='cancel' value='Cancel'>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>