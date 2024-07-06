<?php
session_start();
include_once '../private/dbconfig.php';
if (isset($_POST['submit'])) {
    $total=$_POST['Amount'];
    // converting the amount to paisa as the amount should be in paisa for khalti
    $amount = $total*100; 
    $purchase_order_id = $_POST['PurchasedOrderId'];
    $purchase_order_name = $_POST['PurchasedOrderName'];
    $name = $_POST['Name'];
    $email = $_POST['Email'];
    $phone = $_POST['Phone'];
    //data validation
    if(empty($amount) || empty($purchase_order_id) || empty($purchase_order_name) || empty($name) || empty($email) || empty($phone)){
        $_SESSION['failure']='Please Try again later';
        header("Location: ../studentfee.php");
        exit();
    }else{
        
    }

    $postFields = array(
        "return_url" => "http://127.0.0.1/cfm/khalti-payment/payment-response.php",
        "website_url" => "http://127.0.0.1/cfm/khalti-payment/",
        "amount" => $amount,
        "purchase_order_id" => $purchase_order_id,
        "purchase_order_name" => $purchase_order_name,
        "customer_info" => array(
            "name" => $name,
            "email" => $email,
            "phone" => $phone
        )
    );
}
$jsonData = json_encode($postFields);

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://a.khalti.com/api/v2/epayment/initiate/',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => $jsonData,
    CURLOPT_HTTPHEADER => array(
        'Authorization: key live_secret_key_68791341fdd94846a146f0457ff7b455',
        'Content-Type: application/json',
    ),
));

$response = curl_exec($curl);


if (curl_errno($curl)) {
    echo 'Error:' . curl_error($curl);
} else {
    $responseArray = json_decode($response, true);

    if (isset($responseArray['error'])) {
        echo 'Error: ' . $responseArray['error'];
    } elseif (isset($responseArray['payment_url'])) {
        // Redirect the user to the payment page
        header('Location: ' . $responseArray['payment_url']);
    } else {
        echo 'Unexpected response: ' . $response;
    }
}
curl_close($curl);