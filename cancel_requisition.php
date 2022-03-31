<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(4);
?>
<?php
  $requisition = find_by_id('requisition',(int)$_GET['id']);
  if(!$requisition){
    $session->msg("d","Missing requisition id.");
    redirect('edit_requisition.php?id='.$_GET['id'], false);
  }




?>
<?php
 $query="update requisition set cancel_status=1 where id=".$requisition['id'];


  $req_id=$requisition['id'];
  $action_by= $_SESSION['user_id'];
  if($db->query($query)){
      $session->msg("d","Requisition cancelled.");

      $action_by= $_SESSION['user_id'];
      $query="insert into requisition_action 
      (
      req_id, 
      action_by, 
      action_details

      )
      values
      (
      '{$req_id}', 
      '{$action_by}', 

      'Cancelled'
      )";

      $db->query($query);

      redirect('requisition.php', false);
  } else {
      $session->msg("d","Requisition cancelation failed.");
       redirect('requisition.php', false);
  }
?>
