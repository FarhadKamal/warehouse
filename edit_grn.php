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
$last_update_date = date('Y-m-d H:i:s');
$grn_id = (int)$_GET['id'];

$grn = find_grn_edit($grn_id);

$com_name = find_value_by_sql(" select com_name from company  where com_id=".$grn['com'])['com_name'];

$all_requisition_details = find_by_sql('select grn_details.*,products.id as pid, products.name,
  unit_name,unit_type,received,short_code,requisition_details.quantity as reqqty from grn_details 
  inner join grn on grn_details.grn_id=grn.id
  inner join requisition on requisition.id=grn.req_id
  inner join products on products.id=grn_details.product_id 
  inner join units on units.id=products.unit_id 
  inner join requisition_details on (requisition.id=requisition_details.req_id  and 
  requisition_details.product_id=grn_details.product_id ) where grn.id="' . $grn_id . '" and grn.submit_status=0
  and (requisition_details.quantity-requisition_details.received)>0  order by short_code');

if (!$grn) {
  $session->msg("d", "Missing GRN No.");
  redirect('grn.php');
}
?>
<?php




if (isset($_POST['final_submit'])) {


  $submit_by = $_SESSION['user_id'];

  $loc_id    = remove_junk($db->escape($_POST['location']));

  $com_id= $grn['com'];

  $query   = "UPDATE grn SET";
  $query  .= " grn_by ='{$submit_by}',grn_date =now(),submit_status =1";

  $query  .= " WHERE id ='{$grn_id}'";

  if ($db->query($query)) {
    $session->msg('s', ' GRN submitted!');

    $query  = "update grn_details
    inner join grn on grn_details.grn_id=grn.id
    inner join requisition on requisition.id=grn.req_id
    inner join requisition_details on (requisition.id=requisition_details.req_id  and requisition_details.product_id=grn_details.product_id )
    set requisition_details.received=requisition_details.received+grn_details.quantity
    where grn_details.quantity>0 and grn.id='{$grn_id}' ";

    $db->query($query);

    $query  = "insert into stock(
    ref_no,ref_source,product_id,stock_qty,stock_price,stock_by,stock_type,loc_id,com)


    (select   grn_id,'GRN', product_id,quantity,price,'{$submit_by}','receive','{$loc_id}','{$com_id}'   from grn_details where grn_id='{$grn_id}' and quantity>0) ";

    $db->query($query);

    $req_id    = remove_junk($db->escape($_POST['requisition-id']));

    $chkreq = find_value_by_sql("select sum(track) as chk from 
    (select  if(   quantity>received,1,0 )  as track  from requisition_details where req_id={$req_id}) det");

    if ($chkreq['chk'] == 0) {
      $db->query("update requisition set complete_status=1,complete_date=now() where id={$req_id}");


      $action_by = $_SESSION['user_id'];
      $query = "insert into requisition_action 
            (
            req_id, 
            action_by, 
            action_details

            )
            values
            (
            '{$req_id}', 
            '{$action_by}', 

            'Completed'
            )";

      $db->query($query);
    }

    redirect('grn.php', false);
  } else {
    $session->msg('d', 'GRN submission failed');
    redirect('grn.php', false);
  }
}









if (isset($_POST['edit_grn'])) {



  $req_fields = array('location', 'supplier');
  validate_fields($req_fields);

  if (empty($errors)) {




    $submit_by = $_SESSION['user_id'];


    $loc_id    = remove_junk($db->escape($_POST['location']));

    $sup_id    = remove_junk($db->escape($_POST['supplier']));
    $pidlist    = $_POST['pidlist'];
    $newqtylist =   $_POST['newqtylist'];
    $pricelist  =   $_POST['pricelist'];







    $query   = "UPDATE grn SET";
    $query  .= " loc_id ='{$loc_id}',sup_id ='{$sup_id}',grn_date =now()";

    $query  .= " WHERE id ='{$grn_id}'";
    $result = $db->query($query);
    if ($result && $db->affected_rows() === 1) {


      for ($i = 0; $i < count($pidlist); $i++) {

        $pi = $pidlist[$i];
        $qty = $newqtylist[$i];
        $price = $pricelist[$i];
        $query   = "UPDATE grn_details SET";
        $query  .= " quantity ='{$qty}',price ='{$price}'";

        $query  .= " WHERE grn_id ='{$grn_id}' and product_id ='{$pi}'";
        $db->query($query);
      }

      $session->msg('s', "GRN updated ");
      redirect('edit_grn.php?id=' . $grn['id'], false);
    } else {
      $session->msg('d', ' Sorry failed to updated!');
      redirect('edit_grn.php?id=' . $grn['id'], false);
    }
  } else {
    $session->msg("d", $errors);
    redirect('edit_grn.php?id=' . $grn['id'], false);
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
        <span>GRN No: <?php echo (int)$grn['id'] ?></span>
      </strong>
    </div>



    <div class="panel-body">
      <div class="col-md-12">
        <form method="post" action="edit_grn.php?id=<?php echo (int)$grn['id'] ?>">

          
        
         <div class="form-group">
            <label>Company</label>
            <div class="input-group">
              <span class="input-group-addon">
                <i class="glyphicon glyphicon-map-marker"></i>
              </span>
              <input type="text" class="form-control" name="company-name" value="<?php echo remove_junk($com_name); ?>">
            </div>
          </div>
        
          <div class="form-group">
            <label>GRN Date</label>
            <div class="input-group">
              <span class="input-group-addon">
                <i class="glyphicon glyphicon-calendar"></i>
              </span>

              <input type="text" style="width: 300px;" value="<?php echo  remove_junk($grn['grn_date']); ?>" name="grn-date" readonly>
            </div>
          </div>


          <div class="form-group">
            <label>Requisition ID</label>
            <div class="input-group">
              <span class="input-group-addon">
                <i class="glyphicon glyphicon-tag"></i>
              </span>
              <input type="text" class="form-control" name="requisition-id" readonly value="<?php echo remove_junk($grn['req_id']); ?>">
            </div>
          </div>

          <br />

          <div class="form-group"> <label>Stock Location</label>
            <div class="input-group">

              <span class="input-group-addon">
                <i class="glyphicon glyphicon-flag"></i>
              </span>
              <select class="form-control" name="location">
                <option value="">Select Location</option>
                <?php foreach ($all_locations as $loc) : ?>
                  <option value="<?php echo (int)$loc['id'] ?>" <?php if ($grn['loc_id'] === $loc['id']) : echo "selected";
                                                                endif; ?>>
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
              <select class="form-control" name="supplier">
                <option value="">Select Location</option>
                <?php foreach ($all_sup as $sup) : ?>
                  <option value="<?php echo (int)$sup['id'] ?>" <?php if ($grn['sup_id'] === $sup['id']) : echo "selected";
                                                                endif; ?>>
                    <?php echo $sup['sup_name'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <br />

          <div class="panel-body">
            <table class="table table-bordered table-striped table-hover">
              <thead>
                <tr>
                  <th class="text-center" style="width: 50px;">#</th>
                  <th>Short Code</th>
                  <th style="width: 400px;">Products</th>
                  <th>Req. Qty</th>
                  <th>Prev. Rcv</th>
                  <th>New Rcv</th>
                  <th style="width: 100px;">Unit</th>
                  <th>Total Price</th>

                </tr>
              </thead>
              <tbody>
                <?php


                foreach ($all_requisition_details as $reqdetails) :
                  $pendqty = $reqdetails['reqqty'] - $reqdetails['received'];
                  $newqty = $reqdetails['quantity'];
                  if ($reqdetails['unit_type'] == 'number') {
                    $pendqty = intval($pendqty);
                    $newqty = intval($newqty);
                  }

                ?>
                  <tr>
                    <td class="text-center"><?php echo count_id(); ?></td>
                    <td><?php echo $reqdetails['short_code']; ?></td>
                    <td><?php echo $reqdetails['name']; ?></td>
                    <td><?php if ($reqdetails['unit_type'] == 'number') echo intval($reqdetails['reqqty']);
                        else echo  $reqdetails['reqqty']; ?></td>
                    <td><?php if ($reqdetails['unit_type'] == 'number') echo intval($reqdetails['received']);
                        else echo  $reqdetails['received']; ?></td>



                    <td>
                      <input type="hidden" name="pidlist[]" value="<?php echo remove_junk($reqdetails['product_id']); ?>">
                      <input type="number" class="form-control" name="newqtylist[]" value="<?php echo remove_junk($newqty); ?>" <?php if ($reqdetails['unit_type'] == 'decimal') echo "step='any'"; ?> max="<?php echo remove_junk($pendqty); ?>">
                    </td>

                    <td><?php echo $reqdetails['unit_name']; ?></td>


                    <td <?php if ($reqdetails['price'] <= 0 and $newqty > 0) echo 'style="background-color: #FFCCCB;"'; ?>>

                      <input type="number" class="form-control" name="pricelist[]" value="<?php echo remove_junk($reqdetails['price']); ?>" step="any" min="0">
                    </td>



                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <button type="submit" name="edit_grn" class="btn btn-info">Update</button>



          </div>
        </form>
        <?php



        $current_user = current_user();

        $login_level = $current_user['user_level'];



        ?>
        <form method="post" action="edit_grn.php?id=<?php echo $grn_id;  ?>">
          <input type="hidden" name="location" value="<?php echo  remove_junk($grn['loc_id']); ?>" />
          <input type="hidden" name="requisition-id" value="<?php echo  remove_junk($grn['req_id']); ?>" />
          <button type="submit" name="final_submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to receive?');">Receive</button>



        </form>








      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
<script>
  $(document).ready(function() {

    $("#product-id").flexselect();

    $("#product-id").change(function() {

      var id = $('#product-id').val();



      if (id != '') {
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

      } else $('#prev-short-code').val("");

    });



  });
</script>
<script src="libs/js/liquidmetal.js" type="text/javascript"></script>
<script src="libs/js/jquery.flexselect.js" type="text/javascript"></script>