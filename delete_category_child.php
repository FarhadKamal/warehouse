<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
?>
<?php
  $categorie = find_by_id('categories',(int)$_GET['id']);
  if(!$categorie){
    $session->msg("d","Missing Child Category Id.");
    redirect('add_category_child.php');
  }
?>
<?php


  $total=duplicate_check("categories","parent",(int)$categorie['id']);

  if($total>0)
  {
     $session->msg("w", "You cannot delete this category, because it has sub category.");
     redirect('add_category_child.php',false);
  }


 $total=duplicate_check("products","categorie_id",(int)$categorie['id']);

  if($total>0)
  {
     $session->msg("w", "You cannot delete this category, because it has assigned on some products");
     redirect('add_category_child.php',false);
  }



  $delete_id = delete_by_id('categories',(int)$categorie['id']);








  if($delete_id){
      $session->msg("s","Child Category deleted.");
      redirect('add_category_child.php');
  } else {
      $session->msg("d","Child Category deletion failed.");
      redirect('add_category_child.php');
  }
?>
