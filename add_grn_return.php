    <link rel="stylesheet" href="libs/css/flexselect.css" type="text/css" media="screen" />
    <?php
    $page_title = 'New GRN Return';
    require_once('includes/load.php');
    // Checkin What level user has permission to view this page
    page_require_level(4);
   

  
    $all_returns = find_by_sql('select * from grn where  submit_status=1 and cancel_status=0 order by id desc limit 100');

    ?>
    <?php

    
    
    if(isset($_POST['add_grn_return'])){
      

      $req_fields = array('grn-no');
      validate_fields($req_fields);
      if(empty($errors)){


  
        $grn_id    = remove_junk($db->escape($_POST['grn-no']));
  
        $remarks    = remove_junk($db->escape($_POST['remarks']));


        $submit_by= $_SESSION['user_id'];




        $date    = make_date();
        $query  = "INSERT INTO grn_return (";
        $query .=" grn_id,rtn_by,rtn_date,remarks";
        $query .=") VALUES (";
        $query .=" '{$grn_id}', '{$submit_by}', now(), '{$remarks}'";
        $query .=")";
       //$query .=" ON DUPLICATE KEY UPDATE name='{$p_name}'";
        if($db->query($query)){
         $session->msg('s',"GRN Return saved!");


         $track_id = find_value_by_sql(" select max(id)  as track_id from grn_return where rtn_by='{$submit_by}'  ")['track_id'];


        


     
         redirect('edit_grn_return.php?id='.$track_id, false);
         //redirect('add_grn_return.php', false);
       
       } else {
         $session->msg('d',' Sorry failed to save GRN Return!');
         redirect('add_grn_return.php', false);
       }

     } else{
       $session->msg("d", $errors);
       redirect('add_grn_return.php',false);
     }

   }

   ?>
   <?php include_once('layouts/header.php'); ?>
   <div class="row">
    <div class="col-md-12">
      <?php echo display_msg($msg); ?>
    </div>
  </div>
  <div class="row">
    <div class="col-md-8">
      <div class="panel panel-default">
        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>GRN Return</span>
          </strong>
        </div>
        <div class="panel-body">
         <div class="col-md-12">
          <form method="post" action="add_grn_return.php" class="clearfix">
            
            <div class="form-group"> <label>Return Date</label>
              <div class="input-group">

                <span class="input-group-addon">
                 <i class="glyphicon glyphicon-calendar"></i>
               </span>
               <?php    
              $idate = strtotime(date("Y-m-d H:i:s"));




               ?>
               <input  type="text" class="form-control"  style="width: 300px;"
                 value="<?php echo  date('Y-m-d H:i:s', $idate); ?>"  readonly>
             </div>
           </div> 

            <div class="form-group"> <label>GRN No</label>
              <div class="input-group">

                <span class="input-group-addon">
                 <i class="glyphicon glyphicon-tag"></i>
               </span>
                <select class="form-control" id="grn-no"  name="grn-no" >
                <option value="">Select GRN No</option>
                <?php  foreach ($all_returns as $iss): ?>
                  <option value="<?php echo (int)$iss['id'] ?>">
                    <?php echo $iss['id'] ?></option>
                  <?php endforeach; ?>
                </select>
             </div>
           </div>

            


          <div class="form-group">
            <div class="form-group"> <label>Remarks</label>
              <textarea class="form-control" id="exampleFormControlTextarea1" name="remarks" rows="3"></textarea>
            </div>
           </div> 



        


           <button type="submit" name="add_grn_return" id="add" class="btn btn-success">Save</button>



            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include_once('layouts/footer.php'); ?>
  <script>
    $(document).ready(function() {

      // $("#product-id").flexselect();

     


    });
  </script>
  <script src="libs/js/liquidmetal.js" type="text/javascript"></script>
  <script src="libs/js/jquery.flexselect.js" type="text/javascript"></script>