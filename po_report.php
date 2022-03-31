<?php
$page_title = 'PO Report';
$results = '';
require_once('includes/reports_query.php');

// PO Report Information
$result = po_report($_GET["id"]);

// PO Report Item List
$result_item_list = po_item_list($_GET["id"]);

// PO Report Terms and Condition
$result_terms_and_condition = po_terms_and_condition($_GET["id"]);

while ($row = mysqli_fetch_assoc($result)) {
    $data = $row;
  }

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
  
<div class="container" id="report">
    <div class="row">
        <div class="col-md-12">
            <strong><?= date_format(date_create($data["po_date"])," jS F, Y"); ?></strong>
            <p><?= $data["ref"]; ?></p>
            <br>
            <p>To,</p>
            <strong><?= $data["sup_name"]; ?></strong>
            <p>
                <?= $data["sup_address"]; ?>
                <br>
                Email: <?= $data["sup_email"]; ?>
            </p>
            <strong>Attn: <?= $data["attn"]; ?></strong>
            <br>
            <br>
            <strong>Sub: <?= $data["subject_details"]; ?></strong>
            <br>
            <br>
            <p>Dear <?= (isset($data["gender"]) AND $data["gender"] == "Mr.")?"Sir":"Madam"; ?>,</p>
            <p>
                As per discussion with you & subsequent to your quotation, we are please to issue a work order for supply
                us the below item at an urgent basis under the terms & condition.
            </p>

            <?php
                if(isset($result_item_list) AND $result_item_list->num_rows > 0) {
            ?>
            <table class="table table-sm table-bordered">
                <thead>
                    <th class="text-center">Sl. No.</th>
                    <th class="text-center">Item Name & Description</th>
                    <th class="text-center">Size</th>
                    <th class="text-center">Quantity</th>
                    <th class="text-center">Unit</th>
                    <th class="text-center">Rate</th>
                    <th class="text-center">Total Amount</th>
                </thead>
                <tbody>
                    <?php
                        $i = 1;
                        $total_amount = 0;
                        while($row = mysqli_fetch_assoc($result_item_list)){
                    ?>
                    
                    <tr>
                        <td class="text-center"><p><?= $i; ?></p></td>
                        <td><p><strong><?= $row["product_name"]; ?></strong></p></td>
                        <td class="text-center"><p><?= "&nbsp;"; ?></p></td>
                        <td class="text-center"><p><?= $row["po_quantity"];; ?></p></td>
                        <td class="text-center"><p><?= $row["unit_name"];; ?></p></td>
                        <td class="text-center"><p><?= number_format($row["price"]/$row["po_quantity"],2,".",","); ?></p></td>
                        <td class="text-center"><p><?= number_format($row["price"],2,".",","); ?></p></td>
                    </tr>

                    <?php 
                        $total_amount = $total_amount + $row["price"];
                            $i++;
                        }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="text-center" colspan="6"><p class="small"><?= "Total: ".convert_number($total_amount); ?></p></td>
                        <td class="text-center"><p class="small"><strong>BDT. <?= number_format($total_amount,2,".",","); ?></strong></p></td>
                    </tr>
                </tfoot>
            </table>
            <?php
                }
                // end ifs
            ?>
            <p>
                <?php 
                if(isset($result_terms_and_condition) AND $result_terms_and_condition->num_rows > 0) {
                    
                ?>
                <ol>
                    <?php while($row2 = mysqli_fetch_assoc($result_terms_and_condition)): ?>
                    <li><strong><?= $row2["option_name"]; ?></strong>: <?= $row2["opton_details"]; ?></li>
                    <?php endwhile; ?>
                </ol> 
                <?php
                    }
                    // end ifs
                ?>
            </p>

            <p>
                Thanking You,
                <br>
                
                <hr style="width:30%;text-align:left;margin-left:0;", color=black>
                <strong>( <?= $data["name"]; ?> )</strong>
                <br>
                <?= $data["designation"] ?>, <?= $data["department"] ?>
            </p>
        </div>
    </div>
</div>

<a href="javascript:createPDF()">Dowload PDF</a>

</body>
</html>

<script>
    function createPDF() {
        var sTable = document.getElementById('report').innerHTML;

        var style = "<style>";
        style = style + "img {width: width: 180px ; height: 160px;}";
        style = style + "table {width: 100%;font: 17px Calibri;}";
        style = style + "table, th, td {border: solid 1px #DDD; border-collapse: collapse;";
        style = style + "padding: 2px 3px;text-align: center;}";
        style = style + "</style>";

        // CREATE A WINDOW OBJECT.
        var win = window.open('', '', 'height=700,width=700');

        win.document.write('<html><head>');
        win.document.write('<title>Information</title>');   // <title> FOR PDF HEADER.
        win.document.write(style);          // ADD STYLE INSIDE THE HEAD TAG.
        win.document.write('</head>');
        win.document.write('<body>');
        win.document.write(sTable);         // THE TABLE CONTENTS INSIDE THE BODY TAG.
        win.document.write('</body></html>');

        win.document.close(); 	// CLOSE THE CURRENT WINDOW.

        win.print();    // PRINT THE CONTENTS.
    }
</script>