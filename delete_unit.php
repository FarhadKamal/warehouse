<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
?>
<?php
  $unit = find_by_id('units',(int)$_GET['id']);
  if(!$unit){
    $session->msg("d","Missing Unit id.");
    redirect('add_unit.php');
  }
?>
<?php

   
  $total=duplicate_check("products","unit_id",(int)$unit['id']);

  if($total>0)
  {
     $session->msg("w", "You cannot delete this unit, because it has assigned on some products");
     redirect('add_unit.php',false);
  }



  $delete_id = delete_by_id('units',(int)$unit['id']);
  if($delete_id){
      $session->msg("s","Unit deleted.");
      redirect('add_unit.php');
  } else {
      $session->msg("d","Unit deletion failed.");
      redirect('add_unit.php');
  }
?>
