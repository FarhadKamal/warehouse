<link rel="stylesheet" href="libs/css/flexselect.css" type="text/css" media="screen" />

<?php


$page_title = 'PO';
require_once('includes/load.php');
// Checkin What level user has permission to view this page


page_require_level(5);


$all_requisitions = find_by_sql('select * from requisition where approve_status=1 and submit_status=1 and cancel_status=0 and complete_status=0
order by id desc');

$last_update_date = date('Y-m-d H:i:s');
$po_id = (int)$_GET['id'];

$supplier = find_value_by_sql('select * from  suppliers where id in(select sup_id from po where id="' . $po_id . '" ) ');



$po_options = find_by_sql('select * from po_options order by order_level');

$po_options_groups = find_by_sql('select distinct option_name from po_options order by order_level');

$po_option_list = find_by_sql('select option_name,opton_details,po_id, po_terms.id  from po_terms 
inner join po_options on po_terms.option_id=po_options.id
where po_id="' . $po_id . '" order by order_level');

$po = find_po_edit($po_id);
$all_po_details = find_by_sql('
select po_items.*, products.name,short_code,unit_name,unit_type
  from po_items 
  inner join po on po_items.po_id=po.id
  inner join products on products.id=po_items.product_id
  inner join units on units.id=products.unit_id
  where po.id=' . $po_id . '  and po.submit_status=0
  order by short_code');


if (!$po) {
  $session->msg("d", "Missing PO No.");
  redirect('po.php');
}
?>
<?php




if (isset($_POST['final_submit'])) {


  $submit_by = $_SESSION['user_id'];

  $query   = "UPDATE po SET";
  $query  .= " submit_by ='{$submit_by}',submit_date =now(),submit_status =1";
  $query  .= " WHERE id ='{$po_id}'";

  $db->query($query);

  $session->msg('s', 'PO submitted');
  redirect('po.php', false);
}





if (isset($_POST['add_terms'])) {

  $terms_conditions    = remove_junk($db->escape($_POST['terms-conditions']));
  $terms_group    = remove_junk($db->escape($_POST['terms-group']));
  $new_terms    = remove_junk($db->escape($_POST['new-terms']));
  $option_id = 0;


  $option_id = $terms_conditions;

  if (strlen($new_terms) < 5 && $terms_conditions == 0) {
    $session->msg('d', "Please select terms and conditions! ");
    redirect('edit_po.php?id=' . $po_id, false);
  } else if (strlen($new_terms) >= 5 && strlen($terms_group) < 5) {

    $session->msg('d', "Please select new term group! ");
    redirect('edit_po.php?id=' . $po_id, false);
  }


  if (strlen($new_terms) >= 5 && strlen($terms_group) >= 5) {


    $lv =  find_value_by_sql("select order_level  from po_options where option_name='" . $terms_group . "'");

    $str = " insert into po_options 
      (
      option_name, 
      opton_details, 
      order_level
      )
      values
      (
     '" . $terms_group . "', 
      '" . $new_terms . "', 
     '" . $lv['order_level'] . "'
      )";

    $db->query($str);

    $mx =  find_value_by_sql("select max(id)  as mxid from po_options");



    $option_id = $mx['mxid'];
  }



  $str = "insert into po_terms 
	(
	po_id, 
	option_id
	)
	values
	(
	$po_id, 
	$option_id
	)";

  $db->query($str);

  $session->msg('s', "Added ");
  redirect('edit_po.php?id=' . $po_id, false);
}

if (isset($_POST['save_po'])) {

  $address              = remove_junk($db->escape($_POST['address']));
  $attn                 = remove_junk($db->escape($_POST['attn']));
  $sup_email            = remove_junk($db->escape($_POST['sup_email']));
  $sup_mobile           = remove_junk($db->escape($_POST['sup_mobile']));
  $reference            = remove_junk($db->escape($_POST['reference']));
  $sup_id               = remove_junk($db->escape($_POST['sup_id']));
  $gender               = remove_junk($db->escape($_POST['gender']));

  $query = "update suppliers 
          set

          sup_mobile = '{$sup_mobile}' , 
          attn = '{$attn}' , 
          sup_address = '{$address}' , 
          sup_email = '{$sup_email}'
          
          where
          id =" . $sup_id;

  $db->query($query);


  $query = "update po 
          set

          ref = '{$reference}' , 
          gender = '{$gender}' 

          
          where
          id =" . $po_id;

  $db->query($query);
  $session->msg('s', "saved ");
  redirect('edit_po.php?id=' . $po_id, false);
}



if (isset($_POST['add_item_po'])) {



  $req_fields = array('requisition-id');
  validate_fields($req_fields);

  if (empty($errors)) {




    $submit_by = $_SESSION['user_id'];


    $req_id    = remove_junk($db->escape($_POST['requisition-id']));


    $pid          = $_POST['pid'];
    $pqqty        = $_POST['pqqty'];
    $reqqty        = $_POST['reqqty'];
    $pdetails        = $_POST['pdetails'];

    $pqqtychk     = $_POST['pqqtychk'];
    $price     = $_POST['price'];


    $forchk = false;

    if (isset($pqqtychk)) {
      for ($i = 0; $i < count($pqqtychk); $i++) {
        $forchk = true;

        $prc = $price[$pqqtychk[$i]];
        if ($prc == null or $prc == "") $prc = 0;


        $str = "insert into po_items 
      (
      po_id, 
      product_id, 
      po_quantity, 
      req_quantity, 
      details, 
      req_id, 
      price
      )
      values
      (
      '" . $po_id . "', 
      '" . $pid[$pqqtychk[$i]] . "',  
      '" . $pqqty[$pqqtychk[$i]] . "', 
      '" . $reqqty[$pqqtychk[$i]] . "', 
      '" . $pdetails[$pqqtychk[$i]] . "', 
      '" . $req_id . "', 
      '" . $prc . "'
      )";
        $db->query($str);
        //print($str);
      }
    }

    if ($forchk == false) {
      $session->msg('d', ' please select item!');
      redirect('edit_po.php?id=' . $po_id, false);
    }

    $session->msg('s', "Added ");
    redirect('edit_po.php?id=' . $po_id, false);
  } else {
    $session->msg("d", $errors);
    redirect('edit_po.php?id=' . $po_id, false);
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
        <span>PO No: <?php echo (int)$po['id'] ?></span>
      </strong>
    </div>



    <div class="panel-body">
      <div class="col-md-12">


        <div class="form-group">
          <label>PO Date</label>
          <div class="input-group">
            <span class="input-group-addon">
              <i class="glyphicon glyphicon-calendar"></i>
            </span>

            <input type="text" style="width: 300px;" value="<?php echo  remove_junk($po['po_date']); ?>" name="po-date" readonly>
          </div>
        </div>

        <form method="post" action="edit_po.php?id=<?php echo (int)$po['id'] ?>">
          <div class="form-group">
            <label>Supplier</label>
            <div class="input-group">
              <span class="input-group-addon">
                <i class="glyphicon glyphicon-user"></i>
              </span>
              <input type="text" class="form-control" name="requisition-id" readonly value="<?php echo remove_junk($supplier['sup_name']); ?>"><br />
              <input type="text" class="form-control" placeholder="Address" name="address" value="<?php echo remove_junk($supplier['sup_address']); ?>"><br />
              <input type="text" class="form-control" placeholder="Contact Person" name="attn" value="<?php echo remove_junk($supplier['attn']); ?>"><br />
              <input type="text" class="form-control" placeholder="Email" name="sup_email" value="<?php echo remove_junk($supplier['sup_email']); ?>"><br />
              <input type="text" class="form-control" placeholder="Mobile" name="sup_mobile" value="<?php echo remove_junk($supplier['sup_mobile']); ?>">
              <input type="text" class="form-control" placeholder="Reference" name="reference" value="<?php echo remove_junk($po['ref']); ?>">

              <select class=" form-control" name="gender">
                <option value=" Mr." <?php if ($po['gender'] == "Mr.") {
                                        echo "selected";
                                      } ?>>Male</option>
                <option value="Mrs." <?php if ($po['gender'] == "Mrs.") {
                                        echo "selected";
                                      } ?>>Female</option>
              </select>
              <input type="hidden" name="sup_id" value="<?php echo  remove_junk($supplier['id']); ?>" />
              <button type="submit" name="save_po" class="btn btn-info">Save</button>

            </div>
          </div>
        </form>

        <form method="post" action="edit_po.php?id=<?php echo (int)$po['id'] ?>">
          <div class="form-group"> <label>Requisition ID</label>
            <div class="input-group">

              <span class="input-group-addon">
                <i class="glyphicon glyphicon-tag"></i>
              </span>
              <select class="form-control" id="requisition-id" name="requisition-id">
                <option value="">Select Requisition No</option>
                <?php foreach ($all_requisitions as $req) : ?>
                  <option value="<?php echo (int)$req['id'] ?>">
                    <?php echo $req['id'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <br />

          <div class="form-group">
            <div class="row">
              <div class="col-md-12">
                <div id="div-result">

                </div>
              </div>
            </div>
          </div>

          <br />


        </form>



        <div class="panel-body">
          <table class="table table-bordered table-striped table-hover">
            <thead>
              <tr>
                <th class="text-center" style="width: 50px;">#</th>
                <th>Short Code</th>
                <th style="width: 400px;">Products</th>
                <th style="width: 400px;">Details</th>
                <th>Req. ID</th>
                <th>Req. Qty</th>
                <th>PO. Qty</th>

                <th style="width: 100px;">Unit</th>
                <th>Total Price</th>

              </tr>
            </thead>
            <tbody>
              <?php


              foreach ($all_po_details as $podetails) :
                $pendqty = $podetails['reqqty'] - $podetails['received'];
                $newqty = $podetails['quantity'];
                if ($podetails['unit_type'] == 'number') {
                  $pendqty = intval($pendqty);
                  $newqty = intval($newqty);
                }

              ?>


                <tr>
                  <td class="text-center"><?php echo count_id(); ?></td>
                  <td><?php echo $podetails['short_code']; ?></td>
                  <td><?php echo $podetails['name']; ?></td>
                  <td><?php echo $podetails['details']; ?></td>
                  <td><?php echo $podetails['req_id']; ?></td>
                  <td><?php if ($reqdetails['unit_type'] == 'decimal') echo $podetails['req_quantity'];
                      else echo round($podetails['req_quantity'], 0); ?> </td>
                  <td><?php if ($reqdetails['unit_type'] == 'decimal') echo $podetails['po_quantity'];
                      else echo round($podetails['po_quantity'], 0); ?> </td>
                  <td><?php echo $podetails['unit_name']; ?></td>

                  <td><?php echo $podetails['price']; ?></td>
                  <td class="text-center">
                    <div class="btn-group">
                      <a href="delete_po_items.php?id=<?php echo $po_id; ?>&det=<?php echo (int)$podetails['id']; ?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
                        <span class="glyphicon glyphicon-trash"></span>
                      </a>
                    </div>
                  </td>






                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <!-- <button type="submit" name="edit_grn" class="btn btn-info">Update</button> -->



        </div>

        <form method="post" action="edit_po.php?id=<?php echo (int)$po['id'] ?>">
          <div class="form-group"> <label>Terms & Conditions</label>
            <div class="input-group">

              <span class="input-group-addon">
                <i class="glyphicon glyphicon-paste"></i>
              </span>
              <select class="form-control" name="terms-conditions" id="terms-conditions">
                <option value="0">Select Terms & Condition</option>
                <?php foreach ($po_options as $poo) : ?>
                  <option value="<?php echo (int)$poo['id'] ?>">
                    <?php echo $poo['option_name'] . " : " . $poo['opton_details']; ?>
                  </option>
                <?php endforeach; ?>
              </select>

              <br /> <br />

              <select style="width: 200px;" class="form-control" name="terms-group">
                <option value="">New Select</option>
                <?php foreach ($po_options_groups as $pog) : ?>
                  <option value="<?php echo $pog['option_name'] ?>">
                    <?php echo $pog['option_name']; ?>
                  </option>
                <?php endforeach; ?>
              </select>

              <input type="text" style="width: 900px;" name="new-terms" placeholder="Details" class="form-control" />
              <button type="submit" name="add_terms" class="btn btn-info">Add Term</button>

            </div>
          </div>
        </form>


        <div class="panel-body">
          <table class="table table-bordered table-striped table-hover">
            <thead>
              <tr>
                <th>Terms</th>
                <th>Details</th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach ($po_option_list as $popl) :
              ?>
                <tr>
                  <td><?php echo $popl['option_name']; ?></td>
                  <td><?php echo $popl['opton_details']; ?></td>
                  <td class="text-center">
                    <div class="btn-group">
                      <a href="delete_po_terms.php?id=<?php echo $po_id; ?>&det=<?php echo (int)$popl['id']; ?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
                        <span class="glyphicon glyphicon-trash"></span>
                      </a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <!-- <button type="submit" name="edit_grn" class="btn btn-info">Update</button> -->



        </div>

        <?php



        $current_user = current_user();

        $login_level = $current_user['user_level'];



        ?>
        <form method="post" action="edit_po.php?id=<?php echo $po_id;  ?>">

          <button type="submit" name="final_submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to submit?');">submit</button>



        </form>








      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
<script>
  $(document).ready(function() {

    $("#terms-conditions").flexselect();

    $("#requisition-id").change(function() {

      var id = $('#requisition-id').val();

      //alert(id);

      if (id != '') {
        $.post('modal.php?call=6', {

            'req': id,

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