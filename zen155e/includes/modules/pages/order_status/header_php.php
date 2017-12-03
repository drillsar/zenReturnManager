<?php
/**
 * Returns (RMA)
 *
 * Displays information related to a single specific order
 *
 * @package templateSystem
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: header_php.php 1.0.1 11/27/2017 davewest $
 */
 // abuse deterrents like spam from tell-a-freand
define('ORDER_STATUS_THROTTLE_TIMER', 1); // can't do another more frequently than this many seconds 60
// This should be first line of the script:
$zco_notifier->notify('NOTIFY_HEADER_START_ORDER_STATUS');

if (REGISTERED_RETURN == 'true'){
  if (!$_SESSION['customer_id']) {
    $_SESSION['navigation']->set_snapshot();
    $messageStack->add_session('login', TEXT_RETURN_REQUEST_INTRO, 'warning');
    zen_redirect(zen_href_link(FILENAME_LOGIN, '', 'SSL'));
  }
}

//log on users should not have to enter a email address
if ($_SESSION['customer_id']) {
 $customer_info_query = "SELECT customers_email_address FROM " . TABLE_CUSTOMERS . " WHERE customers_id = :coID";
  $customer_info_query = $db->bindVars($customer_info_query, ':coID', $_SESSION['customer_id'], 'integer');
  $customer_info = $db->Execute($customer_info_query);
$email_address = $customer_info->fields['customers_email_address'];
}

//kill order status page from COWAA only switch
if (COWOA_ORDER_STATUS == 'false') zen_redirect(zen_href_link(FILENAME_DEFAULT)); 

require(DIR_WS_MODULES . zen_get_module_directory('require_languages.php'));

$breadcrumb->add(NAVBAR_TITLE);

$killreturns = (RETURNS_AUTHOR_ON == true) ? true : false;

$docancel = false;

if (isset($_GET['action']) && ($_GET['action'] == 'status')) {
$error = false;


  if (isset($_SESSION['order_status_timeout']) && ((int)$_SESSION['order_status_timeout'] + ORDER_STATUS_THROTTLE_TIMER) > time()) $error = true;
  
$orderID = zen_db_prepare_input($_POST['order_id']);
$email_address = zen_db_prepare_input($_POST['email_address']); 
$antiSpam = isset($_POST['kickmyass_bot']) ? zen_db_prepare_input($_POST['kickmyass_bot']) : '';
$query_email_address = $email_address; //fix download issues due to name change

if(!isset($orderID) || (isset($orderID) && !is_numeric($orderID))) {
  $error = true;
  $messageStack->add('order_status', ERROR_INVALID_ORDER);
 }
 
if(!isset($email_address) || zen_validate_email($email_address) == false) {
  $error = true;
  $messageStack->add('order_status', ERROR_INVALID_EMAIL);
}

if((!$error) && ($antiSpam == '')) { //check to see if this address is in our database

 $customer_info_query = "SELECT customers_email_address, shipping_module_code, payment_method, orders_id FROM " . TABLE_ORDERS . " WHERE orders_id = :ordersID";
  $customer_info_query = $db->bindVars($customer_info_query, ':ordersID', $orderID, 'integer');
  $customer_info = $db->Execute($customer_info_query);
 
  if ($customer_info->fields['orders_id'] == '') {
 $error = true ;
 $messageStack->add('order_status', ERROR_NO_MATCH);
 }elseif ($customer_info->fields['customers_email_address'] != $email_address) {
 $error = true ;
 $messageStack->add('order_status', ERROR_INVALID_EMAIL);
 }
if (($customer_info->fields['shipping_module_code'] == 'free') || ($customer_info->fields['payment_method'] == 'Free Order')) {
   $killreturns = true;
 }
}
    
    
if($antiSpam != '') $error = true ;
 
if($error == false)
{

  $customer_info_query = "SELECT customers_email_address, customers_id, orders_id 
                          FROM   " . TABLE_ORDERS . "
                          WHERE  orders_id = :ordersID";

  $customer_info_query = $db->bindVars($customer_info_query, ':ordersID', $orderID, 'integer');
  $customer_info = $db->Execute($customer_info_query);
  
if($customer_info->fields['orders_id'] != $orderID) {
  $messageStack->add('order_status', ERROR_NO_MATCH);
}else{

    $statuses_query = "SELECT os.orders_status_id, os.orders_status_name, osh.date_added, osh.orders_status_id, osh.comments, 
                       orh.rma_number, orh.products_id, orh.orders_status_id
                       FROM   " . TABLE_ORDERS_STATUS . " os, " . TABLE_ORDERS_STATUS_HISTORY . " osh, " . TABLE_ORDER_RETURN_MANAGER . " orh
                       WHERE      osh.orders_id = :ordersID
                       AND        osh.orders_status_id = os.orders_status_id
                       AND        os.language_id = :languagesID
                       AND        osh.customer_notified >= 0
                       ORDER BY   osh.date_added DESC LIMIT 1";

    $statuses_query = $db->bindVars($statuses_query, ':ordersID', $orderID, 'integer');
    $statuses_query = $db->bindVars($statuses_query, ':languagesID', $_SESSION['languages_id'], 'integer');
    $statuses = $db->Execute($statuses_query);
//echo '<pre>' . print_r($statuses) . '</pre>';
$order_status = $statuses->fields['orders_status_name'];

    require(DIR_WS_CLASSES . 'order.php');
    $order = new order($orderID);
       
   $_SESSION['order_status_timeout'] = time(); //set the timer
   $flag_rma = $statuses->fields['rma_number'];
   $custorder_id = $customer_info->fields['customers_id'];
   
if (KILL_CANCEL == 'true') {
   $killreturns = false;
   $docancel = false;   
 }elseif (($order_status == 'Pending') || ($order_status == 'Processing') || ($order_status == 'Cancel Item'))  {
   $killreturns = true;
   $docancel = true;
 }else{
   $killreturns = false;
   $docancel = false;
 }

if (REGISTERED_RETURN == 'true')  {
    $killreturns = true;
    }
   
  } //order_id matched
 } //error is true

//eof action status
}

// This should be last line of the script:
$zco_notifier->notify('NOTIFY_HEADER_END_ORDER_STATUS');
// eof
