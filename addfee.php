<?php
require_once 'private/autoload.php';
$name = $amount = $semester =$batch='';
$loc=isset($_SESSION['url'])?$_SESSION['url']:"fee.php";
if (isset($_POST['btnfee'])) {
    $err = [];
    if (isset($_POST['batch']) && !empty($_POST['batch']) && trim($_POST['batch'])) {
        $batch = $_POST['batch'];
    } else {
        $err['batch'] = "Select batch name";
    }
    if (isset($_POST['semester']) && !empty($_POST['semester']) && trim($_POST['semester'])) {
        $semester = $_POST['semester'];
        $sql = "SELECT COUNT(semester_id) AS count FROM feestructure WHERE semester_id = '$semester' 
        AND batch_id=$batch";
        $result = mysqli_query($connection, $sql);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $count = $row['count'];
            if ($count > 1) {
                $err['semester'] = 'semester fee already created';
            }
        }
    }else{
        $err['semester']='choose semester';
    }
    $data = [];
    if (isset($_POST['data'])) {
        $particulars = $_POST['data'];
        foreach ($particulars as $index => $entry) {
            if (empty(trim($entry['particulars'])) || empty(trim($entry['amount'])) || !is_numeric($entry['amount'])) {
                $err['particulars'] = 'Please enter valid particulars and amount.';
                break;
            }
            $data[] = ['particulars' => trim($entry['particulars']), 'amount' => floatval($entry['amount'])];
        }
    } else {
        $err['particulars'] = 'Please enter particulars and amount.';
    }

    if (isset($_POST['rank']) && !empty($_POST['rank']) && trim($_POST['rank'])) {
        $rank = $_POST['rank'];
        if ($rank < 0) {
            $err['rank'] = 'correct the rank';
        }
    } else {
        $err['rank'] = "Enter rank";
    }
    if (count($err) == 0) {
        $json_data = json_encode($data);
        $fee_amount = array_sum(array_column($data, 'amount'));
        $insert = "INSERT INTO feestructure(semester_id, batch_id,amount,rank,description) 
        VALUES ('$semester', $batch, $fee_amount,$rank,'$json_data')";
        $result = $connection->query($insert);
        if ($result>0) {
            $_SESSION['success'] .= 'Semester fee created successfully';
            header('location:'.$loc) ;
        } else {
            $_SESSION['failure']='Semester fee Creation Failure';
            header('location:'.$loc) ;
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
    <title>Semester</title>
    <link rel="stylesheet" href="css/add.css">
    <script>
        function addRow() {
            const container = document.getElementById('dataEntries');
            const index = container.children.length;
            const row = document.createElement('div');
            row.className = 'data-entry';

            row.innerHTML = `
                <label for="particulars">Particulars:</label>
                <input type="text" name="data[${index}][particulars]" placeholder="Particulars">
                <label for="amount">Amount:</label>
                <input type="number" name="data[${index}][amount]" placeholder="Amount">
                <button type="button" onclick="removeRow(this)">Remove</button>
            `;
            container.appendChild(row);
        }

        function removeRow(button) {
            button.parentNode.remove();
        }
    </script>
</head>
<body>
    <?php include_once 'menu.php';?>
    <div class='container'>
        <div class='header'>
            <div class='header-left'>
                <div class='detail'>Fee</div>
            </div>
        </div>
        <div class='form-body-1'>
            <div class='form-body-r'>
                <form action="<?php echo $_SERVER['PHP_SELF'];?>" method='post'>
                    <div class='form-row'>
                        <label for="batch">Batch</label>
                        <select name="batch" id="batch">
                            <option value="">Select Batch</option>
                            <?php
                            $select = "SELECT b.* FROM batch_tb b WHERE b.status = '1'";
                            $result = $connection->query($select);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    ?>
                                    <option value="<?php echo $row['batch_id']; ?>" <?php echo ($batch == $row['batch_id']) ? 'selected' : ''; ?>>
                                        <?php echo $row['batch_name']; ?> batch
                                    </option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                        <span class='err-msg'><?php echo (isset($err['batch']))?$err['batch']:''; ?></span>
                    </div>
                    <div class='form-row'>
                        <label for="semester">Semester</label>
                        <select name="semester" id="semester">
                            <option value="">Select semester</option>
                            <?php
                            $select = "SELECT * FROM semester_tb  ORDER BY rank";
                            $result = $connection->query($select);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    ?>
                                    <option value="<?php echo $row['semester_id']; ?>"
                                        <?php echo ($semester == $row['semester_id']) ? 'selected' : ''; ?>>
                                        <?php echo $row['semester_name']; ?> semester
                                    </option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                        <span class='err-msg'><?php echo (isset($err['semester']))?$err['semester']:''; ?></span>
                    </div>
                    <div id="dataEntries" class="form-row">
                        <div class="data-entry">
                            <label for="particulars">Particulars:</label>
                            <input type="text" name="data[0][particulars]" placeholder="Particulars">
                            <label for="amount">Amount:</label>
                            <input type="number" name="data[0][amount]" placeholder="Amount">
                            <button type="button" onclick="removeRow(this)">Remove</button>
                        </div>
                        <span class="err-msg"><?php echo isset($err['particulars']) ? $err['particulars'] : ''; ?></span>
                    </div>
                    <button type="button" onclick="addRow()">Add Row</button>
                    <div class='form-row'>
                        <label for="rank">Rank</label>
                        <input type="number" name='rank' value="<?php echo $rank;?>">
                        <span class='err-msg'><?php echo (isset($err['rank']))?$err['rank']:''; ?></span>
                    </div>
                    <div class='form-row'>
                        <input type="submit" name='btnfee' value='Save'>
                        <input type="submit" name='cancel' value='Cancel'>
                    </div>
                </form>
            </div>
        </div>
    </div>    
</body>
</html>
