<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(4);
?>
<?php
  $transfer = find_by_id_sub_id('transfer_details',(int)$_GET['det'],'tran_id',(int)$_GET['id']);
  if(!$transfer){
    $session->msg("d","Missing transfer No.");
    redirect('transfer.php' ,false);
  }



?>
<?php
  $delete_id = delete_by_id('transfer_details',(int)$transfer['id']);
  if($delete_id){
      $session->msg("d","Product deleted.");
      redirect('edit_transfer.php?id='.$transfer['tran_id'], false);
  } else {
      $session->msg("d","Product deletion failed.");
      redirect('edit_transfer.php?id='.$transfer['tran_id'], false);
  }
?>
