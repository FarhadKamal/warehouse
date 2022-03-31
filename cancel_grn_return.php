<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(4);
?>
<?php
  $grn_return = find_by_id('grn_return',(int)$_GET['id']);
  if(!$grn_return){
    $session->msg("d","Missing Return No.");
    redirect('grn_return.php', false);
  }




?>
<?php


 $grn_id=$grn_return['id'];
 $action_by= $_SESSION['user_id'];
 $query="update grn_return set cancel_status=1,cancel_by='$action_by',cancel_date=now()  where id=".$grn_id;


 
  if($db->query($query)){
      $session->msg("d","GRN Return cancelled.");

  

      redirect('grn_return.php', false);
  } else {
      $session->msg("d","GRN Return cancelation failed.");
       redirect('grn_return.php', false);
  }
?>
