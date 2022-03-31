<?php
$page_title = 'Purchase History';
$results = '';
require_once('includes/load.php');
// Checkin What level user has permission to view this page
page_require_level(10);
?>
<?php
if (isset($_POST['submit'])) {
  $req_dates = array('start-date', 'end-date', 'product');
  validate_fields($req_dates);

  if (empty($errors)) {
    $start_date   = remove_junk($db->escape($_POST['start-date']));
    $end_date     = remove_junk($db->escape($_POST['end-date']));
    $location     = remove_junk($db->escape($_POST['location']));
    $product     = remove_junk($db->escape($_POST['product']));
    $supplier         = remove_junk($db->escape($_POST['supplier']));

    $com_id     = remove_junk($db->escape($_POST['company']));
   
    $com_name = find_value_by_sql(" select com_name from company  where com_id=".$com_id)['com_name'];
   




    if ($end_date  < $start_date) {
      $session->msg("d", "start date cannot be greater than to date!");
      redirect('purchase_report.php', false);
    }




    $results      = find_purchase_by_dates($start_date, $end_date, $location, $product, $supplier,$com_id );
  } else {
    $session->msg("d", $errors);
    redirect('purchase_report.php', false);
  }
} else {
  $session->msg("d", "Select dates");
  redirect('purchase_report.php', false);
}
?>
<!doctype html>
<html lang="en-US">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Purchase History Report</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" />
  <style>
    @media print {

      html,
      body {
        font-size: 9.5pt;
        margin: 0;
        padding: 0;
      }

      .page-break {
        page-break-before: always;
        width: auto;
        margin: auto;
      }
    }

    .page-break {
      width: 980px;
      margin: 0 auto;
    }

    .sale-head {
      margin: 40px 0;
      text-align: center;
    }

    .sale-head h1,
    .sale-head strong {
      padding: 10px 20px;
      display: block;
    }

    .sale-head h1 {
      margin: 0;
      border-bottom: 1px solid #212121;
    }

    .table>thead:first-child>tr:first-child>th {
      border-top: 1px solid #000;
    }

    table thead tr th {
      text-align: center;
      border: 1px solid #ededed;
    }

    table tbody tr td {
      vertical-align: middle;
    }

    .sale-head,
    table.table thead tr th,
    table tbody tr td,
    table tfoot tr td {
      border: 1px solid #212121;
      white-space: nowrap;
    }

    .sale-head h1,
    table thead tr th,
    table tfoot tr td {
      background-color: #f8f8f8;
    }

    tfoot {
      color: #000;
      text-transform: uppercase;
      font-weight: 500;
    }
  </style>
</head>

<body>
  <input type="button" class="btn btn-sm" onclick="tableToExcel('excel', 'Purchase History Report', 'PurchaseReport.xls')" value="Export to Excel" />
  <?php if ($results) : ?>
    <div class="page-break" id="excel">
      <div class="sale-head pull-center">
        <h1>Purchase History</h1>
        <strong><?php 
        
        echo $com_name."<br/>";
        if (isset($start_date)) {
                  echo $start_date;
                } ?> To <?php if (isset($end_date)) {
                                                                        echo $end_date;
                                                                      } ?> </strong>

      </div>
      <table class="table table-border">
        <thead>
          <tr>
            <th>#</th>

            <th>Date</th>


            <th>Supplier</th>
            <th>Location</th>

            <th>Short Code</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Unit</th>
          </tr>

        </thead>
        <tbody>


          <?php
          $total_qty = 0;
          $total_price = 0;
          foreach ($results as $result) :
            $total_qty = $result['quantity'] + $total_qty;
            $total_price = $result['price'] + $total_price;

          ?>
            <tr>
              <td class="text-right"><?php echo count_id(); ?></td>

              <td><?php echo ($result['grn_date']); ?></td>

              <td><?php echo ($result['sup_name']); ?></td>
              <td><?php echo ($result['loc_name']); ?></td>


              <td><?php echo ($result['short_code']); ?></td>
              <td><?php echo ($result['name']); ?></td>

              <?php if ($result['unit_type'] == 'number') { ?>
                <td><?php echo intval($result['quantity']); ?></td>
              <?php } else { ?>
                <td><?php echo sprintf('%0.2f', $result['quantity']) ?></td>
              <?php }  ?>
              <td align="right"><?php echo sprintf('%0.2f', $result['price']); ?></td>
              <td><?php echo ($result['unit_name']); ?></td>

            </tr>
          <?php


          endforeach; ?>
          <tr>
            <td colspan="6" align="right"><b>Total</b></td>
            <td><?php echo ($total_qty); ?></td>
            <td align="right"><?php echo sprintf('%0.2f', $total_price); ?></td>

          </tr>

        </tbody>

      </table>
    </div>
  <?php
  else :
    $session->msg("d", "Sorry no purchase has been found. ");
    redirect('purchase_report.php', false);
  endif;
  ?>
</body>
<script type="text/javascript">
  function tableToExcel(table, name, filename) {
    let uri = 'data:application/vnd.ms-excel;base64,',
      template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><title></title><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><meta http-equiv="content-type" content="text/plain; charset=UTF-8"/></head><body><table>{table}</table></body></html>',
      base64 = function(s) {
       
        return window.btoa(decodeURIComponent(unescape(encodeURIComponent(s))))
      },
      format = function(s, c) {
        return s.replace(/{(\w+)}/g, function(m, p) {
          return c[p];
        })
      }

    if (!table.nodeType) table = document.getElementById(table)
    var ctx = {
      worksheet: name || 'Worksheet',
      table: table.innerHTML
    }

    var link = document.createElement('a');
    link.download = filename;
    link.href = uri + base64(format(template, ctx));
    link.click();
  }
</script>

</html>
<?php if (isset($db)) {
  $db->db_disconnect();
} ?>