    <link rel="stylesheet" href="libs/css/flexselect.css" type="text/css" media="screen" />
    <?php
    $page_title = 'New Adjustment';
    require_once('includes/load.php');
    // Checkin What level user has permission to view this page
    page_require_level(4);
   

  
    $com_id = find_value_by_sql(" select com from users where id=".$_SESSION['user_id'])['com'];
  
    $all_locations = find_by_sql('select * from  locations where com='.$com_id.' order by loc_name');


    ?>
    <?php

    
    
    if(isset($_POST['add_adjust'])){
      

      $req_fields = array('location');
      validate_fields($req_fields);
      if(empty($errors)){


  
        $loc_id    = remove_junk($db->escape($_POST['location']));
  
        $remarks    = remove_junk($db->escape($_POST['remarks']));


        $submit_by= $_SESSION['user_id'];




        $date    = make_date();
        $query  = "INSERT INTO adjust (";
        $query .=" loc_id,adj_by,adj_date,remarks,com";
        $query .=") VALUES (";
        $query .=" '{$loc_id}', '{$submit_by}', now(), '{$remarks}', '{$com_id}'";
        $query .=")";
       //$query .=" ON DUPLICATE KEY UPDATE name='{$p_name}'";
        if($db->query($query)){
         $session->msg('s',"Adjustment saved!");


         $track_id = find_value_by_sql(" select max(id)  as track_id from adjust where adj_by='{$submit_by}'  ")['track_id'];


        


     
         redirect('edit_adjust.php?id='.$track_id, false);
         //redirect('add_adjust.php', false);
       
       } else {
         $session->msg('d',' Sorry failed to save Adjustment!');
         redirect('add_adjust.php', false);
       }

     } else{
       $session->msg("d", $errors);
       redirect('add_adjust.php',false);
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
            <span>Physical Adjustment</span>
          </strong>
        </div>
        <div class="panel-body">
         <div class="col-md-12">
          <form method="post" action="add_adjust.php" class="clearfix">
            
            <div class="form-group"> <label>Stock Record Date</label>
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

           <div class="form-group"> <label>Stock Location</label>
              <div class="input-group">

                <span class="input-group-addon">
                 <i class="glyphicon glyphicon-flag"></i>
               </span>
                <select class="form-control"  name="location" >
                <option value="">Select Location</option>
                <?php  foreach ($all_locations as $loc): ?>
                  <option value="<?php echo (int)$loc['id'] ?>">
                    <?php echo " # " ?><?php echo $loc['loc_name'] ?></option>
                  <?php endforeach; ?>
                </select>
             </div>
           </div>

            


          <div class="form-group">
            <div class="form-group"> <label>Remarks</label>
              <textarea class="form-control" id="exampleFormControlTextarea1" name="remarks" rows="3"></textarea>
            </div>
           </div> 



        


           <button type="submit" name="add_adjust" id="add" class="btn btn-success">Save</button>



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