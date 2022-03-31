<?php

require_once('includes/load.php');
$rtn_id=(int)$_GET['id'];


  // Checkin What level user has permission to view this page
page_require_level(4);
?>

<?php



$consume = get_consume_return_by_id($rtn_id);
$com_name = find_value_by_sql(" select com_name from company  where com_id=".$consume['com'])['com_name'];
$all_con_details = find_by_sql('select short_code,products.name,unit_type,unit_name,quantity  from consume_return_details 
  inner join products on consume_return_details.product_id=products.id
  inner join units on units.id=products.unit_id
  where rtn_id='.$rtn_id.' order by short_code');


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>
    Issue Return
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
            <span>Issue Return No: <?php echo $consume['id'] ?></span>
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
            <span>Claimer : <?php echo $consume['claimer'] ?> [<?php echo $consume['designation'] ?>]</span>
          </strong>
        </div>

        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-tag"></span>
            <span>Issue No: 
              <a href="view_consume.php?id=<?php echo ($consume['con_id']);?>" target="_blank"><?php echo ($consume['con_id']);?></a>

            </span>
          </strong>
        </div>

        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-calendar"></span>
            <span>Return Date:</span>
          </strong>
          <?php echo date ('F j, Y', strtotime($consume['rtn_date']));  ?>
        </div>

         <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-flag"></span>
            <span>Store Location: <?php echo $consume['loc_name'] ?></span>
          </strong>
        </div>


        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-user"></span>
            <span>Consumer : <?php echo $consume['consumer'] ?></span>
          </strong>
        </div>


         <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-edit"></span>
            <span>Reason:</span>
          </strong>
     
           <?php echo $consume['remarks'] ?>
        </div>




        <div class="panel-body">
          <div class="panel-heading">
            <strong>
              <span class="glyphicon glyphicon-list"></span>
              <span>Return Details</span>
            </strong>
          </div>
          <table class="table table-bordered table-striped table-hover ">
            <thead>
              <tr>
                <th class="text-center" style="width: 50px;">#</th>
                <th style="width: 100px;">Short Code</th>
                <th style="width: 400px;" >Products</th>
                <th >Return Qty.</th>
                <th style="width: 100px;">Unit</th>
               


              </tr>
            </thead>
            <tbody>
              <?php foreach ($all_con_details as $issdetails):?>
                <tr>
                  <td class="text-center"><?php echo count_id();?></td>
                  <td><?php echo $issdetails['short_code']; ?></td>
                  <td><?php echo $issdetails['name']; ?></td>
                  <td><?php if($issdetails['unit_type']=='number') echo intval($issdetails['quantity']); else echo  $issdetails['quantity']; ?></td>
                  <td><?php echo $issdetails['unit_name']; ?></td>
                


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

