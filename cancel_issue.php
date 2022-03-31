<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(4);
?>
<?php
  $consume = find_by_id('consume',(int)$_GET['id']);
  if(!$consume){
    $session->msg("d","Missing Issue No.");
    redirect('consume.php', false);
  }




?>
<?php


 $cron_id=$consume['id'];
 $action_by= $_SESSION['user_id'];
 $query="update consume set cancel_status=1,cancel_by='$action_by',cancel_date=now()  where id=".$cron_id;


 
  if($db->query($query)){
      $session->msg("d","Issue cancelled.");

  

      redirect('consume.php', false);
  } else {
      $session->msg("d","Issue cancelation failed.");
       redirect('consume.php', false);
  }
?>
