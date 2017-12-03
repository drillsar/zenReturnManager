<?php
/**
 * Returns Manager Install
 * For Zen-Cart 1.5.5
 * Last Updated: 11/27/2017
 *
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 *
 * designed for Zen Cart 1.5.5e and php 7.1
 *@version $Id: return_functions.php  v1.0 10 2017-09-11 16:32:39Z davewest $
 *
 *
 * configure Return Manager 
*/

  if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

    // set version
        $version = '1.0.1';

    // Set table name
 	$table_name = DB_PREFIX . 'order_return_manager';
 	
    
if ((!defined ('RETURN_MANAGER_VERSION')) || (RETURN_MANAGER_VERSION != $version)) {

$configuration = $db->Execute("SELECT configuration_group_id FROM " . TABLE_CONFIGURATION_GROUP . " WHERE configuration_group_title = 'Return Manager' ORDER BY configuration_group_id ASC;");

if ($configuration->RecordCount() > 0) {

 $categoryid = array();
	$id_result = $db->Execute("SELECT configuration_group_id FROM ". TABLE_CONFIGURATION_GROUP . " WHERE configuration_group_title = 'Return Manager'");
	if (!$id_result->EOF) {
			$categoryid = $id_result->fields;
		
			$ra_configuration_id = $categoryid['configuration_group_id'];
			// kill config
			$db->Execute("DELETE FROM ".TABLE_CONFIGURATION." WHERE configuration_group_id = '" . $ra_configuration_id ."'");
                        $db->Execute("DELETE FROM ". TABLE_CONFIGURATION_GROUP . " WHERE configuration_group_id = '" . $ra_configuration_id ."'");
         }               
                        
}else{
global $sniffer;

$insert_result1 = $db->Execute("INSERT INTO " . TABLE_CONFIGURATION_GROUP . " (configuration_group_title, configuration_group_description, sort_order, visible) VALUES ('Return Manager', 'Return Manager Display Settings', '1', '1');");

$db->Execute("UPDATE ". TABLE_CONFIGURATION_GROUP . " SET `sort_order` = LAST_INSERT_ID() WHERE configuration_group_id = LAST_INSERT_ID()");

if ($insert_result1 === false) exit ('Error in Createing New Configuration Group - Return Manager<br/> ');

// Get the id of the new configuration category
    $categoryid = array();
	$id_result = $db->Execute("SELECT configuration_group_id FROM ". TABLE_CONFIGURATION_GROUP . " WHERE configuration_group_title = 'Return Manager'");
	if (!$id_result->EOF) {
			$categoryid = $id_result->fields;
			$ra_configuration_id = $categoryid['configuration_group_id'];
    } else {
    	    exit ('Failed Finding Return Manager Configuration_Group ID<br/>Exit');
    }
    

//-- ADD VALUES TO RETURN MANAGER CONFIGURATION GROUP (Admin > Configuration > Return Manager) --
$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . "(configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('Display Name field options','RETURN_NAME','1','Display Name field as:<br />0 = Display as Optional<br />1 = Display as Required<br />2 = Do not Display', '".$ra_configuration_id."',1, NOW(), NOW(), NULL, 'zen_cfg_select_option(array(\'0\', \'1\', \'2\'),')");

$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . "(configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('Display Reason Text Area options', 'RETURN_REASON', '1', 'Is Reason Text Area as:<br />0 = Display as Optional<br />1 = Display as Required', '".$ra_configuration_id."', 2, NOW(), NOW(), NULL, 'zen_cfg_select_option(array(\'0\', \'1\'),')");

$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . "(configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('Set Return Action Dropdown List', 'RETURN_ACTION_LIST_OPTIONS', 'Get a Refund, Get a Replacement, Exchange for another item, Repair under warranty', 'Format: Action 1,  Action 2', '".$ra_configuration_id."', 3, NOW(), NOW(), NULL, 'zen_cfg_textarea(')");

$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . "(configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('Set Cancel Action Dropdown List', 'CANCEL_ACTION_LIST_OPTIONS', 'Would not arrive on time,Found a cheaper price,Decided not to buy now,No reason', 'Format: Action 1,  Action 2', '".$ra_configuration_id."', 4, NOW(), NOW(), NULL, 'zen_cfg_textarea(')");

