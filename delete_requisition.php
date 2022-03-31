<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(4);
?>
<?php
  $requisition = find_by_id_sub_id('requisition_details',(int)$_GET['det'],'req_id',(int)$_GET['id']);
  if(!$requisition){
    $session->msg("d","Missing requisition id.");
    redirect('edit_requisition.php?id='.$_GET['id'], false);
  }



?>
<?php
  $delete_id = delete_by_id('requisition_details',(int)$requisition['id']);
  if($delete_id){
      $session->msg("d","Product deleted.");
      redirect('edit_requisition.php?id='.$requisition['req_id'], false);
  } else {
      $session->msg("d","Product deletion failed.");
      redirect('edit_requisition.php?id='.$requisition['req_id'], false);
  }
?>
