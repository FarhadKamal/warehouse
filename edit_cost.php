<?php
  $page_title = 'Edit Cost Centre';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(4);
?>
<?php
  //Display all catgories.
  $cost = find_by_id('cost_centre',(int)$_GET['id']);
  if(!$cost){
    $session->msg("d","Missing Cost Centre id.");
    redirect('add_cost.php');
  }
?>

<?php
if(isset($_POST['edit_cost'])){
  $req_field = array('cost-centre-name');
  validate_fields($req_field);
  $cost_name = remove_junk($db->escape($_POST['cost-centre-name']));
  if(empty($errors)){

     $total=duplicate_check("cost_centre","cost_name",$cost_name);
      if($total>0)
      {
         $session->msg("w", "Nothing updated, ".$cost_name." has aleady there.");
         redirect('add_cost.php',false);
      }



        $sql = "UPDATE cost_centre SET cost_name='{$cost_name}'";
       $sql .= " WHERE id='{$cost['id']}'";
     $result = $db->query($sql);
     if($result && $db->affected_rows() === 1) {
       $session->msg("s", "Successfully updated Cost Centre");
       redirect('add_cost.php',false);
     } else {
       $session->msg("d", "Sorry! Failed to Update");
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
   <div class="col-md-5">
     <div class="panel panel-default">
       <div class="panel-heading">
         <strong>
           <span class="glyphicon glyphicon-th"></span>
           <span>Editing <?php echo remove_junk(ucfirst($cost['cost_name']));?></span>
        </strong>
       </div>
       <div class="panel-body">
         <form method="post" action="edit_cost.php?id=<?php echo (int)$cost['id'];?>">
           <div class="form-group">
               <input type="text" class="form-control" name="cost-centre-name" value="<?php echo remove_junk(ucfirst($cost['cost_name']));?>">
           </div>
           <button type="submit" name="edit_cost" class="btn btn-primary">Update Cost Centre</button>
       </form>
       </div>
       
     </div>
   </div>
</div>



<?php include_once('layouts/footer.php'); ?>
