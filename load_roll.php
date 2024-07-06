<?php
$batch_id = $_POST['batch'];
require_once 'private/autoload.php';
$sql = "SELECT MAX(roll_no) AS last_value FROM student_tb WHERE batch_id='$batch_id'";
$result = mysqli_query($connection, $sql);

if ($result) {
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $lastValue = $row['last_value'];
        if ($lastValue !== null) {
            ++$lastValue;
        }else{
            $batch = "SELECT CAST(SUBSTRING(batch_name, REGEXP_INSTR(batch_name, '[0-9]+')) AS UNSIGNED) 
            AS extracted_integer
            FROM batch_tb WHERE batch_id='$batch_id';";
            $res = mysqli_query($connection, $batch);
            if($res){
                if(mysqli_num_rows($res) > 0) {
                    $bat= mysqli_fetch_assoc($res);
                    $l= $bat['extracted_integer'];
                    $roll=$l*1000 +1;
                    $lastValue=$roll;
                }   
            }
        }
    }
    $roll = $lastValue;
    echo $roll;
}

?>
