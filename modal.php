<?php

require_once('includes/load.php');



if ($_GET['call'] == 1) get_short_code($_POST['scode']);
else if ($_GET['call'] == 2) get_unit($_POST['pid']);
else if ($_GET['call'] == 3) get_product_by_rec($_POST['req']);
else if ($_GET['call'] == 4) get_grn_unit($_POST['pid']);
else if ($_GET['call'] == 5) get_supplier_by_id($_POST['sid']);
else if ($_GET['call'] == 6) get_product_for_po($_POST['req']);



function get_short_code($id)
{

  $sql = "select short_code from categories where id=" . $id;

  echo find_value_by_sql($sql)['short_code'];
}


function get_grn_unit($id)
{

  $sql = "select unit_name,unit_type from products inner  join units
  on units.id=products.unit_id  where products.id=" . $id;

  $data = find_value_by_sql($sql);

  $unit_name = $data['unit_name'];
  $unit_type = $data['unit_type'];


  echo '    <label >Quantity</label><table><tr><td>';
  if ($unit_type == 'decimal')
    echo '<input type="number" class="form-control" name="product-quantity" step="any">';
  else echo '<input type="number" class="form-control" name="product-quantity" >';
  echo '</td><td>';

  echo  $unit_name . "&nbsp;&nbsp;&nbsp;";



  echo '</td><tr></table>';

  echo '<br/>';

  echo '    <label >Price</label><table><tr><td>';

  echo '<input type="number" class="form-control" required name="product-price"  step="any">';

  echo '</td><td>';



  echo '<button type="submit" name="add_item" id="add" class="btn btn-success">Add</button>';
  echo '</td><tr></table>';
}



function get_unit($id)
{

  $sql = "select unit_name,unit_type from products inner  join units
  on units.id=products.unit_id  where products.id=" . $id;

  $data = find_value_by_sql($sql);

  $unit_name = $data['unit_name'];
  $unit_type = $data['unit_type'];


  echo '    <label >Quantity</label><table><tr><td>';
  if ($unit_type == 'decimal')
    echo '<input type="number" class="form-control" name="product-quantity" step="any">';
  else echo '<input type="number" class="form-control" name="product-quantity" >';
  echo '</td><td>';

  echo  $unit_name . "&nbsp;&nbsp;&nbsp;";
  echo '</td><td>';


  echo '<button type="submit" name="add_requisition" id="add" class="btn btn-success">Add</button>';
  echo '</td><tr></table>';
}


function get_product_by_rec($id)
{

  $sql = "select requisition_details.*,name,products.id as pid,short_code, unit_name,unit_type from requisition_details
  inner join products on requisition_details.product_id=products.id
  inner join units on units.id=products.unit_id
  where requisition_details.req_id=" . $id . " order by short_code";

  $all_products = find_by_sql($sql);




  echo '<table class="table table-bordered table-striped table-hover">';
  echo  '<thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>  
              <th>Short Code</th>
              <th>Products</th>
              <th>Quantity</th>
              <th>Unit</th>
            </tr>
          </thead>
          <tbody>';




  foreach ($all_products as $prd) :
    $pid = (int)$prd['pid'];
    $unit_type = $prd['unit_type'];
    echo '<tr>';
    echo  '<td class="text-center">' . count_id() . '</td>';
    echo  '<td class="text-center">' . $prd['short_code'] . '</td>';
    echo  '<td class="text-center">' . $prd['name'] . '</td>';
    if ($unit_type == 'decimal') {
      echo  '<td class="text-center">' . $prd['quantity'] . '</td>';
    } else echo  '<td class="text-center">' . intval($prd['quantity']) . '</td>';
    echo  '<td class="text-center">' . $prd['unit_name'] . '</td>';
    echo '</tr>';
  endforeach;

  echo '</tbody></table>';

  echo '<button type="submit" name="add_grn" id="add" class="btn btn-success">Save</button>';
}




function get_supplier_by_id($id)
{

  $sql = "select * from suppliers where id=" . $id;

  $sup = find_value_by_sql($sql);

  echo '<table class="table table-bordered table-striped table-hover">';


  echo '<tr>';
  echo '<td>Address:</td>';
  echo '<td><input type="text" style="width:600px;"  value="' . $sup['sup_address'] . '" name="supplier-address"/></td>';
  echo '</tr>';

  echo '<tr>';
  echo '<td>Email:</td>';
  echo '<td><input type="text" value="' . $sup['sup_email'] . '" name="supplier-email"/></td>';
  echo '</tr>';

  echo '<tr>';
  echo '<td>Phone/Mobile:</td>';
  echo '<td><input type="text" value="' . $sup['sup_mobile'] . '" name="supplier-mobile"/></td>';
  echo '</tr>';

  echo '<tr>';
  echo '<td>Contact Person:</td>';
  echo '<td><input type="text" style="width:400px;" value="' . $sup['attn'] . '" name="contact-person"/></td>';
  echo '</tr>';

  echo '</table>';
  echo '<button type="submit" name="add_po" id="add" class="btn btn-success">Save</button>';
}



function get_product_for_po($id)
{

  $sql = "select requisition_details.*,name,products.id as pid,short_code, unit_name,unit_type from requisition_details
  inner join products on requisition_details.product_id=products.id
  inner join units on units.id=products.unit_id
  where requisition_details.req_id=" . $id . " order by short_code";

  $all_products = find_by_sql($sql);




  echo '<table class="table table-bordered table-striped table-hover">';
  echo  '<thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>  
              <th>Short Code</th>
              <th>Products</th>
              <th>Details</th>
              <th>Req. Quantity</th>
              <th>PO. Quantity</th>
              <th>Unit</th>
              <th>Total Price</th>
            </tr>
          </thead>
          <tbody>';



  $sl = 0;
  foreach ($all_products as $prd) :
    $pid = (int)$prd['pid'];
    $unit_type = $prd['unit_type'];
    echo '<tr>';
    echo  '<td class="text-center">' . count_id() . '</td>';
    echo  '<td class="text-center">' . $prd['short_code'] . '</td>';
    echo  '<td class="text-center">' . $prd['name'] . '</td>';

    echo  '<td class="text-center"> <input type="text" name="pdetails[]" value="' . $prd['details'] . '" /></td>';
    if ($unit_type == 'decimal') {
      echo  '<td class="text-center">' . $prd['quantity'] . '</td>';
      echo  '<td class="text-center">
    <input type="hidden" name="pid[]" value="' . $prd['pid'] . '" />
    <input type="hidden" name="reqqty[]" value="' . $prd['quantity'] . '" />
    <input type="text" style="width:60px" name="pqqty[]" value="' . $prd['quantity'] . '" />
    <input type="checkbox" name="pqqtychk[]" value="' . $sl . '" /></td>';
    } else {
      echo  '<td class="text-center">' . intval($prd['quantity']) . '</td>';
      echo  '<td class="text-center">
    <input type="hidden" name="pid[]" value="' . intval($prd['pid']) . '" />
    <input type="hidden" name="reqqty[]" value="' . intval($prd['quantity']) . '" />
    <input type="text" style="width:60px" name="pqqty[]" value="' . intval($prd['quantity']) . '" />
    <input type="checkbox" name="pqqtychk[]" value="' . $sl . '" /></td>';
    }




    echo  '<td class="text-center">' . $prd['unit_name'] . '</td>';

    echo  '<td class="text-center"> <input type="number" step="0.01" name="price[]"  /></td>';
    echo '</tr>';





    $sl++;
  endforeach;

  echo '</tbody></table>';

  echo '<button type="submit" name="add_item_po" id="add" class="btn btn-success">Add</button>';
}
