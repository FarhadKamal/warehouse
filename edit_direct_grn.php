
<link rel="stylesheet" href="libs/css/flexselect.css" type="text/css" media="screen" />

<?php


$page_title = 'GRN';
require_once('includes/load.php');
            // Checkin What level user has permission to view this page


page_require_level(4);
$all_products = find_by_sql('select * from products order by short_code');


$com_id = find_value_by_sql(" select com from users where id=".$_SESSION['user_id'])['com'];
$all_locations = find_by_sql('select * from  locations where com='.$com_id.' order by loc_name');


$all_sup = find_by_sql('select * from  suppliers order by sup_name');
$last_update_date=date('Y-m-d H:i:s');
$grn_id=(int)$_GET['id'];

$grn = find_grn_edit($grn_id);

$com_name = find_value_by_sql(" select com_name from company  where com_id=".$grn['com'])['com_name'];

$all_requisition_details = find_by_sql('select grn_details.*,products.id as pid, products.name,
  unit_name,unit_type,short_code from grn_details 
  inner join grn on grn_details.grn_id=grn.id
  inner join products on products.id=grn_details.product_id 
  inner join units on units.id=products.unit_id 
  where grn.id="'.$grn_id.'" and grn.submit_status=0
  order by short_code');

if(!$grn){
  $session->msg("d","Missing GRN No.");
  redirect('grn.php');
}
?>
<?php




if(isset($_POST['final_submit'])){


 $submit_by= $_SESSION['user_id'];

 $loc_id    = remove_junk($db->escape($_POST['location']));

 $com_id=$grn['com'];

 $query   = "UPDATE grn SET";
 $query  .=" grn_by ='{$submit_by}',grn_date =now(),submit_status =1";

 $query  .=" WHERE id ='{$grn_id}'";

 if($db->query($query)){
   $session->msg('s',' GRN submitted!');



   $query  ="insert into stock(
   ref_no,ref_source,product_id,stock_qty,stock_price,stock_by,stock_type,loc_id,com)


   (select   grn_id,'GRN', product_id,quantity,price,'{$submit_by}','receive','{$loc_id}','{$com_id}'   from grn_details where grn_id='{$grn_id}' and quantity>0) ";

   $db->query($query);





   redirect('grn.php', false);
 }else {
   $session->msg('d','GRN submission failed');
   redirect('grn.php', false);
 }



} 







if(isset($_POST['add_item'])){

  $product_id          = remove_junk($db->escape($_POST['product-name']));
  $product_quantity    = remove_junk($db->escape($_POST['product-quantity']));
  $price               = remove_junk($db->escape($_POST['product-price']));
  $submit_by           = $_SESSION['user_id'];

  $req_fields = array('product-name' );
  validate_fields($req_fields);
  if($errors){
    $session->msg("d", $errors);
    redirect('edit_direct_grn.php?id='.$grn['id'], false);

  }

  if($product_quantity==0){
    $session->msg("d", "product quantity cannot be zero!");
    redirect('edit_direct_grn.php?id='.$grn['id'], false);
  }

  $track_id=$grn['id'];









  $db->query("delete from grn_details where grn_id={$track_id} and product_id={$product_id}");
  $query  = "INSERT INTO grn_details (";
  $query .=" grn_id, product_id, quantity,price";
  $query .=") VALUES (";
  $query .=" '{$track_id}', '{$product_id}', '{$product_quantity}', '{$price}'";
  $query .=")";


  if($db->query($query)){
    $session->msg('s',"Product added!");
    redirect('edit_direct_grn.php?id='.$grn['id'], false);
  }else {
   $session->msg('d',' Sorry failed to add product!');
   redirect('edit_direct_grn.php?id='.$grn['id'], false);
 }

} 






