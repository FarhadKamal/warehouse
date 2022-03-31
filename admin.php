<?php
  $page_title = 'Admin Home Page';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(1);
?>
<?php
 $c_products     = count_by_id('products');
 $s_price         = find_value_by_sql('select sum(stock_price) as price from stock');
 $i_price          = find_value_by_sql('select sum(stock_price)*(-1) as price from stock where ref_source="Issue"');
 $t_issue          = find_value_by_sql('select count(id) as total from consume where  submit_status=1');
 $products_consume   = find_higest_consumtion('10');
 $recent_stock = find_recent_stock_added('5');
 $recent_consume    = find_recent_consume_added('10')
?>
<?php include_once('layouts/header.php'); ?>

<div class="row">
   <div class="col-md-6">
     <?php echo display_msg($msg); ?>
   </div>
</div>
  <div class="row">
    <div class="col-md-3">
       <div class="panel panel-box clearfix">
         <div class="panel-icon pull-left bg-green">
          <i class="glyphicon glyphicon-share"></i>
        </div>
        <div class="panel-value pull-right">
          <h2 class="margin-top"> <?php  echo $t_issue['total']; ?> </h2>
          <p class="text-muted">Issues</p>
        </div>
       </div>
    </div>
    <div class="col-md-3">
       <div class="panel panel-box clearfix">
         <div class="panel-icon pull-left bg-yellow">
          <i class="glyphicon glyphicon-th"></i>
        </div>
        <div class="panel-value pull-right">
          <h2 class="margin-top"> <?php  echo $c_products['total']; ?> </h2>
          <p class="text-muted">Total Products</p>
        </div>
       </div>
    </div>
    <div class="col-md-3">
       <div class="panel panel-box clearfix">
         <div class="panel-icon pull-left bg-blue">
           <i class="glyphicon">&#2547;</i>
        </div>
        <div class="panel-value pull-right">
          <h2 class="margin-top"> <?php  echo $s_price['price']; ?> </h2>
          <p class="text-muted">Stock Value</p>
        </div>
       </div>
    </div>
    <div class="col-md-3">
       <div class="panel panel-box clearfix">
         <div class="panel-icon pull-left bg-red">
          <i class="glyphicon">&#2547;</i>
        </div>
        <div class="panel-value pull-right">
          <h2 class="margin-top"> <?php  echo $i_price['price']; ?> </h2>
          <p class="text-muted">Consumption</p>
        </div>
       </div>
    </div>
</div>

  <div class="row">
   <div class="col-md-4">
     <div class="panel panel-default">
       <div class="panel-heading">
         <strong>
           <span class="glyphicon glyphicon-th"></span>
           <span>Highest Consumption Products</span>
         </strong>
       </div>
       <div class="panel-body">
         <table class="table table-striped table-bordered table-condensed">
          <thead>
           <tr>
             <th>Title</th>
             <th>Consumed Cost</th>
             <th>Total Quantity</th>
            <th>Unit</th>
           <tr>
          </thead>
          <tbody>
            <?php foreach ($products_consume as  $pcon): ?>
              <tr>
                <td><?php echo remove_junk(first_character($pcon['name'])); ?></td>
                <td><?php echo $pcon['conPrice']; ?></td>
                <td>
                <?php 
                if($pcon['unit_type']=="number")
                echo intval($pcon['totalQty']); 
                else echo $pcon['totalQty'];   ?>
                </td>
                <td><?php echo $pcon['unit_name']; ?></td>
              </tr>
            <?php endforeach; ?>
          <tbody>
         </table>
       </div>
     </div>
   </div>
   <div class="col-md-4">
      <div class="panel panel-default">
        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>LATEST CONSUMPTION</span>
          </strong>
        </div>
        <div class="panel-body">
          <table class="table table-striped table-bordered table-condensed">
       <thead>
         <tr>
           <th class="text-center" style="width: 50px;">#</th>
           <th>Product&nbsp;Name</th>
           <th>Date</th>
           <th>Consumption&nbsp;Cost</th>
         </tr>
       </thead>
       <tbody>
         <?php foreach ($recent_consume as  $rcon): ?>
         <tr>
           <td class="text-center"><?php echo count_id();?></td>
           <td>
          
             <?php echo remove_junk(first_character($rcon['name'])); ?>
         
           </td>
           <td><?php echo remove_junk(ucfirst($rcon['stock_date'])); ?></td>
           <td> &#2547; <?php echo remove_junk(first_character($rcon['stock_price'])); ?></td>
        </tr>

       <?php endforeach; ?>
       </tbody>
     </table>
    </div>
   </div>
  </div>
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Recently Added Stock</span>
        </strong>
      </div>
      <div class="panel-body">

        <div class="list-group">
      <?php foreach ($recent_stock as  $rst): ?>
            <a class="list-group-item clearfix" href="edit_product.php?id=<?php echo    (int)$rst['id'];?>">
                <h4 class="list-group-item-heading">
                 <?php if($rst['media_id'] === '0'): ?>
                    <img class="img-avatar img-circle" src="uploads/products/no_image.jpg" alt="">
                  <?php else: ?>
                  <img class="img-avatar img-circle" src="uploads/products/<?php echo $rst['image'];?>" alt="" />
                <?php endif;?>
                <?php echo remove_junk(first_character($rst['name']));?>
                  <span class="label label-warning pull-right">
                  &#2547;<?php echo $rst['stock_price'];  ?>
                  </span>
                </h4>
                <span class="list-group-item-text pull-right">
                <?php echo remove_junk(first_character($rst['categorie'])); ?>
              </span>
          </a>
      <?php endforeach; ?>
    </div>
  </div>
 </div>
</div>
 </div>
  <div class="row">

  </div>



<?php include_once('layouts/footer.php'); ?>
