<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(4);
?>
<?php
  $cost = find_by_id('cost_centre',(int)$_GET['id']);
  if(!$cost){
    $session->msg("d","Missing Cost Centre id.");
    redirect('add_cost.php');
  }
?>
<?php
  $delete_id = delete_by_id('cost_centre',(int)$cost['id']);
  if($delete_id){
      $session->msg("s","Cost Centre deleted.");
      redirect('add_cost.php');
  } else {
      $session->msg("d","Cost Centre deletion failed.");
      redirect('add_cost.php');
  }
?>
