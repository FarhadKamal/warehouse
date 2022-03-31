
<link rel="stylesheet" href="libs/css/flexselect.css" type="text/css" media="screen" />

<?php


$page_title = 'Issue Return';
require_once('includes/load.php');
              // Checkin What level user has permission to view this page


page_require_level(4);

$last_update_date=date('Y-m-d H:i:s');
$rtn_id=(int)$_GET['id'];
$consume_return = find_by_id('consume_return',$rtn_id);





if(!$consume_return){
  $session->msg("d","Missing Return No.");
  redirect('issue_return.php');
}
$consume = find_by_id('consume',$consume_return['con_id']);

$com_name = find_value_by_sql(" select com_name from company  where com_id=".$consume['com'])['com_name'];


$all_products = find_by_sql('select products. *,(quantity-con_return)  as tot from consume_details 
  inner join products   on products.id=consume_details.product_id
  where  con_id='.$consume['id'].' and (quantity-con_return)>0
   order by short_code ');


 



$locations= find_by_id('locations',$consume['loc_id']);

$all_issue_details = find_by_sql('select consume_return_details.*,products.id as pid, products.name,
  unit_name,unit_type,short_code from consume_return_details 
  inner join consume_return on consume_return_details.rtn_id=consume_return.id
  inner join products on products.id=consume_return_details.product_id 
  inner join units on units.id=products.unit_id 
  where consume_return.id="'.$consume_return['id'].'" and consume_return.submit_status=0
  order by short_code');


  ?>
  <?php

  if(isset($_POST['final_submit'])){


   $submit_by= $_SESSION['user_id'];
   $track_id=$consume_return['id'];
   $loc_id=$consume['loc_id'];

   $com_id= $consume['com'];

   foreach ($all_issue_details as $issdetails): 

    $schk=find_value_by_sql('select (quantity-con_return) as tot,products.name  from consume_details 

      inner join products on products.id=consume_details.product_id
      where product_id='.$issdetails['product_id'].' and  con_id='.$consume['id']);

    if($schk['tot']<$issdetails['quantity'] )
    {
      $qty=$issdetails['quantity'];
      if($issdetails['unit_type']=='number')$qty=intval($qty);
      $session->msg('d',$schk['name']." qty: [".$qty."] did not issue to the consumer");
      redirect('edit_issue_return.php?id='.$track_id, false);

    }

  endforeach;



  $query   = "UPDATE consume_return SET";
  $query  .=" rtn_by ='{$submit_by}',rtn_date =now(),submit_status =1";

  $query  .=" WHERE id ='{$track_id}'";

  if($db->query($query)){
   $session->msg('s',' Issue Return submitted!');

   

   $query  ="insert into stock(
   ref_no,ref_source,product_id,stock_qty,stock_price,stock_by,stock_type,loc_id,com)
   (select   rtn_id,'Issue Return', product_id,quantity,
   ifnull(quantity*(select round(sum(stock.stock_price)/sum(stock.stock_qty),2) from stock where stock.product_id=consume_return_details.product_id),0),'{$submit_by}','receive','{$loc_id}','{$com_id}'   
   from consume_return_details where rtn_id='{$track_id}' and quantity>0) ";

   $db->query($query);


   $query  ="update
   consume_return_details
   inner join consume_return on consume_return.id=consume_return_details.rtn_id
   inner join consume on consume.id=consume_return.con_id
   inner join consume_details on(consume_details.product_id=consume_return_details.product_id
   and consume_details.con_id=consume.id)
   set consume_details.con_return=consume_details.con_return+consume_return_details.quantity
   where consume_return.id='{$track_id}'";

   $db->query($query);





   

   redirect('issue_return.php', false);
 }else {
   $session->msg('d','Issue Return submission failed');
   redirect('issue_return', false);
 }
}









if(isset($_POST['add_requisition'])){

  $product_id    = remove_junk($db->escape($_POST['product-name']));
  $product_quantity    = remove_junk($db->escape($_POST['product-quantity']));
  $submit_by= $_SESSION['user_id'];

  $req_fields = array('product-name' );
  validate_fields($req_fields);
  if($errors){
    $session->msg("d", $errors);
    redirect('edit_issue_return.php?id='.$consume_return['id'], false);

  }

  if($product_quantity<=0){
    $session->msg("d", "product quantity cannot be zero or negative!");
    redirect('edit_issue_return.php?id='.$consume_return['id'], false);
  }

  $track_id=$consume_return['id'];

  $loc_id=$consume['loc_id'];





    $schk=find_value_by_sql('select (quantity-con_return) as tot,products.name  from consume_details 

      inner join products on products.id=consume_details.product_id
      where product_id='.$product_id.' and  con_id='.$consume['id']);

  if($schk['tot']<$product_quantity )
  {

   
    $session->msg('d',$schk['name']." qty: [".$product_quantity."] did not issue to the consumer");
    redirect('edit_issue_return.php?id='.$consume_return['id'], false);

  }




  $db->query("delete from consume_return_details where rtn_id={$track_id} and product_id={$product_id}");
  $query  = "INSERT INTO consume_return_details (";
  $query .=" rtn_id, product_id, quantity";
  $query .=") VALUES (";
  $query .=" '{$track_id}', '{$product_id}', '{$product_quantity}'";
  $query .=")";


  if($db->query($query)){
    $session->msg('s',"Product added!");
    redirect('edit_issue_return.php?id='.$consume_return['id'], false);
  }else {
   $session->msg('d',' Sorry failed to add product!');
   redirect('edit_issue_return.php?id='.$consume_return['id'], false);
 }

} 



if(isset($_POST['edit_issue_return'])){



  $req_fields = array('rtn-date');
  validate_fields($req_fields);

  if(empty($errors)){



    $remarks    = remove_junk($db->escape($_POST['remarks']));


    $submit_by= $_SESSION['user_id'];

    $query   = "UPDATE consume_return SET";
    $query  .=" rtn_by ='{$submit_by}',remarks ='{$remarks}',rtn_date =now()";

    $query  .=" WHERE id ='{$rtn_id}'";
    $result = $db->query($query);
    if($result && $db->affected_rows() === 1){

      $session->msg('s',"Issue Return updated ");
      redirect('edit_issue_return.php?id='.$consume_return['id'], false);
    } else {
     $session->msg('d',' Sorry failed to Issue Return!');
     redirect('edit_issue_return.php?id='.$consume_return['id'], false);
   }

 } else{
   $session->msg("d", $errors);
   redirect('edit_issue_return.php?id='.$consume_return['id'], false);
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
        <span>Issue Return No: <?php echo $consume_return['id']; ?></span>
      </strong>
    </div>

    <div class="form-group">
      <label>Company</label>
      <div class="input-group">
        <span class="input-group-addon">
          <i class="glyphicon glyphicon-map-marker"></i>
        </span>
        <input type="text" class="form-control" name="company-name" value="<?php echo remove_junk($com_name); ?>">
      </div>
    </div>



    <div class="panel-body">
     <div class="col-md-12">
       <form method="post" action="edit_issue_return.php?id=<?php echo $consume_return['id'] ?>">

         <div class="form-group">
          <label>Return Date</label>
          <div class="input-group">
            <span class="input-group-addon">
             <i class="glyphicon glyphicon-calendar"></i>
           </span>

           <input  type="text"  style="width: 300px;"
           value="<?php echo  remove_junk($consume_return['rtn_date']); ?>"  name="rtn-date" readonly>
         </div>
       </div>



       <br/>
       <div class="form-group"> <label>Issue No</label>
        <div class="input-group">

          <span class="input-group-addon">
           <i class="glyphicon glyphicon-tag"></i>
         </span>
         <input  
         value="<?php echo  remove_junk($consume_return['con_id']); ?>"  readonly>

       </div>
     </div>

     <br/>
     <div class="form-group"> <label>Consumer</label>
      <div class="input-group">

        <span class="input-group-addon">
         <i class="glyphicon glyphicon-user"></i>
       </span>
       <input  
       value="<?php echo  remove_junk($consume['consumer']); ?>"  readonly>

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
    rows="3"><?php echo  remove_junk($consume_return['remarks']); ?></textarea>
  </div>
</div> 
<button type="submit" name="edit_issue_return" id="add" class="btn btn-success">Save</button>
</form> 
<br/>

<form method="post" action="edit_issue_return.php?id=<?php echo (int)$consume_return['id'] ?>">
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


        foreach ($all_issue_details as $issdetails): 
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
                <a href="delete_issue_return.php?id=<?php echo (int)$consume_return['id'];?>&det=<?php echo (int)$issdetails['id']; ?>"  class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
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
  <form method="post" action="edit_issue_return.php?id=<?php echo (int)$consume_return['id'];   ?>">


    <button type="submit" name="final_submit" class="btn btn-danger"
    onclick="return confirm('Are you sure you want to submit?');"

    >Issue Return</button>



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