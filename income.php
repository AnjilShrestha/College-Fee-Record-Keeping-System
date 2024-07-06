<style>
.main-content {
    margin-left: 250px;
    padding: 20px;
}

.section_content {
    border-radius: 8px;
    padding: 20px;
}

form {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

form input[type="date"],select {
    padding: 8px;
    border-radius: 4px;
    border: 1px solid #ccc;
    margin-right: 10px;
    max-width: 200px;
}

form input[type="submit"], form a {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    background-color: #28a745;
    color: #fff;
    text-decoration: none;
    cursor: pointer;
}
.Form {
    background-color: #fff;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
}
.search-row {
    display: block;
    margin-bottom: 10px;
}

label {
    display: inline-block;
    width: 100px;
    font-weight: bold;
}

.err-msg {
    color: red;
    font-size: 12px;
    margin-left: 5px;
}


@media (max-width: 600px) {
    select, input[type="date"], .submit-data {
        width: 100%;
    }
}

form input[type="submit"]:hover, form a:hover {
    background-color: #218838;
}

form a {
    margin-left: 10px;
    background-color: #dc3545;
}

form a:hover {
    background-color: #c82333;
}

.table {
    overflow-x: auto;
}

.table-earning {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}

.table-earning th, .table-earning td {
    padding: 12px 15px;
    border: 1px solid #ddd;
    text-align: left;
}

.table-earning th {
    background-color: #f2f2f2;
}

.table-earning tr:nth-child(even) {
    background-color: #f9f9f9;
}

.table-earning tr:hover {
    background-color: #f1f1f1;
}

.table-earning th, .table-earning td {
    text-align: center;
}

</style>
<?php
include_once 'menu.php';
require_once 'private/autoload.php';
$err=[];
$batch=$course=$from=$to='';
if (isset($_GET['submit'])) {
    if (isset($_GET['course']) && !empty($_GET['course'])) {
        $course = $_GET['course'];
    } else {
        $err['course'] = '*';
    }
    if (isset($_GET['batch']) && !empty($_GET['batch'])) {
        $batch = $_GET['batch'];
    } else {
        $err['batch'] = '*';
    }
    $from = $_GET['from'];
    if (empty($from)) {
        $err['from']='*';
    }
    $to = $_GET['to'] . ' 23:59:59';
    if (empty($to)) {
        $err['to']='*';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Income</title>
</head>
<body>
<div class="main-content">
    <div class="section_content">
        <div class="container-flow">
            <div class="row">
                <div>
                    <div class='Form'>
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
                            <?php require_once 'search.php';?>        
                            <span class='search-row'>
                                <label for="from">From:</label>
                                <input type="date" name="from" value="<?php echo substr($from, 0, 10); ?>" max="<?php echo $current_date; ?>" >
                                <span class='err-msg'><?php echo isset($err['from']) ? $err['from'] : ''; ?></span>
                            </span>
                            <span>
                                <label for="to">To:</label>
                                <input type="date" name="to" value="<?php echo substr($to, 0, 10); ?>" max="<?php echo $current_date; ?>" >
                                <span class='err-msg'><?php echo isset($err['to']) ? $err['to'] : ''; ?></span>
                            </span>
                            <input type="submit" name="submit" value="Submit" class="submit-data">
                            <a href="income.php">Reset</a>
                        </form>
                        <?php
                        if(count($err)==0){
                            $res = mysqli_query($connection, 
                            "SELECT pays.*, sf.*, s.*,b.*,sem.semester_name FROM pays
                            JOIN feestructure AS sf ON sf.fee_id = pays.fee_id
                            JOIN semester_tb sem ON sem.semester_id=sf.semester_id
                            JOIN student_tb s ON s.student_id = pays.student_id
                            JOIN batch_tb  b ON b.batch_id = sf.batch_id
                            JOIN course_tb c ON c.course_id=b.course_id
                            WHERE c.course_name='$course' AND b.batch_name='$batch' 
                            AND pays.payment_date BETWEEN '$from' AND '$to' ORDER BY pays.payment_date");
                        if (mysqli_num_rows($res) > 0) {
                            ?>
                            <div class="table">
                                <table class="table-earning">
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Semester</th>
                                        <th>Student Name</th>
                                        <th>Roll no</th>
                                        <th>Payment date</th>
                                        <th>Amount</th>
                                    </tr>
                                    <?php
                                    $final_amount = 0;
                                    $i=1;
                                    while ($row = mysqli_fetch_assoc($res)) {
                                        $final_amount += $row['amount'];
                                        ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td><?php echo $row['semester_name']; ?></td>
                                            <td><?php echo $row['name']; ?></td>
                                            <td><?php echo $row['roll_no']; ?></td>
                                            <td><?php echo $row['payment_date']; ?></td>
                                            <td><?php echo $row['amount']; ?></td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <th colspan="5">Total</th>
                                        <th><?php echo $final_amount; ?></th>
                                    </tr>
                                </table>
                            </div>
                        <?php } else {
                        }
                    }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>