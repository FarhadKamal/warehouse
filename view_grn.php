<?php

require_once('includes/load.php');
$grn_id = (int)$_GET['id'];


// Checkin What level user has permission to view this page
//page_require_level(4);
?>

<?php



$grn = get_grn_by_id($grn_id);
$com_name = find_value_by_sql(" select com_name from company  where com_id=".$grn['com'])['com_name'];
$all_grn_details = find_by_sql('select short_code,products.name,unit_type,unit_name,quantity,price  from grn_details 
  inner join products on grn_details.product_id=products.id
  inner join units on units.id=products.unit_id
  where grn_id=' . $grn_id . ' order by short_code');


?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>
    GRN
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
            <span>GRN No: <?php echo (int)$grn['id'] ?></span>
          </strong>
        </div>

        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-user"></span>
            <span>Claimer : <?php echo $grn['claimer'] ?> [<?php echo $grn['designation'] ?>]</span>
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
            <span class="glyphicon glyphicon-calendar"></span>
            <span>GRN Date:</span>
          </strong>
          <?php echo date('F j, Y', strtotime($grn['grn_date']));  ?>
        </div>

        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-flag"></span>
            <span>Store Location: <?php echo $grn['loc_name'] ?></span>
          </strong>
        </div>

        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-user"></span>
            <span>Supplier: <?php echo $grn['sup_name'] ?></span>
          </strong>
        </div>


        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-tag"></span>
            <span>Requisition No:


              <?php if ($grn['req_id'] == null) {
                echo "without requisition";
              } else { ?>
                <a href="view_requisition.php?id=<?php echo ($grn['req_id']); ?>" target="_blank"><?php echo ($grn['req_id']); ?></a>
              <?php } ?>
            </span>
          </strong>
        </div>


        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-calendar"></span>
            <span>Expected Date:</span>
          </strong>
          <?php echo date('F j, Y', strtotime($grn['expected_date']));  ?>
        </div>


        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-user"></span>
            <span>Contact Person: </span>
          </strong>
          <?php echo $grn['contact_person'] ?>
        </div>

        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-edit"></span>
            <span>Reference: </span>
          </strong>
          <?php echo $grn['req_ref'] ?>
        </div>


        <div class="panel-body">
          <div class="panel-heading">
            <strong>
              <span class="glyphicon glyphicon-list"></span>
              <span>GRN Details</span>
            </strong>
          </div>
          <table class="table table-bordered table-striped table-hover ">
            <thead>
              <tr>
                <th class="text-center" style="width: 50px;">#</th>
                <th style="width: 100px;">Short Code</th>
                <th style="width: 400px;">Products</th>
                <th>Rcv. Qty.</th>
                <th style="width: 100px;">Unit</th>
                <th style="width: 100px;">Total price</th>


              </tr>
            </thead>
            <tbody>
              <?php foreach ($all_grn_details as $grndetails) : ?>
                <tr>
                  <td class="text-center"><?php echo count_id(); ?></td>
                  <td><?php echo $grndetails['short_code']; ?></td>
                  <td><?php echo $grndetails['name']; ?></td>
                  <td><?php if ($grndetails['unit_type'] == 'number') echo intval($grndetails['quantity']);
                      else echo  $grndetails['quantity']; ?></td>
                  <td><?php echo $grndetails['unit_name']; ?></td>
                  <td><?php echo $grndetails['price']; ?></td>


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