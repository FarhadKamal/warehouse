<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(4);
?>
<?php
  $consume_return = find_by_id('consume_return',(int)$_GET['id']);
  if(!$consume_return){
    $session->msg("d","Missing Return No.");
    redirect('issue_return.php', false);
  }




?>
<?php


 $cron_id=$consume_return['id'];
 $action_by= $_SESSION['user_id'];
 $query="update consume_return set cancel_status=1,cancel_by='$action_by',cancel_date=now()  where id=".$cron_id;


 
  if($db->query($query)){
      $session->msg("d","Issue Return cancelled.");

  

      redirect('issue_return.php', false);
  } else {
      $session->msg("d","Issue Return cancelation failed.");
       redirect('issue_return.php', false);
  }
?>
