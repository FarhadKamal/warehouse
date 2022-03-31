<?php
$page_title = 'Location wise Cost Allocation';

require_once('includes/load.php');
  // Checkin What level user has permission to view this page
page_require_level(4);


  if(isset($_POST['submit'])){
    $req_dates = array('start-date','end-date');
    validate_fields($req_dates);

    if(empty($errors)){
      $start_date   = remove_junk($db->escape($_POST['start-date']));
      $end_date     = remove_junk($db->escape($_POST['end-date']));
    


      if($end_date  < $start_date)
      {
        $session->msg("d", "start date cannot be greater than to date!");
        redirect('stock_report.php', false);
      }



     
    }
    else{
      $session->msg("d", $errors);
      redirect('cost_locwise.php', false);
    }

  } else {
    $session->msg("d", "Select dates");
    redirect('cost_locwise.php', false);
  }



$locsql = " select loc_id,loc_name from stock 
inner join locations on locations.id=stock.loc_id
group by loc_id order by loc_id ";
$resloc = find_by_sql($locsql);

$sql = "select  cost_id,cost_name ";
$i=1;
foreach($resloc as $rloc): 
  $sql .=", sum(if(stock.loc_id=".$rloc['loc_id'].",stock_price,0)) as tot".$i." ";
  $i++;
endforeach;
$sql .=" from stock  ";
$sql .=" inner join consume on consume.id=stock.ref_no ";
$sql .=" inner join cost_centre on cost_centre.id=consume.cost_id ";

$sql .=" where ref_source='Issue' and date(stock_date)>='$start_date' and date(stock_date)<='$end_date' ";
$sql .=" group by cost_id order by cost_name ";

$results = find_by_sql($sql);


?>
<!doctype html>
<html lang="en-US">
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
 <title>Location wise Cost Allocation</title>
 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"/>
 <style>
   @media print {
     html,body{
      font-size: 9.5pt;
      margin: 0;
      padding: 0;
      }.page-break {
       page-break-before:always;
       width: auto;
       margin: auto;
     }
   }
   .page-break{
    width: 980px;
    margin: 0 auto;
  }
  .sale-head{
   margin: 40px 0;
   text-align: center;
   }.sale-head h1,.sale-head strong{
     padding: 10px 20px;
     display: block;
     }.sale-head h1{
       margin: 0;
       border-bottom: 1px solid #212121;
       }.table>thead:first-child>tr:first-child>th{
         border-top: 1px solid #000;
       }
       table thead tr th {
         text-align: center;
         border: 1px solid #ededed;
         }table tbody tr td{
           vertical-align: middle;
           }.sale-head,table.table thead tr th,table tbody tr td,table tfoot tr td{
             border: 1px solid #212121;
             white-space: nowrap;
             }.sale-head h1,table thead tr th,table tfoot tr td{
               background-color: #f8f8f8;
               }tfoot{
                 color:#000;
                 text-transform: uppercase;
                 font-weight: 500;
               }
             </style>
           </head>
           <body>
            <?php if($results): ?>
              <input 
              type="button" class="btn btn-sm" 
              onclick="tableToExcel('excel', 'Location wise Cost Allocation', 'CostAllocation.xls')" 
              value="Export to Excel"/>
              <div class="page-break" id="excel">
               <div class="sale-head pull-center">
                 <h1>Location wise Cost Allocation</h1>    
                  <strong><?php if(isset($start_date)){ echo $start_date;}?> To <?php if(isset($end_date)){echo $end_date;}?> </strong>
               </div>
               <table class="table table-border">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Cost Id</th>
                    <th>Cost Centre Name</th>
                    <?php  foreach($resloc as $rloc):  ?>
                      <th><?php  echo $rloc['loc_name']; ?></th>
                    <?php  endforeach; ?>
                    <th>Total</th>

                  </tr>

                </thead>
                <tbody>
                  <?php 

                  foreach($results as $result): 

                      $total=0;
                    ?>
                    <tr>
                      <td class="text-right"><?php echo count_id() ;?></td>
                      <td class="text-right"><?php echo remove_junk($result['cost_id']);?></td>
                      <td class="desc">
                        <h6><?php echo remove_junk(ucfirst($result['cost_name']));?></h6>
                      </td>

                      <?php $i=1; foreach($resloc as $rloc):  

                        $total=$result['tot'.$i]+$total;
                      ?>

                        
                        <td class="text-right"><?php echo ($result['tot'.$i]*-1);?></td>

                      <?php $i++; endforeach; ?>

                      <td class="text-right"><?php echo sprintf('%0.2f',$total*-1);?></td>
                     

                    </tr>
                  <?php endforeach; ?>
                </tbody>


              </table>
            </div>
            <?php
          else:
            $session->msg("d", "Sorry no stock has been found. ");
            redirect('stock_report.php', false);
          endif;
          ?>
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
        <?php if(isset($db)) { $db->db_disconnect(); } ?>
