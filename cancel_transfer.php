<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(4);
?>
<?php
  $transfer = find_by_id('transfer',(int)$_GET['id']);
  if(!$transfer){
    $session->msg("d","Missing Transfer No.");
    redirect('transfer.php', false);
  }




?>
<?php


 $tran_id=$transfer['id'];
 $action_by= $_SESSION['user_id'];
 $query="update transfer set cancel_status=1,cancel_by='$action_by',cancel_date=now()  where id=".$tran_id;


 
  if($db->query($query)){
      $session->msg("d","Transfer cancelled.");

  

      redirect('transfer.php', false);
  } else {
      $session->msg("d","Transfer cancelation failed.");
       redirect('transfer.php', false);
  }
?>
