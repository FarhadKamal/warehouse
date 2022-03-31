<?php
$page_title = 'Home Page';
require_once('includes/load.php');
if (!$session->isUserLoggedIn(true)) {
  redirect('index.php', false);
}
?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
  <?php
  $current_user = current_user();

  $login_level = $current_user['user_level'];

  if ($login_level == 4) {

  ?>

    <div class="col-md-3">
      <div class="panel panel-box clearfix">
        <div class="panel-icon pull-left bg-red">
          <i class="glyphicon glyphicon-volume-up"></i>
        </div>
        <div class="panel-value pull-right">
          <h2 class="margin-top"><a href="requisition_approval.php">
              <?php


              if ( in_array($_SESSION['user_id'], array(4,0))){
                $multi_com = find_value_by_sql(" select multi_com from users where id=".$_SESSION['user_id'])['multi_com'];
               
               $total_pending    = find_value_by_sql('SELECT  count(*) as tot FROM requisition r
                inner join users u on u.id=r.submit_by
                where cancel_status=0 and approve_status=0  and r.com in ('. $multi_com.') and
                r.id in(select distinct req_id from requisition_action where action_by =7  or action_by =16 ) ');
               } else $total_pending    = find_value_by_sql('select count(distinct req_id) as tot from requisition_details where 
            sub_approved_status=0 and forward_status=1 and forward_to=' . $_SESSION['user_id']);
              echo $total_pending['tot'];


              ?> </a></h2>
          <p class="text-muted">Pending Requisition</p>
        </div>
      </div>
    </div>

  <?php } ?>
  <div class="col-md-12">
    <div class="panel">
      <div class="jumbotron text-center">
        <h1>Have a nice day!</h1>

      </div>
    </div>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>


<?php


if ($login_level == 4) {

?>
  <meta http-equiv="refresh" content="5">
<?php
}
?>