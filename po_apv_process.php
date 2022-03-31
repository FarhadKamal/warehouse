<?php
require_once('includes/load.php');
// Checkin What level user has permission to view this page
page_require_level(5);
?>
<?php
$po = find_by_id('po', (int)$_GET['id']);
if (!$po) {
  $session->msg("d", "Missing PO No.");
  redirect('po_apv.php', false);
}




?>
<?php



$po_id = $po['id'];
$action_by = $_SESSION['user_id'];


if ($db->query($query)) {

  $db->query("update po set approve_status=1,approved_by={$action_by},approved_date=now() where id={$po_id}");


  $session->msg("s", "Approved.");
  redirect('po_apv.php', false);
} else {
  $session->msg("d", "PO approval failed.");
  redirect('po_apv.php', false);
}
?>
