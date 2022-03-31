<?php
  $page_title = 'Edit unit';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
?>
<?php
  //Display all catgories.
  $unit = find_by_id('units',(int)$_GET['id']);
  if(!$unit){
    $session->msg("d","Missing unit id.");
    redirect('add_unit.php');
  }
?>

<?php
if(isset($_POST['edit_unit'])){
  $req_field = array('unit-name');
  validate_fields($req_field);
  $unit_name = remove_junk($db->escape($_POST['unit-name']));
  $unit_type = remove_junk($db->escape($_POST['unit-type']));
  if(empty($errors)){

     // $total=duplicate_check("units","unit_name",$unit_name);
     //  if($total>0)
     //  {
     //     $session->msg("w", "Nothing updated, ".$unit_name." has aleady there.");
     //     redirect('add_unit.php',false);
     //  }



        $sql = "UPDATE units SET unit_name='{$unit_name}',unit_type='{$unit_type}'";
       $sql .= " WHERE id='{$unit['id']}'";
     $result = $db->query($sql);
     if($result && $db->affected_rows() === 1) {
       $session->msg("s", "Successfully updated units");
       redirect('add_unit.php',false);
     } else {
       $session->msg("d", "Sorry! Failed to Update");
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
   <div class="col-md-5">
     <div class="panel panel-default">
       <div class="panel-heading">
         <strong>
           <span class="glyphicon glyphicon-th"></span>
           <span>Editing <?php echo remove_junk(ucfirst($unit['unit_name']));?></span>
        </strong>
       </div>
       <div class="panel-body">
         <form method="post" action="edit_unit.php?id=<?php echo (int)$unit['id'];?>">
           <div class="form-group">
               


               <div class="row">
                <div class="col-md-6">                
                    <input type="text" class="form-control" name="unit-name" value="<?php echo remove_junk(ucfirst($unit['unit_name']));?>">
                </div>
              </div>

               <div class="row">
                <div class="col-md-6">                
                    <select class="form-control" name="unit-type">
                          <option value="number"  <?php if($unit['unit_type']=='number')echo "selected"; ?> >number</option>
                          <option value="decimal" <?php if($unit['unit_type']=='decimal')echo "selected"; ?> >decimal</option>
                    </select>
                </div>
              </div>
           </div>
           <button type="submit" name="edit_unit" class="btn btn-primary">Update Unit</button>
       </form>
       </div>
     </div>
   </div>
</div>



<?php include_once('layouts/footer.php'); ?>
