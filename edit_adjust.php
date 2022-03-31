
<link rel="stylesheet" href="libs/css/flexselect.css" type="text/css" media="screen" />

<?php


$page_title = 'Adjustment';
require_once('includes/load.php');
              // Checkin What level user has permission to view this page


page_require_level(4);

$last_update_date=date('Y-m-d H:i:s');
$adj_id=(int)$_GET['id'];
$adjust = find_by_id('adjust',$adj_id);

$com_name = find_value_by_sql(" select com_name from company  where com_id=".$adjust['com'])['com_name'];



if(!$adjust){
  $session->msg("d","Missing Return No.");
  redirect('adjustment.php');
}



$all_products = find_by_sql('
select products. *,sum(stock_qty)  as tot,unit_type from stock 
inner join products   on products.id=stock.product_id 
inner join units on units.id=products.unit_id 

where stock_date<"'.$adjust['adj_date'].'" and loc_id="'.$adjust['loc_id'].'"
group by product_id ');






$locations= find_by_id('locations',$adjust['loc_id']);

$all_adjust_details = find_by_sql('select adjust_details.*,products.id as pid, products.name,sum(stock_qty) as stock_qty,
  unit_name,unit_type,short_code from adjust_details 
  inner join adjust on adjust_details.adj_id=adjust.id
  inner join products on products.id=adjust_details.product_id 
  inner join units on units.id=products.unit_id 

  inner join stock on (stock.product_id=adjust_details.product_id and stock_date<"'.$adjust['adj_date'].'"
   and stock.loc_id="'.$adjust['loc_id'].'")


  where adjust.id="'.$adjust['id'].'" and adjust.submit_status=0
  group by products.id
  order by short_code');


  ?>
  <?php

  if(isset($_POST['final_submit'])){


   $submit_by= $_SESSION['user_id'];
   $track_id=$adjust['id'];
   $loc_id=$adjust['loc_id'];
   $com_id= $adjust['com'];




  $query   = "UPDATE adjust SET";
  $query  .=" adj_by ='{$submit_by}',adj_date =now(),submit_status =1";

  $query  .=" WHERE id ='{$track_id}'";

  $adj_date=$adjust['adj_date'];

  if($db->query($query)){
   $session->msg('s',' Physical Adjustment submitted!');

   

   $query  ="insert into stock(
   ref_no,ref_source,product_id,stock_qty,stock_price,stock_by,stock_type,loc_id,com)
   (select   adj_id,'Adjustment', product_id,diff,
   diff*(select round(sum(stock.stock_price)/sum(stock.stock_qty),2) from 
   stock where stock.product_id=adjust_details.product_id  and stock.com= '{$com_id}' ),'{$submit_by}',adj_type,'{$loc_id}','{$com_id}'   
   from adjust_details where adj_id='{$track_id}') ";

   $db->query($query);






   

   redirect('adjustment.php', false);
 }else {
   $session->msg('d','GRN Return submission failed');
   redirect('adjustment.php', false);
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
    redirect('edit_adjust.php?id='.$adjust['id'], false);

  }


  if($product_quantity =="" or $product_quantity==null)$product_quantity=0;

 if($product_quantity<0){
    $session->msg("d", "Physical product quantity cannot be  negative!");
    redirect('edit_adjust.php?id='.$adjust['id'], false);
  }

  $track_id=$adjust['id'];

  $loc_id=$adjust['loc_id'];





  $schk=find_value_by_sql('select products. *,sum(stock_qty)  as tot from stock 
  inner join products   on products.id=stock.product_id where stock_date<"'.$adjust['adj_date'].'" and loc_id="'.$loc_id.'"
  and product_id='.$product_id);

  if($schk['tot']==$product_quantity )
  {


    $session->msg('d',$schk['name']." qty: [".$product_quantity."] cannnot be same as stock!");
    redirect('edit_adjust.php?id='.$adjust['id'], false);

  }

  $adj_type="receive";
  $diff=  $product_quantity-$schk['tot'];

  if($diff<0)
  $adj_type="issue";

  $db->query("delete from adjust_details where adj_id={$track_id} and product_id={$product_id}");
  $query  = "INSERT INTO adjust_details (";
  $query .=" adj_id, product_id, quantity,diff,adj_type";
  $query .=") VALUES (";
  $query .=" '{$track_id}', '{$product_id}', '{$product_quantity}', '{$diff}', '{$adj_type}'";
  $query .=")";


  if($db->query($query)){
    $session->msg('s',"Product added!");
    redirect('edit_adjust.php?id='.$adjust['id'], false);
  }else {
   $session->msg('d',' Sorry failed to add product!');
    redirect('edit_adjust.php?id='.$adjust['id'], false);
 }

} 



if(isset($_POST['edit_adjust'])){



  $req_fields = array('adj-date');
  validate_fields($req_fields);

  if(empty($errors)){



    $remarks    = remove_junk($db->escape($_POST['remarks']));


    $submit_by= $_SESSION['user_id'];

    $query   = "UPDATE adjust SET";
    $query  .=" adj_by ='{$submit_by}',remarks ='{$remarks}',adj_date =now()";

    $query  .=" WHERE id ='{$adj_id}'";
    $result = $db->query($query);
    if($result && $db->affected_rows() === 1){

      $session->msg('s',"Adjustment updated ");
      redirect('edit_adjust.php?id='.$adjust['id'], false);
    } else {
     $session->msg('d',' Sorry failed to Adjustment!');
     redirect('edit_adjust.php?id='.$adjust['id'], false);
   }

 } else{
   $session->msg("d", $errors);
   redirect('edit_adjust.php?id='.$adjust['id'], false);
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
        <span>Adjustment No: <?php echo $adjust['id']; ?></span>
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
       <form method="post" action="edit_adjust.php?id=<?php echo $adjust['id'] ?>">

         <div class="form-group">
          <label>Stock Record Date</label>
          <div class="input-group">
            <span class="input-group-addon">
             <i class="glyphicon glyphicon-calendar"></i>
           </span>

           <input  type="text"  style="width: 300px;"
           value="<?php echo  remove_junk($adjust['adj_date']); ?>"  name="adj-date" readonly>
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
      rows="3"><?php echo  remove_junk($adjust['remarks']); ?></textarea>
    </div>
  </div> 
  <button type="submit" name="edit_adjust"  class="btn btn-success">Save</button>
</form> 
<br/>

<form method="post" action="edit_adjust.php?id=<?php echo (int)$adjust['id'] ?>">
  <div class="form-group">
    <div class="row">
      <div class="col-md-12">
        <label >Product</label>

        
        <select class="form-control" id="product-id" name="product-name"  style="width: 500px;" >
          <option value="">Select Product</option>
          <?php  foreach ($all_products as $prd): ?>
            <option value="<?php echo (int)$prd['id'] ?>">
              <?php echo $prd['short_code'] ?> <?php echo " # " ?><?php echo $prd['name'] ?><?php 
              if($prd['unit_type']="number") echo "&nbsp;&nbsp;&nbsp;[".intval($prd['tot'])."]" ; 
              else  echo "&nbsp;&nbsp;&nbsp;[".$prd['tot']."]"; ?></option>
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
          <th>Stock Qty</th>
          <th>Physical Qty</th>
          <th style="width: 100px;">Unit</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php 


        foreach ($all_adjust_details as $issdetails): 
          $qty=$issdetails['quantity'];
          $sqty=$issdetails['stock_qty'];
          if($issdetails['unit_type']=='number'){ $qty=intval($qty);  $sqty=intval($sqty); }

          ?>
          <tr>
            <td class="text-center"><?php echo count_id();?></td>
            <td><?php echo $issdetails['short_code']; ?></td>
            <td><?php echo $issdetails['name']; ?></td>            
            <td><?php echo $sqty; ?></td>
            <td><?php echo $qty; ?></td>
            <td><?php echo $issdetails['unit_name']; ?></td>

            <td class="text-center">
              <div class="btn-group">
                <a href="delete_adjust.php?id=<?php echo (int)$adjust['id'];?>&det=<?php echo (int)$issdetails['id']; ?>"  class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
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
  <form method="post" action="edit_adjust.php?id=<?php echo (int)$adjust['id'];   ?>">


    <button type="submit" name="final_submit" class="btn btn-danger"
    onclick="return confirm('Are you sure you want to submit?');"

    >Submit</button>



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