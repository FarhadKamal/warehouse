<?php
  $page_title = 'All Locations';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
  $com_id = find_value_by_sql(" select com from users where id=".$_SESSION['user_id'])['com'];
 
  $all_locations = find_by_sql('select * from locations where com='.$com_id )
?>
<?php




 if(isset($_POST['add_loc'])){
   $req_field = array('location-name');
   validate_fields($req_field);
   $loc_name = remove_junk($db->escape($_POST['location-name']));
   if(empty($errors)){
      
      $total=duplicate_check("locations","loc_name",$loc_name);
      if($total>0)
      {
         $session->msg("d", "Sorry, ".$loc_name." has aleady added.");
         redirect('add_location.php',false);
      }

      $sql  = "INSERT INTO locations (loc_name,com)";
      $sql .= " VALUES ('{$loc_name}','{$com_id}')";
      if($db->query($sql)){
        $session->msg("s", "Successfully Added Location");
        redirect('add_location.php',false);
      } else {
        $session->msg("d", "Sorry Failed to insert.");
        redirect('add_location.php',false);
      }
   } else {
     $session->msg("d", $errors);
     redirect('add_location.php',false);
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
            <span>Add New Location</span>
         </strong>
        </div>
        <div class="panel-body">
          <form method="post" action="add_location.php">
            <div class="form-group">
                <input type="text" class="form-control" name="location-name" placeholder="Location Name">
            </div>
            <button type="submit" name="add_loc" class="btn btn-primary">Add location</button>
        </form>
        </div>
      </div>
    </div>
    <div class="col-md-7">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>All Locations</span>
       </strong>
      </div>
        <div class="panel-body">
          <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>Locations</th>
                    <th class="text-center" style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
              <?php foreach ($all_locations as $loc):?>
                <tr>
                    <td class="text-center"><?php echo count_id();?></td>
                    <td><?php echo remove_junk(ucfirst($loc['loc_name'])); ?></td>
                    <td class="text-center">
                      <div class="btn-group">
                        <a href="edit_location.php?id=<?php echo (int)$loc['id'];?>"  class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
                          <span class="glyphicon glyphicon-edit"></span>
                        </a>
                        <a href="delete_location.php?id=<?php echo (int)$loc['id'];?>"  class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
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
