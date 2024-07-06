<?php
require_once '../private/dbconfig.php';
if(isset($_SESSION['esewat'])){
    $id = $_SESSION['esewat'];
    $sql = "DELETE FROM pays where payment_id='$id'";
    if(mysqli_query($connection,$sql)){
        unset($_SESSION['esewat']);
    }
    header('Location:../studentfee.php');
    exit();
}
?>