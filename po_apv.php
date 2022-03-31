<link rel="stylesheet" type="text/css" href="libs/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="libs/css/dataTables.bootstrap.min.css">




<?php
$page_title = 'All PO';
require_once('includes/load.php');
// Checkin What level user has permission to view this page
page_require_level(5);
$grn = join_apv_po_table();
?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
  <div class="col-md-12">
    <div class="panel panel-default">
    
      <div class="panel-body">
        <table class="table table-striped table-bordered" id="example" style="width:100%">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th class="text-center">PO No </th>

              <th> PO by </th>
              <th> PO Date </th>
              <th class="text-center">Supplier </th>





              <th class="text-center" style="width: 100px;"> Actions </th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($grn as $g) : ?>
              <tr>
                <td class="text-center"><?php echo count_id(); ?></td>

                <td class="text-center"> <?php echo remove_junk($g['id']); ?></td>


                <td> <?php echo remove_junk($g['claimer']); ?></td>
                <td> <?php if ($g['cancel_status'] == 0 and $g['submit_status'] == 1) echo remove_junk($g['po_date']); ?></td>
                <td> <?php echo remove_junk($g['sup_name']); ?></td>


                <td class="text-center">
                  <div>

                    <?php
                    if ($g['cancel_status'] == 0 and $g['submit_status'] == 1 and $g['approve_status'] == 0) {
                    ?>



                      <a href="po_apv_process.php?id=<?php echo (int)$g['id']; ?>" class="btn btn-info btn-xs" title="Recommend" onclick="return confirm('Are you sure you want to Approve? PO NO :<?php echo (int)$g["id"]; ?> ');" data-toggle="tooltip">
                        <span class="glyphicon glyphicon-ok-sign"></span>
                      </a>

                    <?php

                    }

                    ?>


                    <?php
                    if ($g['cancel_status'] == 0 and $g['submit_status'] == 1 and $g['approve_status'] == 0) {
                    ?>


                    <?php } ?>

                    <a target="_blank" href="po_report.php?id=<?php echo (int)$g['id']; ?>" class="btn btn-info btn-xs" title="View" data-toggle="tooltip">
                      <span class="glyphicon glyphicon-eye-open"></span>
                    </a>


                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          </tabel>
      </div>
    </div>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>


<!-- <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>  -->
<script type="text/javascript" src="libs/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="libs/js/dataTables.bootstrap.min.js"></script>

<script type="text/javascript">
  $(document).ready(function() {
    $('#example').DataTable();
  });
</script>