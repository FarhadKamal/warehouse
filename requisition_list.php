<link rel="stylesheet" type="text/css" href="libs/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="libs/css/dataTables.bootstrap.min.css">




<?php
$page_title = 'All Requisition';
require_once('includes/load.php');
  // Checkin What level user has permission to view this page
page_require_level(5);
$requisition = join_requisition_table();
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
          <th class="text-center" >Requisition ID </th>
          <th> Claimer </th>
          <th> Submit Date </th>
          <th> Reason   </th>
          <th> Expected Date   </th>
          <th> Contact Person </th>
          <th> Status   </th>



          <th class="text-center" style="width: 100px;"> Actions </th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($requisition as $req):

          $status="";
          if($req['submit_status']==0 and $req['cancel_status']==0 )
          {

            $status="Not yet submitted";
          }

          else if($req['submit_status']==1 and $req['approve_status']==0 and $req['cancel_status']==0 )
          {

            $status="Waiting for approval";
          }

          else if($req['submit_status']==1 and $req['approve_status']==1 and $req['cancel_status']==0 )
          {

            $status="Approved";
            if($req['recom_status']==1  )
            $status="Approved & Recommended";  
          }

          if($req['submit_status']==1 and $req['approve_status']==1 and $req['complete_status']==1 and $req['cancel_status']==0 )
          {

            $status="Completed";
          }

    


          ?>
          <tr>
            <td class="text-center"><?php echo count_id();?></td>

            <td class="text-center"> <?php echo remove_junk($req['id']); ?></td>
            <td> <?php echo remove_junk($req['claimer']); ?></td>
            <td> <?php if( $req['cancel_status']==0 and $req['submit_status']==1) echo remove_junk($req['submit_date']); ?></td>
            <td> <?php echo remove_junk($req['request_reason']); ?></td>
           
            <td> <?php echo    date ('F j, Y', strtotime($req['expected_date']));    ?></td>
             <td> <?php echo remove_junk($req['contact_person']); ?></td>
            <td <?php  if($req['approve_status']==1 and $req['cancel_status']==0 and $req['complete_status']==1 )echo 'style="background-color: #00FF00;"';?>> <?php echo $status; ?></td>

            <td class="text-center">
              <div>

                 <a  target="_blank" href="view_requisition.php?id=<?php echo (int)$req['id'];?>" class="btn btn-info btn-xs"  title="View"data-toggle="tooltip">
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