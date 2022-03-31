<?php
$page_title = 'All Units';
require_once('includes/load.php');
// Checkin What level user has permission to view this page
page_require_level(1);

$all_units = find_all('units')
?>
<?php




if(isset($_POST['add_unit'])){
 $req_field = array('unit-name');
 validate_fields($req_field);
 $unit_name = remove_junk($db->escape($_POST['unit-name']));
 $unit_type = remove_junk($db->escape($_POST['unit-type']));
 if(empty($errors)){

  $total=duplicate_check("units","unit_name",$unit_name);
  if($total>0)
  {
   $session->msg("d", "Sorry, ".$unit_name." has aleady added.");
   redirect('add_unit.php',false);
 }

 $sql  = "INSERT INTO units (unit_name,unit_type)";
 $sql .= " VALUES ('{$unit_name}','{$unit_type}')";
 if($db->query($sql)){
  $session->msg("s", "Successfully Added Unit");
  redirect('add_unit.php',false);
} else {
  $session->msg("d", "Sorry Failed to insert.");
  redirect('add_unit.php',false);
}
} else {
 $session->msg("d", $errors);
 redirect('add_unit.php',false);
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
  <div class="col-md-5">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Add New Unit</span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="add_unit.php">
          <div class="form-group">
            <div class="row">
              <div class="col-md-6">
                <input type="text" class="form-control" name="unit-name" placeholder="Unit Name">
              </div>
            </div> 
            <div class="row">
              <div class="col-md-6">                
                  <select class="form-control" name="unit-type">
                        <option value="number">number</option>
                        <option value="decimal">decimal</option>
                  </select>
              </div>
            </div>
          </div>
          <button type="submit" name="add_unit" class="btn btn-primary">Add Unit</button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-md-7">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>All Units</span>
        </strong>
      </div>
      <div class="panel-body">
        <table class="table table-bordered table-striped table-hover">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Units</th>
              <th>Type</th>
              <th class="text-center" style="width: 100px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($all_units as $unit):?>
              <tr>
                <td class="text-center"><?php echo count_id();?></td>
                <td><?php echo remove_junk(ucfirst($unit['unit_name'])); ?></td>
                 <td><?php echo remove_junk(ucfirst($unit['unit_type'])); ?></td>
                <td class="text-center">
                  <div class="btn-group">
                    <a href="edit_unit.php?id=<?php echo (int)$unit['id'];?>"  class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
                      <span class="glyphicon glyphicon-edit"></span>
                    </a>
                    <a href="delete_unit.php?id=<?php echo (int)$unit['id'];?>"  class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
                      <span class="glyphicon glyphicon-trash"></span>
                    </a>
                  </div>
                </td>

              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</div>
<?php include_once('layouts/footer.php'); ?>
