    <link rel="stylesheet" href="libs/css/flexselect.css" type="text/css" media="screen" />
    <?php
    $page_title = 'New GRN';
    require_once('includes/load.php');
    // Checkin What level user has permission to view this page
    page_require_level(4);
    $all_products = find_by_sql('select * from products order by short_code');

    $com_id = find_value_by_sql(" select com from users where id=".$_SESSION['user_id'])['com'];

    $all_requisitions = find_by_sql('select * from requisition where approve_status=1 and submit_status=1 and cancel_status=0 and complete_status=0 and com='.$com_id );

    $all_locations = find_by_sql('select * from  locations where com='.$com_id.' order by loc_name');


    $all_sup = find_by_sql('select * from  suppliers order by sup_name');

    ?>
    <?php

    
    
    if(isset($_POST['add_grn'])){
      
      

      

  

    

      $req_fields = array('requisition-id','location','supplier' );
      validate_fields($req_fields);
      if(empty($errors)){


        $req_id    = remove_junk($db->escape($_POST['requisition-id']));
        $loc_id    = remove_junk($db->escape($_POST['location']));
        $sup_id    = remove_junk($db->escape($_POST['supplier']));



        $submit_by= $_SESSION['user_id'];




        $date    = make_date();
        $query  = "INSERT INTO grn (";
        $query .=" req_id,loc_id,grn_by,grn_date,sup_id,com";
        $query .=") VALUES (";
        $query .=" '{$req_id}', '{$loc_id}', '{$submit_by}', now(), '{$sup_id}', '{$com_id}'";
        $query .=")";
       //$query .=" ON DUPLICATE KEY UPDATE name='{$p_name}'";
        if($db->query($query)){
         $session->msg('s',"GRN saved!");


         $track_id = find_value_by_sql(" select max(id)  as track_id from grn where grn_by='{$submit_by}'  ")['track_id'];


         $query="insert into grn_details 
          (
          grn_id, 
          product_id, 
          quantity,
          price
          )
          
          (
            select  grn.id,product_id,quantity-received,0.00 from grn
            inner join requisition on requisition.id=grn.req_id
            inner join requisition_details on requisition_details.req_id=requisition.id
            where grn.id={$track_id} and (requisition_details.quantity-received)>0 
          )"; 

          $db->query($query); 


     
         redirect('edit_grn.php?id='.$track_id, false);
       } else {
         $session->msg('d',' Sorry failed to save GRN!');
         redirect('add_grn.php', false);
       }

     } else{
       $session->msg("d", $errors);
       redirect('add_grn.php',false);
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
          <form method="post" action="add_grn.php" class="clearfix">
            
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



            <div class="form-group"> <label>Requisition ID</label>
              <div class="input-group">

                <span class="input-group-addon">
                 <i class="glyphicon glyphicon-tag"></i>
               </span>
                <select class="form-control" id="requisition-id"  name="requisition-id" >
                <option value="">Select Requisition No</option>
                <?php  foreach ($all_requisitions as $req): ?>
                  <option value="<?php echo (int)$req['id'] ?>">
                    <?php echo $req['id'] ?></option>
                  <?php endforeach; ?>
                </select>
             </div>
           </div>






              <div class="form-group">
                <div class="row">
                  <div class="col-md-12">

                    <div id="div-result">

                    </div>  
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

      $( "#requisition-id" ).change(function() {

        var id = $('#requisition-id').val();
        
        //alert(id);

        if(id!='')
        { 
          $.post('modal.php?call=3', {

            'req': id,

          },

          function(result) {

            if (result) {


             $("#div-result").empty();
             $("#div-result").append(result);

           }
         }
         );

        }else $('#prev-short-code').val("");

      });



    });
  </script>
  <script src="libs/js/liquidmetal.js" type="text/javascript"></script>
  <script src="libs/js/jquery.flexselect.js" type="text/javascript"></script>