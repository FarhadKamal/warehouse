<?php
  $page_title = 'All Suppliers';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(5);
  
  $all_sup = find_all('suppliers')
?>
<?php




 if(isset($_POST['add_sup'])){
   $req_field = array('supplier-name');
   validate_fields($req_field);
   $sup_name = remove_junk($db->escape($_POST['supplier-name']));
   if(empty($errors)){
      
      $total=duplicate_check("suppliers","sup_name",$sup_name);
      if($total>0)
      {
         $session->msg("d", "Sorry, ".$sup_name." has aleady added.");
         redirect('add_sup.php',false);
      }

      $sql  = "INSERT INTO suppliers (sup_name)";
      $sql .= " VALUES ('{$sup_name}')";
      if($db->query($sql)){
        $session->msg("s", "Successfully Added Supplier");
        redirect('add_sup.php',false);
      } else {
        $session->msg("d", "Sorry Failed to insert.");
        redirect('add_sup.php',false);
      }
   } else {
     $session->msg("d", $errors);
     redirect('add_sup.php',false);
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
            <span>Add New Supplier</span>
         </strong>
        </div>
        <div class="panel-body">
          <form method="post" action="add_sup.php">
            <div class="form-group">
                <input type="text" class="form-control" name="supplier-name" placeholder="Supplier Name">
            </div>
            <button type="submit" name="add_sup" class="btn btn-primary">Add Supplier</button>
        </form>
        </div>
      </div>
    </div>
    <div class="col-md-7">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>All Suppliers</span>
       </strong>
      </div>
        <div class="panel-body">
          <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>Name</th>
                    <th class="text-center" style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
              <?php foreach ($all_sup as $sup):?>
                <tr>
                    <td class="text-center"><?php echo count_id();?></td>
                    <td><?php echo remove_junk(ucfirst($sup['sup_name'])); ?></td>
                    <td class="text-center">
                      <div class="btn-group">
                        <a href="edit_sup.php?id=<?php echo (int)$sup['id'];?>"  class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
                          <span class="glyphicon glyphicon-edit"></span>
                        </a>
                        <a href="delete_sup.php?id=<?php echo (int)$sup['id'];?>"  class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
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
