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
 * @version $Id: header_main.php 1.0 09/02/2017 davewest $
 */
 
// This should be first line of the script:
$zco_notifier->notify('NOTIFY_HEADER_START_RETURN_REQUEST');

require(DIR_WS_MODULES . zen_get_module_directory('require_languages.php'));

if (REGISTERED_RETURN == 'true'){
  if (!$_SESSION['customer_id']) {
    $_SESSION['navigation']->set_snapshot();
    zen_redirect(zen_href_link(FILENAME_LOGIN, '', 'SSL'));
  }
}

// include template specific file name defines
$define_page = zen_get_file_directory(DIR_WS_LANGUAGES . $_SESSION['language'] . '/html_includes/', FILENAME_DEFINE_RETURNS, 'false');

$breadcrumb->add(NAVBAR_TITLE);

if (isset($_GET['action']) && ($_GET['action'] == 'send')) { 
  $error = false;
  
    $name = zen_db_prepare_input($_POST['contactname']);
    $order_number = zen_db_prepare_input($_POST['order_id']);
    $CoID = zen_db_prepare_input($_POST['coID']);
    $telephone = zen_db_prepare_input($_POST['telephone']);
    $address = zen_db_prepare_input($_POST['address']);
    $city = zen_db_prepare_input($_POST['city']);
    $state = zen_db_prepare_input($_POST['state']);
    $country = zen_db_prepare_input($_POST['country']);
    $postcode = zen_db_prepare_input($_POST['postcode']);
    
    $email_address = zen_db_prepare_input($_POST['email']);
    $rma_number = zen_db_prepare_input($_POST['rma_number']); 
    $action = zen_db_prepare_input($_POST['action']);
    $reason = zen_db_prepare_input(zen_sanitize_string($_POST['reason']));

    $autoRMA = ORDER_STATUS_RMA;
    $reason = addslashes($reason);

if(!isset($order_number) || (isset($order_number) && !is_numeric($order_number))) {  //read only   
  $error = true;
  $messageStack->add('returns', ERROR_INVALID_ORDER);
 }
 
if(!isset($email_address) || zen_validate_email($email_address) == false) {  //read only
  $error = true;
  $messageStack->add('returns', ERROR_INVALID_EMAIL);
}

if (empty($address)){
$error = true;
$messageStack->add('returns', ENTRY_ADDRESS_ERROR_STACK);
}
if (empty($city)){
$error = true;
$messageStack->add('returns', ENTRY_CITY_TEXT_ERROR_STACK);
}
if (empty($state)){
$error = true;
$messageStack->add('returns', ENTRY_STATE_TEXT_ERROR_STACK);
}
if (empty($postcode)){
$error = true;
$messageStack->add('returns', ENTRY_POSTCODE_ERROR_STACK);
} 
if (empty($country)){
$error = true;
$messageStack->add('returns', ENTRY_COUNTRY_TEXT_ERROR_STACK);
}

if (empty($reason) && RETURN_REASON == '1'){
$error = true;
$messageStack->add('returns', ENTRY_REASON_TEXT_ERROR_STACK);
}

if($antiSpam != '') $error = true ;

if ($error == true) {
    zen_redirect(zen_href_link(FILENAME_RETURNS, '', 'SSL'));
}

 
 
//get and setup products selected from Order Status page.
$howmany = count($_POST['notify']);
$y = 0;

  // Loop to get the values of individual checked checkbox.
for ($i=0, $n=$howmany; $i<$n; $i++) {
$productsID = $_POST['notify'][$i];

     $proqty = $_POST['Prod_qty'][$y];
  
 $orders_query_raw = "SELECT op.products_name, op.products_price, op.products_quantity, op.products_id, p.products_image, p.products_id FROM " . TABLE_ORDERS_PRODUCTS . " op, " . TABLE_PRODUCTS . " p WHERE op.orders_id = :ordersID AND op.products_id =" . (int)$productsID . " AND p.products_id = op.products_id";

$orders_query_raw = $db->bindVars($orders_query_raw, ':ordersID', (int)$order_number, 'integer');
$order_info = $db->Execute($orders_query_raw);

$productname = $order_info->fields['products_name'];
$price = $currencies->format($order_info->fields['products_price']);
$price_raw = $order_info->fields['products_price'];
$prodID = $productsID; 

$email_item[$y] = "Product ID: " . ". . . . ." . $prodID . "\n" .
              "Item Name" . ". . . . . . ." . $productname . "\n" .
              "Item Price Each: " . ". . ." . $price . "\n" .
              "Returning: " . ". . . . ." . $proqty . "\n";

// orders_id, customers_id, orders_status_id, date_added, customers_name, customers_email_address, comments, products_id, products_name, products_price, products_quantity, rma_number, action, rma_type, return_telephone, return_street_address, return_city, return_state, return_postcode, return_country, return_value

$db->Execute("insert into " . TABLE_ORDER_RETURN_MANAGER . " (orders_id, customers_id, orders_status_id, date_added, customers_name, customers_email_address, comments, products_id, products_name, products_price, products_quantity, rma_number, action, rma_type, return_telephone, return_street_address, return_city, return_state, return_postcode, return_country) values ('" . (int)$order_number ."', '" . $CoID . "', '" . $autoRMA ."', now(), '" . $name . "', '" . $email_address . "', '" . $reason ."', '" . $prodID ."', '" . $productname ."', '" . $price_raw ."', '" . $proqty ."', '" . $rma_number ."', '" . $action . "', 2, '" . $telephone ."', '" . $address ."', '" . $city ."', '" . $state ."', '" . $postcode ."', '" . $country . "')");


 $y++;

    } //end for


$dogorder = '<address>'. $address . '<br />' . $city . '<br />' . $state . ' ' . $postcode . '<br />' . $country . '</address>';
$_SESSION['retunaddress'] = $dogorder;
$_SESSION['retunrma'] = $rma_number;
$_SESSION['ordernum'] = $order_number;
$_SESSION['returnQTY'] = $proqty;

if (ORDER_STATUS_RMA_OPTION == 'true') {
$db->Execute("update " . TABLE_ORDERS . " set orders_status = $autoRMA, last_modified = now() where orders_id = '" . (int)$order_number . "'");
}

if (ORDER_COMMENTS_RMA_OPTION == 'true') {
$db->Execute("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('" . (int)$order_number ."', '" . $autoRMA ."', now(), 0, '" . $reason ."')");
}


		$send_to_email = EMAIL_FROM;
		$send_to_name =  STORE_NAME;
		
	
    // Prepare extra-info details
    $extra_info = email_collect_extra_info($name, $email_address, $customer_name, $customer_email, $telephone);
    // Prepare Text-only portion of message
	$text_message = 
OFFICE_FROM . ". . . . . . ." . $name . "\n" . 
OFFICE_EMAIL . ". . . . . . . . . " . $email_address . "\n" .
"Phone Number:" . ". . . . . . ." . $telephone . "\n" .
"Address:" . ". . . . . . . ." . $address . "\n" .
"City:" . ". . . . . . . . . ." . $city . "\n" .
"Post Code:" . ". . . . . ." . $postcode . "\n" .
"Country:" . ". . . . . . ." . $country . "\n" .
"State:" . ". . . . . . . ." . $state . "\n" .
"Order Number:" . ". . . ." . $order_number . "\n" .
"RMA Number:" . ". . . . ." . $rma_number . "\n";

$howmany = count($email_item);
for ($i=0, $n=$howmany; $i<$n; $i++) {
$text_message .= $email_item[$i];
}

$text_message .= 
"Action Requested:" . "." . $action . "\n"	. "\n" . 

'------------------------------------------------------' . "\n" .
"Reason:" . "\n" . 
$reason .  "\n" .
'------------------------------------------------------' . "\n" .
            $extra_info['TEXT'];
      //$email_text = sprintf(EMAIL_GREET_NONE, $name);
      $email_text .= EMAIL_TEXT;
      $email_text .= EMAIL_CONTACT ;
      $email_text .= EMAIL_SHIPPING_URL_BOF . zen_href_link(FILENAME_SHIPPING) . EMAIL_SHIPPING_URL_EOF;
     // $email_text .= "\n\n" . EMAIL_WARNING . "\n" . EMAIL_CONTACT_URL_BOF . zen_href_link(FILENAME_SHIPPING) . EMAIL_CONTACT_URL_EOF . "\n\n";

	  // Prepare HTML-portion of message
        // Prepare HTML-portion of message
      $html_msg['EMAIL_NAME'] = $name;
      $html_msg['EMAIL_MESSAGE_HTML'] = $email_text;
      $html_msg['EXTRA_INFO'] = $extra_info['HTML'];



	  // Send message
$email_subject = EMAIL_SUBJECT . ' RMA# ' . $rma_number;

     zen_mail($name, $email_address, $email_subject, $email_text, $send_to_name, $send_to_email, $html_msg, 'returns');
     
    $html_msg['EMAIL_MESSAGE_HTML'] = $text_message;
      $html_msg['EMAIL_GREETING'] = '';
	  $html_msg['EMAIL_WELCOME'] = '';
      $html_msg['CONTACT_US_OFFICE_FROM'] = OFFICE_FROM . ' ' . $name . '<br />' . OFFICE_EMAIL . '(' . $email_address . ')';
    $html_msg['EXTRA_INFO'] = '';
  
     zen_mail(STORE_OWNER, EMAIL_FROM, EMAIL_SUBJECT, $text_message, $name, $email_address, $html_msg, 'returns');
	
   zen_redirect(zen_href_link(FILENAME_RETURNS, 'action=success', 'SSL')); 
   

} else {

$CoID = zen_db_prepare_input($_POST['coID']); //customer ID
$orderID = zen_db_prepare_input($_POST['order_id']);  //order ID
$email_address = zen_db_prepare_input($_POST['email_address']);  //email address

//products selected for returns within  $_POST['notify'] which is an array of product ID's

  $sql = "SELECT c.customers_id, c.customers_firstname, c.customers_lastname, c.customers_email_address, c.customers_default_address_id, c.customers_telephone, ab.customers_id, ab.entry_street_address, ab.entry_city, ab.entry_postcode, ab.entry_state, ab.entry_zone_id, ab.entry_country_id, z.zone_id, z.zone_code, cn.countries_id, cn.countries_name FROM " . TABLE_CUSTOMERS . " c, " . TABLE_ADDRESS_BOOK . " ab, "  . TABLE_ZONES  . " z, " . TABLE_COUNTRIES . " cn WHERE c.customers_id = :customersID AND ab.customers_id = c.customers_id AND ab.entry_country_id = cn.countries_id";
  
  $sql = $db->bindVars($sql, ':customersID', (int)$CoID, 'integer');
  $check_customer = $db->Execute($sql);
  
  //we should have everything from this order, now see what to use
  $name = $check_customer->fields['customers_firstname'] . ' ' . $check_customer->fields['customers_lastname'];
  $customer_email = $check_customer->fields['customers_email_address'];
  $telephone = $check_customer->fields['customers_telephone'];
  $address = $check_customer->fields['entry_street_address'];
  $city = $check_customer->fields['entry_city'];


  if (isset($check_customer->fields['zone_id']) && zen_not_null($check_customer->fields['zone_id'])) {
        $state = zen_get_zone_code($check_customer->fields['entry_country_id'], $check_customer->fields['entry_zone_id'], $check_customer->fields['entry_state']);
      }

//  $state = $check_customer->fields['entry_state'];
  $country = $check_customer->fields['countries_name'];
  $postcode = $check_customer->fields['entry_postcode'];

//set a variable to display address used in shipping this order to customer
$dogorder = '<address><b>'. $address . '<br />' . $city . '<br />' . $state . ' ' . $postcode . '<br />' . $country . '</b></address>';

$RMAback = randomKey(RMAKEY); //creates a random set of characters
//$RMAback = date('mdY'); //old not used here revers to use date and not key

//format is: orderID divider customerID divider random characters  1015-5-DE4FR
$rma_number = $orderID . RATDIV . $CoID . RATDIV . $RMAback; 

//get and setup products selected from Order Status page.
$howmany = count($_POST['notify']);
$y = 0;
  // Loop to store and display values of individual checked checkbox.
for ($i=0, $n=$howmany; $i<$n; $i++) {
$productsID = $_POST['notify'][$i];
 $proqty = $_POST['Prod_qty'][$y];
     
 $orders_query_raw = "SELECT op.products_name, op.products_price, op.products_id, p.products_image, p.products_id FROM " . TABLE_ORDERS_PRODUCTS . " op, " . TABLE_PRODUCTS . " p WHERE op.orders_id = :ordersID AND op.products_id =" . (int)$productsID . " AND p.products_id = op.products_id";

$orders_query_raw = $db->bindVars($orders_query_raw, ':ordersID', (int)$orderID, 'integer');
$order_info = $db->Execute($orders_query_raw);

$productname = $order_info->fields['products_name'];
$price = $currencies->format($order_info->fields['products_price']);
$productimage = $order_info->fields['products_image']; 

$image[$y]= zen_image('images/' . $productimage, addslashes($productname), 75, 75) ; //format the image for display
$product_info[$y] = '<b>Name:</b> ' . $productname ; //format the product info for display
$prodID[$y] = $productsID; //set this for the checkbox code
$prodQTY[$y] = $proqty;

 $y++;

    } //end for
      

}


// This should be last line of the script:
$zco_notifier->notify('NOTIFY_HEADER_END_RETURN_REQUEST');
?>
