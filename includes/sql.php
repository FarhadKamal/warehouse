<?php
    require_once('includes/load.php');

    /*--------------------------------------------------------------*/
    /* Function for find all database table rows by table name
    /*--------------------------------------------------------------*/
    function find_all($table)
    {
        global $db;
        if (tableExists($table)) {
            return find_by_sql("SELECT * FROM " . $db->escape($table));
        }
    }
    /*--------------------------------------------------------------*/
    /* Function for Perform queries
    /*--------------------------------------------------------------*/
    function find_by_sql($sql)
    {
        global $db;
        $result = $db->query($sql);
        $result_set = $db->while_loop($result);
        return $result_set;
    }
    /*--------------------------------------------------------------*/



    /*--------------------------------------------------------------*/
    /* Function for Perform queries for return value by sql
    /*--------------------------------------------------------------*/
    function find_value_by_sql($sql)
    {
        global $db;
        $result = $db->query($sql);

        return $db->fetch_assoc($result);
    }
    /*--------------------------------------------------------------*/

    /*--------------------------------------------------------------*/
    /* Function for generate short code for product
    /*--------------------------------------------------------------*/
    function generate_short_code($id)
    {
        global $db;
        $newcode = "";

        $result = $db->query("select count(*) as total from products where categorie_id=" . $id);
        $total = $db->fetch_assoc($result)['total'];


        $result2 = $db->query("select short_code from categories where id=" . $id);
        $short_code = $db->fetch_assoc($result2)['short_code'];

        if ($total == 0) {
            $newcode = $short_code . ".0001";
        } else {
            $result3 = $db->query("select  short_code,CAST(SUBSTRING(short_code,  -4)  as signed)  as digit  from products 
				where categorie_id=" . $id . " order by short_code desc  limit 1");
            $digit = $db->fetch_assoc($result3)['digit'];
            $digit = $digit + 1;
            $zero = ".";

            if (strlen($digit) == 1) {
                $zero = ".000";
            } elseif (strlen($digit) == 2) {
                $zero = ".00";
            } elseif (strlen($digit) == 3) {
                $zero = ".0";
            }
            $newcode = $short_code . $zero . $digit;
        }

        return $newcode;
    }


    function generate_short_code_edit($id, $oid, $pid)
    {
        if ($id <> $oid) {
            $newcode = generate_short_code($id);
        } else {
            global $db;
            $result = $db->query("select short_code from products where id=" . $pid);
            $newcode = $db->fetch_assoc($result)['short_code'];
        }


        return $newcode;
    }

    /*--------------------------------------------------------------*/








    /*  Function for Find data from table by id
    /*--------------------------------------------------------------*/
    function find_by_id($table, $id)
    {
        global $db;
        $id = (int)$id;
        if (tableExists($table)) {
            $sql = $db->query("SELECT * FROM {$db->escape($table)} WHERE id='{$db->escape($id)}' LIMIT 1");
            if ($result = $db->fetch_assoc($sql)) {
                return $result;
            } else {
                return null;
            }
        }
    }
    /*--------------------------------------------------------------*/



    /*  Function for Find details data from table by id and sub id
    /*--------------------------------------------------------------*/
    function find_by_id_sub_id($table, $sub_id, $field, $id)
    {
        global $db;
        $id = (int)$id;
        if (tableExists($table)) {
            $sql = $db->query("SELECT * FROM {$db->escape($table)} WHERE id='{$db->escape($sub_id)}' and {$db->escape($field)}='{$db->escape($id)}' LIMIT 1");
            if ($result = $db->fetch_assoc($sql)) {
                return $result;
            } else {
                return null;
            }
        }
    }
    /*--------------------------------------------------------------*/



    /*  Function for Find details data from table by id and sub id
    /*--------------------------------------------------------------*/
    function find_grn_edit($id)
    {
        global $db;
        $id = (int)$id;
        if (tableExists($table)) {
            $sql = $db->query("SELECT * FROM grn WHERE id='{$db->escape($id)}' and submit_status=0 and cancel_status=0 LIMIT 1");
            if ($result = $db->fetch_assoc($sql)) {
                return $result;
            } else {
                return null;
            }
        }
    }
    /*--------------------------------------------------------------*/




    /*  Function for Find details data from table by id
    /*--------------------------------------------------------------*/
    function find_po_edit($id)
    {
        global $db;
        $id = (int)$id;
        $sql = $db->query("SELECT * FROM po WHERE id='{$db->escape($id)}' and submit_status=0 and cancel_status=0 LIMIT 1");
        if ($result = $db->fetch_assoc($sql)) {
            return $result;
        } else {
            return null;
        }
    }
    /*--------------------------------------------------------------*/







    /* Function for checking duplicate entries by given field name
    /*--------------------------------------------------------------*/
    function duplicate_check($table, $field_name, $value)
    {
        global $db;
        $sql    = "SELECT COUNT(*) AS total FROM " . $db->escape($table) . " where {$field_name}='" . $db->escape($value) . "'";
        $result = $db->query($sql);


        $total = ($db->fetch_assoc($result));
        return  $total['total'];
    }
    /*--------------------------------------------------------------*/


    /* Function for checking duplicate entries by given field name for table categories
    /*--------------------------------------------------------------*/
    function duplicate_check_cat($table, $field_name, $value, $parent)
    {
        global $db;
        $sql    = "SELECT COUNT(*) AS total FROM " . $db->escape($table);
        $sql    .= " where parent={$parent} and  {$field_name}='" . $db->escape($value) . "'";

        $result = $db->query($sql);


        $total = ($db->fetch_assoc($result));
        return  $total['total'];
    }
    /*--------------------------------------------------------------*/





    /* Function for Delete data from table by id
    /*--------------------------------------------------------------*/
    function delete_by_id($table, $id)
    {
        global $db;
        if (tableExists($table)) {
            $sql = "DELETE FROM " . $db->escape($table);
            $sql .= " WHERE id=" . $db->escape($id);
            $sql .= " LIMIT 1";
            $db->query($sql);
            return ($db->affected_rows() === 1) ? true : false;
        }
    }
    /*--------------------------------------------------------------*/
    /* Function for Count id  By table name
    /*--------------------------------------------------------------*/

    function count_by_id($table)
    {
        global $db;
        if (tableExists($table)) {
            $sql    = "SELECT COUNT(id) AS total FROM " . $db->escape($table);
            $result = $db->query($sql);
            return ($db->fetch_assoc($result));
        }
    }
    /*--------------------------------------------------------------*/
    /* Determine if database table exists
    /*--------------------------------------------------------------*/
    function tableExists($table)
    {
        global $db;
        $table_exit = $db->query('SHOW TABLES FROM ' . DB_NAME . ' LIKE "' . $db->escape($table) . '"');
        if ($table_exit) {
            if ($db->num_rows($table_exit) > 0) {
                return true;
            } else {
                return false;
            }
        }
    }
    /*--------------------------------------------------------------*/
    /* Login with the data provided in $_POST,
    /* coming from the login form.
    /*--------------------------------------------------------------*/
    function authenticate($username = '', $password = '')
    {
        global $db;
        $username = $db->escape($username);
        $password = $db->escape($password);
        $sql  = sprintf("SELECT id,username,password,user_level FROM users WHERE username ='%s' LIMIT 1", $username);
        $result = $db->query($sql);
        if ($db->num_rows($result)) {
            $user = $db->fetch_assoc($result);
            $password_request = sha1($password);
            if ($password_request === $user['password']) {
                return $user['id'];
            }
        }
        return false;
    }
    /*--------------------------------------------------------------*/
    /* Login with the data provided in $_POST,
    /* coming from the login_v2.php form.
    /* If you used this method then remove authenticate function.
    /*--------------------------------------------------------------*/
    function authenticate_v2($username = '', $password = '')
    {
        global $db;
        $username = $db->escape($username);
        $password = $db->escape($password);
        $sql  = sprintf("SELECT id,username,password,user_level FROM users WHERE username ='%s' LIMIT 1", $username);
        $result = $db->query($sql);
        if ($db->num_rows($result)) {
            $user = $db->fetch_assoc($result);
            $password_request = sha1($password);
            if ($password_request === $user['password']) {
                return $user;
            }
        }
        return false;
    }


    /*--------------------------------------------------------------*/
    /* Find current log in user by session id
    /*--------------------------------------------------------------*/
    function current_user()
    {
        static $current_user;
        global $db;
        if (!$current_user) {
            if (isset($_SESSION['user_id'])) :
                $user_id = intval($_SESSION['user_id']);
            $current_user = find_by_id('users', $user_id);
            endif;
        }
        return $current_user;
    }
    /*--------------------------------------------------------------*/
    /* Find all user by
    /* Joining users table and user gropus table
    /*--------------------------------------------------------------*/
    function find_all_user()
    {
        global $db;
        $results = array();
        $sql = "SELECT u.id,u.name,u.username,u.user_level,u.status,u.last_login,u.designation,";
        $sql .= "g.group_name,c.com_name ";
        $sql .= "FROM users u ";
        $sql .= "LEFT JOIN company c ";
        $sql .= "ON c.com_id=u.com ";
        $sql .= "LEFT JOIN user_groups g ";
        $sql .= "ON g.group_level=u.user_level ORDER BY u.name ASC";
        $result = find_by_sql($sql);
        return $result;
    }
    /*--------------------------------------------------------------*/
    /* Function to update the last log in of a user
    /*--------------------------------------------------------------*/

    function updateLastLogIn($user_id)
    {
        global $db;
        $date = make_date();
        $sql = "UPDATE users SET last_login='{$date}' WHERE id ='{$user_id}' LIMIT 1";
        $result = $db->query($sql);
        return ($result && $db->affected_rows() === 1 ? true : false);
    }

    /*--------------------------------------------------------------*/
    /* Find all Group name
    /*--------------------------------------------------------------*/
    function find_by_groupName($val)
    {
        global $db;
        $sql = "SELECT group_name FROM user_groups WHERE group_name = '{$db->escape($val)}' LIMIT 1 ";
        $result = $db->query($sql);
        return ($db->num_rows($result) === 0 ? true : false);
    }
    /*--------------------------------------------------------------*/
    /* Find group level
    /*--------------------------------------------------------------*/
    function find_by_groupLevel($level)
    {
        global $db;
        $sql = "SELECT group_level FROM user_groups WHERE group_level = '{$db->escape($level)}' LIMIT 1 ";
        $result = $db->query($sql);
        return ($db->num_rows($result) === 0 ? true : false);
    }
    /*--------------------------------------------------------------*/
    /* Function for cheaking which user level has access to page
    /*--------------------------------------------------------------*/
    function page_require_level($require_level)
    {
        global $session;
        $current_user = current_user();
        $login_level = find_by_groupLevel($current_user['user_level']);
        //if user not login
        if (!$session->isUserLoggedIn(true)) :
            $session->msg('d', 'Please login...');
        redirect('index.php', false);
        //if Group status Deactive
        elseif ($login_level['group_status'] === '0') :
            $session->msg('d', 'This level user has been band!');
        redirect('home.php', false);
        //cheackin log in User level and Require level is Less than or equal to
        elseif ($current_user['user_level'] <= (int)$require_level) :
            return true; else :
            $session->msg("d", "Sorry! you dont have permission to view the page.");
        redirect('home.php', false);
        endif;
    }
    /*--------------------------------------------------------------*/
    /* Function for Finding all product name
    /* JOIN with categorie  and media database table
    /*--------------------------------------------------------------*/
    function join_product_table()
    {
        global $db;
        $sql  = " SELECT p.id,p.name,p.short_code,p.media_id,p.date as pdate,c.name as cname,u.unit_name,";
        $sql  .= " m.file_name AS image";
        $sql  .= " FROM products p";
        $sql  .= " INNER JOIN categories c ON c.id = p.categorie_id";
        $sql  .= " INNER JOIN units u ON u.id = p.unit_id";
        $sql  .= " LEFT JOIN media m ON m.id = p.media_id";
        $sql  .= " ORDER BY p.short_code ASC";
        return find_by_sql($sql);
    }
    /*--------------------------------------------------------------*/
    /* Function for Finding all grn

    /*--------------------------------------------------------------*/
    function join_grn_table()
    {
        global $db;
        $com_id = find_value_by_sql(" select com from users where id=".$_SESSION['user_id'])['com'];

        $sql  = " SELECT g. *, u.name as claimer,loc_name,sup_name";

        $sql  .= " FROM grn g";
        $sql  .= " inner join users u on u.id=g.grn_by";
        $sql  .= " inner join locations l on l.id=g.loc_id";
        $sql  .= " inner join suppliers sup  on sup.id=g.sup_id";
        $sql  .= " where cancel_status=0 ";
        $sql  .= " and g.com=". $com_id;
        $sql  .= "  ORDER BY g.id desc";
        return find_by_sql($sql);
    }

    /*--------------------------------------------------------------*/


    /* Function for Finding all grn

    /*--------------------------------------------------------------*/
    function join_po_table()
    {
        global $db;
        $sql  = " SELECT po. *, u.name as claimer,sup_name";

        $sql  .= " FROM po ";
        $sql  .= " inner join users u on u.id=po.submit_by";
        $sql  .= " inner join suppliers sup  on sup.id=po.sup_id";
        $sql  .= " where cancel_status=0 ORDER BY po.id desc";
        return find_by_sql($sql);
    }

    /*--------------------------------------------------------------*/


    /* Function for Finding all grn

    /*--------------------------------------------------------------*/
    function join_apv_po_table()
    {
        global $db;
        $sql  = " SELECT po. *, u.name as claimer,sup_name";

        $sql  .= " FROM po ";
        $sql  .= " inner join users u on u.id=po.submit_by";
        $sql  .= " inner join suppliers sup  on sup.id=po.sup_id";
        $sql  .= " where cancel_status=0 and submit_status=1 ORDER BY po.id desc";
        return find_by_sql($sql);
    }

    /*--------------------------------------------------------------*/



    /* Function for Finding all issue

    /*--------------------------------------------------------------*/
    function join_issue_table()
    {
        global $db;
        $com_id = find_value_by_sql(" select com from users where id=".$_SESSION['user_id'])['com'];

        $sql  = " SELECT c. *, u.name as claimer,loc_name,cost_name";

        $sql  .= " FROM consume c";
        $sql  .= " inner join users u on u.id=c.con_by";
        $sql  .= " inner join locations l on l.id=c.loc_id";
        $sql  .= " inner join cost_centre cost on cost.id=c.cost_id";
        $sql  .= " where cancel_status=0 ";
        $sql  .= " and c.com=".$com_id;
        $sql  .= "  ORDER BY c.id desc";
        return find_by_sql($sql);
    }

    /*--------------------------------------------------------------*/



    /* Function for Finding all issue

    /*--------------------------------------------------------------*/
    function join_issue_return_table()
    {
        global $db;

        $com_id = find_value_by_sql(" select com from users where id=".$_SESSION['user_id'])['com'];
        $sql  = " SELECT cr. *, u.name as claimer,consumer,loc_name";

        $sql  .= " FROM consume_return cr";
        $sql  .= " inner join  consume c on cr.con_id=c.id";
        $sql  .= " inner join users u on u.id=cr.rtn_by";
        $sql  .= " inner join locations l on l.id=c.loc_id";
        $sql  .= " where cr.cancel_status=0  ";
        $sql  .= " and cr.com=".$com_id ;
        $sql  .= "  ORDER BY cr.id desc";
        return find_by_sql($sql);
    }

    /*--------------------------------------------------------------*/



    /* Function for Finding all grn

    /*--------------------------------------------------------------*/
    function join_grn_return_table()
    {
        global $db;
        $sql  = " SELECT gr. *, u.name as claimer,loc_name";

        $sql  .= " FROM grn_return gr";
        $sql  .= " inner join  grn g on gr.grn_id=g.id";
        $sql  .= " inner join users u on u.id=gr.rtn_by";
        $sql  .= " inner join locations l on l.id=g.loc_id";
        $sql  .= " where gr.cancel_status=0  ORDER BY gr.id desc";
        return find_by_sql($sql);
    }

    /*--------------------------------------------------------------*/



    /* Function for Finding all transfer

    /*--------------------------------------------------------------*/
    function join_transfer_table()
    {
        global $db;
        $com_id = find_value_by_sql(" select com from users where id=".$_SESSION['user_id'])['com'];

        $sql  = " SELECT t. *, u.name as claimer,l1.loc_name as locfrom ,l2.loc_name as locto ";

        $sql  .= " FROM transfer t";
        $sql  .= " inner join users u on u.id=t.tran_by";
        $sql  .= " inner join locations l1 on l1.id=t.tran_from";
        $sql  .= " inner join locations l2 on l2.id=t.tran_to";
        $sql  .= " where cancel_status=0 ";
        $sql  .= " and t.com=".$com_id;
        $sql  .= " ORDER BY t.id desc";
        return find_by_sql($sql);
    }

    /*--------------------------------------------------------------*/



    /* Function for Finding all requisition
    /* JOIN with requisition  and media database table
    /*--------------------------------------------------------------*/
    function join_requisition_table()
    {
        global $db;
        $com_id = find_value_by_sql(" select com from users where id=".$_SESSION['user_id'])['com'];
        $user_level = find_value_by_sql(" select user_level from users where id=".$_SESSION['user_id'])['user_level'];
    

        $sql  = " SELECT r. *, u.name as claimer";

        $sql  .= " FROM requisition r";
        $sql  .= " inner join users u on u.id=r.submit_by";
    
        $sql  .= " where cancel_status=0 ";

        if ($user_level ==2) {
            $sql  .= " and r.com=". $com_id." ";
        }

        $sql  .= "  ORDER BY r.id desc";
        return find_by_sql($sql);
    }

    function join_apv_requisition_table()
    {
        global $db;

        $multi_com = find_value_by_sql(" select multi_com from users where id=".$_SESSION['user_id'])['multi_com'];
    
    
        $sql  = " SELECT r. *, u.name as claimer";

        $sql  .= " FROM requisition r";
        $sql  .= " inner join users u on u.id=r.submit_by";
    
        $sql  .= " where cancel_status=0 and r.com in (".$multi_com.") and
		 ( r.id in(select distinct req_id from requisition_action where action_by =7  or action_by =19 )  or approve_status=1  ) ORDER BY r.id desc";
        return find_by_sql($sql);
    }

    function join_requisition_sub_approval_table()
    {
        global $db;

        $supervisor = $_SESSION['user_id'];

        $sql  = " SELECT r. *, u.name as claimer,sub_approved_status";

        $sql  .= " FROM requisition r";
        $sql  .= " inner join users u on u.id=r.submit_by";
        $sql  .= " inner join requisition_details rd on rd.req_id=r.id";

        $sql  .= " where forward_to=" . $supervisor . " and cancel_status=0  Group BY r.id desc";
        return find_by_sql($sql);
    }


    /* Function for Finding all requisition
    /* JOIN with requisition  and media database table
    /*--------------------------------------------------------------*/
    function join_requisition_proc_table()
    {
        global $db;
        $sql  = " SELECT r. *, u.name as claimer, GROUP_CONCAT(distinct rd.assigned SEPARATOR '----') as asgn";

        $sql  .= " FROM requisition r";
        $sql  .= " inner join users u on u.id=r.submit_by";
        $sql  .= " inner join requisition_details rd on rd.req_id=r.id";
    
        $sql  .= " where cancel_status=0  and (approve_status=1 or sub_approved_status=1) Group BY r.id desc";
        return find_by_sql($sql);
    }


    /* Function for Finding all adjustment
    /* JOIN with adjustment  table
    /*--------------------------------------------------------------*/
    function join_adjustment_table()
    {
        global $db;
        $com_id = find_value_by_sql(" select com from users where id=".$_SESSION['user_id'])['com'];

        $sql  = " SELECT a. *, u.name as claimer";

        $sql  .= " FROM adjust a";
        $sql  .= " inner join users u on u.id=a.adj_by";

        $sql  .= " where cancel_status=0 ";
        $sql  .= " and a.com=".$com_id ;
        $sql  .= "  ORDER BY a.id desc";
        return find_by_sql($sql);
    }

    /*--------------------------------------------------------------*/
    function join_requisition_approval_table()
    {
        global $db;
        $sql  = " SELECT r. *, u.name as claimer";

        $sql  .= " FROM requisition r";
        $sql  .= " inner join users u on u.id=r.submit_by";
    
        $sql  .= " where cancel_status=0 and submit_status=1 ORDER BY r.id desc";
        return find_by_sql($sql);
    }
    /*--------------------------------------------------------------*/





    /*--------------------------------------------------------------*/
    /* Function for Find get specific requisition by id
    /*--------------------------------------------------------------*/
    function get_requisition_by_id($id)
    {
        global $db;
        $sql  = " SELECT r. *, u.name as claimer,u.designation as udes";

        $sql  .= " FROM requisition r";
        $sql  .= " inner join users u on u.id=r.submit_by";

        $sql  .= " where r.id=" . $id;
        return find_value_by_sql($sql);
    }
    /*--------------------------------------------------------------*/




    /*--------------------------------------------------------------*/
    /* Function for Find get specific grn by id
    /*--------------------------------------------------------------*/
    function get_grn_by_id($id)
    {
        global $db;
        $sql  = " select grn.*,loc_name,request_reason,contact_person,expected_date,cl.name as claimer,designation,sup_name";

        $sql  .= " from grn";
        $sql  .= " inner join locations on locations.id=grn.loc_id";
        $sql  .= " left join requisition on requisition.id=grn.req_id";
        $sql  .= " inner join users cl on cl.id=grn.grn_by";
        $sql  .= " inner join suppliers sup on sup.id=grn.sup_id";
        $sql  .= " where grn.id=" . $id;
        return find_value_by_sql($sql);
    }
    /*--------------------------------------------------------------*/

    /* Function for Find get issue
    /*--------------------------------------------------------------*/
    function get_consume_by_id($id)
    {
        global $db;
        $sql  = " select consume.*,loc_name,cost_name,cl.name as claimer,designation";

        $sql  .= " from consume";
        $sql  .= " inner join locations on locations.id=consume.loc_id";
        $sql  .= " inner join cost_centre cost on cost.id=consume.cost_id";
        $sql  .= " inner join users cl on cl.id=consume.con_by";
        $sql  .= " where consume.id=" . $id;
        return find_value_by_sql($sql);
    }
    /*--------------------------------------------------------------*/


    /* Function for Find get issue
    /*--------------------------------------------------------------*/
    function get_consume_return_by_id($id)
    {
        global $db;
        $sql  = " select consume_return.*,loc_name,consumer,cl.name as claimer,designation";

        $sql  .= " from consume_return";
        $sql  .= " inner join consume on consume_return.con_id=consume.id";
        $sql  .= " inner join locations on locations.id=consume.loc_id";
        $sql  .= " inner join users cl on cl.id=consume_return.rtn_by";
        $sql  .= " where consume_return.id=" . $id;
        return find_value_by_sql($sql);
    }
    /*--------------------------------------------------------------*/


    /* Function for Find get issue
    /*--------------------------------------------------------------*/
    function get_grn_return_by_id($id)
    {
        global $db;
        $sql  = " select grn_return.*,loc_name,cl.name as claimer,designation";

        $sql  .= " from grn_return";
        $sql  .= " inner join grn on grn_return.grn_id=grn.id";
        $sql  .= " inner join locations on locations.id=grn.loc_id";
        $sql  .= " inner join users cl on cl.id=grn_return.rtn_by";

        $sql  .= " where grn_return.id=" . $id;
        return find_value_by_sql($sql);
    }
    /*--------------------------------------------------------------*/


    /* Function for Find get adjust
    /*--------------------------------------------------------------*/
    function get_adjust_by_id($id)
    {
        global $db;
        $sql  = " select adjust.*,loc_name,cl.name as claimer,designation";

        $sql  .= " from adjust";

        $sql  .= " inner join locations on locations.id=adjust.loc_id";

        $sql  .= " inner join users cl on cl.id=adjust.adj_by";

        $sql  .= " where adjust.id=" . $id;
        return find_value_by_sql($sql);
    }
    /*--------------------------------------------------------------*/





    /* Function for Find get transfer
    /*--------------------------------------------------------------*/
    function get_transfer_by_id($id)
    {
        global $db;
        $sql  = " select transfer.*,l1.loc_name as locfrom,l2.loc_name as locto,cl.name as claimer,designation";

        $sql  .= " from transfer";
        $sql  .= " inner join locations l1 on l1.id=transfer.tran_from";
        $sql  .= " inner join locations l2 on l2.id=transfer.tran_to";
        $sql  .= " inner join users cl on cl.id=transfer.tran_by";
        $sql  .= " where transfer.id=" . $id;
        return find_value_by_sql($sql);
    }
    /*--------------------------------------------------------------*/




    /*--------------------------------------------------------------*/
    /* Function for Find get specific requisition history by id
    /*--------------------------------------------------------------*/
    function requisition_history_by_id($id)
    {
        global $db;
        $sql  = " SELECT ra.*,u.name as person,designation";
        $sql  .= " FROM requisition_action ra";
        $sql  .= " inner join  users u on u.id=ra.action_by";
        $sql  .= " where ra.req_id=" . $id;
        return find_by_sql($sql);
    }
    /*--------------------------------------------------------------*/





    /*--------------------------------------------------------------*/


    /*--------------------------------------------------------------*/

    /*--------------------------------------------------------------*/
    /* Function for Display Recent product Added
    /*--------------------------------------------------------------*/
    function find_recent_product_added($limit)
    {
        global $db;
        $sql   = " SELECT p.id,p.name,p.media_id,c.name AS categorie,";
        $sql  .= "m.file_name AS image FROM products p";
        $sql  .= " LEFT JOIN categories c ON c.id = p.categorie_id";
        $sql  .= " LEFT JOIN media m ON m.id = p.media_id";
        $sql  .= " ORDER BY p.id DESC LIMIT " . $db->escape((int)$limit);
        return find_by_sql($sql);
    }
    /*--------------------------------------------------------------*/



    /* Function for Display Recent product Added
    /*--------------------------------------------------------------*/
    function find_recent_stock_added($limit)
    {
        global $db;
        $sql   = " select  p.id,p.name,p.media_id,c.name AS categorie,m.file_name AS image,  ";


        $sql  .= " (select round(sum(sub.stock_price)/sum(sub.stock_qty),2) from stock sub where sub.product_id=s.product_id) as stock_price ";
        $sql  .= "from stock s";
        $sql  .= "  inner JOIN products p ON s.product_id = p.id";
        $sql  .= "  inner JOIN categories c ON c.id = p.categorie_id";
        $sql  .= " LEFT JOIN media m ON m.id = p.media_id";

        $sql  .= " where ref_source='GRN' ORDER BY s.id DESC LIMIT " . $db->escape((int)$limit);
        return find_by_sql($sql);
    }
    /*--------------------------------------------------------------*/



    /* Function for Find Highest Consumtion Product
    /*--------------------------------------------------------------*/
    function find_higest_consumtion($limit)
    {
        global $db;
        $sql  = "SELECT p.name, sum(-1*s.stock_price) AS conPrice, SUM(-1*s.stock_qty) AS totalQty,unit_type,unit_name";
        $sql .= " FROM stock s";
        $sql .= " inner JOIN products p ON p.id = s.product_id ";
        $sql .= " inner JOIN units  ON units.id = p.unit_id";
        $sql .= " where ref_source='Issue'  GROUP BY s.product_id";
        $sql .= " ORDER BY totalQty  DESC LIMIT " . $db->escape((int)$limit);
        return $db->query($sql);
    }


    /*--------------------------------------------------------------*/
    /* Function for Display Recent Consumtion
    /*--------------------------------------------------------------*/
    function find_recent_consume_added($limit)
    {
        global $db;
        $sql  = "SELECT stock.*,p.name,stock_date,unit_type,unit_name";
        $sql .= " FROM stock";

        $sql .= " inner JOIN products p ON stock.product_id = p.id";
        $sql .= " inner JOIN units  ON units.id = p.unit_id";
        $sql .= " where ref_source='Issue' ORDER BY stock_date,stock.id DESC LIMIT " . $db->escape((int)$limit);
        return find_by_sql($sql);
    }
    /*--------------------------------------------------------------*/
    /* Function for Generate stock report by two dates
    /*--------------------------------------------------------------*/
    function find_stock_by_dates($start_date, $end_date, $loc, $com_id)
    {
        global $db;
        $start_date  = date("Y-m-d", strtotime($start_date));
        $end_date    = date("Y-m-d", strtotime($end_date));
        $sql  = "select  units.unit_type,product_id,name,short_code,units.unit_name ";
        $sql .= ",sum(  if(date(stock_date) <'$start_date', stock_qty,0 )     )  as opening_qty";
        $sql .= ",sum(  if(date(stock_date) <='$end_date', stock_qty,0 )     )  as closing_qty";
        $sql .= ",sum(  if( date(stock_date) >='$start_date'  and date(stock_date) <='$end_date' and stock_type='receive' , stock_qty,0 )     )  as receive_qty";
        $sql .= ",sum(  if( date(stock_date) >='$start_date'  and date(stock_date) <='$end_date' and stock_type='issue' , stock_qty,0 )     )  as issue_qty";

        $sql .= ",ifnull(sum(stock_price)/sum(stock_qty),0) as price";

        $sql .= " from stock ";
        $sql .= " inner join products on stock.product_id=products.id ";
        $sql .= " inner join units on units.id=products.unit_id ";
        $sql .= " where stock.com= ".$com_id ;
        $sql .= " group by product_id order by short_code ";

        if ($loc > 0) {
            $sql  = "select  units.unit_type,product_id,name,short_code,units.unit_name ";
            $sql .= ",sum(  if(date(stock_date) <'$start_date' and loc_id='$loc', stock_qty,0 )     )  as opening_qty";
            $sql .= ",sum(  if(date(stock_date) <='$end_date' and loc_id='$loc', stock_qty,0 )     )  as closing_qty";
            $sql .= ",sum(  if( date(stock_date) >='$start_date'  and date(stock_date) <='$end_date'  and loc_id='$loc' and stock_type='receive' , stock_qty,0 )     )  as receive_qty";
            $sql .= ",sum(  if( date(stock_date) >='$start_date'  and date(stock_date) <='$end_date'  and loc_id='$loc' and stock_type='issue' , stock_qty,0 )     )  as issue_qty";

            $sql .= ",ifnull(sum(stock_price)/sum(stock_qty),0)  as price";


            $sql .= " from stock ";
            
            $sql .= " inner join products on stock.product_id=products.id ";
            $sql .= " inner join units on units.id=products.unit_id ";
            $sql .= " where stock.com= ".$com_id ;
            $sql .= " group by product_id order by short_code ";
        }
        //print($sql);
        return $db->query($sql);
    }
    /*--------------------------------------------------------------*/




    /*--------------------------------------------------------------*/
    /* Function for Generate stock report by two dates
    /*--------------------------------------------------------------*/
    function find_stock_by_product($start_date, $end_date, $loc, $pid)
    {
        $start_date  = date("Y-m-d", strtotime($start_date));
        $end_date    = date("Y-m-d", strtotime($end_date));
        $sql  = "select  units.unit_type,product_id,name,short_code,units.unit_name ";

        if ($loc > 0) {
            $sql .= ",sum(  if(date(stock_date) <'$start_date' and loc_id='{$loc}', stock_qty,0 )     )  as opening_qty";
            $sql .= ",sum(  if(date(stock_date) <='$end_date' and loc_id='{$loc}', stock_qty,0 )     )  as closing_qty";
        } else {
            $sql .= ",sum(  if(date(stock_date) <'$start_date', stock_qty,0 )     )  as opening_qty";
            $sql .= ",sum(  if(date(stock_date) <='$end_date', stock_qty,0 )     )  as closing_qty";
        }
        $sql .= ",ifnull(sum(stock_price)/sum(stock_qty),0)  as price";

        $sql .= " from stock ";
        $sql .= " inner join products on stock.product_id=products.id ";
        $sql .= " inner join units on units.id=products.unit_id ";
        $sql .= " where ";

        $sql .= "  product_id='$pid' ";

        return find_value_by_sql($sql);
    }
    /*--------------------------------------------------------------*/




    /* Function for Generate issue report by two dates
    /*--------------------------------------------------------------*/
    function find_issue_by_dates($start_date, $end_date, $loc, $pid, $cid, $com_id)
    {
        global $db;
        $start_date  = date("Y-m-d", strtotime($start_date));
        $end_date    = date("Y-m-d", strtotime($end_date));
        $sql  = "select short_code,p.name, quantity,unit_name, unit_type,cost_name,loc_name,consumer,con_date,
	(select ifnull(sum(stock_price)/sum(stock_qty),0) from stock where stock.product_id=cd.product_id)  as price ";


        $sql .= " from consume_details  cd ";
        $sql .= " inner join consume c on c.id=cd.con_id  ";
        $sql .= " inner join products p on cd.product_id=p.id  ";
        $sql .= " inner join units on units.id=p.unit_id  ";
        $sql .= " inner join cost_centre cs on cs.id=c.cost_id  ";
        $sql .= " inner join locations l on l.id=c.loc_id  ";
        $sql .= " where c.com=".$com_id." and cancel_status=0 and submit_status=1 and ";
        $sql .= "  date(con_date) >='$start_date'  and date(con_date) <='$end_date' and quantity>0 ";
        if ($loc > 0) {
            $sql .= " and loc_id='{$loc}'  ";
        }

        if ($pid > 0) {
            $sql .= " and cd.product_id='$pid' ";
        }

        if ($cid > 0) {
            $sql .= " and cost_id='$cid' ";
        }


        //echo $sql ;

        return $db->query($sql);
    }
    /*--------------------------------------------------------------*/



    /* Function for Generate purchase report by two dates
    /*--------------------------------------------------------------*/
    function find_purchase_by_dates($start_date, $end_date, $loc, $pid, $sid, $com_id)
    {
        global $db;
        $start_date  = date("Y-m-d", strtotime($start_date));
        $end_date    = date("Y-m-d", strtotime($end_date));
        $sql  = " select short_code,p.name, quantity,unit_name, unit_type,loc_name,grn_date,sup_name, gd.price as price";


        $sql .= "  from grn_details  gd  ";
        $sql .= "  inner join grn g on g.id=gd.grn_id  ";
        $sql .= "  inner join products p on gd.product_id=p.id    ";
        $sql .= " inner join units on units.id=p.unit_id  ";
        $sql .= " inner join suppliers sp on sp.id=g.sup_id ";
        $sql .= " inner join locations l on l.id=g.loc_id ";
        $sql .= " where g.com=".$com_id." and cancel_status=0 and submit_status=1 and ";
        $sql .= "  date(grn_date) >='$start_date'  and date(grn_date) <='$end_date' and quantity>0 ";

        if ($loc > 0) {
            $sql .= " and loc_id='{$loc}'  ";
        }

        if ($pid > 0) {
            $sql .= " and gd.product_id='$pid' ";
        }

        if ($sid > 0) {
            $sql .= " and sup_id='$sid' ";
        }


        //echo $sql ;

        return $db->query($sql);
    }
    /*--------------------------------------------------------------*/




    /* Function for Generate stock report by two dates
    /*--------------------------------------------------------------*/
    function find_stock_ledger_by_dates($start_date, $end_date, $loc, $pid, $com_id)
    {
        global $db;
        $start_date  = date("Y-m-d", strtotime($start_date));
        $end_date    = date("Y-m-d", strtotime($end_date));
        $sql  = "select units.unit_type,stock_date,product_id,name,short_code,units.unit_name ,";
        $sql  .= "sum( if( stock_type='receive' , stock_qty,0 ) ) as receive_qty, ";
        $sql  .= "sum( if(   stock_type='issue' , stock_qty,0 ) ) as issue_qty";


        $sql .= " from stock ";
        $sql .= " inner join products on stock.product_id=products.id  ";
        $sql .= " inner join units on units.id=products.unit_id  ";
        $sql .= " where  ";
        if ($loc > 0) {
            $sql .= "  loc_id='{$loc}' and ";
        }

        $sql .= "  date(stock_date) >='$start_date'  and date(stock_date) <='$end_date' ";

        $sql .= " and product_id='$pid' ";

        $sql .= " and com='$com_id' ";

        $sql .= " group by product_id,stock_date order by stock_date ";

        //echo $sql ;

        return $db->query($sql);
    }
    /*--------------------------------------------------------------*/




    /* Function for Generate stock report by two dates
    /*--------------------------------------------------------------*/
    function find_opening_stock_ledger_by_dates($start_date, $end_date, $loc, $pid, $com_id)
    {
        $start_date  = date("Y-m-d", strtotime($start_date));
        $end_date    = date("Y-m-d", strtotime($end_date));
        $sql  = "select units.unit_type,stock_date,product_id,name,short_code,units.unit_name ,";
        $sql  .= "sum( stock_qty ) as opening_qty ";



        $sql .= " from stock ";
        $sql .= " inner join products on stock.product_id=products.id  ";
        $sql .= " inner join units on units.id=products.unit_id  ";
        $sql .= " where  ";
        if ($loc > 0) {
            $sql .= "  loc_id='{$loc}' and ";
        }

        $sql .= "  date(stock_date) <='$start_date'";

        $sql .= " and product_id='$pid' ";

        $sql .= " and com='$com_id' ";



        return find_value_by_sql($sql);
    }
    /*--------------------------------------------------------------*/











    /* Function for tracking products
    /*--------------------------------------------------------------*/
    function product_tracking_by_dates($start_date, $end_date, $loc, $pid, $com_id)
    {
        global $db;
        $start_date  = date("Y-m-d", strtotime($start_date));
        $end_date    = date("Y-m-d", strtotime($end_date));
        $sql  = "select  stock.*,units.unit_type,units.unit_type,units.unit_name";


        $sql .= " from stock ";
        $sql .= " inner join products on stock.product_id=products.id ";
        $sql .= " inner join units on units.id=products.unit_id ";
        $sql .= " where  stock.com=".$com_id." and ";
        if ($loc > 0) {
            $sql .= "  loc_id='{$loc}' and ";
        }

        $sql .= "  date(stock_date) >='$start_date'  and date(stock_date) <='$end_date' ";

        $sql .= " and product_id='$pid' ";

    

        return $db->query($sql);
    }
    /*--------------------------------------------------------------*/




    /*--------------------------------------------------------------*/
    /* Function for tracking product price, opening, closing stock
    /*--------------------------------------------------------------*/
    function track_product_stock($start_date, $end_date, $loc, $pid, $com_id)
    {
        $start_date  = date("Y-m-d", strtotime($start_date));
        $end_date    = date("Y-m-d", strtotime($end_date));
        $sql  = "select  units.unit_type,product_id,name,short_code,units.unit_name ";

        if ($loc > 0) {
            $sql .= ",sum(  if(date(stock_date) <'$start_date' and loc_id='{$loc}', stock_qty,0 )     )  as opening_qty";
            $sql .= ",sum(  if(date(stock_date) <='$end_date' and loc_id='{$loc}', stock_qty,0 )     )  as closing_qty";
        } else {
            $sql .= ",sum(  if(date(stock_date) <'$start_date', stock_qty,0 )     )  as opening_qty";
            $sql .= ",sum(  if(date(stock_date) <='$end_date', stock_qty,0 )     )  as closing_qty";
        }
        $sql .= ",ifnull(sum(stock_price)/sum(stock_qty),0)  as price";

        $sql .= " from stock ";
        $sql .= " inner join products on stock.product_id=products.id ";
        $sql .= " inner join units on units.id=products.unit_id ";
        $sql .= " where stock.com=".$com_id." and ";

        $sql .= "  product_id='$pid' ";

        return find_value_by_sql($sql);
    }
    /*--------------------------------------------------------------*/




    /* Checking expected date
/*--------------------------------------------------------------*/
    function expected_date($newdate)
    {
        $expdate = strtotime(date("Y-m-d"));
        $expdate = strtotime("+7 day", $expdate);
        $expdate = date('Y-m-d', $expdate);
        $sql = "SELECT DATEDIFF('$newdate', '$expdate') as days";
        $days = find_value_by_sql($sql)['days'];

        if ($days >= 0) {
            return true;
        } else {
            return false;
        }
    }