$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . "(configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('Show Store Name, Address and Phone for Returns (ship to) on Returns Page', 'RETURN_STORE_NAME_ADDRESS', 'true', '(Admin>Configuration>My Store>Store Name & Store Address and Phone)', '".$ra_configuration_id."', 5, NOW(), NOW(), NULL, 'zen_cfg_select_option(array(''true'', ''false''), ')");

$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . "(configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('Show Store Name, Address and Phone for Returns (ship to) on Returns Success Page', 'RETURN_STORE_NAME_ADDRESS_SUCCESS', 'false', '(Admin>Configuration>My Store>Store Name & Store Address and Phone)', '".$ra_configuration_id."', 6, NOW(), NOW(), NULL, 'zen_cfg_select_option(array(''true'', ''false''), ')");

$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . "(configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('Show Different Returns (ship to) Address on Returns Success Page', 'RETURN_STORE_NAME_ADDRESS_DIFF', 'Your Store<br />123 North Main Street<br />Your Town, Your State #####', 'Include a Store Name & Store Address and Phone<br />Show Store Name, Address and Phone for Returns (ship to) on Returns Success Page (Set to false)', '".$ra_configuration_id."', 7, NOW(), NOW(), NULL, 'zen_cfg_textarea(')");

$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . "(configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('Show comment notes!', 'DISPLAY_COMMENTS', 'false', 'Display comment notes on Order Statuse Page for all to see!', '".$ra_configuration_id."', 8, NOW(), NOW(), NULL, 'zen_cfg_select_option(array(''true'', ''false''), ')");

$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . "(configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('Define Page options', 'DEFINE_RETURNS_STATUS', '1', '(Admin>Tools>Define Pages Editor>define_returns.php)<br />Enable the Defined Return Manager Link/Text?<br />0= Link ON, Define Text OFF<br />1= Link ON, Define Text ON<br />2= Link OFF, Define Text ON<br />3= Link OFF, Define Text OFF', '".$ra_configuration_id."', 9, NOW(), NOW(), NULL, 'zen_cfg_select_option(array(\'0\', \'1\', \'2\', \'3\'),')");

$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . "(configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('Define Page options', 'DEFINE_CANCELME_STATUS', '1', '(Admin>Tools>Define Pages Editor>define_cancelme.php)<br />Enable the Defined Cancel Item Link/Text?<br />0= Link ON, Define Text OFF<br />1= Link ON, Define Text ON<br />2= Link OFF, Define Text ON<br />3= Link OFF, Define Text OFF', '".$ra_configuration_id."', 10, NOW(), NOW(), NULL, 'zen_cfg_select_option(array(\'0\', \'1\', \'2\', \'3\'),')");

$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . "(configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('Turn off the ability of Cancellations!', 'KILL_CANCEL', 'false', 'Turn off the ability of a customer canceling a order! Only Returns will work. <br />OFF=True ON=false', '".$ra_configuration_id."', 11, NOW(), NOW(), NULL, 'zen_cfg_select_option(array(''true'', ''false''), ')");

$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . "(configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('<strong>Admin</strong> Registered Customers Only?', 'REGISTERED_RETURN', 'false', 'Only Registered Customers may submit a return request', '".$ra_configuration_id."', 12, NULL, NOW(), NULL, 'zen_cfg_select_option(array(''true'', ''false''), ')");

$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . "(configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('<strong>Admin</strong> Update Order Status option', 'ORDER_STATUS_RMA_OPTION', 'true', 'Update <strong>Admin</strong> Order Status upon RMA Success', '".$ra_configuration_id."', 13, NOW(), NOW(), NULL, 'zen_cfg_select_option(array(''true'', ''false''), ')");

$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . "(configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('<strong>Admin</strong> Order Status for Return Items', 'ORDER_STATUS_RMA', '2', 'Number of the order status assigned when an RMA is submitted.', '".$ra_configuration_id."', 14, NOW(), NOW(), 'zen_get_order_status_name', 'zen_cfg_pull_down_order_statuses(')");

$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . "(configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('<strong>Admin</strong> Order Status for Delivered Items', 'ORDER_STATUS_COMPARE', '3', 'Number of the order status assigned when an order is (Complete - Shipped).', '".$ra_configuration_id."', 15, NOW(), NOW(), 'zen_get_order_status_name', 'zen_cfg_pull_down_order_statuses(')");

