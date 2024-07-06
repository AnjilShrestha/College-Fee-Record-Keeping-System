<?php
$course_name=$durationyears=$course_type='';
require_once 'private/autoload.php';
$loc=isset($_SESSION['url'])?$_SESSION['url']:'course.php';
if (!isset($_GET['id'])) {
    header('location:'.$loc);
} else {
    $id = $_GET['id'];
    $sql = "SELECT * FROM course_tb WHERE course_id='$id'";
    $result = mysqli_query($connection, $sql);
    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        extract($data);
        $course_id = $data['course_id'];
    } else {
        header('location:'.$loc);
    }
}

if (isset($_POST['btncourse'])) {
    $err = [];
    if (isset($_POST['course_name']) && !empty($_POST['course_name']) && trim($_POST['course_name'])) {
        $course_name = $_POST['course_name'];
        $select="SELECT COUNT(*) AS count FROM course_tb WHERE course_name='$course_name' AND course_id!=$course_id;";
        $result=mysqli_query($connection, $select);
        if($result){
            $row = mysqli_fetch_assoc($result);
            $count = $row['count'];
            if($count>0)
            {
                $err['course_name']="Program already exists";
            }
        }
    } else {
        $err['course_name'] = "Enter name of program";
    }
    if (isset($_POST['field']) && !empty($_POST['field']) && trim($_POST['field'])) {
        $field = $_POST['field'];
    } else {
        $err['field'] = "Enter course field";
    }
    if(isset($_POST['duration']) && !empty($_POST['duration']) && trim($_POST['duration'])){
        $duration= $_POST['duration'];
        if($duration<0)
        {
            $err['duration']="Enter correct duration period"; 
        }
    }else{
        $err['duration']="Enter duration";
    }
    if (count($err) == 0) {
        $update = "UPDATE course_tb SET course_name='$course_name', field='$field',durationyears='$duration' WHERE course_id=$course_id";
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
    <title>Edit Course</title>
    <link rel="stylesheet" href="css/add.css">
</head>
<body>
    <?php include_once 'menu.php'; ?>
    <div class='container'>
        <div class='header'>
            <div class='header-left'>
                <div class='detail'>Edit Course </div>
            </div>
        </div>
        <div class='form-body-1'>
            <div class='form-body-r'>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $id ?>" method='post'>
                    <div class='form-row'>
                        <label for="course_name">Course Name</label>
                        <input type="text" name='course_name' value="<?php echo $course_name; ?>">
                        <span class='err-msg'><?php echo (isset($err['course_name'])) ? $err['course_name'] : ''; ?></span>
                    </div>
                    <div class='form-row'>
                            <label for="field">Field</label>
                            <input type="text" name='field' value="<?php echo $field; ?>">
                            <span class='err-msg'><?php echo (isset($err['field']))?$err['field']:''; ?></span>
                        </div>
                    <div class='form-row'>
                        <label for="duration">Duration</label>
                        <input type="text" name='duration' value="<?php echo $durationyears; ?>">-year
                        <span class='err-msg'><?php echo (isset($err['duration'])) ? $err['duration'] : ''; ?></span>
                    </div>
                    <div class='form-row'>
                        <input type="submit" name='btncourse' value='Update'>
                        <input type="submit" name='cancel' value='Cancel'>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
