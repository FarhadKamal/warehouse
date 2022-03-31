<?php

require_once('includes/load.php');
$req_id=(int)$_POST['requisition-id'];

redirect('view_requisition.php?id='.$req_id, false);
$assigned=$_POST['assigned-person'];




  // Checkin What level user has permission to view this page
page_require_level(7);
?>

<?php



$requisition = get_requisition_by_id($req_id);
$all_requisition_details = find_by_sql('select short_code,products.name,unit_type,unit_name,quantity ,requisition_details.id,products.id as pid,req_id from requisition_details 
  inner join products on requisition_details.product_id=products.id
  inner join units on units.id=products.unit_id
  where req_id='.$req_id.'  and assigned="'.$assigned.'"   order by id desc');

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
   <input 
            type="button" class="btn btn-sm" 
            onclick="tableToExcel('excel', 'Requisition', 'requisition.xls')" 
            value="Export to Excel"/>
  <div class="row" id="excel">
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
            <span>Claimer : <?php echo $requisition['claimer'] ?> [<?php echo $requisition['udes'] ?>]</span>
          </strong>
        </div>



         <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-calendar"></span>
            <span>Expected Date:</span>
          </strong>
          <?php echo date ('F j, Y', strtotime($requisition['expected_date']));  ?>
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
                <th >Products</th>
                <th style="width: 100px;">Quantity</th>
                <th style="width: 100px;">Unit</th>


              </tr>
            </thead>
            <tbody>
              <?php foreach ($all_requisition_details as $reqdetails):?>
                <tr>
                  <td class="text-center"><?php echo count_id();?></td>
                  <td><?php echo $reqdetails['short_code']; ?></td>
                  <td><?php echo $reqdetails['name']; ?></td>
                  <td>
                    <a href="view_requisition_changelog.php?id=<?php echo (int)$reqdetails['req_id'];?>&pid=<?php echo (int)$reqdetails['pid'];?>" target="_blank" >
                    <?php if($reqdetails['unit_type']=='number') echo intval($reqdetails['quantity']); else echo  $reqdetails['quantity']; ?></a></td>
                  <td><?php echo $reqdetails['unit_name']; ?></td>



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
                <th >Person</th>
                <th style="width: 100px;">Designation</th>
                <th style="width: 100px;">Action</th>
                <th>Remarks</th>

              </tr>
            </thead>
            <tbody>
              <?php 

              $sl=0;
              foreach ($history as $his): $sl++;  ?>
                <tr>
                  <td class="text-center"><?php echo $sl;?></td>
                  <td><?php echo $his['action_date']; ?></td>
                  <td><?php echo $his['person']; ?></td>
                  <td><?php echo $his['designation']; ?></td>
                  <td><?php echo $his['action_details']; ?></td>
                  <td><?php echo $his['action_remarks']; ?></td>
          



                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>


        </div>
      </div>
    </div>
  </div>
</body>
   <script type="text/javascript">
      function tableToExcel(table, name, filename) {
        let uri = 'data:application/vnd.ms-excel;base64,', 
        template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><title></title><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><meta http-equiv="content-type" content="text/plain; charset=UTF-8"/></head><body><table>{table}</table></body></html>', 
        base64 = function(s) { return window.btoa(decodeURIComponent(encodeURIComponent(s))) },         format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; })}

        if (!table.nodeType) table = document.getElementById(table)
          var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}

        var link = document.createElement('a');
        link.download = filename;
        link.href = uri + base64(format(template, ctx));
        link.click();
      }
    </script>
</html>

