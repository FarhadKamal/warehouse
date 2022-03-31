<?php

require_once('includes/load.php');
$req_id = (int)$_GET['id'];




// Checkin What level user has permission to view this page
//page_require_level(5);
?>

<?php

$all_grn = find_by_sql('select id from grn where req_id=' . $req_id . " and submit_status=1");

$all_po = find_by_sql('select distinct po_id from po_items
inner join po p on p.id=po_items.po_id
where req_id='.$req_id.'  and submit_status=1 and approve_status=1');

$requisition = get_requisition_by_id($req_id);


$com_name = find_value_by_sql(" select com_name from company  where com_id=".$requisition['com'])['com_name'];
$com_id=$requisition['com'];


$all_requisition_details = find_by_sql('select frd.name as frdperson,short_code,products.name,unit_type,unit_name,quantity ,requisition_details.id,products.id as pid,req_id,
ifnull((select sum(stock_qty)   from stock  where stock.com='.$com_id.' and stock.product_id=requisition_details.product_id),0)as curstock,

ifnull((select stock_price/stock_qty from stock where stock.product_id=requisition_details.product_id and ref_source="GRN" order by stock_date desc limit 1),0)as lpp,
(select stock_date from stock inner join grn on grn.id=stock.ref_no where stock.product_id=requisition_details.product_id and ref_source="GRN" and  grn.req_id<>requisition_details.req_id order by stock_date desc limit 1)as lppd,
(SELECT ifnull(round( sum(stock_qty*-1)/count(distinct year(stock_date),month(stock_date)  )     ,2),0)
FROM stock where  stock.product_id=requisition_details.product_id and stock.com='.$com_id.' and ref_source="Issue" and stock_type="issue") as amc

 from requisition_details 
  inner join products on requisition_details.product_id=products.id
  inner join units on units.id=products.unit_id
  left join users frd on frd.id=requisition_details.forward_to
  where req_id=' . $req_id . ' order by id desc');

$history = requisition_history_by_id($req_id);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>
    Requisition
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




        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-tag"></span>
            <span>Requisition No: <?php echo (int)$requisition['id'] ?></span>
          </strong>
        </div>

        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-user"></span>
            <span>Claimer : <?php echo $requisition['claimer'] ?>
              [<?php echo $requisition['udes'] ?>]</span>
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
            <span>Expected Date:</span>
          </strong>
          <?php echo date('F j, Y', strtotime($requisition['expected_date']));  ?>
        </div>


        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-user"></span>
            <span>Contact Person: </span>
          </strong>
          <?php echo $requisition['contact_person'] ?>
        </div>

        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-edit"></span>
            <span>Reason: </span>
          </strong>
          <?php echo $requisition['request_reason'] ?>
        </div>

        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-tag"></span>
            <span>GRN: </span>
          </strong>

          <?php foreach ($all_grn as $grn) : ?>
          <a href="view_grn.php?id=<?php echo($grn['id']); ?>"
            target="_blank"><b><?php echo($grn['id']); ?></b></a>&nbsp;&nbsp;
          <?php endforeach; ?>
        </div>


        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-tag"></span>
            <span>PO: </span>
          </strong>

          <?php foreach ($all_po as $po) : ?>

          <a href="view_po.php?id=<?php echo($po['po_id']); ?>"
            target="_blank"><b><?php echo($po['po_id']); ?></b></a>&nbsp;&nbsp;
          <?php endforeach; ?>
        </div>


        <div class="panel-body">
          <div class="panel-heading">
            <strong>
              <span class="glyphicon glyphicon-list"></span>
              <span>Requisition Details</span>
            </strong>
          </div>
          <table class="table table-bordered table-striped table-hover ">
            <thead>
              <tr>
                <th class="text-center" style="width: 50px;">#</th>
                <th style="width: 100px;">Short Code</th>
                <th>Products</th>
                <th>Req. Quantity</th>
                <th>Unit</th>
                <th>Current Stock</th>
                <th>Average Monthly Issue</th>
                <th>Last Purchased Price</th>
                <th>Last GRN Date</th>
                <th>Forwarded</th>

              </tr>
            </thead>
            <tbody>
              <?php foreach ($all_requisition_details as $reqdetails) : ?>
              <tr>
                <td class="text-center"><?php echo count_id(); ?>
                </td>
                <td><?php echo $reqdetails['short_code']; ?>
                </td>
                <td><?php echo $reqdetails['name']; ?>
                </td>
                <td>
                  <a href="view_requisition_changelog.php?id=<?php echo (int)$reqdetails['req_id']; ?>&pid=<?php echo (int)$reqdetails['pid']; ?>"
                    target="_blank">
                    <?php if ($reqdetails['unit_type'] == 'number') {
    echo intval($reqdetails['quantity']);
} else {
    echo  $reqdetails['quantity'];
} ?>
                  </a>
                </td>


                <td><?php echo $reqdetails['unit_name']; ?>
                </td>
                <td>
                  <?php if ($reqdetails['unit_type'] == 'number') {
    echo intval($reqdetails['curstock']);
} else {
    echo  $reqdetails['curstock'];
} ?></a>
                </td>
                </td>
                <td>
                  <?php if ($reqdetails['unit_type'] == 'number') {
    echo intval($reqdetails['amc']);
} else {
    echo  $reqdetails['amc'];
} ?></a>
                </td>
                </td>
                <td><?php echo sprintf('%0.2f', $reqdetails['lpp']); ?>
                </td>

                <td><?php echo $reqdetails['lppd']; ?>
                </td>
                <td><?php echo $reqdetails['frdperson']; ?>
                </td>




              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <div class="panel-heading">
            <strong>
              <span class="glyphicon glyphicon-pushpin"></span>
              <span>Flow History</span>
            </strong>
          </div>


          <table class="table table-bordered table-striped table-hover ">
            <thead>
              <tr>
                <th class="text-center" style="width: 50px;">#</th>
                <th style="width: 100px;">Date</th>
                <th>Person</th>
                <th style="width: 100px;">Designation</th>
                <th style="width: 100px;">Action</th>
                <th>Remarks</th>

              </tr>
            </thead>
            <tbody>
              <?php

              $sl = 0;
              foreach ($history as $his) : $sl++;  ?>
              <tr>
                <td class="text-center"><?php echo $sl; ?>
                </td>
                <td><?php echo $his['action_date']; ?>
                </td>
                <td><?php echo $his['person']; ?>
                </td>
                <td><?php echo $his['designation']; ?>
                </td>
                <td><?php echo $his['action_details']; ?>
                </td>
                <td><?php echo $his['action_remarks']; ?>
                </td>




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