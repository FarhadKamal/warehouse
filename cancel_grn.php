<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(4);
?>
<?php
  $grn = find_by_id('grn',(int)$_GET['id']);
  if(!$grn){
    $session->msg("d","Missing GRN No.");
    redirect('grn.php', false);
  }




?>
<?php


 $grn_id=$grn['id'];
 $action_by= $_SESSION['user_id'];
 $query="update grn set cancel_status=1,cancel_by='$action_by',cancel_date=now()  where id=".$grn_id;


 
  if($db->query($query)){
      $session->msg("d","GRN cancelled.");

  

      redirect('grn.php', false);
  } else {
      $session->msg("d","Requisition cancelation failed.");
       redirect('grn.php', false);
  }
?>
