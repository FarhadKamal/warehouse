<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(4);
?>
<?php
  $requisition = find_by_id('requisition',(int)$_GET['id']);
  if(!$requisition){
    $session->msg("d","Missing requisition id.");
    redirect('requisition.php', false);
  }




?>
<?php
 $query="update requisition set complete_status=1,complete_date=now() where id=".$requisition['id'];


  $req_id=$requisition['id'];
  $action_by= $_SESSION['user_id'];
  if($db->query($query)){
      $session->msg("s","Requisition completeted!");

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

      'Completed'
      )";

      $db->query($query);

      redirect('requisition.php', false);
  } else {
      $session->msg("d","Requisition completion failed.");
       redirect('requisition.php', false);
  }
?>
