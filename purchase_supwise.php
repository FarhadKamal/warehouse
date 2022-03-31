<?php
$page_title = 'Supplier wise Purchase';
require_once('includes/load.php');
  // Checkin What level user has permission to view this page
page_require_level(4);


?>
<?php include_once('layouts/header.php'); 




?>
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
        <form class="clearfix" method="post" target="_blank" action="purchase_supwise_process.php">
          <div class="form-group">
            <label class="form-label">Date Range</label>
            <div class="input-group">
              <?php   

              $pdate = strtotime(date("Y-m-d"));
              $pdate = strtotime("-30 day", $pdate); ?>
              <input type="text" class="datepicker form-control" name="start-date" placeholder="From" value="<?php echo date("Y-m-d",$pdate); ?>">
              <span class="input-group-addon"><i class="glyphicon glyphicon-menu-right"></i></span>
              <input type="text" class="datepicker form-control" name="end-date" placeholder="To" value="<?php echo date("Y-m-d"); ?>">
            </div>
          </div>

          


         <br/>
         <div class="form-group">
           <button type="submit" name="submit" class="btn btn-primary">Generate Report</button>
         </div>
       </form>
     </div>

   </div>
 </div>

</div>
<?php include_once('layouts/footer.php'); ?>
