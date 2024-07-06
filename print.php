<?php
$loc=isset($_SESSION['url'])?$_SESSION['url']:'paidfee.php';
require_once 'private/convert.php';
if (!isset($_GET['id'])) {
    header('location:paidfee.php');
} else {
    $id = $_GET['id'];
    require_once 'private/autoload.php';
    $id=mysqli_real_escape_string($connection, $id);
    $student_id=$_SESSION['id'];
    $student_id=mysqli_real_escape_string($connection, $student_id);
    $sql = "SELECT *,p.status AS stat FROM pays AS p
    INNER JOIN feestructure f ON f.fee_id = p.fee_id
    INNER JOIN semester_tb sem ON sem.semester_id=f.semester_id
    INNER JOIN student_tb s ON s.student_id = p.student_id
    INNER JOIN batch_tb b ON b.batch_id=s.batch_id
    INNER JOIN course_tb c ON c.course_id=b.course_id
    WHERE invoice_no=$id AND p.student_id=$student_id";
    $retrieve_payment = $connection->query($sql);
    if ($retrieve_payment->num_rows > 0) {
        while ($row = $retrieve_payment->fetch_assoc()) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Print</title>
  <link rel="stylesheet" href="css/print.css"/>
</head>
<body>
    <div class="bill" id='printableArea'>
        <div class="bill-header">
            <h2>Patan Multiple Campus</h2>
            <h3>Address:Patandhoka</h3>
            <h4>Phone:01-929291929</h4>
        </div>
        <div class="bill-details">
            <?php
            $number=numberToWords($row['amount']);
            $billHTML=
            '<div class="student-details">
                <div>Name:
                    <span class="dotted-underline">'.$row['name'].'</span>
                </div>
                <div>
                    <span class="details-left">Course:
                        <span class="dotted-underline">'.$row['course_name'].'</span>
                    </span>
                    <span class="details-right">Semester:
                        <span class="dotted-underline">'.$row['semester_name'].'</span>
                    </span>
                </div>
                <div>
                    <span class="details-left">Batch:
                        <span class="dotted-underline">'.$row['batch_name'].'</span>
                    </span>
                    <span class="details-right">Roll_no:
                        <span class="dotted-underline">'.$row['roll_no'].'</span>
                    </span>
                </div>
                <div>
                    <span class="details-left">Invoice_no:
                        <span class="dotted-underline">'.$row['invoice_no'].'</span>
                    </span>
                    <span class="details-right">Date:
                        <span class="dotted-underline">'.$row['payment_date'].'</span>
                    </span>
                </div>
                <div>
                    <span class="details-left">Mobile:
                        <span class="dotted-underline">'.$row['phone_number'].'</span>
                    </span>
                    <span class="details-right">Email:
                        <span class="dotted-underline">'.$row['email'].'</span>
                    </span>
                </div>
            </div>
            <table class="bill-items">
            <tr>
            <th>Particulars</th>
            <th>Amount</th>
            </tr>';
            $data = json_decode($row['description'], true);
            $formattedData = ""; 
            foreach ($data as $entry) {
                $particulars = $entry['particulars'];
                $amount = number_format($entry['amount'], 2);             
                $billHTML.='<tr>
                <td>' . $particulars . '</td>
                <td>' . $amount . '</td>
                </tr>';
            } 
            $billHTML.='</tr>
            <tr>
                <th>Total</th>
                <td>'.$row['amount'].'</td>
            </tr>
            <tr>
            <td colspan=2>Amount in words:'.$number.' only </td>
            </tr>
            <tr>
            
            <td colspan=2>Payment Mode:'.$row['payment_mode'].'</td>
            </tr>
            ';
        $billHTML .= '</table>';
        
        echo $billHTML;
        }
    }
        ?>
    </div>
</div>
<button class="print-button" onclick="printDiv('printableArea')">Print</button>
<a href="<?php echo $loc ?>"><button>back</button></a>
<?php 
}
?>
    <script>
        function printDiv(divId) {
            var printContents = document.getElementById(divId).innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();

            document.body.innerHTML = originalContents;
            window.location.reload(); 
        }
    </script>
</body>
</html>

