<?php
require_once '../private/dbconfig.php';
if( isset($_REQUEST['oid']) && isset( $_REQUEST['amt']) && isset( $_REQUEST['refId']))
{
	$sql = "SELECT * FROM pays WHERE invoice_no = '".$_REQUEST['oid']."'"	;
	$result = mysqli_query( $connection, $sql);
	if(  $result )
	{	
		if( mysqli_num_rows($result) == 1)
		{
			$payment = mysqli_fetch_assoc( $result);
			$url = "https://uat.esewa.com.np/epay/transrec";
		
			$data =[
			'amt'=> $payment['amount'],
			'rid'=>  $_REQUEST['refId'],
			'pid'=>  $payment['invoice_no'],
			'scd'=> 'epay_payment'
			];

			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($curl);
			$response_code = get_xml_node_value('response_code',$response  );

			if ( trim($response_code)  == 'Success')
			{
				$sql = "UPDATE pays SET status='paid',payment_mode='esewa' WHERE payment_id='".$payment['payment_id']."'";
				mysqli_query($connection, $sql);
				if(isset($_SESSION['esewat'])){
					unset($_SESSION['esewat']);
				}
				$_SESSION['success']='Payment successfull';
				header('Location: ../paidfee.php');
				exit();
			}
	
	
		}
	}
}
function get_xml_node_value($node, $xml) {
    if ($xml == false) {
        return false;
    }
    $found = preg_match('#<'.$node.'(?:\s+[^>]+)?>(.*?)'.
            '</'.$node.'>#s', $xml, $matches);
    if ($found != false) {
            return $matches[1]; 
         
    }	  
   return false;
}