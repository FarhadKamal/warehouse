<?php
require_once('includes/load.php');
// Checkin What level user has permission to view this page
page_require_level(5);
?>
<?php
$po = find_by_id('po', (int)$_GET['id']);

if (!$po) {
  $session->msg("d", "Missing PO No.");
  redirect('po.php', false);
}

$po_id = $po['id'];


$action_by = $_SESSION['user_id'];

$db->query("update po set cancel_status=1,cancel_by={$action_by},cancel_date=now() where id={$po_id}");

$session->msg("d", "PO canceled.");
redirect('po.php', false);


?>
