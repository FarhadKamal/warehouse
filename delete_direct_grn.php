<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(4);
?>
<?php
  $grn_details = find_by_id_sub_id('grn_details',(int)$_GET['det'],'grn_id',(int)$_GET['id']);
  if(!$grn_details){
    $session->msg("d","Missing Return No.");
    redirect('grn.php' ,false);
  }



?>
<?php
  $delete_id = delete_by_id('grn_details',(int)$grn_details['id']);
  if($delete_id){
      $session->msg("d","Product deleted.");
      redirect('edit_direct_grn.php?id='.$grn_details['grn_id'], false);
  } else {
      $session->msg("d","Product deletion failed.");
      redirect('edit_direct_grn.php?id='.$grn_details['grn_id'], false);
  }
?>
