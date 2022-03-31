    <link rel="stylesheet" href="libs/css/flexselect.css" type="text/css" media="screen" />
    <?php
    $page_title = 'Requisition Seperation';
    require_once('includes/load.php');
    // Checkin What level user has permission to view this page
    page_require_level(4);
    $req_id = (int)$_GET['id'];
    $req = find_by_id('requisition',$req_id);
    if(!$req){
      $session->msg("d","Missing Requisition No.");
      redirect('req_sep.php', false);
    }
    $requisition = get_requisition_by_id($req_id);
    $all_assigned = find_by_sql('select * from assigned_person order by person_name');
    



      ?>
      <?php



      if(isset($_POST['add_sep'])){




        $req_fields = array('assigned-person' );
        validate_fields($req_fields);
        if(empty($errors)){


          $person_name    = remove_junk($db->escape($_POST['assigned-person']));
          $chk=0;
          if(isset($_POST['reqdetid'])){
            $reqsel=$_POST['reqdetid'];
            for ($i=0;$i<count($reqsel);$i++) {
              $query="update requisition_details set assigned='".$person_name."' where id=".$reqsel[$i];
              $db->query($query);
              $chk=1;
            }

          }
          if($chk==1)
          {
            $session->msg('s',' Assigned!');
            redirect('req_sep_process.php?id='.$req_id, false);
          }

        } else {
         $session->msg('d',' Please select assigned-person!');
         redirect('req_sep_process.php?id='.$req_id, false);
       }





     }


     $all_requisition_details = find_by_sql('select short_code,products.name,unit_type,unit_name,quantity,assigned ,requisition_details.id,products.id as pid,req_id from requisition_details 
      inner join products on requisition_details.product_id=products.id
      inner join units on units.id=products.unit_id
      where req_id='.$req_id.' order by id desc');

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


            <div class="panel-heading">
              <strong>
                <span class="glyphicon glyphicon-tag"></span>
                <span>Requisition No: <?php echo (int)$requisition['id'] ?></span>
              </strong>
            </div>

            <div class="panel-heading">
              <strong>
                <span class="glyphicon glyphicon-user"></span>
                <span>Claimer : <?php echo $requisition['claimer'] ?> [<?php echo $requisition['udes'] ?>]</span>
              </strong>
            </div>



            <div class="panel-heading">
              <strong>
                <span class="glyphicon glyphicon-calendar"></span>
                <span>Expected Date:</span>
              </strong>
              <?php echo date ('F j, Y', strtotime($requisition['expected_date']));  ?>
            </div>


            <div class="panel-heading">
              <strong>
                <span class="glyphicon glyphicon-user"></span>
                <span>Contact Person: </span>
              </strong>
              <?php echo $requisition['contact_person'] ?>
            </div>

            <div class="panel-heading">
              <strong>
                <span class="glyphicon glyphicon-edit"></span>
                <span>Reason: </span>
              </strong>
              <?php echo $requisition['request_reason'] ?>
            </div>



            <form method="post" action="req_sep_process.php?id=<?php echo $req_id; ?>" class="clearfix">


              <table class="table table-bordered table-striped table-hover ">
                <thead>
                  <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th style="width: 100px;">Short Code</th>
                    <th >Products</th>
                    <th style="width: 100px;">Quantity</th>
                    <th style="width: 100px;">Unit</th>
                    <th style="width: 100px;">Assigned</th>
                    <th style="width: 100px;">Action</th>

                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($all_requisition_details as $reqdetails):?>
                    <tr>
                      <td class="text-center"><?php echo count_id();?></td>
                      <td><?php echo $reqdetails['short_code']; ?></td>
                      <td><?php echo $reqdetails['name']; ?></td>
                      <td>

                        <?php if($reqdetails['unit_type']=='number') echo intval($reqdetails['quantity']); else echo  $reqdetails['quantity']; ?></td>
                        <td><?php echo $reqdetails['unit_name']; ?></td>
                        <td><?php echo $reqdetails['assigned']; ?></td>
                        <td><input type="checkbox"  name="reqdetid[]" value="<?php echo $reqdetails['id']; ?>"></td>

                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>


                <div class="form-group"> <label>Assigned Person</label>
                  <div class="input-group">

                    <span class="input-group-addon">
                     <i class="glyphicon glyphicon-tag"></i>
                   </span>
                   <select class="form-control" id="assigned-person"  name="assigned-person" style="width: 300px;">
                    <option value="">Select Assigned Person</option>
                    <?php  foreach ($all_assigned as $asd): ?>
                      <option value="<?php echo $asd['person_name'] ?>">
                        <?php echo $asd['person_name'] ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>

                <button type="submit" name="add_sep" id="add" class="btn btn-success">Save</button>



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