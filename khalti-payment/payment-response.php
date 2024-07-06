<?php
session_start();
// Get the pidx from the URL
$pidx = $_GET['pidx'] ?? null;
$transaction_id=$_GET['transaction_id'] ?? null;

if ($pidx) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://a.khalti.com/api/v2/epayment/lookup/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode(['pidx' => $pidx]),
        CURLOPT_HTTPHEADER => array(
            'Authorization: key live_secret_key_68791341fdd94846a146f0457ff7b455',
            'Content-Type: application/json',
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    if ($response) {
        $responseArray = json_decode($response, true);
        switch ($responseArray['status']) {
            case 'Completed':
                $pay=$_SESSION['esewat'];
                update_pay($pay);
                $_SESSION['success']='Payment made successfully';
                header('Location: ../paidfee.php');
                exit();
                break;
            case 'Expired':
                $pay=$_SESSION['esewat'];
                delete_pay($pay);
                header('Location: ../studentfee.php');
                exit();
                break;
            case 'User canceled':
                $pay=$_SESSION['esewat'];
                delete_pay($pay);
                header('Location: ../studentfee.php');
                exit();
                break;
            default:
                $pay=$_SESSION['esewat'];
                delete_pay($pay);
                header('Location: ./studentfee.php');
                exit();
                break;
        }
    }
}
function update_pay($pay){
    require_once '../private/dbconfig.php';
    $sql = "UPDATE pays SET status='paid',payment_mode='Khalti' WHERE payment_id='$pay'";
    mysqli_query($connection, $sql);
    if(isset($_SESSION['esewat'])){
        unset($_SESSION['esewat']);
    }
}
function delete_pay($pay){
    require_once '../private/dbconfig.php';
    $delete="DELETE FROM pays WHERE payment_id='$pay'";
    mysqli_query($connection, $delete);
    if(isset($_SESSION['esewat'])){
        unset($_SESSION['esewat']);
    }
}