<!-- Connection and Sql queries -->

<?php
error_reporting(E_ALL);
require 'mssql.php';
require 'invoice.php';

$db = $_GET['d'];
$tsql = "SELECT *,(quantity * s_qnty) as totalQnty , CONVERT(VARCHAR, creationDate, 20) AS date_created, CONVERT(VARCHAR, lastUpdated, 20) AS updated_date , convertedFromValue*(0.1) as GST, total-(0.1*convertedFromValue) as subTotal From dbo.[ebay-cust] where orderId='$db'";

$stmt = sqlsrv_query($conn, $tsql);

if ($stmt == false) {
    mysqli_error($conn);
    //   echo("stmt failed");
}
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Order Tracking</title>
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>

<!-- Header -->

  <div style="background-color: #009CFF">
  <h3 style="color: #ffffff">Nexus World</h3>
  <p style="color: #ffffff">Tel: 1300 88 90 80</p>
  </div>


<!-- User Details -->



      <div  class="card half">
        <div class="card-body">
        <h5 class="card-title">Order Number: <?php echo $row[
            'orderId'
        ]; ?> </h5>
   
            <div class="card-list">
              <div class="card-left">
                <ul  style="list-style: none">
               <?php if ($row['trackingCourier'] == 'Sendle') {
                   echo '<a href=https://track.sendle.com/tracking?ref=' .
                       $row['trackingNumber'] .
                       '>Track my Parcel</a>';
               } elseif ($row['trackingCourier'] == 'Australia Post') {
                   echo '<a href=https://auspost.com.au/mypost/track/#/details/' .
                       $row['trackingNumber'] .
                       '>Track my Parcel</a>';
               } ?>
               <br>
        <?php if ($row['sellerId'] == 'nexdesign' or $row['sellerId'] == 'giftandcraftstore'){
        echo '<a href=https://www.ebay.com/itm/' .
            $row['lineItemId'] .
            '>Reorder</a>';} ?>
                  <li><b><?php echo $row['fullName']; ?></b> <?php echo $row[
    'username'
]; ?> </li>
                  <li> <?php echo $row['email']; ?></li>
                  <li><?php echo $row['phoneNumber']; ?></li>
                  <li><b>Date Created: </b><?php echo $row[
                      'date_created'
                  ]; ?></li>
                </ul>
              </div>
              
            </div class="card-list">
        </div class="card-body">
      </div class="card half">      
      
<!-- Postage Details -->

        <div class="card half">
          <div class="card-body">
            <h5 class="card-title">Postage Details</h5> 
            <div class="card-list">
              <div class="card-left">
                <ul style="list-style: none">
                  <li><b>Postage Service:</b> <b><?php echo $row[
                      'trackingCourier'
                  ]; ?></b></li>
                  <li><b>Tracking:</b> <?php if (
                      $row['trackingNumber'] == 'XX'
                  ) {
                      echo 'Not available';
                  } elseif ($row['trackingCourier'] == 'Sendle') {
                      echo '<a href=https://track.sendle.com/tracking?ref=' .
                          $row['trackingNumber'] .
                          '>' .
                          $row['trackingNumber'] .
                          '</a>';
                  } elseif ($row['trackingCourier'] == 'Australia Post') {
                      echo '<a href=https://auspost.com.au/mypost/track/#/details/' .
                          $row['trackingNumber'] .
                          '>' .
                          $row['trackingNumber'] .
                          '</a>';
                  } ?></li>
                  <li><b>Date Posted:</b> <?php echo $row[
                      'updated_date'
                  ]; ?></li>
                   <li><b><?php echo $row['fullName']; ?></b><li>
                  <li><?php echo $row['addressLine1']; ?></li>
                  <li><?php echo $row['city']; ?> 
                  <?php echo $row['stateOrProvince']; ?>, 
                 <?php echo $row['postalCode']; ?>
                  </li>
                  <br>
                  <form target="_blank" method = "POST" action = "invoice.php">
    <input type = "hidden" name = "order_id" value = "<?php echo $row[
        'orderId'
    ]; ?>" />
    <input type="submit" name="print_pdf" class="button" value="Download Invoice " />
  </form>
  </br>
                </ul>
                </div class="card-list"> 
        </div class="card-body">
      </div class="card half">
  
     <!-- Purchase And Item Details -->

   
 <h4 >Purchase Details</h4>
  <div style="background-color: #009CFF" class="wrapper">
      <div class="card">
        <div class="card-body">
          <p class="card-text">
          </p>
          <table class="table">
  <thead class="thead-dark">
    <tr>
      <th scope="col">#</th>
      <th scope="col">Item Details</th>
      <th scope="col">Quantity</th>
      <th scope="col">Total</th>
    </tr>
  </thead>
  <tbody >
    <tr>
      <th scope="row">1</th>
      <td><?php echo $row['title']; ?> (SKU: <?php if ($row['sku'] == 'xxx') {
     echo 'Not Available';
 } else {
     echo $row['sku'];
 } ?>)
 (Qnty: <?php echo $row['quantity']; ?> x <?php echo $row['s_qnty']; ?>
           pcs)
      </td>
      <td><?php echo $row['totalQnty']; ?></td>
      <td maxlength><?php echo round($row['total'], 2); ?></td>
    </tr>
  </tbody>
</table>
<div style="text-align: right; padding-right: 30px">
Subtotal: <?php echo round($row['subTotal'], 2); ?>
</div>
<div style="text-align: right; padding-right: 30px">
GST 10%: <?php echo round($row['GST'], 2); ?>
</div>
<div style="text-align: right; padding-right: 30px">
Total Amount: <?php echo round($row['total'], 2); ?>
</div>

<div>
<b><?php echo $row['buyerCheckoutNotes']; ?></b>
</div>

<?php if ($row['trackingCourier'] == 'Sendle') {
    echo '<a href=https://track.sendle.com/tracking?ref=' .
        $row['trackingNumber'] .
        '>Track my Parcel</a>';
} elseif ($row['trackingCourier'] == 'Australia Post') {
    echo '<a href=https://auspost.com.au/mypost/track/#/details/' .
        $row['trackingNumber'] .
        '>Track my Parcel</a>';
} ?>
 

      <br>
<?php if ($row['sellerId'] == 'nexdesign' or $row['sellerId'] == 'giftandcraftstore') {
    echo '<a href=https://www.ebay.com/itm/' .
        $row['lineItemId'] .
        '>Reorder</a>';
} ?>
        </div>
      </div>

    <!-- Live Chat -->

      <script type="text/javascript" id="zsiqchat">var $zoho=$zoho || {};$zoho.salesiq = $zoho.salesiq || {widgetcode: "3cc7aa57d3ae460fb8d0d29885bc7f1f9651a6282ef5cf3269d22606dd4629ca", values:{},ready:function(){}};var d=document;s=d.createElement("script");s.type="text/javascript";s.id="zsiqscript";s.defer=true;s.src="https://salesiq.zoho.com/widget";t=d.getElementsByTagName("script")[0];t.parentNode.insertBefore(s,t);</script>

  </body>
</html>


<!-- Download PDF -->

