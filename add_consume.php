    <link rel="stylesheet" href="libs/css/flexselect.css" type="text/css" media="screen" />
    <?php
    $page_title = 'New Issue';
    require_once('includes/load.php');
    // Checkin What level user has permission to view this page
    page_require_level(4);
   


    $com_id = find_value_by_sql(" select com from users where id=".$_SESSION['user_id'])['com'];

    $all_locations = find_by_sql('select * from  locations where com='. $com_id .' order by loc_name');

    $all_cost = find_by_sql('select * from  cost_centre where com='. $com_id .' order by cost_name');

    ?>
    <?php

    
    
    if(isset($_POST['add_consume'])){
      

      $req_fields = array('receiver-name','location','cost-centre-name' );
      validate_fields($req_fields);
      if(empty($errors)){


  
        $loc_id    = remove_junk($db->escape($_POST['location']));
        $cost_id    = remove_junk($db->escape($_POST['cost-centre-name']));
        $consumer    = remove_junk($db->escape($_POST['receiver-name']));
        $remarks    = remove_junk($db->escape($_POST['remarks']));


        $submit_by= $_SESSION['user_id'];




        $date    = make_date();
        $query  = "INSERT INTO consume (";
        $query .=" loc_id,con_by,con_date,consumer,remarks,cost_id,com";
        $query .=") VALUES (";
        $query .=" '{$loc_id}', '{$submit_by}', now(), '{$consumer}', '{$remarks}', '{$cost_id}', '{$com_id}'";
        $query .=")";
       //$query .=" ON DUPLICATE KEY UPDATE name='{$p_name}'";
        if($db->query($query)){
         $session->msg('s',"Issue saved!");


         $track_id = find_value_by_sql(" select max(id)  as track_id from consume where con_by='{$submit_by}'  ")['track_id'];


        


     
         redirect('edit_consume.php?id='.$track_id, false);
       
       } else {
         $session->msg('d',' Sorry failed to save Issue!');
         redirect('add_consume.php', false);
       }

     } else{
       $session->msg("d", $errors);
       redirect('add_consume.php',false);
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
            <span>Issue</span>
          </strong>
        </div>
        <div class="panel-body">
         <div class="col-md-12">
          <form method="post" action="add_consume.php" class="clearfix">
            
            <div class="form-group"> <label>Issue Date</label>
              <div class="input-group">

                <span class="input-group-addon">
                 <i class="glyphicon glyphicon-calendar"></i>
               </span>
               <?php    
              $idate = strtotime(date("Y-m-d H:i:s"));




               ?>
               <input  type="text" class="form-control" name="issuedate" style="width: 300px;"
                 value="<?php echo  date('Y-m-d H:i:s', $idate); ?>"  readonly>
             </div>
           </div> 

            <div class="form-group"> <label>Receiver Name</label>
              <div class="input-group">

                <span class="input-group-addon">
                 <i class="glyphicon glyphicon-user"></i>
               </span>
               <input type="text" class="form-control" name="receiver-name" placeholder="Receiver Name">
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


            <div class="form-group"> <label>Cost Centre</label>
              <div class="input-group">

                <span class="input-group-addon">
                 <i class="glyphicon glyphicon-flag"></i>
               </span>
                <select class="form-control"  name="cost-centre-name"  id="cost_id">
                <option value="">Select Cost Centre</option>
                <?php  foreach ($all_cost as $cost): ?>
                  <option value="<?php echo (int)$cost['id'] ?>">
                    <?php echo $cost['cost_name'] ?><?php echo " # " ?><?php echo $cost['id'] ?></option>
                  <?php endforeach; ?>
                </select>
             </div>
           </div>


          <div class="form-group">
            <div class="form-group"> <label>Remarks</label>
              <textarea class="form-control" id="exampleFormControlTextarea1" name="remarks" rows="3"></textarea>
            </div>
           </div> 



        


           <button type="submit" name="add_consume" id="add" class="btn btn-success">Save</button>



            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include_once('layouts/footer.php'); ?>
  <script>
    $(document).ready(function() {

       $("#cost_id").flexselect();

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