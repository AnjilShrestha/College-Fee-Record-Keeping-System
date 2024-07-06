<?php
function getadmin(){
    if(isset($_SESSION['user_type']) && $_SESSION['user_type']==='admin')
    {
    }else{
        header('location:access.php');
    }
}
function getuser(){
    @session_start();
    if(isset($_SESSION['user_type']) && $_SESSION['user_type']==='student')
    {

    }else{
        header('location:access.php');
    }
}
function getmessage(){
    require_once './private/call.php';
}