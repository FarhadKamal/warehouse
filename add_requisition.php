    <link rel="stylesheet" href="libs/css/flexselect.css" type="text/css" media="screen" />
    <?php
    $page_title = 'New Requisition';
    require_once('includes/load.php');
    // Checkin What level user has permission to view this page
    page_require_level(4);
    $all_products = find_by_sql('select * from products order by short_code');
    $com_id = find_value_by_sql(" select com from users where id=".$_SESSION['user_id'])['com'];

    ?>
    <?php

    
    
    if(isset($_POST['add_requisition'])){
      
      

      $expected_date   = remove_junk($db->escape($_POST['exp-date']));

  

      if(expected_date($expected_date)==false)
       {
          $session->msg("d", "Expected date should be at least 7 days greater than current date!");
          redirect('add_requisition.php',false);

       } 

      $req_fields = array('contact-person','product-name' );
      validate_fields($req_fields);
      if(empty($errors)){








        $product_id    = remove_junk($db->escape($_POST['product-name']));
        $product_quantity    = remove_junk($db->escape($_POST['product-quantity']));

        $contact_person    = remove_junk($db->escape($_POST['contact-person']));
        $request_reason   = remove_junk($db->escape($_POST['reason']));
        $submit_by= $_SESSION['user_id'];




        if($product_quantity==0){
          $session->msg("d", "product quantity cannot be zero!");
          redirect('add_requisition.php',false);
        }


        $date    = make_date();
        $query  = "INSERT INTO requisition (";
        $query .=" contact_person,request_reason,submit_by,expected_date,com";
        $query .=") VALUES (";
        $query .=" '{$contact_person}', '{$request_reason}', '{$submit_by}', '{$expected_date}', '{$com_id}'";
        $query .=")";
       //$query .=" ON DUPLICATE KEY UPDATE name='{$p_name}'";
        if($db->query($query)){
         $session->msg('s',"Requisition saved!");


         $track_id = find_value_by_sql(" select max(id)  as track_id from requisition where submit_by='{$submit_by}'  ")['track_id'];


         $query  = "INSERT INTO requisition_details (";
         $query .=" req_id, product_id, quantity";
         $query .=") VALUES (";
         $query .=" '{$track_id}', '{$product_id}', '{$product_quantity}'";
         $query .=")";
         $db->query($query);
         redirect('edit_requisition.php?id='.$track_id, false);
       } else {
         $session->msg('d',' Sorry failed to save requisition!');
         redirect('add_requisition.php', false);
       }

     } else{
       $session->msg("d", $errors);
       redirect('add_requisition.php',false);
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
            <span>Requisition</span>
          </strong>
        </div>
        <div class="panel-body">
         <div class="col-md-12">
          <form method="post" action="add_requisition.php" class="clearfix">
            
            <div class="form-group"> <label>Expected Date</label>
              <div class="input-group">

                <span class="input-group-addon">
                 <i class="glyphicon glyphicon-calendar"></i>
               </span>
               <?php    
              $expdate = strtotime(date("Y-m-d"));
              $expdate = strtotime("+7 day", $expdate);



               ?>
               <input  type="text" class="datepicker form-control " style="width: 150px;"
                 value="<?php echo  date('Y-m-d', $expdate); ?>"  name="exp-date" placeholder="Date" readonly>
             </div>
           </div> 


            <div class="form-group"> <label>Contact Person Name</label>
              <div class="input-group">

                <span class="input-group-addon">
                 <i class="glyphicon glyphicon-user"></i>
               </span>
               <input type="text" class="form-control" name="contact-person" placeholder="Contact Person Name">
             </div>
           </div>



           <div class="form-group">
            <div class="form-group"> <label>Reason</label>
              <textarea class="form-control" id="exampleFormControlTextarea1" name="reason" rows="3"></textarea>
            </div>
           </div> 




            <div class="form-group">
              <div class="row">
                <div class="col-md-12">
                  <label >Product</label>
                  <select class="form-control" id="product-id" name="product-name" >
                    <option value="">Select Product</option>
                    <?php  foreach ($all_products as $prd): ?>
                      <option value="<?php echo (int)$prd['id'] ?>">
                        <?php echo $prd['short_code'] ?> <?php echo " # " ?><?php echo $prd['name'] ?></option>
                      <?php endforeach; ?>
                    </select>
                    
                  </div>

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

      $("#product-id").flexselect();

      $( "#product-id" ).change(function() {

        var id = $('#product-id').val();
        
        

        if(id!='')
        { 
          $.post('modal.php?call=2', {

            'pid': id,

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