<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(4);
?>
<?php
  $adjust_details = find_by_id_sub_id('adjust_details',(int)$_GET['det'],'adj_id',(int)$_GET['id']);
  if(!$adjust_details){
    $session->msg("d","Missing Adjustment No.");
    redirect('adjustment.php' ,false);
  }



?>
<?php
  $delete_id = delete_by_id('adjust_details',(int)$adjust_details['id']);
  if($delete_id){
      $session->msg("d","Product deleted.");
      redirect('edit_adjust.php?id='.$adjust_details['adj_id'], false);
  } else {
      $session->msg("d","Product deletion failed.");
      redirect('edit_adjust.php?id='.$adjust_details['adj_id'], false);
  }
?>
