<?php




require_once('includes/load.php');
$req_id = (int)$_GET['id'];
$product_id = (int)$_GET['pid'];


// Checkin What level user has permission to view this page
//page_require_level(5);
?>

<?php




$all_requisition_details = find_by_sql('select requisition_details_track.*,products.name,unit_name,unit_type,short_code,users.name as tracker from requisition_details_track

inner join products on requisition_details_track.product_id=products.id
inner join units on units.id=products.unit_id
inner join users on users.id=requisition_details_track.track_by
where requisition_details_track.product_id=' . $product_id . ' and requisition_details_track.req_id=' . $req_id);


?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>
    Change History
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
    <div class="col-md-8">
      <div class="panel panel-default">


        <div class="panel-body">
          <div class="panel-heading">
            <strong>
              <span class="glyphicon glyphicon-list"></span>
              <span>Change History</span>
            </strong>
          </div>
          <table class="table table-bordered table-striped table-hover ">
            <thead>
              <tr>
                <th class="text-center" style="width: 50px;">#</th>
                <th style="width: 100px;">Short Code</th>
                <th>Products</th>
                <th style="width: 100px;">Quantity</th>
                <th style="width: 100px;">Unit</th>
                <th>Input by</th>
                <th>Input Date</th>


              </tr>
            </thead>
            <tbody>
              <?php foreach ($all_requisition_details as $reqdetails) : ?>
                <tr>
                  <td class="text-center"><?php echo count_id(); ?></td>
                  <td><?php echo $reqdetails['short_code']; ?></td>
                  <td><?php echo $reqdetails['name']; ?></td>
                  <td><?php if ($reqdetails['unit_type'] == 'number') echo intval($reqdetails['quantity']);
                      else echo  $reqdetails['quantity']; ?></td>
                  <td><?php echo $reqdetails['unit_name']; ?></td>
                  <td><?php echo $reqdetails['tracker']; ?></td>
                  <td><?php echo $reqdetails['track_date']; ?></td>



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