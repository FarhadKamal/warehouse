<?php
$page_title = 'Stock';
$results = '';
require_once('includes/load.php');
// Checkin What level user has permission to view this page
page_require_level(10);
?>
<?php
if (isset($_POST['submit'])) {
  $req_dates = array('start-date', 'end-date');
  validate_fields($req_dates);

  if (empty($errors)) {
    $start_date   = remove_junk($db->escape($_POST['start-date']));
    $end_date     = remove_junk($db->escape($_POST['end-date']));
    $location     = remove_junk($db->escape($_POST['location']));
    $com_id     = remove_junk($db->escape($_POST['company']));
   
    $com_name = find_value_by_sql(" select com_name from company  where com_id=".$com_id)['com_name'];
   
    if ($end_date  < $start_date) {
      $session->msg("d", "start date cannot be greater than to date!");
      redirect('stock_report.php', false);
    }

    $results      = find_stock_by_dates($start_date, $end_date, $location,$com_id );
  } else {
    $session->msg("d", $errors);
    redirect('stock_report.php', false);
  }
} else {
  $session->msg("d", "Select dates");
  redirect('stock_report.php', false);
}
?>
<!doctype html>
<html lang="en-US">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Stock Report</title>
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
  <?php if ($results) : ?>


    <input type="button" class="btn btn-sm" onclick="tableToExcel('excel', 'Stock Report', 'StockReport.xls')" value="Export to Excel" />
    <div class="page-break" id="excel">
      <div class="sale-head pull-center">
        <h1>Stock</h1>
        <strong><?php if (isset($start_date)) {
                  echo $start_date;
                } ?> To <?php if (isset($end_date)) {
                                                                        echo $end_date;
                                                                      } ?> </strong>
        <?php if ($location > 0) {
          $loc = find_by_id('locations', $location);
        ?>
          <strong><?php echo  $loc['loc_name']; ?> </strong>
         
        <?php } ?>
        <strong><?php echo  $com_name; ?> </strong>
      </div>
      <table class="table table-border">
        <thead>
          <tr>
            <th rowspan="2">#</th>
            <th rowspan="2">Short Code</th>
            <th rowspan="2">Product Title</th>
            <th colspan="4">Quantity</th>
            <th colspan="4">Value</th>
            <th rowspan="2">Unit</th>
          </tr>
          <tr>


            <th>Opening</th>
            <th>Received</th>
            <th>Issued</th>
            <th>Closed</th>

            <th>Opening</th>
            <th>Received</th>
            <th>Issued</th>
            <th>Closed</th>

          </tr>
        </thead>
        <tbody>
          <?php

          $tot_open_qty = 0;
          $tot_rcv_qty = 0;
          $tot_issue_qty = 0;
          $tot_close_qty = 0;

          $tot_open_price = 0;
          $tot_rcv_price = 0;
          $tot_issue_price = 0;
          $tot_close_price = 0;
          foreach ($results as $result) :

            $tot_open_price = $result['price'] * $result['opening_qty'] + $tot_open_price;
            $tot_rcv_price = $result['price'] * $result['receive_qty'] + $tot_rcv_price;
            $tot_issue_price = $result['price'] * $result['issue_qty'] + $tot_issue_price;
            $tot_close_price = $result['price'] * $result['closing_qty'] + $tot_close_price;

            $tot_open_qty =  $result['opening_qty'] + $tot_open_qty;
            $tot_rcv_qty =   $result['receive_qty'] + $tot_rcv_qty;
            $tot_issue_qty = $result['issue_qty'] + $tot_issue_qty;
            $tot_close_qty = $result['closing_qty'] + $tot_close_qty;

          ?>
            <tr>
              <td class="text-right"><?php echo count_id(); ?></td>
              <td class="text-right"><?php echo remove_junk($result['short_code']); ?></td>
              <td class="desc">
                <h6><?php echo remove_junk(ucfirst($result['name'])); ?></h6>
              </td>
              <?php

              if ($result['unit_type'] == 'decimal') { ?>
                <td class="text-right"><?php echo ($result['opening_qty']); ?></td>
                <td class="text-right"><?php echo ($result['receive_qty']); ?></td>
                <td class="text-right"><?php echo ($result['issue_qty']); ?></td>
                <td class="text-right"><?php echo ($result['closing_qty']); ?></td>
              <?php } else { ?>
                <td class="text-right"><?php echo intval($result['opening_qty']); ?></td>
                <td class="text-right"><?php echo intval($result['receive_qty']); ?></td>
                <td class="text-right"><?php echo intval($result['issue_qty']); ?></td>
                <td class="text-right"><?php echo intval($result['closing_qty']); ?></td>
              <?php } ?>

              <td class="text-right"><?php echo sprintf('%0.2f', $result['price'] * $result['opening_qty']); ?></td>
              <td class="text-right"><?php echo sprintf('%0.2f', $result['price'] * $result['receive_qty']); ?></td>
              <td class="text-right"><?php echo sprintf('%0.2f', $result['price'] * $result['issue_qty']); ?></td>
              <td class="text-right"><?php echo sprintf('%0.2f', $result['price'] * $result['closing_qty']); ?></td>


              <td class="text-right"><?php echo ($result['unit_name']); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr class="text-right">

            <td colspan="3">Total</td>
            <td><?php echo $tot_open_qty; ?></td>
            <td><?php echo $tot_rcv_qty; ?></td>
            <td><?php echo $tot_issue_qty; ?></td>
            <td><?php echo $tot_close_qty; ?></td>
            <td> &#2547;
              <?php echo sprintf('%0.2f', $tot_open_price); ?>
            </td>
            <td> &#2547;
              <?php echo sprintf('%0.2f', $tot_rcv_price); ?>
            </td>
            <td> &#2547;
              <?php echo sprintf('%0.2f', $tot_issue_price); ?>
            </td>
            <td> &#2547;
              <?php echo sprintf('%0.2f', $tot_close_price); ?>
            </td>
            <td></td>
          </tr>

        </tfoot>

      </table>
    </div>
  <?php
  else :
    $session->msg("d", "Sorry no stock has been found. ");
    redirect('stock_report.php', false);
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