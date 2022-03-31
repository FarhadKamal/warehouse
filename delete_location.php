<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
?>
<?php
  $location = find_by_id('locations',(int)$_GET['id']);
  if(!$location){
    $session->msg("d","Missing Location id.");
    redirect('add_location.php');
  }
?>
<?php
  $delete_id = delete_by_id('locations',(int)$location['id']);
  if($delete_id){
      $session->msg("s","Location deleted.");
      redirect('add_location.php');
  } else {
      $session->msg("d","Location deletion failed.");
      redirect('add_location.php');
  }
?>
