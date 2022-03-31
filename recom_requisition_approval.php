<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(5);
?>
<?php
  $requisition = find_by_id('requisition',(int)$_GET['id']);
  if(!$requisition){
    $session->msg("d","Missing requisition id.");
    redirect('proc_req_list.php', false);
  }




?>
<?php


 $session->msg("s","Requisition recommended.");
      $req_id=$requisition['id'];
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

      'Recommended'
      )";


  if($db->query($query)){
  	$recom_by= $_SESSION['user_id'];
    $db->query("update requisition set recom_status=1,recom_track=concat(recom_track,',',{$recom_by}) where id={$req_id}");



      redirect('proc_req_list.php', false);
  } else {
      $session->msg("d","Requisition recommendation failed.");
       redirect('proc_req_list.php', false);
  }
?>
