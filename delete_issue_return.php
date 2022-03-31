<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(4);
?>
<?php
  $consume_return_details = find_by_id_sub_id('consume_return_details',(int)$_GET['det'],'rtn_id',(int)$_GET['id']);
  if(!$consume_return_details){
    $session->msg("d","Missing Return No.");
    redirect('issue_return.php' ,false);
  }



?>
<?php
  $delete_id = delete_by_id('consume_return_details',(int)$consume_return_details['id']);
  if($delete_id){
      $session->msg("d","Product deleted.");
      redirect('edit_issue_return.php?id='.$consume_return_details['rtn_id'], false);
  } else {
      $session->msg("d","Product deletion failed.");
      redirect('edit_issue_return.php?id='.$consume_return_details['rtn_id'], false);
  }
?>
