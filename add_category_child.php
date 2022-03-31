   <link rel="stylesheet" href="libs/css/flexselect.css" type="text/css" media="screen" />
   


  <?php
  $page_title = 'All child categories';
  require_once('includes/load.php');
    // Checkin What level user has permission to view this page
  page_require_level(1);




  $all_main_categories = find_by_sql('select * from categories order by short_code');

  $all_categories = find_by_sql('select * from categories where parent>0 order by short_code');
  ?>
  <?php
  if(isset($_POST['add_cat'])){
   $req_field = array('parent-category','child-categorie-name');





   validate_fields($req_field);
   
  
   $parent_category = $_POST['parent-category'];

   $cat_name = remove_junk($db->escape($_POST['child-categorie-name']));

   $short_code = strtolower(remove_junk($db->escape($_POST['short-code'])));

   $prev_short_code = remove_junk($db->escape($_POST['prev-short-code']));

   $new_gen_code= $prev_short_code.".".$short_code ;






   if(empty($errors)){





    $total=duplicate_check_cat("categories","name",$cat_name,$parent_category);
    if($total>0)
    {
     $session->msg("d", "Sorry, ".$cat_name." has aleady added.");
     redirect('add_category_child.php',false);
   }


   if(validate_short_code( $short_code )==false){
    $session->msg("d", "short code should be (2 to 4) digit alphanumeric");
    redirect('add_category_child.php',false);
  }



  $total=duplicate_check_cat("categories","short_code",$short_code,$parent_category);
  if($total>0)
  {
   $session->msg("d", "Sorry, short code: ".$short_code." has aleady added for a parent category.");
   redirect('add_category_child.php',false);
  }





  $sql  = "INSERT INTO categories (name,short_code,parent)";
  $sql .= " VALUES ('{$cat_name}','{$new_gen_code}','{$parent_category}')";
  if($db->query($sql)){
    $session->msg("s", "Successfully added new child category");
    redirect('add_category_child.php',false);
  } else {
    $session->msg("d", "Sorry failed to insert.");
    redirect('add_category_child.php',false);
  }
  } else {
   $session->msg("d", $errors);
   redirect('add_category_child.php',false);
  }
  }
  ?>
  <?php include_once('layouts/header.php'); ?>
    <link rel="stylesheet" href="libs/css/flexselect.css" type="text/css" media="screen" />
    <script src="libs/js/liquidmetal.js" type="text/javascript"></script>
    <script src="libs/js/jquery.flexselect.js" type="text/javascript"></script>


  <script>
    $(document).ready(function() {

    $("#parent-category").flexselect();

     $( "#parent-category" ).change(function() {

      var id = $('#parent-category').val();

      

      if(id!='')
      { 
        $.post('modal.php?call=1', {

          'scode': id,

        },

        function(result) {

          if (result) {


           $('#prev-short-code').val(result.replace(/\s+/, "") );

         }
       }
       );

      }else $('#prev-short-code').val("");

    });

   });
  </script>


  <div class="row">
   <div class="col-md-12">
     <?php echo display_msg($msg); ?>
   </div>
  </div>
  <div class="row">
    <div class="col-md-5">
      <div class="panel panel-default">
        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>Add New Child Category</span>
          </strong>
        </div>
        <div class="panel-body">
          <form method="post" action="add_category_child.php">
            <div class="form-group">
              <div class="row">
                <div class="col-md-6">
                 <select class="form-control" id="parent-category" name="parent-category" >
                  <option value="">Select Parent Category</option>
                  <?php  foreach ($all_main_categories as $cat): ?>
                    <option value="<?php echo (int)$cat['id'] ?>">
                      <?php echo $cat['short_code'] ?> <?php echo " # " ?><?php echo $cat['name'] ?></option>
                    <?php endforeach; ?>
                  </select>
                </div> 
              </div>  

              <div class="row">
                <div class="col-md-12">
                 <input type="text" class="form-control" name="child-categorie-name" placeholder="Child Categorie Name">
               </div>  

             </div>    



             <div class="row">

               <div class="col-md-4">
                <table>
                  <tr>
                    <td>
                      <input type="text" size=18  name="prev-short-code" readonly="" id="prev-short-code" > 
                    </td>
                    <td>.</td>
                    <td>
                     <input type="text" size=12 name="short-code" placeholder="Short Code">
                   </td>
                 </tr>
               </table>
             </div>


           </div>  

         </div>
         <button type="submit" name="add_cat" class="btn btn-primary">Add category</button>
       </form>
     </div>
   </div>
  </div>
  <div class="col-md-7">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>All Child Categories</span>
        </strong>
      </div>
      <div class="panel-body">
        <table class="table table-bordered table-striped table-hover">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              
              <th>Short Code</th>
              <th>Categories</th>
              <th class="text-center" style="width: 100px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($all_categories as $cat):?>
              <tr>
                <td class="text-center"><?php echo count_id();?></td>
               
                <td><?php echo $cat['short_code']; ?></td>
                <td><?php echo remove_junk(ucfirst($cat['name'])); ?></td>
                <td class="text-center">
                  <div class="btn-group">
                    <a href="edit_category_child.php?id=<?php echo (int)$cat['id'];?>"  class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
                      <span class="glyphicon glyphicon-edit"></span>
                    </a>
                    <a href="delete_category_child.php?id=<?php echo (int)$cat['id'];?>"  class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
                      <span class="glyphicon glyphicon-trash"></span>
                    </a>


                  </div>
                </td>

              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  </div>
  </div>
  <?php include_once('layouts/footer.php'); ?>

 <script src="libs/js/liquidmetal.js" type="text/javascript"></script>
<script src="libs/js/jquery.flexselect.js" type="text/javascript"></script>