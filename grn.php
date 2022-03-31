<link rel="stylesheet" type="text/css" href="libs/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="libs/css/dataTables.bootstrap.min.css">




<?php
$page_title = 'All GRN';
require_once('includes/load.php');
  // Checkin What level user has permission to view this page
page_require_level(4);
$grn = join_grn_table();
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
       <a href="add_grn.php" class="btn btn-primary">New</a>
     </div>
   </div>
   <div class="panel-body">
    <table class="table table-striped table-bordered" id="example" style="width:100%"> 
      <thead>
        <tr>
          <th class="text-center" style="width: 50px;">#</th>
          <th class="text-center" >GRN No </th>
          <th class="text-center" >Requisition ID </th>
          <th class="text-center" >Location </th>
          <th> GRN by </th>
          <th> GRN Date </th>
          <th class="text-center" >Supplier </th>
          
          



          <th class="text-center" style="width: 100px;"> Actions </th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($grn as $g):?>
          <tr>
            <td class="text-center"><?php echo count_id();?></td>

            <td class="text-center"> <?php echo remove_junk($g['id']); ?></td>
            <td> 
             <?php  if($g['req_id']==null){ echo "without requisition";}else{ ?>
              
              <a href="view_requisition.php?id=<?php echo ($g['req_id']);?>" target="_blank"><?php echo ($g['req_id']);?></a>
            <?php } ?>
          </td>
          <td> <?php echo remove_junk($g['loc_name']); ?></td>
          <td> <?php echo remove_junk($g['claimer']); ?></td>
          <td> <?php if( $g['cancel_status']==0 and $g['submit_status']==1) echo remove_junk($g['grn_date']); ?></td>
           <td> <?php echo remove_junk($g['sup_name']); ?></td>
          

          <td class="text-center">
            <div>
              
             <?php
             if($g['cancel_status']==0 and $g['submit_status']==0)
             { 
              if($g['req_id']==null){ ?>

                <a href="edit_direct_grn.php?id=<?php echo (int)$g['id'];?>" class="btn btn-info btn-xs"  title="Edit" data-toggle="tooltip">
                  <span class="glyphicon glyphicon-edit"></span>
                </a>

              <?php }else{

                ?>
                <a href="edit_grn.php?id=<?php echo (int)$g['id'];?>" class="btn btn-info btn-xs"  title="Edit" data-toggle="tooltip">
                  <span class="glyphicon glyphicon-edit"></span>

                  <?php
                }

              }

              ?>


              <?php
              if( $g['cancel_status']==0 and $g['submit_status']==0)
              { 
                ?>
                <a href="cancel_grn.php?id=<?php echo (int)$g['id'];?>" class="btn btn-danger btn-xs"  title="Delete" onclick="return confirm('Are you sure you want to cancel? GRN No:<?php echo (int)$g["id"];?> ');" data-toggle="tooltip">
                  <span class="glyphicon glyphicon-trash"></span>
                </a>
                
              <?php } ?>

              <a  target="_blank" href="view_grn.php?id=<?php echo (int)$g['id'];?>" class="btn btn-info btn-xs"  title="View"data-toggle="tooltip">
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