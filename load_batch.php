<?php
require_once 'private/autoload.php';
$course = $_POST['course'];
$selectedBatch = isset($_GET['batch']) ? $_GET['batch'] : '';
$sql = "SELECT * FROM batch_tb b
INNER JOIN course_tb c ON c.course_id=b.course_id 
WHERE c.course_name='$course'";
$result = mysqli_query($connection, $sql);
$batchOptions = "<option value=''>Select Batch</option>";
if ($result->num_rows > 0) {
    while ($batch = $result->fetch_assoc()) {
        $batchOptions .= "<option value='" . $batch['batch_name'] . "' " . ($selectedBatch == $batch['batch_name'] ? 'selected' : '') . ">" . $batch['batch_name'] . "</option>";
    }
}
echo $batchOptions;
?>