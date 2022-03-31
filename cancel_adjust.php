<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(4);
?>
<?php
  $adjust = find_by_id('adjust',(int)$_GET['id']);
  if(!$adjust){
    $session->msg("d","Missing Adjustment No.");
    redirect('adjustment.php', false);
  }




?>
<?php


 $adj_id=$adjust['id'];
 $action_by= $_SESSION['user_id'];
 $query="update adjust set cancel_status=1,cancel_by='$action_by',cancel_date=now()  where id=".$adj_id;


 
  if($db->query($query)){
      $session->msg("d","Adjustment cancelled.");

  

      redirect('adjustment.php', false);
  } else {
      $session->msg("d","Adjustment cancelation failed.");
       redirect('adjustment.php', false);
  }
?>
