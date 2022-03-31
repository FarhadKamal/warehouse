<?php
  $page_title = 'Edit Supplier';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(4);
?>
<?php
  //Display all catgories.
  $sup = find_by_id('suppliers',(int)$_GET['id']);
  if(!$sup){
    $session->msg("d","Missing Supplier id.");
    redirect('add_sup.php');
  }
?>

<?php
if(isset($_POST['edit_sup'])){
  $req_field = array('supplier-name');
  validate_fields($req_field);
  $sup_name = remove_junk($db->escape($_POST['supplier-name']));
  if(empty($errors)){

     $total=duplicate_check("suppliers","sup_name",$sup_name);
      if($total>0)
      {
         $session->msg("w", "Nothing updated, ".$sup_name." has aleady there.");
         redirect('add_sup.php',false);
      }



        $sql = "UPDATE suppliers SET sup_name='{$sup_name}'";
       $sql .= " WHERE id='{$sup['id']}'";
     $result = $db->query($sql);
     if($result && $db->affected_rows() === 1) {
       $session->msg("s", "Successfully updated Supplier");
       redirect('add_sup.php',false);
     } else {
       $session->msg("d", "Sorry! Failed to Update");
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
   <div class="col-md-5">
     <div class="panel panel-default">
       <div class="panel-heading">
         <strong>
           <span class="glyphicon glyphicon-th"></span>
           <span>Editing <?php echo remove_junk(ucfirst($sup['sup_name']));?></span>
        </strong>
       </div>
       <div class="panel-body">
         <form method="post" action="edit_sup.php?id=<?php echo (int)$sup['id'];?>">
           <div class="form-group">
               <input type="text" class="form-control" name="supplier-name" value="<?php echo remove_junk(ucfirst($sup['sup_name']));?>">
           </div>
           <button type="submit" name="edit_sup" class="btn btn-primary">Update Supplier</button>
       </form>
       </div>
       
     </div>
   </div>
</div>



<?php include_once('layouts/footer.php'); ?>