$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . "(configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('<strong>Admin</strong> Order Status for Canceled Items', 'ORDER_STATUS_CANCEL', '4', 'Number of the order status assigned when an order (Item is Canceled).', '".$ra_configuration_id."', 16, NOW(), NOW(), 'zen_get_order_status_name', 'zen_cfg_pull_down_order_statuses(')");

$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . "(configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('<strong>Admin</strong> Return Grace Period', 'RETURN_GRACE_PERIOD', '30', '<strong>Numeric Number Only</strong> This represents your <strong>30</strong> Day Return Policy or how ever many days you allow a customer to return an item.', '".$ra_configuration_id."', 17, NOW(), NOW(), NULL, NULL)");

$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . "(configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('<strong>Admin</strong> Update Order Comments option', 'ORDER_COMMENTS_RMA_OPTION', 'true', 'Update <strong>Admin</strong> Order Comments upon RMA Success',  '".$ra_configuration_id."', 18, NOW(), NOW(), NULL, 'zen_cfg_select_option(array(''true'', ''false''), ')");

$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . "(configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('<strong>Email</strong> RMA Grace Period', 'RMA_GRACE_PERIOD', '15', 'This tells your customer how many days till the RMA# expires.', '".$ra_configuration_id."', 19, NOW(), NOW(), NULL, NULL)");

$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . "(configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('<strong>Admin Email</strong> Copy to admin!', 'ADMIN_EMAIL_COPY', 'true', 'Send a copy of the Admin created email to Store Owner.', '".$ra_configuration_id."', 20, NOW(), NOW(), NULL, 'zen_cfg_select_option(array(''true'', ''false''), ')");

$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . "(configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('RMA Divider', 'RATDIV', '-', 'This is a divider for the RMA number set<br />(ie.. 1015-5-FSE7D)', '".$ra_configuration_id."', 21, NOW(), NOW(), NULL, NULL)");

$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . "(configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('RMA Key', 'RMAKEY', 5, 'This is a number of charaters to use in the random genarater ie.. 5<br />5 is the default number used.', '".$ra_configuration_id."', 22, NOW(), NOW(), NULL, NULL)");

$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . "(configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('<strong>Admin Return Manager Version</strong>', 'RETURN_MANAGER_VERSION', '" . $version . "', 'Return Manager Version',  '".$ra_configuration_id."', 30, NOW(), NOW(), NULL, NULL)");
         
		
//remove old table data no longer used
if ($sniffer->field_exists(TABLE_ORDERS_STATUS_HISTORY,'rma_number')) $db->Execute("ALTER TABLE " . TABLE_ORDERS_STATUS_HISTORY . " DROP rma_number");
if ($sniffer->field_exists(TABLE_ORDERS_STATUS_HISTORY,'action')) $db->Execute("ALTER TABLE " . TABLE_ORDERS_STATUS_HISTORY . " DROP action");

//setup orders_status table for returns and cancels
$next_id_value = $db->Execute ("SELECT MAX(orders_status_id) + 1 as next_id, language_id FROM " . TABLE_ORDERS_STATUS );
$next_ID = $next_id_value->fields['next_id'];
$languageID = $next_id_value->fields['language_id'];

$check_status = $db->Execute("SELECT 'orders_status_name' FROM ". TABLE_ORDERS_STATUS ." WHERE orders_status_name = 'RMA# Issued' LIMIT 0, 300 ");
if ($check_status->RecordCount() < 1) {
$db->Execute("INSERT INTO " . TABLE_ORDERS_STATUS . " (orders_status_id, language_id, orders_status_name) VALUES (" . (int)$next_ID . ", " . (int)$languageID . ", 'RMA# Issued')");  
}

$check_status = $db->Execute("SELECT 'orders_status_name' FROM ". TABLE_ORDERS_STATUS ." WHERE orders_status_name = 'Cancel Item' LIMIT 0, 300 ");
if ($check_status->RecordCount() < 1) {
$next_ID++;
$db->Execute("INSERT INTO " . TABLE_ORDERS_STATUS . " (orders_status_id, language_id, orders_status_name) VALUES (" . (int)$next_ID . ", " . (int)$languageID . ", 'Cancel Item')");  
}

