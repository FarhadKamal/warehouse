    <link rel="stylesheet" href="libs/css/flexselect.css" type="text/css" media="screen" />
    <?php
    $page_title = 'Requisition Seperation';
    require_once('includes/load.php');
    // Checkin What level user has permission to view this page
    page_require_level(4);


    $all_requisitions = find_by_sql('select * from requisition where  submit_status=1 and cancel_status=0 order by id desc limit 50');



    ?>
    <?php

    
    
    if(isset($_POST['add_grn'])){
      
      $req_fields = array('requisition-id' );
      validate_fields($req_fields);
      if(empty($errors)){


        $req_id    = remove_junk($db->escape($_POST['requisition-id']));
    



     
         redirect('req_sep_process.php?id='.$req_id, false);
       } else {
         $session->msg('d',' Sorry failed to select requisition-id!');
         redirect('req_sep.php', false);
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
            <span>Requisition Seperation</span>
          </strong>
        </div>
        <div class="panel-body">
         <div class="col-md-12">
          <form method="post" action="req_sep.php" class="clearfix">
            

            <div class="form-group"> <label>Requisition ID</label>
              <div class="input-group">

                <span class="input-group-addon">
                 <i class="glyphicon glyphicon-tag"></i>
               </span>
                <select class="form-control" id="requisition-id"  name="requisition-id" style="width: 300px;">
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