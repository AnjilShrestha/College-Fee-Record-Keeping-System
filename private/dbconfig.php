<?php
@session_start();
try{
    $servername='localhost';
    $username='root';
    $password='';
    $database='cfm1';
    $connection = mysqli_connect($servername,$username,$password,$database);
}catch(Exception $ex){
    die('Database Error: ' . $ex->getMessage());
}
?>