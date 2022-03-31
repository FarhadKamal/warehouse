<?php
$page_title = 'Stock Ledger Report';
require_once('includes/load.php');
// Checkin What level user has permission to view this page
page_require_level(10);


?>
<?php include_once('layouts/header.php');


$multi_com = find_value_by_sql(" select multi_com from users where id=".$_SESSION['user_id'])['multi_com'];
$all_company = find_by_sql('select * from  company  where com_id in('.$multi_com .')  order by com_name');
$all_locations = find_by_sql('select * from  locations where com in('.$multi_com .') order by loc_name');



$all_products = find_by_sql('select * from products order by short_code');

?>
<link rel="stylesheet" href="libs/css/flexselect.css" type="text/css" media="screen" />
<div class="row">
  <div class="col-md-6">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<div class="row">
  <div class="col-md-6">
    <div class="panel">
      <div class="panel-heading">

      </div>
      <div class="panel-body">
        <form class="clearfix" method="post" target="_blank" action="stock_ledger_report_process.php">
          <div class="form-group">
            <label class="form-label">Date Range</label>
            <div class="input-group">
              <?php

              $pdate = strtotime(date("Y-m-d"));
              $pdate = strtotime("-30 day", $pdate); ?>

              <input type="text" class="datepicker form-control" name="start-date" placeholder="From" value="<?php echo date("Y-m-d", $pdate); ?>">
              <span class="input-group-addon"><i class="glyphicon glyphicon-menu-right"></i></span>
              <input type="text" class="datepicker form-control" name="end-date" placeholder="To" value="<?php echo date("Y-m-d"); ?>">
            </div>
          </div>
          <div class="form-group"> <label>Company</label>
            <div class="input-group">

              <span class="input-group-addon">
                <i class="glyphicon glyphicon-flag"></i>
              </span>


              <select class="form-control" name="company">
                <?php foreach ($all_company as $comp) : ?>
                  <option value="<?php echo (int)$comp['com_id'] ?>">
                    <?php echo $comp['com_name'] ?></option>
                <?php endforeach; ?>
              </select>

            </div>
          </div>

          <div class="form-group"> <label>Location</label>
            <div class="input-group">

              <span class="input-group-addon">
                <i class="glyphicon glyphicon-flag"></i>
              </span>

              <select class="form-control" name="location">
                <option value="0">ALL</option>
                <?php foreach ($all_locations as $loc) : ?>
                  <option value="<?php echo (int)$loc['id'] ?>">
                    <?php echo $loc['loc_name'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-group"> <label>Product</label>
            <div class="input-group">

              <span class="input-group-addon">
                <i class="glyphicon glyphicon-th"></i>
              </span>

              <select class="form-control" id="product-id" name="product" style="width: 500px;">
                <option value="">Select Product</option>
                <?php foreach ($all_products as $prd) : ?>
                  <option value="<?php echo (int)$prd['id'] ?>">
                    <?php echo $prd['short_code'] ?> <?php echo " # " ?><?php echo $prd['name'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>








          <div class="form-group">
            <button type="submit" name="submit" class="btn btn-primary">Generate Report</button>
          </div>
        </form>
      </div>

    </div>
  </div>

</div>
<?php include_once('layouts/footer.php'); ?>


<script>
  $(document).ready(function() {

    $("#product-id").flexselect();

  });
</script>
<script src="libs/js/liquidmetal.js" type="text/javascript"></script>
<script src="libs/js/jquery.flexselect.js" type="text/javascript"></script>