// return_id, orders_id, customers_id, orders_status_id, date_added, customers_name, customers_email_address, comments, products_id, products_name, products_price, products_quantity, products_num, rma_number, action, rma_type, return_telephone, return_street_address, return_city, return_state, return_postcode, return_country, return_value

//varchar(255) NOT NULL  -- changed from varchar(15) NOT NULL DEFAULT '0.00'

//create new table if not existing already
if (!$sniffer->table_exists($table_name)) {  
      $result = $db->Execute("CREATE TABLE IF NOT EXISTS " . $table_name ." (
                             `return_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                             `orders_id` int(11) NOT NULL DEFAULT '0',
                             `customers_id` int(11) NOT NULL DEFAULT '0',
                             `orders_status_id` int(5) NOT NULL DEFAULT '0',
                             `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                             `customers_name` varchar(64) NOT NULL DEFAULT '',
                             `customers_email_address` varchar(96) NOT NULL DEFAULT '',
                             `comments` text NOT NULL,
                             `products_id` int(11) NOT NULL DEFAULT '0',
                             `products_name` varchar(64) NOT NULL DEFAULT '',
                             `products_price` varchar(15) NOT NULL DEFAULT '',
                             `products_quantity` float NOT NULL DEFAULT '0',
                             `products_num` varchar(15) NOT NULL DEFAULT '',
                             `rma_number` varchar(255) NOT NULL DEFAULT '',
                             `action` varchar(255) NOT NULL DEFAULT '',
                             `rma_type` TINYINT(1) NOT NULL DEFAULT '0', 
                             `return_telephone` varchar(32) NOT NULL DEFAULT '',
                             `return_street_address` varchar(64) NOT NULL DEFAULT '',
                             `return_city` varchar(32) NOT NULL DEFAULT '',
                             `return_state` varchar(32) DEFAULT NULL,
                             `return_postcode` varchar(10) NOT NULL DEFAULT '',
                             `return_country` varchar(32) NOT NULL DEFAULT '',
                             `return_value` varchar(255) NOT NULL,
                             PRIMARY KEY  (`return_id`))");
 
                              
            if ($result) {
		echo 'New Order Return Manager Database Table Successfully Created.<br />';
	        $db->Execute("insert into " . $table_name . " (return_id, orders_id, customers_id, orders_status_id, date_added, customers_name, customers_email_address, comments, products_id, products_name, products_price, products_quantity, products_num, rma_number, action) values (1, 1000, 0, 5, now(), 'Dave', 'dave@addme.com', 'This is a test', 1, 'test me', '$1.00', 5, '101test', '1000-0-WFE8S', 'Get Refund')");
		} else { 
		echo 'There was a problem in trying to create the database. You may need to do this manually using the following SQL code: <br>' . $table_name; 
		}
    } else {
    echo $table_name . ' Already exist!<br />';
   $field_name = 'return_value';
   $field_type = 'varchar(15)';
   if ($sniffer->field_type($table_name, $field_name, $field_type)) $db->Execute("ALTER TABLE " . $table_name ." CHANGE `return_value` `return_value` VARCHAR(255) NOT NULL");

     }
     
/********* register Return Manager admin pages for Zen 1.5.x *****************/

    if (function_exists('zen_register_admin_page')) {
    
    zen_deregister_admin_pages('configReturnAuthorization');
    zen_deregister_admin_pages('configReturnMan');
    zen_register_admin_page('configReturnMan', 'BOX_CONFIGURATION_RETURN', 'FILENAME_CONFIGURATION', 'gID='. $ra_configuration_id . '', 'configuration', 'Y', 410);
    zen_deregister_admin_pages('ReturnManager');
    zen_register_admin_page('ReturnManager', 'BOX_CUSTOMERS_RETURN_MANAGER', 'FILENAME_RETURNS', '', 'customers', 'Y', 510);
    }  
 } //RA exist
} //RA same version installed

/** fuctions used in this mod not part of install **/

/**
 * get customer comments
 * modified for returns
 */
  function zen_get_returns_comments($orders_id, $returns_id) {
    global $db;
    $returns_comments_query = "SELECT orh.comments from " .
                              TABLE_ORDER_RETURN_MANAGER . " orh
                              where orh.orders_id = '" . (int)$orders_id . "'
                              and orh.return_id = '" . (int)$returns_id . "'
                              order by orh.return_id
                              limit 1";
    $returns_comments = $db->Execute($returns_comments_query);
    if ($returns_comments->EOF) return '';
    return $returns_comments->fields['comments'];
  }

