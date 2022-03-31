<link rel="stylesheet" type="text/css" href="libs/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="libs/css/dataTables.bootstrap.min.css">




<?php
$page_title = 'All Transfer';
require_once('includes/load.php');
  // Checkin What level user has permission to view this page
page_require_level(4);
$transfer = join_transfer_table();
?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
 <div class="col-md-12">
   <?php echo display_msg($msg); ?>
 </div>
 <div class="col-md-12">
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
     <div class="pull-right">
       <a href="add_transfer.php" class="btn btn-primary">New</a>
     </div>
   </div>
   <div class="panel-body">
    <table class="table table-striped table-bordered" id="example" style="width:100%"> 
      <thead>
        <tr>
          <th class="text-center" style="width: 50px;">#</th>
          <th class="text-center" >Transfer No </th>
          <th class="text-center" >Location From</th>
          <th class="text-center" >Transfer From</th>
          <th> Transfer by </th>
          <th> Transfer Date </th>

   
      



          <th class="text-center" style="width: 100px;"> Actions </th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($transfer as $tran):?>
          <tr>
            <td class="text-center"><?php echo count_id();?></td>

            <td class="text-center"> <?php echo remove_junk($tran['id']); ?></td>
            <td> <?php echo remove_junk($tran['locfrom']); ?></td>
            <td> <?php echo remove_junk($tran['locto']); ?></td>
            <td> <?php echo remove_junk($tran['claimer']); ?></td>
            <td> <?php echo remove_junk($tran['tran_date']); ?></td>
          
          

            <td class="text-center">
              <div>
                
                 <?php
                if($tran['cancel_status']==0 and $tran['submit_status']==0)
                { 
                  ?>
                  <a href="edit_transfer.php?id=<?php echo (int)$tran['id'];?>" class="btn btn-info btn-xs"  title="Edit" data-toggle="tooltip">
                  <span class="glyphicon glyphicon-edit"></span>
                </a>

                <?php } ?>


                <?php
                if( $tran['cancel_status']==0 and $tran['submit_status']==0)
                { 
                  ?>
                  <a href="cancel_transfer.php?id=<?php echo (int)$tran['id'];?>" class="btn btn-danger btn-xs"  title="Delete" onclick="return confirm('Are you sure you want to cancel? Transfer No:<?php echo (int)$tran["id"];?> ');" data-toggle="tooltip">
                    <span class="glyphicon glyphicon-trash"></span>
                  </a>
                  
                <?php } ?>

                 <a  target="_blank" href="view_transfer.php?id=<?php echo (int)$tran['id'];?>" class="btn btn-info btn-xs"  title="View"data-toggle="tooltip">
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
  } );
</script>