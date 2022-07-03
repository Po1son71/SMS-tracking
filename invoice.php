<?php
error_reporting(E_ALL);
require 'mssql.php';

function usePost($json_array)
{
    $headers = [
        'X-API-KEY:u_nex_world_cd355028-df9e-4ddb-b6fc-530ecabcd50a',
        'Content-Type:application/json',
    ];
    $url = 'https://api.exportsdk.com/v1/pdf';
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_array);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    $file = curl_exec($ch);
    curl_close($ch);

    header('Content-Type: ' . curl_getinfo($ch)['content_type']);
    var_dump($file);
}
// Function for curl
function printPDF($row)
{
    $payload = [];
    $payload['templateId'] = '7f9fc52f-5bc6-4274-9361-18ffd1a67f7c';
    // $data = [];
    $data['Description'] = $row['title'];
    $data['Quantity'] = $row['totalQnty'];
    // $data['companyName'] = $row['username'];
    $data['companyAddress1'] = $row['addressLine1'];
    $data['city'] = $row['city'];
    $data['state'] = $row['stateOrProvince'];
    $data['postalCode'] = $row['postalCode'];
    $data['customerEmail'] = $row['email'];
    $data['customerPhoneNumber'] = $row['phoneNumber'];
    $data['total'] = round($row['total'], 2);
    $data['subTotal'] = round($row['subTotal'], 2);
    $data['GST'] = round($row['GST'], 2);
    $data['fullname'] = $row['fullName'];
    $data['sku'] = $row['sku'];
    $data['quantity'] = $row['quantity'];
    $data['s_qnty'] = $row['s_qnty'];
    $data['buyerNote'] = $row['buyerCheckoutNotes'];
    // $data['phoneNumber'] = '1300 88 90 80 / +61 02 9063 1744 ';
    // $data['total'] = $row['total'];
    // $data['userName'] = $row['username'];

    $payload['data'] = $data;

    $payloadJSON = json_encode($payload);

    usePost($payloadJSON);
}

// For db connection
if (isset($_POST['print_pdf'])) {
    // $db = $_GET['d'];
    $db = $_POST['order_id'];
    $tsql = "SELECT *,(quantity * s_qnty) as totalQnty , CONVERT(VARCHAR, creationDate, 20) AS date_created, CONVERT(VARCHAR, lastUpdated, 20) AS updated_date , convertedFromValue*(0.1) as GST, total-(0.1*convertedFromValue) as subTotal From dbo.[ebay-cust] where orderId='$db'";

    $stmt = sqlsrv_query($conn, $tsql);

    if ($stmt == false) {
        mysqli_error($conn);
        //   echo("stmt failed");
    }
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    printPDF($row);
}
?>

