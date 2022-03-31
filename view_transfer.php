<?php

require_once('includes/load.php');
$tran_id=(int)$_GET['id'];


  // Checkin What level user has permission to view this page
page_require_level(4);
?>

<?php



$transfer = get_transfer_by_id($tran_id);
$com_name = find_value_by_sql(" select com_name from company  where com_id=".$transfer['com'])['com_name'];
$all_tran_details = find_by_sql('select short_code,products.name,unit_type,unit_name,quantity  from transfer_details 
  inner join products on transfer_details.product_id=products.id
  inner join units on units.id=products.unit_id
  where tran_id='.$tran_id.' order by short_code');


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>
    Transfer
  </title>
<!--     <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" /> -->



  <link rel="stylesheet" href="libs/css/bootstrap.min.css">


</head>
<body>  
  <div class="row">
    <div class="col-md-6">
      <?php echo display_msg($msg); ?>
    </div>
  </div>
  <div class="row">
    <div class="col-md-7">
      <div class="panel panel-default">

        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-tag"></span>
            <span>Transfer No: <?php echo $transfer['id'] ?></span>
          </strong>
        </div>

        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-map-marker"></span>
            <span>Company : <?php echo $com_name ?> </span>
          </strong>
        </div>

        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-user"></span>
            <span>Claimer : <?php echo $transfer['claimer'] ?> [<?php echo $transfer['designation'] ?>]</span>
          </strong>
        </div>

        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-calendar"></span>
            <span>Transfer Date:</span>
          </strong>
          <?php echo date ('F j, Y', strtotime($transfer['tran_date']));  ?>
        </div>

         <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-flag"></span>
            <span>Location from: <?php echo $transfer['locfrom'] ?></span>
          </strong>
        </div>

          <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-flag"></span>
            <span>Location to: <?php echo $transfer['locto'] ?></span>
          </strong>
        </div>




         <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-edit"></span>
            <span>Reason:</span>
          </strong>
     
           <?php echo $transfer['remarks'] ?>
        </div>




        <div class="panel-body">
          <div class="panel-heading">
            <strong>
              <span class="glyphicon glyphicon-list"></span>
              <span>Transfer Details</span>
            </strong>
          </div>
          <table class="table table-bordered table-striped table-hover ">
            <thead>
              <tr>
                <th class="text-center" style="width: 50px;">#</th>
                <th style="width: 100px;">Short Code</th>
                <th style="width: 400px;" >Products</th>
                <th >Transfer Qty.</th>
                <th style="width: 100px;">Unit</th>
               


              </tr>
            </thead>
            <tbody>
              <?php foreach ($all_tran_details as $trandetails):?>
                <tr>
                  <td class="text-center"><?php echo count_id();?></td>
                  <td><?php echo $trandetails['short_code']; ?></td>
                  <td><?php echo $trandetails['name']; ?></td>
                  <td><?php if($trandetails['unit_type']=='number') echo intval($trandetails['quantity']); else echo  $trandetails['quantity']; ?></td>
                  <td><?php echo $trandetails['unit_name']; ?></td>
                


                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>


        </div>
      </div>
    </div>
  </div>
</body>
</html>

