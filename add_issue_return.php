    <link rel="stylesheet" href="libs/css/flexselect.css" type="text/css" media="screen" />
    <?php
    $page_title = 'New Issue Return';
    require_once('includes/load.php');
    // Checkin What level user has permission to view this page
    page_require_level(4);
   
    $com_id = find_value_by_sql(" select com from users where id=".$_SESSION['user_id'])['com'];
  
    $all_returns = find_by_sql('select * from consume where  submit_status=1 and cancel_status=0  and com='.$com_id.'  order by id desc limit 100');

    ?>
    <?php

    
    
    if(isset($_POST['add_issue_return'])){
      

      $req_fields = array('issue-no');
      validate_fields($req_fields);
      if(empty($errors)){


  
        $con_id    = remove_junk($db->escape($_POST['issue-no']));
  
        $remarks    = remove_junk($db->escape($_POST['remarks']));


        $submit_by= $_SESSION['user_id'];




        $date    = make_date();
        $query  = "INSERT INTO consume_return (";
        $query .=" con_id,rtn_by,rtn_date,remarks,com";
        $query .=") VALUES (";
        $query .=" '{$con_id}', '{$submit_by}', now(), '{$remarks}', '{$com_id}'";
        $query .=")";
       //$query .=" ON DUPLICATE KEY UPDATE name='{$p_name}'";
        if($db->query($query)){
         $session->msg('s',"Issue Return saved!");


         $track_id = find_value_by_sql(" select max(id)  as track_id from consume_return where rtn_by='{$submit_by}'  ")['track_id'];


        


     
         redirect('edit_issue_return.php?id='.$track_id, false);
         //redirect('add_issue_return.php', false);
       
       } else {
         $session->msg('d',' Sorry failed to save Issue Return!');
         redirect('add_issue_return.php', false);
       }

     } else{
       $session->msg("d", $errors);
       redirect('add_issue_return.php',false);
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
            <span>Issue Return</span>
          </strong>
        </div>
        <div class="panel-body">
         <div class="col-md-12">
          <form method="post" action="add_issue_return.php" class="clearfix">
            
            <div class="form-group"> <label>Return Date</label>
              <div class="input-group">

                <span class="input-group-addon">
                 <i class="glyphicon glyphicon-calendar"></i>
               </span>
               <?php    
              $idate = strtotime(date("Y-m-d H:i:s"));




               ?>
               <input  type="text" class="form-control" style="width: 300px;"
                 value="<?php echo  date('Y-m-d H:i:s', $idate); ?>"  readonly>
             </div>
           </div> 

            <div class="form-group"> <label>Issue No</label>
              <div class="input-group">

                <span class="input-group-addon">
                 <i class="glyphicon glyphicon-tag"></i>
               </span>
                <select class="form-control" id="issue-no"  name="issue-no" >
                <option value="">Select Issue No</option>
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



        


           <button type="submit" name="add_issue_return" id="add" class="btn btn-success">Save</button>



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