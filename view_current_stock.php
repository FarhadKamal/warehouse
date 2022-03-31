<?php
$page_title = 'Current Stock';

require_once('includes/load.php');
// Checkin What level user has permission to view this page
page_require_level(10);

$com_id     = find_value_by_sql(" select com from users where id=".$_SESSION['user_id'])['com'];
   
$com_name = find_value_by_sql(" select com_name from company  where com_id=".$com_id)['com_name'];


$results = find_by_sql('select  unit_name,unit_type,product_id,short_code,products.name,sum(stock_price) as totprice,sum(stock_qty) as totqty  from stock
  inner join products on products.id= stock.product_id
  inner join units on units.id= products.unit_id
  where stock.com='.$com_id.'
  group by product_id
  order by short_code  ');


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
    <input type="button" class="btn btn-sm" onclick="tableToExcel('excel', 'Current Stock Report', 'CurrentStockReport.xls')" value="Export to Excel" />
    <div class="page-break" id="excel">
      <div class="sale-head pull-center">
        <h1>Current Stock</h1>
        <strong><?php echo date("F j, Y, g:i a"); ?></strong>
        <strong><?php echo $com_name; ?></strong>
      </div>
      <table class="table table-border">
        <thead>
          <tr>
            <th>#</th>
            <th>Short Code</th>
            <th>Product Title</th>




            <th>Quantity</th>
            <th>UOM</th>
            <th>Rate Price</th>
            <th>Total Price</th>


          </tr>

        </thead>
        <tbody>
          <?php

          $totalqty = 0;
          $totalprice = 0;
          $totalnetprice = 0;
          foreach ($results as $result) :

            $totalqty = $result['totqty'] + $totalqty;
            $totalprice = $result['totprice'] + $totalprice;

            // $totalnetprice=($result['totprice']/$result['totqty'])+$totalnetprice;
          ?>
            <tr>
              <td class="text-right"><?php echo count_id(); ?></td>
              <td class="text-right"><?php echo remove_junk($result['short_code']); ?></td>
              <td class="desc">
                <h6><?php echo remove_junk(ucfirst($result['name'])); ?></h6>
              </td>



              <?php if ($result['unit_type'] == 'number') { ?>
                <td class="text-right"><?php echo intval($result['totqty']); ?></td>
              <?php } else { ?>
                <td class="text-right"><?php echo ($result['totqty']); ?></td>
              <?php }  ?>

              <td class="text-right"><?php echo ($result['unit_name']); ?></td>

              <td class="text-right"><?php echo sprintf('%0.2f', $result['totprice'] / $result['totqty']); ?></td>
              <td class="text-right"><?php echo sprintf('%0.2f', $result['totprice']); ?></td>

            </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr class="text-right">

            <td colspan="3">Total</td>
            <td><?php echo $totalqty; ?></td>
            <td></td>
            <td></td>
            <td><?php echo $totalprice; ?></td>

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
        return window.btoa(decodeURIComponent(encodeURIComponent(s)))
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