<?php
$name='';
require_once 'private/autoload.php';
$loc = isset($_SESSION['url']) ? $_SESSION['url'] : "fee.php";
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT s.*, s.amount AS fee,sem.*,b.batch_name, c.course_name FROM feestructure AS s
            INNER JOIN batch_tb AS b ON s.batch_id = b.batch_id
            INNER JOIN semester_tb sem ON sem.semester_id=s.semester_id
            INNER JOIN course_tb AS c ON c.course_id = b.course_id
            WHERE fee_id = '$id'";
    $result = mysqli_query($connection, $sql);
    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        extract($data);
        $batch = $data['batch_id'];
        $details = json_decode($data['description'], true);
    }
} else {
    header('Location:' . $loc);
}
?>

<?php
if (isset($_POST['btnUpdate'])) {
    $err = [];
    
    if (isset($_POST['semester']) && !empty(trim($_POST['semester']))) {
        $semester = $_POST['semester'];
        $sql = "SELECT COUNT(*) AS count FROM feestructure WHERE 
        semester_id = '$semester' AND batch_id=$batch AND fee_id!='$id'";
        $result = mysqli_query($connection, $sql);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $count = $row['count'];
            if ($count > 1) {
                $err['semester'] = 'semester fee already';
            }
        }
    } else {
        $err['semester'] = 'Please enter the semester.';
    }

    $data = [];
    if (isset($_POST['data'])) {
        $particulars = $_POST['data'];
        foreach ($particulars as $index => $entry) {
            if (empty(trim($entry['particulars'])) || empty(trim($entry['amount'])) || !is_numeric($entry['amount'])) {
                $err['particulars'] = 'Please enter valid particulars and amount.';
            }
            $data[] = ['particulars' => trim($entry['particulars']), 'amount' => floatval($entry['amount'])];
        }
    } else {
        $err['particulars'] = 'Please enter particulars and amount.';
    }

    if (isset($_POST['rank']) && !empty(trim($_POST['rank'])) && is_numeric($_POST['rank'])) {
        $rank = intval($_POST['rank']);
        if ($rank < 0) {
            $err['rank'] = 'Please enter a valid rank.';
        }
    } else {
        $err['rank'] = 'Please enter the rank.';
    }
    if (count($err) == 0) {
        $json_data = json_encode($data);
        $fee_amount = array_sum(array_column($data, 'amount'));
        $update= "UPDATE  feestructure SET description='$json_data',amount='$fee_amount', 
        semester_id='$semester', rank='$rank' WHERE fee_id=$id";
        $connection->query($update);
        if($connection->affected_rows==1){
            $_SESSION['success']='Update Success';
            header('location:'.$loc);
            exit();
        } else {
            $_SESSION['failure']='Not Updated';
            header('location:'.$loc);
            exit();
        }
    }
        
}

if (isset($_POST['cancel'])) {
    header('Location:' . $loc);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Fee</title>
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
    <?php include_once 'menu.php'?>
    <div class="container">
        <div class="form-body-1">
            <div class="form-body-r">
                <form action="<?php echo $_SERVER['PHP_SELF'] ?>?id=<?php echo $id ?>" method="post" onsubmit="return validateForm()">
                    <div class="form-row">
                        <label for="batch">Batch</label>
                        <input type="text" name="batch" value="<?php echo $batch_name; ?>" readonly>
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
                                        <?php echo ($semester_id == $row['semester_id']) ? 'selected' : ''; ?>>
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
                        <?php foreach ($details as $index => $entry): ?>
                            <div class="data-entry">
                                <label for="particulars">Particulars:</label>
                                <input type="text" name="data[<?php echo $index; ?>][particulars]" value="<?php echo $entry['particulars']; ?>" placeholder="Particulars">
                                <label for="amount">Amount:</label>
                                <input type="number" name="data[<?php echo $index; ?>][amount]" value="<?php echo $entry['amount']; ?>" placeholder="Amount">
                                <button type="button" onclick="removeRow(this)">Remove</button>
                            </div>
                        <?php endforeach; ?>
                        <span class="err-msg"><?php echo isset($err['particulars']) ? $err['particulars'] : ''; ?></span>
                    </div>
                    <button type="button" onclick="addRow()">Add Row</button>
                    <div class="form-row">
                        <label for="rank">Rank</label>
                        <input type="number" name="rank" value="<?php echo htmlspecialchars($rank); ?>">
                        <span class="err-msg"><?php echo isset($err['rank']) ? $err['rank'] : ''; ?></span>
                    </div>
                    <div class="form-row">
                        <input type="submit" name="btnUpdate" value="Update">
                        <input type="submit" name="cancel" value="Cancel">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
