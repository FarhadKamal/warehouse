<?php
require_once('includes/load.php');
// Checkin What level user has permission to view this page
page_require_level(5);
?>
<?php
$po = find_by_id_sub_id('po_terms', (int)$_GET['det'], 'po_id', (int)$_GET['id']);
if (!$po) {
  $session->msg("d", "Missing condition id.");
  redirect('edit_po.php?id=' . $_GET['id'], false);
}



?>
<?php
$delete_id = delete_by_id('po_terms', (int)$po['id']);
if ($delete_id) {
  $session->msg("d", "condition deleted.");
  redirect('edit_po.php?id=' . $po['po_id'], false);
} else {
  $session->msg("d", "condition deletion failed.");
  redirect('edit_po.php?id=' . $po['po_id'], false);
}
?>