if(isset($_POST['edit_grn'])){



  $req_fields = array('location','supplier','reference-of-requisition');
  validate_fields($req_fields);

  if(empty($errors)){




    $submit_by= $_SESSION['user_id'];


    $loc_id    = remove_junk($db->escape($_POST['location']));

    $sup_id    = remove_junk($db->escape($_POST['supplier']));

    $req_ref    = remove_junk($db->escape($_POST['reference-of-requisition']));









    $query   = "UPDATE grn SET";
    $query  .=" loc_id ='{$loc_id}',sup_id ='{$sup_id}',grn_date =now(),req_ref ='{$req_ref}'";

    $query  .=" WHERE id ='{$grn_id}'";
    $result = $db->query($query);
    if($result && $db->affected_rows() === 1){




     $session->msg('s',"GRN updated ");
     redirect('edit_direct_grn.php?id='.$grn['id'], false);
   } else {
     $session->msg('d',' Sorry failed to updated!');
     redirect('edit_direct_grn.php?id='.$grn['id'], false);
   }

 } else{
   $session->msg("d", $errors);
   redirect('edit_direct_grn.php?id='.$grn['id'], false);
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
  <div class="panel panel-default">

    <div class="form-group">
      <label>Company</label>
      <div class="input-group">
        <span class="input-group-addon">
          <i class="glyphicon glyphicon-map-marker"></i>
        </span>
        <input type="text" class="form-control" name="company-name" value="<?php echo remove_junk($com_name); ?>">
      </div>
    </div>


    <div class="panel-heading">
      <strong>
        <span class="glyphicon glyphicon-th"></span>
        <span>GRN No: <?php echo (int)$grn['id'] ?></span>
      </strong>
    </div>



    <div class="panel-body">
     <div class="col-md-12">
       <form method="post" action="edit_direct_grn.php?id=<?php echo (int)$grn['id'] ?>">

         <div class="form-group">
          <label>GRN Date</label>
          <div class="input-group">
            <span class="input-group-addon">
             <i class="glyphicon glyphicon-calendar"></i>
           </span>

           <input  type="text"  style="width: 300px;"
           value="<?php echo  remove_junk($grn['grn_date']); ?>"  name="grn-date" readonly>
         </div>
       </div>




       <br/>

       <div class="form-group"> <label>Stock Location</label>
        <div class="input-group">

          <span class="input-group-addon">
           <i class="glyphicon glyphicon-flag"></i>
         </span>
         <select class="form-control"  name="location"  style="width: 300px;" >
          <option value="">Select Location</option>
          <?php  foreach ($all_locations as $loc): ?>
            <option value="<?php echo (int)$loc['id'] ?>"

              <?php if($grn['loc_id'] === $loc['id']): echo "selected"; endif; ?> 
              >
              <?php echo $loc['loc_name'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-group"> <label>Supplier</label>
        <div class="input-group">

          <span class="input-group-addon">
           <i class="glyphicon glyphicon-flag"></i>
         </span>
         <select class="form-control"  name="supplier" style="width: 300px;" >
          <option value="">Select Location</option>
          <?php  foreach ($all_sup as $sup): ?>
            <option value="<?php echo (int)$sup['id'] ?>"

              <?php if($grn['sup_id'] === $sup['id']): echo "selected"; endif; ?> 
              >
              <?php echo $sup['sup_name'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-group">
          <div class="form-group"> <label>Reference of Requisition</label>
            <textarea class="form-control"  name="reference-of-requisition" rows="3"><?php echo remove_junk($grn['req_ref']); ?></textarea>
          </div>
        </div>


      <button type="submit" name="edit_grn" class="btn btn-info">Update</button>
    </form> 
    <br/>

    <form method="post" action="edit_direct_grn.php?id=<?php echo (int)$grn['id'] ?>">
      <div class="form-group">
        <div class="row">
          <div class="col-md-12">
            <label >Product</label>


            <select class="form-control" id="product-id" name="product-name"  style="width: 500px;" >
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

      <div class="panel-body">
        <table class="table table-bordered table-striped table-hover">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Short Code</th>
              <th style="width: 400px;">Products</th>
              <th>Req. Qty</th>
              <th>Price</th>
              <th style="width: 100px;">Unit</th>
              <th>Action</th>

            </tr>
          </thead>
          <tbody>
            <?php 


            foreach ($all_requisition_details as $reqdetails): 

              ?>
              <tr>
                <td class="text-center"><?php echo count_id();?></td>
                <td><?php echo $reqdetails['short_code']; ?></td>
                <td><?php echo $reqdetails['name']; ?></td>
                <td><?php if($reqdetails['unit_type']=='number') echo intval($reqdetails['quantity']); else echo  $reqdetails['quantity']; ?></td>
                <td><?php echo $reqdetails['price']; ?></td>




                <td><?php echo $reqdetails['unit_name']; ?></td>

                <td class="text-center">
                  <div class="btn-group">
                    <a href="delete_direct_grn.php?id=<?php echo (int)$reqdetails['grn_id'];?>&det=<?php echo (int)$reqdetails['id']; ?>"  class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
                      <span class="glyphicon glyphicon-trash"></span>
                    </a>
                  </div>
                </td>





              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>




      </div> 
      
      <?php 



      $current_user = current_user();

      $login_level = $current_user['user_level'];



      ?>
      <form method="post" action="edit_direct_grn.php?id=<?php echo $grn_id;  ?>">
        <input  type="hidden" name="location"
        value="<?php echo  remove_junk($grn['loc_id']); ?>" />

        <button type="submit" name="final_submit" class="btn btn-danger"
        onclick="return confirm('Are you sure you want to receive?');"

        >Receive</button>



      </form> 








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
        $.post('modal.php?call=4', {

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