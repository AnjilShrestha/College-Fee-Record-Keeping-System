<?php
require_once 'private/autoload.php';
$name=$semester=$msg='';
$loc=isset($_SESSION['url'])?$_SESSION['url']:"semester.php";
if (isset($_POST['btnsemester'])){
    $err = [];
    if(isset($_POST['name']) && !empty($_POST['name']) && trim($_POST['name'])){
        $name= $_POST['name'];
        $select="SELECT COUNT(*) AS count FROM semester_tb WHERE semester_name='$name';";
        $result=mysqli_query($connection, $select);
        if($result){
            $row = mysqli_fetch_assoc($result);
            $count = $row['count'];
            if($count>0)
            {
                $err['name']="semester already exists";
            }
        }
        $select="SELECT COUNT(semester_id) AS sem FROM semester_tb;";
        $res=mysqli_query($connection, $select);
        if($res){
            $row = mysqli_fetch_assoc($res);
            $sem = $row['count'];
            if($count>8)
            {
                $err['name']="semester";
            }
        }
    }else{
        $err['name']="Enter name of Course";
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
    if(count($err)==0){
        $insert= "INSERT INTO semester_tb(semester_name,rank) 
        VALUES ('$name','$rank')";
        if($connection->query($insert)){
            $_SESSION['success'] ='Semester added Successfully';
            header('Location:'.$loc);
            exit();
        }
        else{
            $msg ='Semester add failure';
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
    <title>Add Semester</title>
    <link rel="stylesheet" href="css/add.css">
</head>
<body>
    <?php include_once 'menu.php';?>
    <div class='container'>
    <div class='header'>
            <div class='header-left'>
                <div class='detail'>Add Semester</div>
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
                            <label for="name">Semester Name</label>
                            <input type="text" name='name' value="<?php echo $name;?>">
                            <span class='err-msg'><?php echo (isset($err['name']))?$err['name']:''; ?></span>
                        </div>
                        <div class='form-row'>
                            <label for="rank">Rank</label>
                            <input type="number" name="rank" value="<?php echo $rank;?>">
                            <span class='err-msg'><?php echo (isset($err['rank']))?$err['rank']:''; ?></span>
                        </div>
                        <div class='form-row'>
                            <input type="submit" name='btnsemester' value='Create'>
                            <input type="submit" name='cancel' value='Cancel'>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>