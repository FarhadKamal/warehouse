<?php
$page_title = 'Purchase History';
require_once('includes/load.php');
  // Checkin What level user has permission to view this page
page_require_level(7);


?>
<?php include_once('layouts/header.php'); 

$all_requisitions = find_by_sql('select * from requisition where  submit_status=1 and cancel_status=0 order by id desc limit 50');

$all_assigned = find_by_sql('select * from assigned_person order by person_name');


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
        <form class="clearfix" method="post" target="_blank" action="view_req_sep.php">
      

          
         

        <div class="form-group"> <label>Requisition ID</label>
              <div class="input-group">

                <span class="input-group-addon">
                 <i class="glyphicon glyphicon-tag"></i>
               </span>
                <select class="form-control" id="requisition-id"  name="requisition-id" style="width: 300px;">
                <?php  foreach ($all_requisitions as $req): ?>
                  <option value="<?php echo (int)$req['id'] ?>">
                    <?php echo $req['id'] ?></option>
                  <?php endforeach; ?>
                </select>
             </div>
           </div>


           <!-- <div class="form-group"> <label>Assigned Person</label>
                  <div class="input-group">

                    <span class="input-group-addon">
                     <i class="glyphicon glyphicon-tag"></i>
                   </span>
                   <select class="form-control" id="assigned-person"  name="assigned-person" style="width: 300px;">
                    <?php  foreach ($all_assigned as $asd): ?>
                      <option value="<?php echo $asd['person_name'] ?>">
                        <?php echo $asd['person_name'] ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div> -->

    



        




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

    
  });
</script>
<script src="libs/js/liquidmetal.js" type="text/javascript"></script>
<script src="libs/js/jquery.flexselect.js" type="text/javascript"></script>