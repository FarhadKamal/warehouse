
<link rel="stylesheet" href="libs/css/flexselect.css" type="text/css" media="screen" />

<?php


$page_title = 'Transfer';
require_once('includes/load.php');
              // Checkin What level user has permission to view this page


page_require_level(4);

$last_update_date=date('Y-m-d H:i:s');
$tran_id=(int)$_GET['id'];
$transfer = find_by_id('transfer',$tran_id);





if(!$transfer){
  $session->msg("d","Missing Transfer No.");
  redirect('transfer.php');
}

$all_products = find_by_sql('select products. *,sum(stock_qty) as tot from products 
  inner join  stock on products.id=stock.product_id
  where loc_id='.$transfer['tran_from'].'
  group by products.id having tot>0 order by short_code ');



$com_id = find_value_by_sql(" select com from users where id=".$_SESSION['user_id'])['com'];
$all_locations = find_by_sql('select * from  locations where com='.$com_id.' order by loc_name');
$locations= find_by_id('locations',$transfer['tran_from']);
$com_name = find_value_by_sql(" select com_name from company  where com_id=".$transfer['com'])['com_name'];

$all_trans_details = find_by_sql('select transfer_details.*,products.id as pid, products.name,
  unit_name,unit_type,short_code from transfer_details 
  inner join transfer on transfer_details.tran_id=transfer.id
  inner join products on products.id=transfer_details.product_id 
  inner join units on units.id=products.unit_id 
  where transfer.id="'.$tran_id.'" and transfer.submit_status=0
  order by short_code');


  ?>
  <?php

  if(isset($_POST['final_submit'])){


   $submit_by= $_SESSION['user_id'];
   $track_id=$transfer['id'];
   $loc_from=$transfer['tran_from'];
   $loc_to=$transfer['tran_to'];
   $com_id=$transfer['com'];

   foreach ($all_trans_details as $trandetails): 

    $schk=find_value_by_sql('select ifnull(sum(stock_qty),0) as tot,products.name  from stock 
      inner join products on products.id=stock.product_id
      where product_id='.$trandetails['product_id'].' and loc_id='.$loc_from);

    if($schk['tot']<$trandetails['quantity'] )
    {
      $qty=$trandetails['quantity'];
      if($trandetails['unit_type']=='number')$qty=intval($qty);
      $session->msg('d',$schk['name']." qty: ".$qty." not available at stock: ".$locations['loc_name']);
      redirect('edit_transfer.php?id='.$transfer['id'], false);

    }

  endforeach;

  $query   = "UPDATE transfer SET";
  $query  .=" tran_by ='{$submit_by}',tran_date =now(),submit_status =1";

  $query  .=" WHERE id ='{$track_id}'";

  if($db->query($query)){
   $session->msg('s',' Transfer submitted!');

   

 


    $query  ="insert into stock(
   ref_no,ref_source,product_id,stock_qty,stock_price,stock_by,stock_type,loc_id,com)
   (select   tran_id,'Transfer', product_id,quantity,
   ifnull(quantity*(select round(sum(stock.stock_price)/sum(stock.stock_qty),2) from stock where stock.product_id=transfer_details.product_id and stock.com= '{$com_id}' ),0) as price,'{$submit_by}','receive','{$loc_to}','{$com_id}'    
   from transfer_details where tran_id='{$track_id}' and quantity>0) ";
  // echo  $query;
   $db->query($query);


     $query  ="insert into stock(
   ref_no,ref_source,product_id,stock_qty,stock_price,stock_by,stock_type,loc_id,com)
   (select   tran_id,'Transfer', product_id,(-1)*quantity,
   ifnull(-1*quantity*(select round(sum(stock.stock_price)/sum(stock.stock_qty),2) from stock where stock.product_id=transfer_details.product_id and stock.com= '{$com_id}'),0) as price,'{$submit_by}','issue','{$loc_from}','{$com_id}'    
   from transfer_details where tran_id='{$track_id}' and quantity>0) ";

   $db->query($query);

   

   redirect('transfer.php', false);
 }else {
   $session->msg('d','Transfer submission failed');
   redirect('transfer.php', false);
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
    redirect('edit_transfer.php?id='.$transfer['id'], false);

  }

  if($product_quantity==0){
    $session->msg("d", "product quantity cannot be zero!");
    redirect('edit_transfer.php?id='.$transfer['id'], false);
  }

  $track_id=$transfer['id'];

  $loc_id=$transfer['tran_from'];



  $schk=find_value_by_sql('select ifnull(sum(stock_qty),0) as tot,products.name  from stock 
    inner join products on products.id=stock.product_id
    where product_id='.$product_id.' and loc_id='.$loc_id);

  if($schk['tot']<$product_quantity )
  {

    $session->msg('d',$schk['name']." qty: ".$product_quantity." not available at stock: ".$locations['loc_name']);
    redirect('edit_transfer.php?id='.$transfer['id'], false);

  }




  $db->query("delete from transfer_details where tran_id={$track_id} and product_id={$product_id}");
  $query  = "INSERT INTO transfer_details (";
  $query .=" tran_id, product_id, quantity";
  $query .=") VALUES (";
  $query .=" '{$track_id}', '{$product_id}', '{$product_quantity}'";
  $query .=")";


  if($db->query($query)){
    $session->msg('s',"Product added!");
    redirect('edit_transfer.php?id='.$transfer['id'], false);
  }else {
   $session->msg('d',' Sorry failed to add product!');
   redirect('edit_transfer.php?id='.$transfer['id'], false);
 }

} 



if(isset($_POST['edit_transfer'])){



  $req_fields = array('location-from','location-to');
  validate_fields($req_fields);

  if(empty($errors)){



    $remarks    = remove_junk($db->escape($_POST['remarks']));
    $tran_from    = remove_junk($db->escape($_POST['location-from']));
    $tran_to      = remove_junk($db->escape($_POST['location-to']));


    if($tran_to ==$tran_from ){
          $session->msg('d','Locations cannnot be same!');
           redirect('edit_transfer.php?id='.$transfer['id'], false);

        }

    $submit_by= $_SESSION['user_id'];

    $query   = "UPDATE transfer SET";
    $query  .=" tran_by ='{$submit_by}',tran_from ='{$tran_from}',tran_to ='{$tran_to}',remarks ='{$remarks}',tran_date =now()";

    $query  .=" WHERE id ='{$tran_id}'";
    $result = $db->query($query);
    if($result && $db->affected_rows() === 1){

      $session->msg('s',"Transfer updated ");
      redirect('edit_transfer.php?id='.$transfer['id'], false);
    } else {
     $session->msg('d',' Sorry failed to Transfer!');
     redirect('edit_transfer.php?id='.$transfer['id'], false);
   }

 } else{
   $session->msg("d", $errors);
   redirect('edit_transfer.php?id='.$transfer['id'], false);
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
        <span>Transfer No: <?php echo $transfer['id']; ?></span>
      </strong>
    </div>



    <div class="panel-body">
     <div class="col-md-12">
       <form method="post" action="edit_transfer.php?id=<?php echo $transfer['id'] ?>">

         <div class="form-group">
          <label>Transfer Date</label>
          <div class="input-group">
            <span class="input-group-addon">
             <i class="glyphicon glyphicon-calendar"></i>
           </span>

           <input  type="text"  style="width: 300px;"
           value="<?php echo  remove_junk($transfer['tran_date']); ?>"  name="con-date" readonly>



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
       </div>




       <br/>

       <div class="form-group"> <label>Transfer from</label>
        <div class="input-group">

          <span class="input-group-addon">
           <i class="glyphicon glyphicon-flag"></i>
         </span>



         <select class="form-control" name="location-from" style="width: 300px;" >
          <option value="">Select Product</option>
          <?php  foreach ($all_locations as $loc): ?>
            <option value="<?php echo (int)$loc['id'] ?>"  <?php if($transfer['tran_from']==$loc['id'] ) echo "selected"; ?>    >
              <?php echo $loc['loc_name'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-group"> <label>Transfer to</label>
        <div class="input-group">

          <span class="input-group-addon">
           <i class="glyphicon glyphicon-flag"></i>
         </span>

         <select class="form-control" name="location-to" style="width: 300px;" >
          <option value="">Select Product</option>
          <?php  foreach ($all_locations as $loc): ?>
            <option value="<?php echo (int)$loc['id'] ?>"  <?php if($transfer['tran_to']==$loc['id'] ) echo "selected"; ?>    >
              <?php echo $loc['loc_name'] ?></option>
            <?php endforeach; ?>
          </select>
       </div>
     </div>


     <div class="form-group">
      <div class="form-group"> <label>Remarks</label>
        <textarea class="form-control" id="exampleFormControlTextarea1" name="remarks" 
        rows="3"><?php echo  remove_junk($transfer['remarks']); ?></textarea>
      </div>
    </div> 
    <button type="submit" name="edit_transfer" id="add" class="btn btn-success">Save</button>
  </form> 
  <br/>

  <form method="post" action="edit_transfer.php?id=<?php echo (int)$transfer['id'] ?>">
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
            <th>Transfer Qty</th>
            <th style="width: 100px;">Unit</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php 


          foreach ($all_trans_details as $trandetails): 
            $qty=$trandetails['quantity'];
            if($trandetails['unit_type']=='number') $qty=intval($qty);

            ?>
            <tr>
              <td class="text-center"><?php echo count_id();?></td>
              <td><?php echo $trandetails['short_code']; ?></td>
              <td><?php echo $trandetails['name']; ?></td>            
              <td><?php echo $qty; ?></td>
              <td><?php echo $trandetails['unit_name']; ?></td>

              <td class="text-center">
                <div class="btn-group">
                  <a href="delete_transfer.php?id=<?php echo (int)$transfer['id'];?>&det=<?php echo (int)$trandetails['id']; ?>"  class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
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
    <form method="post" action="edit_transfer.php?id=<?php echo $tran_id;  ?>">


      <button type="submit" name="final_submit" class="btn btn-danger"
      onclick="return confirm('Are you sure you want to transfer?');"

      >Transfer</button>



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