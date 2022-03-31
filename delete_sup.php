<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(4);
?>
<?php
  $sup = find_by_id('suppliers',(int)$_GET['id']);
  if(!$sup){
    $session->msg("d","Missing Supplier id.");
    redirect('add_sup.php');
  }
?>
<?php
  $delete_id = delete_by_id('suppliers',(int)$sup['id']);
  if($delete_id){
      $session->msg("s","Supplier deleted.");
      redirect('add_sup.php');
  } else {
      $session->msg("d","Supplier deletion failed.");
      redirect('add_sup.php');
  }
?>
