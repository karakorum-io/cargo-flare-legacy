<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

	$servername = "localhost";
	$username = "dragon_scales";
	$password = "w_h,3H1cuwId";
	$dbname = "gecko_pro_dev";
	$accid="";
	$orid="";
	$due=0;
	$inv=0;
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 

	echo $sql = "SELECT CONCAT( app_entities.prefix, '-', app_entities.number ) AS 'Order ID', app_accounts.id AS 'Account ID', app_accounts.first_name AS 'First Name', app_accounts.last_name AS 'Last Name', app_accounts.company_name AS 'Company Name', app_order_header.dispatched AS 'Invoice Date', app_order_header.total_deposite AS 'Invoice Amount', app_payments.date_received AS 'Paid Date', app_payments.amount AS 'Amount Paid', (
`app_order_header`.`total_deposite` - `app_payments`.`amount` 
) AS 'Balance Due'
FROM app_entities, app_accounts, app_payments, app_order_header
WHERE app_accounts.id = app_entities.account_id
AND app_entities.id = app_payments.entity_id
AND app_order_header.entityid = app_payments.entity_id
AND app_entities.type =3
AND app_entities.parentid =1
AND app_entities.status
IN ( 6, 7, 8, 9 ) 
AND app_order_header.balance_paid_by
IN ( 2,3) 
AND app_order_header.dispatched
BETWEEN '2016-01-01'
AND '2016-09-30'
AND app_payments.deleted =0
AND app_payments.toid !=3
";
	
	$result = $conn->query($sql);
	$counter=1;
	if ($result->num_rows > 0) {		
		
		while($row = $result->fetch_assoc()) {
			
			if($row['Account ID'] == $accid && $row['Order ID']== $orid){
				$bal =	$due-$row['Amount Paid'];
				$inv = $due;
				
			} else{
				if($row["Balance Due"] != 0){
					$due = $row["Balance Due"];
					$orid = $row['Order ID'];
					$accid = $row['Account ID'];
				} 
				$bal =	$row["Invoice Amount"]-$row['Amount Paid'];
				$inv = $row["Invoice Amount"];
			}
			
						
			
			
			
			
			$qq="
			insert into customCOD (orderID,accountID,fname,lname,cname,invoicedate,invoiceAmount,PaidDate,AmountPaid,due) values
			(
				'".$row['Order ID']."',
				'".$row['Account ID']."',
				'".$row['First Name']."',
				'".$row['Last Name']."',
				'".$row['Company Name']."',
				'".$row['Invoice Date']."',
				'".$inv."',
				'".$row['Paid Date']."',
				'".$row['Amount Paid']."',				
				'".$bal."'
			)			
			";
			$conn->query($qq);
			
		}		
	} 
	$conn->close();
?>