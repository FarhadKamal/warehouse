<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(4);
?>
<?php
  $consume = find_by_id_sub_id('consume_details',(int)$_GET['det'],'con_id',(int)$_GET['id']);
  if(!$consume){
    $session->msg("d","Missing Issue No.");
    redirect('consume.php' ,false);
  }



?>
<?php
  $delete_id = delete_by_id('consume_details',(int)$consume['id']);
  if($delete_id){
      $session->msg("d","Product deleted.");
      redirect('edit_consume.php?id='.$consume['con_id'], false);
  } else {
      $session->msg("d","Product deletion failed.");
      redirect('edit_consume.php?id='.$consume['con_id'], false);
  }
?>
