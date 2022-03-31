<link rel="stylesheet" href="libs/css/flexselect.css" type="text/css" media="screen" />
<?php
  $page_title = 'Add Product';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);
  $all_categories = find_by_sql('select * from categories order by short_code');
  $all_photo = find_by_sql('select * from media where id<>3');
  $all_units = find_all('units');
?>
<?php
 if(isset($_POST['add_product'])){
   $req_fields = array('product-name','product-categorie','product-unit' );
   validate_fields($req_fields);
   if(empty($errors)){
     

    
  







     $p_name  = remove_junk($db->escape($_POST['product-name']));
     $p_cat   = remove_junk($db->escape($_POST['product-categorie']));
     $p_unit   = remove_junk($db->escape($_POST['product-unit']));


    $total=duplicate_check("products","name",$p_name);
    if($total>0)
    {
       $session->msg("d", "Sorry, ".$p_name." has aleady added.");
       redirect('add_product.php',false);
    }
  
  
     if (is_null($_POST['product-photo']) || $_POST['product-photo'] === "") {
       $media_id = '0';
     } else {
       $media_id = remove_junk($db->escape($_POST['product-photo']));
     }


     $short_code=generate_short_code($p_cat);

     $date    = make_date();
     $query  = "INSERT INTO products (";
     $query .=" name,unit_id,categorie_id,media_id,date,short_code";
     $query .=") VALUES (";
     $query .=" '{$p_name}', '{$p_unit}',  '{$p_cat}', '{$media_id}', '{$date}', '{$short_code}'";
     $query .=")";
     //$query .=" ON DUPLICATE KEY UPDATE name='{$p_name}'";
     if($db->query($query)){
       $session->msg('s',"Product added ");
       redirect('add_product.php', false);
     } else {
       $session->msg('d',' Sorry failed to added!');
       redirect('product.php', false);
     }

   } else{
     $session->msg("d", $errors);
     redirect('add_product.php',false);
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
  <div class="col-md-8">
      <div class="panel panel-default">
        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>Add New Product</span>
         </strong>
        </div>
        <div class="panel-body">
         <div class="col-md-12">
          <form method="post" action="add_product.php" class="clearfix">
              <div class="form-group"> <label>Product Name</label>
                <div class="input-group">

                  <span class="input-group-addon">
                   <i class="glyphicon glyphicon-th-large"></i>
                  </span>
                  <input type="text" class="form-control" name="product-name" placeholder="Product Name">
               </div>
              </div>
              <div class="form-group">
                <div class="row">
                  <div class="col-md-12">
                    <label >Category</label>
                    <select class="form-control" id="product-category" name="product-categorie">
                      <option value="">Select Product Category</option>
                    <?php  foreach ($all_categories as $cat): ?>
                      <option value="<?php echo (int)$cat['id'] ?>">
                        <?php echo $cat['short_code'] ?> <?php echo " # " ?><?php echo $cat['name'] ?></option>
                    <?php endforeach; ?>
                    </select>
                  </div>
                 
                </div>
                <br/>
                 <div class="row">
                 
                   <div class="col-md-6">
                    <label>Photo</label>
                    <select class="form-control" name="product-photo">
                      <option value="3">Select Product Photo</option>
                    <?php  foreach ($all_photo as $photo): ?>
                      <option value="<?php echo (int)$photo['id'] ?>">
                        <?php echo $photo['file_name'] ?></option>
                    <?php endforeach; ?>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label>Unit</label>
                    <select class="form-control" name="product-unit">
                      <option value="">Select Unit</option>
                    <?php  foreach ($all_units as $unit): ?>
                      <option value="<?php echo (int)$unit['id'] ?>">
                        <?php echo $unit['unit_name'] ?></option>
                    <?php endforeach; ?>
                    </select>
                  </div>
                </div>
              </div>

              <button type="submit" name="add_product" class="btn btn-danger">Add product</button>
          </form>
         </div>
        </div>
      </div>
    </div>
  </div>

<?php include_once('layouts/footer.php'); ?>
 <script>
    $(document).ready(function() {

    $("#product-category").flexselect();

    

   });
  </script>
<script src="libs/js/liquidmetal.js" type="text/javascript"></script>
<script src="libs/js/jquery.flexselect.js" type="text/javascript"></script>