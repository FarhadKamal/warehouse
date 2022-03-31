<?php
  $page_title = 'All Cost Centre';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(4);
  $com_id = find_value_by_sql(" select com from users where id=".$_SESSION['user_id'])['com'];

  $all_cost = find_by_sql('select * from cost_centre where com='.$com_id )
?>
<?php




 if(isset($_POST['add_cost'])){
   $req_field = array('cost-centre-name');
   validate_fields($req_field);
   $cost_name = remove_junk($db->escape($_POST['cost-centre-name']));
   if(empty($errors)){
      
      $total=duplicate_check("cost_centre","cost_name",$cost_name);
      if($total>0)
      {
         $session->msg("d", "Sorry, ".$cost_name." has aleady added.");
         redirect('add_cost.php',false);
      }

      $sql  = "INSERT INTO cost_centre (cost_name,com)";
      $sql .= " VALUES ('{$cost_name}','{$com_id}')";
      if($db->query($sql)){
        $session->msg("s", "Successfully Added Cost Centre");
        redirect('add_cost.php',false);
      } else {
        $session->msg("d", "Sorry Failed to insert.");
        redirect('add_cost.php',false);
      }
   } else {
     $session->msg("d", $errors);
     redirect('add_cost.php',false);
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
            <span>Add New Cost Centre</span>
         </strong>
        </div>
        <div class="panel-body">
          <form method="post" action="add_cost.php">
            <div class="form-group">
                <input type="text" class="form-control" name="cost-centre-name" placeholder="Cost Centre Name">
            </div>
            <button type="submit" name="add_cost" class="btn btn-primary">Add Cost Centre</button>
        </form>
        </div>
      </div>
    </div>
    <div class="col-md-7">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Cost Centre</span>
       </strong>
      </div>
        <div class="panel-body">
          <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>Name</th>
                    <th>Code</th>
                    <th class="text-center" style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
              <?php foreach ($all_cost as $cost):?>
                <tr>
                    <td class="text-center"><?php echo count_id();?></td>
                    <td><?php echo remove_junk(ucfirst($cost['cost_name'])); ?></td>
                      <td>#<?php echo remove_junk(ucfirst($cost['id'])); ?></td>
                    <td class="text-center">
                      <div class="btn-group">
                        <a href="edit_cost.php?id=<?php echo (int)$cost['id'];?>"  class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
                          <span class="glyphicon glyphicon-edit"></span>
                        </a>
                        <a href="delete_cost.php?id=<?php echo (int)$cost['id'];?>"  class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
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
