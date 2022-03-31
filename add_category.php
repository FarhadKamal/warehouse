<?php
  $page_title = 'All parent categories';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
  
  $all_categories = find_by_sql('select * from categories where parent=0 order by short_code');
?>
<?php
 if(isset($_POST['add_cat'])){
   $req_field = array('categorie-name');





   validate_fields($req_field);
   $cat_name = remove_junk($db->escape($_POST['categorie-name']));

   $short_code = strtolower(remove_junk($db->escape($_POST['short-code'])));








   if(empty($errors)){

      



      $total=duplicate_check_cat("categories","name",$cat_name,0);
      if($total>0)
      {
         $session->msg("d", "Sorry, ".$cat_name." has aleady added.");
         redirect('add_category.php',false);
      }


      if(validate_short_code( $short_code )==false){
        $session->msg("d", "short code should be (2 to 4) digit alphanumeric");
        redirect('add_category.php',false);
      }


      $total=duplicate_check_cat("categories","short_code",$short_code,0);
      if($total>0)
      {
         $session->msg("d", "Sorry, short code: ".$short_code." has aleady added for a parent category.");
         redirect('add_category.php',false);
      }





      $sql  = "INSERT INTO categories (name,short_code)";
      $sql .= " VALUES ('{$cat_name}','{$short_code}')";
      if($db->query($sql)){
        $session->msg("s", "Successfully added new parent category");
        redirect('add_category.php',false);
      } else {
        $session->msg("d", "Sorry failed to insert.");
        redirect('add_category.php',false);
      }
   } else {
     $session->msg("d", $errors);
     redirect('add_category.php',false);
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
            <span>Add New Parent Category</span>
         </strong>
        </div>
        <div class="panel-body">
          <form method="post" action="add_category.php">
            <div class="form-group">
                <div class="row">
                  <div class="col-md-6">
                     <input type="text" class="form-control" name="categorie-name" placeholder="Categorie Name">
                  </div>  
                   <div class="col-md-6">
                     <input type="text" class="form-control" name="short-code" placeholder="Short Code">
                  </div>
                </div>  
               
            </div>
            <button type="submit" name="add_cat" class="btn btn-primary">Add category</button>
        </form>
        </div>
      </div>
    </div>
    <div class="col-md-7">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>All Parent Categories</span>
       </strong>
      </div>
        <div class="panel-body">
          <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                     <th>Short Code</th>
                    <th>Categories</th>
                    
                    <th class="text-center" style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
              <?php foreach ($all_categories as $cat):?>
                <tr>
                    <td class="text-center"><?php echo count_id();?></td>
                    <td><?php echo $cat['short_code']; ?></td>
                    <td><?php echo remove_junk(ucfirst($cat['name'])); ?></td>
                   
                    <td class="text-center">
                      <div class="btn-group">
                        <a href="edit_category.php?id=<?php echo (int)$cat['id'];?>"  class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
                          <span class="glyphicon glyphicon-edit"></span>
                        </a>
                        <a href="delete_category.php?id=<?php echo (int)$cat['id'];?>"  class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
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
