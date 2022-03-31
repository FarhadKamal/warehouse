<?php

require_once('includes/load.php');
$adj_id=(int)$_GET['id'];


  // Checkin What level user has permission to view this page
page_require_level(4);
?>

<?php



$adjust = get_adjust_by_id($adj_id);
$com_name = find_value_by_sql(" select com_name from company  where com_id=".$adjust['com'])['com_name'];
$all_con_details = find_by_sql('select adjust_details.*,products.id as pid, products.name,sum(stock_qty) as stock_qty,
  unit_name,unit_type,short_code from adjust_details 
  inner join adjust on adjust_details.adj_id=adjust.id
  inner join products on products.id=adjust_details.product_id 
  inner join units on units.id=products.unit_id 

  inner join stock on (stock.product_id=adjust_details.product_id and stock_date<"'.$adjust['adj_date'].'"
   and stock.loc_id="'.$adjust['loc_id'].'")


  where adjust.id="'.$adj_id.'" 
  group by products.id
  order by short_code');


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>
    Adjustment
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
            <span>Adjustment No: <?php echo $adjust['id'] ?></span>
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
            <span>Claimer : <?php echo $adjust['claimer'] ?> [<?php echo $adjust['designation'] ?>]</span>
          </strong>
        </div>

    

        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-calendar"></span>
            <span>Stock Record Date:</span>
          </strong>
          <?php echo date ('F j, Y H:i:s a', strtotime($adjust['adj_date']));  ?>
        </div>

         <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-flag"></span>
            <span>Store Location: <?php echo $adjust['loc_name'] ?></span>
          </strong>
        </div>


     


         <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-edit"></span>
            <span>Reason:</span>
          </strong>
     
           <?php echo $adjust['remarks'] ?>
        </div>




        <div class="panel-body">
          <div class="panel-heading">
            <strong>
              <span class="glyphicon glyphicon-list"></span>
              <span>Adjustment Details</span>
            </strong>
          </div>
          <table class="table table-bordered table-striped table-hover ">
            <thead>
              <tr>
                <th class="text-center" style="width: 50px;">#</th>
                <th style="width: 100px;">Short Code</th>
                <th style="width: 400px;" >Products</th>
                <th>Stock Qty</th>
                <th>Physical Qty</th>
                <th style="width: 100px;">Unit</th>
               


              </tr>
            </thead>
            <tbody>
              <?php foreach ($all_con_details as $issdetails):?>
                <tr>
                  <td class="text-center"><?php echo count_id();?></td>
                  <td><?php echo $issdetails['short_code']; ?></td>
                  <td><?php echo $issdetails['name']; ?></td>
                  <td><?php if($issdetails['unit_type']=='number') echo intval($issdetails['stock_qty']); else echo  $issdetails['stock_qty']; ?></td>
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

