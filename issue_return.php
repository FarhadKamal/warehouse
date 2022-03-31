<link rel="stylesheet" type="text/css" href="libs/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="libs/css/dataTables.bootstrap.min.css">




<?php
$page_title = 'All Issue Return';
require_once('includes/load.php');
  // Checkin What level user has permission to view this page
page_require_level(4);
$Issue = join_issue_return_table();
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
       <a href="add_issue_return.php" class="btn btn-primary">New</a>
     </div>
   </div>
   <div class="panel-body">
    <table class="table table-striped table-bordered" id="example" style="width:100%"> 
      <thead>
        <tr>
          <th class="text-center" style="width: 50px;">#</th>
          <th class="text-center" > Return No </th>
          <th class="text-center" >Issue No </th>
          <th class="text-center" >Location </th>
          <th> From </th>
          <th> Return by </th>
          <th> Return Date </th>

   
      



          <th class="text-center" style="width: 100px;"> Actions </th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($Issue as $issrtn):?>
          <tr>
            <td class="text-center"><?php echo count_id();?></td>

            <td class="text-center"> <?php echo remove_junk($issrtn['id']); ?></td>
            <td class="text-center"> 
              
 <a href="view_consume.php?id=<?php echo ($issrtn['con_id']);?>" target="_blank"><?php echo ($issrtn['con_id']);?></a>

            </td>
             <td class="text-center"> <?php echo remove_junk($issrtn['loc_name']); ?></td>
              <td> <?php echo remove_junk($issrtn['consumer']); ?></td>
               <td> <?php echo remove_junk($issrtn['claimer']); ?></td>
            <td> <?php if( $issrtn['cancel_status']==0 and $issrtn['submit_status']==1) echo remove_junk($issrtn['rtn_date']); ?></td>
          
          

            <td class="text-center">
              <div>
                
                 <?php
                if($issrtn['cancel_status']==0 and $issrtn['submit_status']==0)
                { 
                  ?>
                  <a href="edit_issue_return.php?id=<?php echo (int)$issrtn['id'];?>" class="btn btn-info btn-xs"  title="Edit" data-toggle="tooltip">
                  <span class="glyphicon glyphicon-edit"></span>
                </a>

                <?php } ?>


                <?php
                if( $issrtn['cancel_status']==0 and $issrtn['submit_status']==0)
                { 
                  ?>
                  <a href="cancel_issue_return.php?id=<?php echo (int)$issrtn['id'];?>" class="btn btn-danger btn-xs"  title="Delete" onclick="return confirm('Are you sure you want to cancel? Return No:<?php echo (int)$issrtn["id"];?> ');" data-toggle="tooltip">
                    <span class="glyphicon glyphicon-trash"></span>
                  </a>
                  
                <?php } ?>

                 <a  target="_blank" href="view_issue_return.php?id=<?php echo (int)$issrtn['id'];?>" class="btn btn-info btn-xs"  title="View"data-toggle="tooltip">
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