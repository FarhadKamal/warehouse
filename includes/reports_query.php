
<?php
// -----------------------------------------------------------------------
// DEFINE SEPERATOR ALIASES
// -----------------------------------------------------------------------
define("URL_SEPARATOR", '/');

define("DS", DIRECTORY_SEPARATOR);

// -----------------------------------------------------------------------
// DEFINE ROOT PATHS
// -----------------------------------------------------------------------
defined('SITE_ROOT')? null: define('SITE_ROOT', realpath(dirname(__FILE__)));
define("LIB_PATH_INC", SITE_ROOT.DS);


require_once(LIB_PATH_INC.'config.php');
require_once(LIB_PATH_INC.'database.php');

function po_report($id)
{
    global $db;
    $sql = "select 	
                po.id, po.po_date, po.ref, po.sup_id,
                suppliers.sup_name, suppliers.sup_address, suppliers.sup_email, suppliers.attn,
                po.subject_details, po.gender, po.approve_status, po.approved_by,
                users.name, po_signature.department, po_signature.designation, po_signature.signature
        
            from po 
            INNER JOIN suppliers ON suppliers.id = po.sup_id
            INNER JOIN users ON users.id = po.approved_by
            INNER JOIN po_signature ON po_signature.user_id = po.approved_by
            Where po.id = $id";
    

    return $db->query($sql);
}


function po_item_list($id)
{
    global $db;
    $sql = "Select
            po_items.product_id, products.name AS product_name, products.unit_id, units.unit_name, po_items.po_quantity, po_items.price
            FROM po_items
            INNER JOIN products ON products.id = po_items.product_id
            INNER JOIN units ON units.id = products.unit_id
            WHERE po_items.po_id = $id";
    return $db->query($sql);
}

function po_terms_and_condition($id)
{
    global $db;
    $sql = "SELECT po_terms.id, po_options.option_name, po_options.opton_details, po_options.order_level
            FROM po_terms
            INNER JOIN po_options ON po_options.id = po_terms.option_id
            WHERE po_terms.po_id = $id
            ORDER BY po_options.order_level";

    return $db->query($sql);
}

function convert_number($number) 
{ 

    if($number<0) {
    $number=$number*(-1);
    return "Employee have to return ".$number; }

    if (($number < 0) || ($number > 999999999)) 
    { 
    throw new Exception("Number is out of range");
    } 

    $Gn = floor($number / 100000);  /* Lak (giga) */ 
    $number -= $Gn * 100000; 
    $kn = floor($number / 1000);     /* Thousands (kilo) */ 
    $number -= $kn * 1000; 
    $Hn = floor($number / 100);      /* Hundreds (hecto) */ 
    $number -= $Hn * 100; 
    $Dn = floor($number / 10);       /* Tens (deca) */ 
    $n = $number % 10;               /* Ones */ 

    $res = ""; 

    if ($Gn) 
    { 
        $res .= convert_number($Gn) . " Lakh"; 
    } 

    if ($kn) 
    { 
        $res .= (empty($res) ? "" : " ") .convert_number($kn) . " Thousand"; 
    } 

    if ($Hn) 
    { 
        $res .= (empty($res) ? "" : " ") .convert_number($Hn) . " Hundred"; 
    } 

    $ones = array("", "One", "Two", "Three", "Four", "Five", "Six", 
        "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", 
        "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen", 
        "Nineteen"); 
    $tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty", 
        "Seventy", "Eigthy", "Ninety"); 

    if ($Dn || $n) 
    { 
        if (!empty($res)) 
        { 
            $res .= " and "; 
        } 

        if ($Dn < 2) 
        { 
            $res .= $ones[$Dn * 10 + $n]; 
        } 
        else 
        { 
            $res .= $tens[$Dn]; 

            if ($n) 
            { 
                $res .= "-" . $ones[$n]; 
            } 
        } 
    } 

    if (empty($res)) 
    { 
        $res = "zero"; 
    } 

    return $res; 
} 

?>