/**
 * compute the days between two dates
 * modified for returns
 */
  function zen_returns_date_diff($date1, $date2) {
  //$date1  m/d/Y  02/20/2017  today, or any other day
  //$date2  m/d/Y  02/20/2017  date to check against

    $d1 = explode("/", $date1);
    $m1 = $d1[0];
    $d1 = $d1[1];
    $y1 = $d1[2];

    $d2 = explode("/", $date2);
    $m2 = $d2[0];
    $d2 = $d2[1];
    $y2 = $d2[2];


    $date1_set = mktime(0,0,0, (int)$m1, (int)$d1, (int)$y1);
    $date2_set = mktime(0,0,0, (int)$m2, (int)$d2, (int)$y2);

    return(round(($date2_set-$date1_set)/(60*60*24)));
  }

// set up random charater set used in RMA number 
// used in tpl_returns_default.php  
function randomKey($length) {
    $pool = array_merge(range(0,9), range('A', 'Z'));  //range('a', 'z'),
    for($i=0; $i < $length; $i++) {
        $key .= $pool[mt_rand(0, count($pool) - 1)];
    }
    return $key;
}

/**
 * In Zen Cart versions 1.5.3 on, this function exist so we do not need it here
 * in admin/functions/plugin_support.php
 * I do not like auto checks so done using a button if you wanted to be tracked use MS Windoze!
 * this function checks ZC site for the latest plugin version for the version of ZC you are running
 * it does not go any where else or send any other info then mod title and current versions.
 */
if (!function_exists('plugin_version_check_for_updates')) { 
  function plugin_version_check_for_updates($plugin_file_id = 0, $version_string_to_compare = '')
  {
    if ($plugin_file_id == 0) return FALSE;
    $new_version_available = FALSE;
    $lookup_index = 0;
    $url1 = 'https://plugins.zen-cart.com/versioncheck/'.(int)$plugin_file_id;
    $url2 = 'https://www.zen-cart.com/versioncheck/'.(int)$plugin_file_id;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url1);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 9);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 9);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Plugin Version Check [' . (int)$plugin_file_id . '] ' . HTTP_SERVER);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $errno = curl_errno($ch);

    if ($error > 0) {
      trigger_error('CURL error checking plugin versions: ' . $errno . ':' . $error . "\nTrying http instead.");
      curl_setopt($ch, CURLOPT_URL, str_replace('tps:', 'tp:', $url1));
      $response = curl_exec($ch);
      $error = curl_error($ch);
      $errno = curl_errno($ch);
    }
    if ($error > 0) {
      trigger_error('CURL error checking plugin versions: ' . $errno . ':' . $error . "\nTrying www instead.");
      curl_setopt($ch, CURLOPT_URL, str_replace('tps:', 'tp:', $url2));
      $response = curl_exec($ch);
      $error = curl_error($ch);
      $errno = curl_errno($ch);
    }
    curl_close($ch);
    if ($error > 0 || $response == '') {
      trigger_error('CURL error checking plugin versions: ' . $errno . ':' . $error . "\nTrying file_get_contents() instead.");
      $ctx = stream_context_create(array('http' => array('timeout' => 5)));
      $response = file_get_contents($url1, null, $ctx);
      if ($response === false) {
        trigger_error('file_get_contents() error checking plugin versions.' . "\nTrying http instead.");
        $response = file_get_contents(str_replace('tps:', 'tp:', $url1), null, $ctx);
      }
      if ($response === false) {
        trigger_error('file_get_contents() error checking plugin versions.' . "\nAborting.");
        return false;
      }
    }

    $data = json_decode($response, true);
    if (!$data || !is_array($data)) return false;
    // compare versions
    if (strcmp($data[$lookup_index]['latest_plugin_version'], $version_string_to_compare) > 0) $new_version_available = TRUE;
    // check whether present ZC version is compatible with the latest available plugin version
    if (!in_array('v'. PROJECT_VERSION_MAJOR . '.' . PROJECT_VERSION_MINOR, $data[$lookup_index]['zcversions'])) $new_version_available = FALSE;
    return ($new_version_available) ? $data[$lookup_index] : FALSE;
  }
 }  
?>
