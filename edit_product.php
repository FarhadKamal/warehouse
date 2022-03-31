<?php
  $page_title = 'Edit product';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(2);
?>
<?php
$product = find_by_id('products',(int)$_GET['id']);
$all_categories = find_by_sql('select * from categories order by short_code');
$all_photo = find_by_sql('select * from media where id<>3');
$all_units = find_all('units');
if(!$product){
  $session->msg("d","Missing product id.");
  redirect('product.php');
}
?>
<?php
 if(isset($_POST['product'])){
    $req_fields = array('product-name','product-categorie','product-unit' );
    validate_fields($req_fields);

   if(empty($errors)){
     

     $p_name  = remove_junk($db->escape($_POST['product-name']));
     $p_cat   = remove_junk($db->escape($_POST['product-categorie']));
     $p_old   = remove_junk($db->escape($_POST['old-categorie']));
     $p_unit   = remove_junk($db->escape($_POST['product-unit']));


       if (is_null($_POST['product-photo']) || $_POST['product-photo'] === "") {
         $media_id = '0';
       } else {
         $media_id = remove_junk($db->escape($_POST['product-photo']));
       }



       $short_code=generate_short_code_edit($p_cat,$p_old,$product['id']);

       $query   = "UPDATE products SET";
       $query  .=" name ='{$p_name}',short_code ='{$short_code}',";
       $query  .=" unit_id ='{$p_unit}', categorie_id ='{$p_cat}',media_id='{$media_id}'";
       $query  .=" WHERE id ='{$product['id']}'";
       $result = $db->query($query);
               if($result && $db->affected_rows() === 1){
                 $session->msg('s',"Product updated ");
                 redirect('product.php', false);
               } else {
                 $session->msg('d',' Sorry failed to updated!');
                 redirect('edit_product.php?id='.$product['id'], false);
               }

   } else{
       $session->msg("d", $errors);
       redirect('edit_product.php?id='.$product['id'], false);
   }

 }

?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>
  <div class="row">
      <div class="panel panel-default">
        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>Add New Product</span>
         </strong>
        </div>
        <div class="panel-body">
         <div class="col-md-7">
           <form method="post" action="edit_product.php?id=<?php echo (int)$product['id'] ?>">
              <div class="form-group">
                <label>Product Name</label>
                <div class="input-group">
                  <span class="input-group-addon">
                   <i class="glyphicon glyphicon-th-large"></i>
                  </span>
                  <input type="text" class="form-control" name="product-name" value="<?php echo remove_junk($product['name']);?>">
               </div>
              </div>
              <br/>
              <div class="form-group">
                <div class="row">
                  <div class="col-md-12">
                    <label >Category</label>
                    <select class="form-control" name="product-categorie">
                    <option value=""> Select a categorie</option>
                   <?php  foreach ($all_categories as $cat): ?>
                     <option value="<?php echo (int)$cat['id']; ?>" <?php if($product['categorie_id'] === $cat['id']): echo "selected"; endif; ?> >
                       <?php echo remove_junk($cat['short_code']); ?></option>
                   <?php endforeach; ?>
                 </select>
                 <input type="hidden" name="old-categorie" value="<?php echo $product['categorie_id']; ?>" />
                  </div>           
                </div>
              </div>


              <div class="form-group">
                <div class="row">
                  <div class="col-md-6">
                    <label >Unit</label>
                    <select class="form-control" name="product-unit">
                   <?php  foreach ($all_units as $unit): ?>
                     <option value="<?php echo (int)$unit['id']; ?>" <?php if($product['unit_id'] === $unit['id']): echo "selected"; endif; ?> >
                       <?php echo remove_junk($unit['unit_name']); ?></option>
                   <?php endforeach; ?>
                 </select>
                  </div>
                  <div class="col-md-6">
                    <label>Photo</label>
                    <select class="form-control" name="product-photo">
                      <option value="3"> No image</option>
                      <?php  foreach ($all_photo as $photo): ?>
                        <option value="<?php echo (int)$photo['id'];?>" <?php if($product['media_id'] === $photo['id']): echo "selected"; endif; ?> >
                          <?php echo $photo['file_name'] ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
              </div>

             
              <button type="submit" name="product" class="btn btn-danger">Update</button>
          </form>
         </div>
        </div>
      </div>
  </div>

<?php include_once('layouts/footer.php'); ?>
