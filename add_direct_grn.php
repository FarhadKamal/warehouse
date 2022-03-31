    <link rel="stylesheet" href="libs/css/flexselect.css" type="text/css" media="screen" />
    <?php
    $page_title = 'New GRN';
    require_once('includes/load.php');
    // Checkin What level user has permission to view this page
    page_require_level(4);
    $all_products = find_by_sql('select * from products order by short_code');

    $com_id = find_value_by_sql(" select com from users where id=".$_SESSION['user_id'])['com'];


    $all_locations = find_by_sql('select * from  locations where com='.$com_id.' order by loc_name');


    $all_sup = find_by_sql('select * from  suppliers order by sup_name');

    $com_id = find_value_by_sql(" select com from users where id=".$_SESSION['user_id'])['com'];

    ?>
    <?php

    
    
    if(isset($_POST['add_grn'])){
      
      

      

  

    

      $req_fields = array('location','supplier','reference-of-requisition'  );
      validate_fields($req_fields);
      if(empty($errors)){


        $loc_id    = remove_junk($db->escape($_POST['location']));
        $sup_id    = remove_junk($db->escape($_POST['supplier']));
        $req_ref    = remove_junk($db->escape($_POST['reference-of-requisition']));

        




        $submit_by= $_SESSION['user_id'];




        $date    = make_date();
        $query  = "INSERT INTO grn (";
        $query .=" loc_id,grn_by,grn_date,sup_id,req_ref,com";
        $query .=") VALUES (";
        $query .="  '{$loc_id}', '{$submit_by}', now(),'{$sup_id}','{$req_ref}','{$com_id}'";
        $query .=")";
       //$query .=" ON DUPLICATE KEY UPDATE name='{$p_name}'";
        if($db->query($query)){
         $session->msg('s',"GRN saved!");


         $track_id = find_value_by_sql(" select max(id)  as track_id from grn where grn_by='{$submit_by}'  ")['track_id'];



        


        
         redirect('edit_direct_grn.php?id='.$track_id, false);
       } else {
         $session->msg('d',' Sorry failed to save GRN!');
         redirect('add_direct_grn.php', false);
       }

     } else{
       $session->msg("d", $errors);
       redirect('add_direct_grn.php',false);
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
            <span>GRN</span>
          </strong>
        </div>
        <div class="panel-body">
         <div class="col-md-12">
          <form method="post" action="add_direct_grn.php" class="clearfix">
            
            <div class="form-group"> <label>GRN Date</label>
              <div class="input-group">

                <span class="input-group-addon">
                 <i class="glyphicon glyphicon-calendar"></i>
               </span>
               <?php    
              $grndate = strtotime(date("Y-m-d H:i:s"));




               ?>
               <input  type="text" class="form-control" name="grndate" style="width: 300px;"
                 value="<?php echo  date('Y-m-d H:i:s', $grndate); ?>"  readonly>
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
                    <?php echo $loc['loc_name'] ?></option>
                  <?php endforeach; ?>
                </select>
             </div>
           </div>


             <div class="form-group"> <label>Supplier</label>
              <div class="input-group">

                <span class="input-group-addon">
                 <i class="glyphicon glyphicon-user"></i>
               </span>
                <select class="form-control"  name="supplier" >
                <option value="">Select Supplier</option>
                <?php  foreach ($all_sup as $sup): ?>
                  <option value="<?php echo (int)$sup['id'] ?>">
                    <?php echo $sup['sup_name'] ?></option>
                  <?php endforeach; ?>
                </select>
             </div>
           </div>


           <div class="form-group">
            <div class="form-group"> <label>Reference of Requisition</label>
              <textarea class="form-control" id="exampleFormControlTextarea1" name="reference-of-requisition" rows="3"></textarea>
            </div>
           </div> 








              <div class="form-group">
                <div class="row">
                  <div class="col-md-12">

                    <button type="submit" name="add_grn" id="add" class="btn btn-success">Save</button> 
                  </div>

                </div>


              </div>


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