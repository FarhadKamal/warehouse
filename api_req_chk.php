<?php

require_once('includes/load.php');
$req_id = $_GET['id'];



$count_req = find_value_by_sql('select count(id) as tot from requisition where submit_status=1 and id='. $req_id);

echo $count_req['tot'];
