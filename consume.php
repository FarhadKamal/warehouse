<link rel="stylesheet" type="text/css" href="libs/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="libs/css/dataTables.bootstrap.min.css">




<?php
$page_title = 'All Issue';
require_once('includes/load.php');
  // Checkin What level user has permission to view this page
page_require_level(4);
$consume = join_issue_table();
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
       <a href="add_consume.php" class="btn btn-primary">New</a>
     </div>
   </div>
   <div class="panel-body">
    <table class="table table-striped table-bordered" id="example" style="width:100%"> 
      <thead>
        <tr>
          <th class="text-center" style="width: 50px;">#</th>
          <th class="text-center" >Issue No </th>
          <th class="text-center" >Location </th>
          <th> Issue by </th>
          <th> Consumer </th>
          <th> Cost Centre</th>
          <th> Issue Date </th>

   
      



          <th class="text-center" style="width: 100px;"> Actions </th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($consume as $con):?>
          <tr>
            <td class="text-center"><?php echo count_id();?></td>

            <td class="text-center"> <?php echo remove_junk($con['id']); ?></td>
            <td> <?php echo remove_junk($con['loc_name']); ?></td>
             <td> <?php echo remove_junk($con['claimer']); ?></td>
              <td> <?php echo remove_junk($con['consumer']); ?></td>
              <td> <?php echo remove_junk($con['cost_name']); ?>
                
                <?php echo ' # ' ?><?php echo $con['cost_id'] ?>
              </td>
            <td> <?php if( $con['cancel_status']==0 and $con['submit_status']==1) echo remove_junk($con['con_date']); ?></td>
          
          

            <td class="text-center">
              <div>
                
                 <?php
                if($con['cancel_status']==0 and $con['submit_status']==0)
                { 
                  ?>
                  <a href="edit_consume.php?id=<?php echo (int)$con['id'];?>" class="btn btn-info btn-xs"  title="Edit" data-toggle="tooltip">
                  <span class="glyphicon glyphicon-edit"></span>
                </a>

                <?php } ?>


                <?php
                if( $con['cancel_status']==0 and $con['submit_status']==0)
                { 
                  ?>
                  <a href="cancel_issue.php?id=<?php echo (int)$con['id'];?>" class="btn btn-danger btn-xs"  title="Delete" onclick="return confirm('Are you sure you want to cancel? Issue No:<?php echo (int)$con["id"];?> ');" data-toggle="tooltip">
                    <span class="glyphicon glyphicon-trash"></span>
                  </a>
                  
                <?php } ?>

                 <a  target="_blank" href="view_consume.php?id=<?php echo (int)$con['id'];?>" class="btn btn-info btn-xs"  title="View"data-toggle="tooltip">
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