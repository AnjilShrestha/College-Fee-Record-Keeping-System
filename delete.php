<?php
if(isset($_POST['Delete'])){
    require_once 'private/autoload.php';
    if(isset($_POST['admin'])){
        $id = $_POST['admin'];
        $loc=isset($_SESSION['url'])?$_SESSION['url']:"admin.php";
        $admin=$_SESSION['id'];
        $check="SELECT * FROM admin_tb WHERE admin_id='$admin'";
        $res=$connection->query($check);
        if($res->num_rows>0){
            $adm=$res->fetch_assoc();
            $ad=$adm['admin_id'];
            if($ad===$id){
                $_SESSION['failure']="You cannot delete your own account";
                header('location:'.$loc);
                exit();
            }else{
                $sql = "DELETE FROM admin_tb where admin_id='$id'";
                $connection->query($sql);
                if($connection->affected_rows==1){
                    $_SESSION['success']="Admin deleted successfully";
                    header('location:'.$loc);
                    exit();
                } else {
                    $_SESSION['failure']= 'Admin Delete failed';
                    header('location:'.$loc);
                    exit();
                }
            }
        }
    }
    if(isset($_POST['batch'])){
        $loc=isset($_SESSION['url'])?$_SESSION['url']:"batch.php";
        $id = $_POST['batch'];
        $sql = "delete from batch_tb where batch_id=$id";
        $connection->query($sql);
        if($connection->affected_rows==1){
            $_SESSION['success']= 'Batch deleted successfully';
            header('location:'.$loc);
            exit();
        } else {
            $_SESSION['failure']= 'Failed to delete batch';
            header('location:'.$loc);
            exit();
        }
    }
    if(isset($_POST['course'])){
        $id = $_POST['course'];
        $loc=isset($_SESSION['url'])?$_SESSION['url']:"course.php";
        $sql = "DELETE from course_tb where course_id=$id";
        $connection->query($sql);
        if($connection->affected_rows==1){
            $_SESSION['success']='Course Deleted';
            header('location:'.$loc);
        } else {
            $_SESSION['failure']= 'Failed to delete course';
            header('location:'.$loc);
        }
    }
    if(isset($_POST['student'])){
        $id = $_POST['student'];
        $loc=isset($_SESSION['url'])?$_SESSION['url']:"student.php";
        $image = "SELECT image FROM student_tb WHERE student_id = '$id'";
        $result = mysqli_query($connection, $image);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            if (!empty($row['image']) && file_exists("./upload/" . $row['image'])) {
                unlink("./upload/" . $row['image']);
            }
        }
        $sql = "DELETE FROM student_tb WHERE student_id=$id";
        $connection->query($sql);
        if($connection->affected_rows==1){
            $_SESSION['success']= 'Student delete success';
            header('location:'.$loc);
        } else {
            $_SESSION['failure']= 'Failed to delete student';
            header('location:'.$loc);
        }
    }
    if(isset($_POST['fee'])){
        $id = $_POST['fee'];
        $loc=isset($_SESSION['url'])?$_SESSION['url']:"fee.php";
        $sql = "DELETE from feestructure where fee_id=$id";
        $connection->query($sql);
        if($connection->affected_rows==1){
            $_SESSION['success']='fee delete success';
            header('location:'.$loc);
        } else {
            $_SESSION['failure']='Failed to delete';
            header('location:'.$loc);
        }
    }
    if(isset($_POST['payment'])){
        $id = $_POST['payment'];
        $loc=isset($_SESSION['url'])?$_SESSION['url']:"payment.php";
        $sql = "DELETE from pays where payment_id=$id";
        $connection->query($sql);
        if($connection->affected_rows==1){
            $_SESSION['success']='Payment delete success';
            header('location:'.$loc);
        } else {
            $_SESSION['failure']= 'Failed to delete';
            header('location:'.$loc);
        }
    }
    if(isset($_POST['semester'])){
        $id = $_POST['semester'];
        $loc=isset($_SESSION['url'])?$_SESSION['url']:"semester.php";
        $sql = "DELETE from semester_tb where semester_id=$id";
        $connection->query($sql);
        if($connection->affected_rows==1){
            $_SESSION['success']='Semester delete success';
            header('location:'.$loc);
        } else {
            $_SESSION['failure']= 'Failed to delete';
            header('location:'.$loc);
        }
    }
}else{
    echo 'Not available';
}