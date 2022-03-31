
<link rel="stylesheet" href="libs/css/flexselect.css" type="text/css" media="screen" />

<?php


$page_title = 'GRN Return';
require_once('includes/load.php');
              // Checkin What level user has permission to view this page


page_require_level(4);

$last_update_date=date('Y-m-d H:i:s');
$rtn_id=(int)$_GET['id'];
$grn_return = find_by_id('grn_return',$rtn_id);





if(!$grn_return){
  $session->msg("d","Missing Return No.");
  redirect('grn_return.php');
}
$grn = find_by_id('grn',$grn_return['grn_id']);


$all_products = find_by_sql('select products. *,(quantity-grn_return)  as tot from grn_details 
  inner join products   on products.id=grn_details.product_id
  where  grn_id='.$grn['id'].' and (quantity-grn_return)>0
  order by short_code ');






$locations= find_by_id('locations',$grn['loc_id']);

$all_grn_details = find_by_sql('select grn_return_details.*,products.id as pid, products.name,
  unit_name,unit_type,short_code from grn_return_details 
  inner join grn_return on grn_return_details.rtn_id=grn_return.id
  inner join products on products.id=grn_return_details.product_id 
  inner join units on units.id=products.unit_id 
  where grn_return.id="'.$grn_return['id'].'" and grn_return.submit_status=0
  order by short_code');


  ?>
  <?php

  if(isset($_POST['final_submit'])){


   $submit_by= $_SESSION['user_id'];
   $track_id=$grn_return['id'];
   $loc_id=$grn['loc_id'];
   $com_id=$grn['com'];

  

   foreach ($all_grn_details as $issdetails): 

    $schk=find_value_by_sql('select (quantity-grn_return) as tot,products.name  from grn_details 

      inner join products on products.id=grn_details.product_id
      where product_id='.$issdetails['product_id'].' and  grn_id='.$grn['id']);

    if($schk['tot']<$issdetails['quantity'] )
    {
      $qty=$issdetails['quantity'];
      if($issdetails['unit_type']=='number')$qty=intval($qty);
      $session->msg('d',$schk['name']." qty: [".$qty."] did not receive for GRN");
      redirect('edit_issue_return.php?id='.$track_id, false);

    }

  endforeach;



  $query   = "UPDATE grn_return SET";
  $query  .=" rtn_by ='{$submit_by}',rtn_date =now(),submit_status =1";

  $query  .=" WHERE id ='{$track_id}'";

  if($db->query($query)){
   $session->msg('s',' GRN Return submitted!');

   

   $query  ="insert into stock(
   ref_no,ref_source,product_id,stock_qty,stock_price,stock_by,stock_type,loc_id,com)
   (select   rtn_id,'GRN Return', product_id,-1*quantity,
   -1*quantity*(select round(sum(stock.stock_price)/sum(stock.stock_qty),2) from stock where stock.product_id=grn_return_details.product_id),'{$submit_by}','issue','{$loc_id}','{$com_id}'   
   from grn_return_details where rtn_id='{$track_id}' and quantity>0) ";

   $db->query($query);


   $query  ="update
   grn_return_details
   inner join grn_return on grn_return.id=grn_return_details.rtn_id
   inner join grn on grn.id=grn_return.grn_id
   inner join grn_details on(grn_details.product_id=grn_return_details.product_id
   and grn_details.grn_id=grn.id)
   set grn_details.grn_return=grn_details.grn_return+grn_return_details.quantity
   where grn_return.id='{$track_id}'";

   $db->query($query);





   

   redirect('grn_return.php', false);
 }else {
   $session->msg('d','GRN Return submission failed');
   redirect('grn_return', false);
 }
}









if(isset($_POST['add_requisition'])){

  $product_id          = remove_junk($db->escape($_POST['product-name']));
  $product_quantity    = remove_junk($db->escape($_POST['product-quantity']));
  $submit_by= $_SESSION['user_id'];

  $req_fields = array('product-name' );
  validate_fields($req_fields);
  if($errors){
    $session->msg("d", $errors);
    redirect('edit_grn_return.php?id='.$grn_return['id'], false);

  }

  if($product_quantity<=0){
    $session->msg("d", "product quantity cannot be zero or negative!");
    redirect('edit_grn_return.php?id='.$grn_return['id'], false);
  }

  $track_id=$grn_return['id'];

  $loc_id=$grn['loc_id'];





  $schk=find_value_by_sql('select (quantity-grn_return) as tot,products.name  from grn_details 

    inner join products on products.id=grn_details.product_id
    where product_id='.$product_id.' and  grn_id='.$grn['id']);

  if($schk['tot']<$product_quantity )
  {


    $session->msg('d',$schk['name']." qty: [".$product_quantity."] did not receive for GRN");
    redirect('edit_grn_return.php?id='.$grn_return['id'], false);

  }




  $db->query("delete from grn_return_details where rtn_id={$track_id} and product_id={$product_id}");
  $query  = "INSERT INTO grn_return_details (";
  $query .=" rtn_id, product_id, quantity";
  $query .=") VALUES (";
  $query .=" '{$track_id}', '{$product_id}', '{$product_quantity}'";
  $query .=")";


  if($db->query($query)){
    $session->msg('s',"Product added!");
    redirect('edit_grn_return.php?id='.$grn_return['id'], false);
  }else {
   $session->msg('d',' Sorry failed to add product!');
   redirect('edit_grn_return.php?id='.$grn_return['id'], false);
 }

} 



if(isset($_POST['edit_grn_return'])){



  $req_fields = array('rtn-date');
  validate_fields($req_fields);

  if(empty($errors)){



    $remarks    = remove_junk($db->escape($_POST['remarks']));


    $submit_by= $_SESSION['user_id'];

    $query   = "UPDATE grn_return SET";
    $query  .=" rtn_by ='{$submit_by}',remarks ='{$remarks}',rtn_date =now()";

    $query  .=" WHERE id ='{$rtn_id}'";
    $result = $db->query($query);
    if($result && $db->affected_rows() === 1){

      $session->msg('s',"GRN Return updated ");
      redirect('edit_grn_return.php?id='.$grn_return['id'], false);
    } else {
     $session->msg('d',' Sorry failed to GRN Return!');
     redirect('edit_grn_return.php?id='.$grn_return['id'], false);
   }

 } else{
   $session->msg("d", $errors);
   redirect('edit_grn_return.php?id='.$grn_return['id'], false);
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
    <div class="panel-heading">
      <strong>
        <span class="glyphicon glyphicon-th"></span>
        <span>GRN Return No: <?php echo $grn_return['id']; ?></span>
      </strong>
    </div>



    <div class="panel-body">
     <div class="col-md-12">
       <form method="post" action="edit_grn_return.php?id=<?php echo $grn_return['id'] ?>">

         <div class="form-group">
          <label>Return Date</label>
          <div class="input-group">
            <span class="input-group-addon">
             <i class="glyphicon glyphicon-calendar"></i>
           </span>

           <input  type="text"  style="width: 300px;"
           value="<?php echo  remove_junk($grn_return['rtn_date']); ?>"  name="rtn-date" readonly>
         </div>
       </div>



       <br/>
       <div class="form-group"> <label>GRN No</label>
        <div class="input-group">

          <span class="input-group-addon">
           <i class="glyphicon glyphicon-tag"></i>
         </span>
         <input  
         value="<?php echo  remove_junk($grn_return['grn_id']); ?>"  readonly>
         <a  target="_blank" href="view_grn.php?id=<?php echo (int)$grn_return['grn_id'];?>" class="btn btn-info btn-xs"  title="View"data-toggle="tooltip">
          <span class="glyphicon glyphicon-eye-open"></span>
        </a>

      </div>
    </div>


    <br/>
    <div class="form-group"> <label>Stock Location</label>
      <div class="input-group">

        <span class="input-group-addon">
         <i class="glyphicon glyphicon-flag"></i>
       </span>
       <input  
       value="<?php echo  remove_junk($locations['loc_name']); ?>"  readonly>

     </div>
   </div>


   <div class="form-group">
    <div class="form-group"> <label>Remarks</label>
      <textarea class="form-control" id="exampleFormControlTextarea1" name="remarks" 
      rows="3"><?php echo  remove_junk($grn_return['remarks']); ?></textarea>
    </div>
  </div> 
  <button type="submit" name="edit_grn_return" id="add" class="btn btn-success">Save</button>
</form> 
<br/>

<form method="post" action="edit_grn_return.php?id=<?php echo (int)$grn_return['id'] ?>">
  <div class="form-group">
    <div class="row">
      <div class="col-md-12">
        <label >Product</label>

        
        <select class="form-control" id="product-id" name="product-name"  style="width: 500px;" >
          <option value="">Select Product</option>
          <?php  foreach ($all_products as $prd): ?>
            <option value="<?php echo (int)$prd['id'] ?>">
              <?php echo $prd['short_code'] ?> <?php echo " # " ?><?php echo $prd['name'] ?><?php echo "&nbsp;&nbsp;&nbsp;[".$prd['tot']."]" ?></option>
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
          <th>Return Qty</th>
          <th style="width: 100px;">Unit</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php 


        foreach ($all_grn_details as $issdetails): 
          $qty=$issdetails['quantity'];
          if($issdetails['unit_type']=='number') $qty=intval($qty);

          ?>
          <tr>
            <td class="text-center"><?php echo count_id();?></td>
            <td><?php echo $issdetails['short_code']; ?></td>
            <td><?php echo $issdetails['name']; ?></td>            
            <td><?php echo $qty; ?></td>
            <td><?php echo $issdetails['unit_name']; ?></td>

            <td class="text-center">
              <div class="btn-group">
                <a href="delete_grn_return.php?id=<?php echo (int)$grn_return['id'];?>&det=<?php echo (int)$issdetails['id']; ?>"  class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
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
  <form method="post" action="edit_grn_return.php?id=<?php echo (int)$grn_return['id'];   ?>">


    <button type="submit" name="final_submit" class="btn btn-danger"
    onclick="return confirm('Are you sure you want to submit?');"

    >GRN Return</button>



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