
<link rel="stylesheet" href="libs/css/flexselect.css" type="text/css" media="screen" />

<?php


$page_title = 'Issue';
require_once('includes/load.php');
              // Checkin What level user has permission to view this page


page_require_level(4);

$last_update_date=date('Y-m-d H:i:s');
$con_id=(int)$_GET['id'];
$consume = find_by_id('consume',$con_id);





if(!$consume){
  $session->msg("d","Missing Issue No.");
  redirect('consume.php');
}

$all_products = find_by_sql('select products. *,sum(stock_qty) as tot from products 
  inner join  stock on products.id=stock.product_id
  where loc_id='.$consume['loc_id'].'
  group by products.id having tot>0 order by short_code ');



$com_id = find_value_by_sql(" select com from users where id=".$_SESSION['user_id'])['com'];
$all_locations = find_by_sql('select * from  locations where com='.$com_id.' order by loc_name');


$all_cost = find_by_sql('select * from  cost_centre where com='. $com_id .' order by cost_name');

$com_name = find_value_by_sql(" select com_name from company  where com_id=".$consume['com'])['com_name'];


$locations= find_by_id('locations',$consume['loc_id']);

$all_issue_details = find_by_sql('select consume_details.*,products.id as pid, products.name,
  unit_name,unit_type,short_code from consume_details 
  inner join consume on consume_details.con_id=consume.id
  inner join products on products.id=consume_details.product_id 
  inner join units on units.id=products.unit_id 
  where consume.id="'.$con_id.'" and consume.submit_status=0
  order by short_code');


  ?>
  <?php

  if(isset($_POST['final_submit'])){


   $submit_by= $_SESSION['user_id'];
   $track_id=$consume['id'];
   $loc_id=$consume['loc_id'];

   $com_id= $consume['com'];

   foreach ($all_issue_details as $issdetails): 

    $schk=find_value_by_sql('select ifnull(sum(stock_qty),0) as tot,products.name  from stock 
      inner join products on products.id=stock.product_id
      where product_id='.$issdetails['product_id'].' and loc_id='.$loc_id);

    if($schk['tot']<$issdetails['quantity'] )
    {
      $qty=$issdetails['quantity'];
      if($issdetails['unit_type']=='number')$qty=intval($qty);
      $session->msg('d',$schk['name']." qty: ".$qty." not available at stock: ".$locations['loc_name']);
      redirect('edit_consume.php?id='.$consume['id'], false);

    }

  endforeach;

  $query   = "UPDATE consume SET";
  $query  .=" con_by ='{$submit_by}',con_date =now(),submit_status =1";

  $query  .=" WHERE id ='{$track_id}'";

  if($db->query($query)){
   $session->msg('s',' Issue submitted!');

   

   $query  ="insert into stock(
   ref_no,ref_source,product_id,stock_qty,stock_price,stock_by,stock_type,loc_id,com)
   (select   con_id,'Issue', product_id,(-1)*quantity,
   -1*quantity*(select round(sum(stock.stock_price)/sum(stock.stock_qty),2) from stock where stock.product_id=consume_details.product_id and stock.com= '{$com_id}' ),'{$submit_by}','issue','{$loc_id}','{$com_id}'    
   from consume_details where con_id='{$track_id}' and quantity>0) ";

   $db->query($query);

   

   redirect('consume.php', false);
 }else {
   $session->msg('d','Issue submission failed');
   redirect('consume.php', false);
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
    redirect('edit_consume.php?id='.$consume['id'], false);

  }

  if($product_quantity<=0){
    $session->msg("d", "product quantity cannot be zero or negative!");
    redirect('edit_consume.php?id='.$consume['id'], false);
  }

  $track_id=$consume['id'];

  $loc_id=$consume['loc_id'];



  $schk=find_value_by_sql('select ifnull(sum(stock_qty),0) as tot,products.name  from stock 
    inner join products on products.id=stock.product_id
    where product_id='.$product_id.' and loc_id='.$loc_id);

  if($schk['tot']<$product_quantity )
  {

    $session->msg('d',$schk['name']." qty: ".$product_quantity." not available at stock: ".$locations['loc_name']);
    redirect('edit_consume.php?id='.$consume['id'], false);

  }




  $db->query("delete from consume_details where con_id={$track_id} and product_id={$product_id}");
  $query  = "INSERT INTO consume_details (";
  $query .=" con_id, product_id, quantity";
  $query .=") VALUES (";
  $query .=" '{$track_id}', '{$product_id}', '{$product_quantity}'";
  $query .=")";


  if($db->query($query)){
    $session->msg('s',"Product added!");
    redirect('edit_consume.php?id='.$consume['id'], false);
  }else {
   $session->msg('d',' Sorry failed to add product!');
   redirect('edit_consume.php?id='.$consume['id'], false);
 }

} 



if(isset($_POST['edit_consume'])){



  $req_fields = array('receiver-name','cost-centre-name');
  validate_fields($req_fields);

  if(empty($errors)){


    $consumer    = remove_junk($db->escape($_POST['receiver-name']));
    $remarks    = remove_junk($db->escape($_POST['remarks']));
    $loc_id    = remove_junk($db->escape($_POST['location']));
    $cost_id    = remove_junk($db->escape($_POST['cost-centre-name']));

    $submit_by= $_SESSION['user_id'];

    $query   = "UPDATE consume SET";
    $query  .=" consumer ='{$consumer}',con_by ='{$submit_by}',loc_id ='{$loc_id}',cost_id ='{$cost_id}',remarks ='{$remarks}',con_date =now()";

    $query  .=" WHERE id ='{$con_id}'";
    $result = $db->query($query);
    if($result && $db->affected_rows() === 1){

      $session->msg('s',"Issue updated ");
      redirect('edit_consume.php?id='.$consume['id'], false);
    } else {
     $session->msg('d',' Sorry failed to Issue!');
     redirect('edit_consume.php?id='.$consume['id'], false);
   }

 } else{
   $session->msg("d", $errors);
   redirect('edit_consume.php?id='.$consume['id'], false);
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
        <span>Issue No: <?php echo $consume['id']; ?></span>
      </strong>
    </div>



    <div class="panel-body">
     <div class="col-md-12">
       <form method="post" action="edit_consume.php?id=<?php echo $consume['id'] ?>">

         <div class="form-group">
          <label>Issue Date</label>
          <div class="input-group">
            <span class="input-group-addon">
             <i class="glyphicon glyphicon-calendar"></i>
           </span>

           <input  type="text"  style="width: 300px;"
           value="<?php echo  remove_junk($consume['con_date']); ?>"  name="con-date" readonly>
         </div>
       </div>

       <div class="form-group"> <label>Receiver Name</label>
        <div class="input-group">

          <span class="input-group-addon">
           <i class="glyphicon glyphicon-user"></i>
         </span>
         <input type="text" class="form-control" name="receiver-name" value="<?php echo  remove_junk($consume['consumer']); ?>"  placeholder="Receiver Name">
       </div>
     </div>                                   


     <br/>

     <div class="form-group"> <label>Stock Location</label>
      <div class="input-group">

        <span class="input-group-addon">
         <i class="glyphicon glyphicon-flag"></i>
       </span>


       <select class="form-control" name="location" style="width: 300px;" >
        <option value="">Select Product</option>
        <?php  foreach ($all_locations as $loc): ?>
          <option value="<?php echo (int)$loc['id'] ?>"  <?php if($consume['loc_id']==$loc['id'] ) echo "selected"; ?>    >
            <?php echo $loc['loc_name'] ?></option>
          <?php endforeach; ?>
        </select>


      </div>
    </div>


    <div class="form-group"> <label>Cost Centre</label>
      <div class="input-group">

        <span class="input-group-addon">
         <i class="glyphicon glyphicon-flag"></i>
       </span>


       <select class="form-control" name="cost-centre-name" style="width: 300px;" id="cost_id">
        <option value="">Select Cost Centre</option>
        <?php  foreach ($all_cost as $cost): ?>
          <option value="<?php echo (int)$cost['id'] ?>"  <?php if($consume['cost_id']==$cost['id'] ) echo "selected"; ?>    >
            <?php echo $cost['cost_name'] ?><?php echo " # " ?><?php echo $cost['id'] ?></option>
          <?php endforeach; ?>
        </select>


      </div>
    </div>


    <div class="form-group">
      <div class="form-group"> <label>Remarks</label>
        <textarea class="form-control" id="exampleFormControlTextarea1" name="remarks" 
        rows="3"><?php echo  remove_junk($consume['remarks']); ?></textarea>
      </div>
    </div> 
    <button type="submit" name="edit_consume" id="add" class="btn btn-success">Save</button>
  </form> 
  <br/>

  <form method="post" action="edit_consume.php?id=<?php echo (int)$consume['id'] ?>">
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
            <th>Issue Qty</th>
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
                  <a href="delete_issue.php?id=<?php echo (int)$consume['id'];?>&det=<?php echo (int)$issdetails['id']; ?>"  class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
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
    <form method="post" action="edit_consume.php?id=<?php echo $con_id;  ?>">


      <button type="submit" name="final_submit" class="btn btn-danger"
      onclick="return confirm('Are you sure you want to issue?');"

      >Issue</button>



    </form> 








  </div>
</div>
</div>
</div>

<?php include_once('layouts/footer.php'); ?>
<script>
  $(document).ready(function() {

    $("#product-id").flexselect();

    $("#cost_id").flexselect();
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