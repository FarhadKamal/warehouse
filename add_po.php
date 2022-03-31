    <link rel="stylesheet" href="libs/css/flexselect.css" type="text/css" media="screen" />
    <?php
    $page_title = 'New PO';
    require_once('includes/load.php');
    // Checkin What level user has permission to view this page
    page_require_level(5);


    $all_sup = find_by_sql('select * from  suppliers order by sup_name');

    ?>
    <?php



    if (isset($_POST['add_po'])) {



      $req_fields = array('supplier', 'supplier-email', 'supplier-mobile', 'contact-person', 'supplier-address');
      validate_fields($req_fields);
      if (empty($errors)) {


        $sup_id    = remove_junk($db->escape($_POST['supplier']));
        $sup_email    = remove_junk($db->escape($_POST['supplier-email']));
        $sup_mobile    = remove_junk($db->escape($_POST['supplier-mobile']));
        $contact_person    = remove_junk($db->escape($_POST['contact-person']));

        $supplier_address    = remove_junk($db->escape($_POST['supplier-address']));

        $submit_by = $_SESSION['user_id'];




        $date    = make_date();
        $query  = "INSERT INTO po (";
        $query .= " submit_by,po_date,sup_id";
        $query .= ") VALUES (";
        $query .= " '{$submit_by}', now(), '{$sup_id}'";
        $query .= ")";
        //$query .=" ON DUPLICATE KEY UPDATE name='{$p_name}'";
        if ($db->query($query)) {
          $session->msg('s', "PO saved!");


          $track_id = find_value_by_sql(" select max(id)  as track_id from po where submit_by='{$submit_by}'  ")['track_id'];

          $query = "update suppliers 
          set

          sup_mobile = '{$sup_mobile}' , 
          attn = '{$contact_person}' , 
          sup_address = '{$supplier_address}' , 
          sup_email = '{$sup_email}'
          
          where
          id =" . $sup_id;




          $db->query($query);


          redirect('edit_po.php?id=' . $track_id, false);
        } else {
          $session->msg('d', ' Sorry failed to save PO!');
          redirect('add_po.php', false);
        }
      } else {
        $session->msg("d", $errors);
        redirect('add_po.php', false);
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
              <span>PO</span>
            </strong>
          </div>
          <div class="panel-body">
            <div class="col-md-12">
              <form method="post" action="add_po.php" class="clearfix">

                <div class="form-group"> <label>PO Date</label>
                  <div class="input-group">

                    <span class="input-group-addon">
                      <i class="glyphicon glyphicon-calendar"></i>
                    </span>
                    <?php
                    $podate = strtotime(date("Y-m-d H:i:s"));




                    ?>
                    <input type="text" class="form-control" name="podate" style="width: 300px;" value="<?php echo  date('Y-m-d H:i:s', $podate); ?>" readonly>
                  </div>
                </div>




                <div class="form-group"> <label>Vendor</label>
                  <div class="input-group">

                    <span class="input-group-addon">
                      <i class="glyphicon glyphicon-user"></i>
                    </span>
                    <select class="form-control" name="supplier" id="supplier-id">
                      <option value="">Select Vendor</option>
                      <?php foreach ($all_sup as $sup) : ?>
                        <option value="<?php echo (int)$sup['id'] ?>">
                          <?php echo $sup['sup_name'] ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>








                <div class="form-group">
                  <div class="row">
                    <div class="col-md-12">

                      <div id="div-result">

                      </div>
                    </div>

                  </div>


                </div>


              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php include_once('layouts/footer.php'); ?>
    <script>
      $(document).ready(function() {

        // $("#product-id").flexselect();

        $("#supplier-id").change(function() {

          var id = $('#supplier-id').val();

          //alert(id);

          if (id != '') {
            $.post('modal.php?call=5', {

                'sid': id,

              },

              function(result) {

                if (result) {


                  $("#div-result").empty();
                  $("#div-result").append(result);

                }
              }
            );

          } else $('#prev-short-code').val("");

        });



      });
    </script>
    <script src="libs/js/liquidmetal.js" type="text/javascript"></script>
    <script src="libs/js/jquery.flexselect.js" type="text/javascript"></script>