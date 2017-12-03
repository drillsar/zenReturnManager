<?php
/**
 * Returns (RMA)
 *
 * Zen Returns Manager 
 *
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: returns.php 1.0.1 11/27/2017 davewest $
 */

require('includes/application_top.php');

  // unset variable which is sometimes tainted by bad plugins like magneticOne tools
  if (isset($module)) unset($module);

  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

  if (isset($_GET['oID'])) $_GET['oID'] = (int)$_GET['oID'];
  if (isset($_GET['download_reset_on'])) $_GET['download_reset_on'] = (int)$_GET['download_reset_on'];
  if (isset($_GET['download_reset_off'])) $_GET['download_reset_off'] = (int)$_GET['download_reset_off'];

  include(DIR_WS_CLASSES . 'order.php');

  // prepare order-status pulldown list
  $orders_statuses = array();
  $orders_status_array = array();
  $orders_status = $db->Execute("select orders_status_id, orders_status_name
                                 from " . TABLE_ORDERS_STATUS . "
                                 where language_id = '" . (int)$_SESSION['languages_id'] . "' order by orders_status_id");
  while (!$orders_status->EOF) {
    $orders_statuses[] = array('id' => $orders_status->fields['orders_status_id'],
                               'text' => $orders_status->fields['orders_status_name'] . ' [' . $orders_status->fields['orders_status_id'] . ']');
    $orders_status_array[$orders_status->fields['orders_status_id']] = $orders_status->fields['orders_status_name'];
    $orders_status->MoveNext();
  }
  
  $createNew = false;
$action = (isset($_GET['action']) ? $_GET['action'] : '');
  
  if (isset($_POST['oID'])) {
    $oID = zen_db_prepare_input(trim($_POST['oID']));
  } elseif (isset($_GET['oID'])) {
    $oID = zen_db_prepare_input(trim($_GET['oID']));
  }

  $killButton = 'false';
   if ($action != 'create_new' && !isset($_GET['oID'])) {
   //existing returns exist so edit them
  $return_exists = false;

  if (isset($_GET['rID']) && trim($_GET['rID']) == '') unset($_GET['rID']);
  if ($action == 'edit' && !isset($_GET['rID'])) $action = '';

  $rID = FALSE;
  if (isset($_POST['rID'])) {
    $rID = zen_db_prepare_input(trim($_POST['rID']));
  } elseif (isset($_GET['rID'])) {
    $rID = zen_db_prepare_input(trim($_GET['rID']));
  }
  if ($rID) {
    $returns = $db->Execute("select return_id from " . TABLE_ORDER_RETURN_MANAGER . " where return_id = '" . (int)$rID . "'");
    $return_exists = true;
    if ($returns->RecordCount() <= 0) {
      $return_exists = false;
      if ($action != '') $messageStack->add_session(ERROR_ORDER_DOES_NOT_EXIST . ' ' . $rID, 'error');
      zen_redirect(zen_href_link(FILENAME_RETURNS, zen_get_all_get_params(array('rID', 'action')), 'NONSSL'));
    }
  } 
 } else{ //take orderID and do edit, this lets admin create returns for a order from orders.php
  $return_exists = true;
  $createNew = true;
  if($action == 'create_new') $killButton = 'true';
  if($action == 'editqty') $killButton = 'false';
  if($action == 'create_new') $action = 'edit';
 
 }
  
/**** start switch section ****************/  
  if (zen_not_null($action) && $return_exists == true) {
    switch ($action) {
   
      case 'editqty':  
      $rID ; //current return ID is still active
      $oID = $_POST['oID'];    
 //return_products[] ratProd_id[] 
$howmany = count($_POST['return_products']);  //number of products in order
$y = 0;

  // Loop to get the values of product box.
for ($i=0, $n=$howmany; $i<$n; $i++) {

$productsID = $_POST['ratProd_id'][$i];     
  $ratqty = $_POST['return_products'][$y];    
  $qtyMax = $_POST['qtyMax'][$i]; 
  $rmatype = $_POST['rmatype_'.$productsID];
  
  if($ratqty > $qtyMax) { 
     $messageStack->add_session('Bad Quantity error!  Return/Cancels must not be grater than ordered quantity!', 'error');
      zen_redirect(zen_href_link(FILENAME_RETURNS, zen_get_all_get_params(array('action')) . 'action=edit', 'NONSSL'));
      }
 
if($ratqty == 0) {  /** Delete a return/cancel item if total is zero 0 */
    
   $db->Execute("DELETE FROM " . TABLE_ORDER_RETURN_MANAGER . " 
                  WHERE orders_id = '" . (int)$oID . "'
                  AND products_id = '" . (int)$productsID . "'
                  ");
     $messageStack->add_session('Return Deleted.', '');             
 }else if($ratqty >= 1)  {   /**  Update an existing return/cancel item total is grater than 0 */
    
   $FinExist = $db->Execute("SELECT products_id FROM " . TABLE_ORDER_RETURN_MANAGER . " WHERE orders_id = '" . (int)$oID . "' and products_id = '" . (int)$productsID .  "'");
    
    if($FinExist->fields['products_id'] == $productsID) {
      $db->Execute("UPDATE " . TABLE_ORDER_RETURN_MANAGER . " 
                  SET products_quantity = '" . (float)$ratqty . "', rma_type = '" . (int)$rmatype . "' 
                  WHERE orders_id = '" . (int)$oID . "'  
                  AND products_id = '" . (int)$productsID . "'
                  ");  
    }else{  /** Create a new return/cancel item total is grater than 0 with no existing return ID */
    
      if($rmatype == 0) $rmatype = 2;
      
 $product = $db->Execute("SELECT p.products_name, p.products_price, p.products_quantity, p.products_id,
                          o.customers_name, o.customers_email_address, o.customers_id
                          FROM " . TABLE_ORDERS_PRODUCTS . " p, " . TABLE_ORDERS . " o
                          WHERE p.orders_id = '" . (int)$oID . "' 
                          AND o.orders_id = p.orders_id
                          AND p.products_id = '" . (int)$productsID . "'
                          ");
 

      $productname = $product->fields['products_name'];
      $price = $currencies->format($product->fields['products_price']);
      $price_raw = $product->fields['products_price'];
      $prodID = $productsID; 
      $retaction = 'No reason'; 
      $reason = 'added by admin for customer'; 
      $autoRMA =  ORDER_STATUS_RMA;  
      $custID = $product->fields['customers_id'];
      $custname =  $product->fields['customers_name'];
      $custemail = $product->fields['customers_email_address'];
        //format is: orderID divider customerID divider random characters  1015-5-DE4FR
      $RMAback = randomKey(RMAKEY); //creates a random set of characters
      $rma_number = (string)$oID . RATDIV . (string)$custID . RATDIV . $RMAback; 
      

      $db->Execute("insert into " . TABLE_ORDER_RETURN_MANAGER . " (orders_id, customers_id, orders_status_id, date_added, customers_name, customers_email_address, comments, products_id, products_name, products_price, products_quantity, rma_number, action, rma_type) values ('" . (int)$oID ."', '" . (int)$custID . "', '" . $autoRMA ."', now(), '" . $custname . "', '" . $custemail . "', '" . $reason ."', '" . $prodID ."', '" . $productname ."', '" . $price_raw ."', '" . $ratqty ."', '" . $rma_number ."', '" . $retaction . "', '" . $rmatype . "')");
      
    } 
    
  }
                  
    $y++;

}    
         $messageStack->add_session('Return Updated.', 'success');
         zen_redirect(zen_href_link(FILENAME_RETURNS, zen_get_all_get_params(array('action')) . 'action=edit', 'NONSSL'));
        break;

      case 'editsave':  
          // return_value
          
          $oID = $_POST['oID'];
            $returns = $db->Execute("select return_id from " . TABLE_ORDER_RETURN_MANAGER . " where orders_id = '" . (int)$oID . "'");
            
            //finOpen,finTAX,finRestock,finShipping,finCOD,finLowOrder,ratDog
            $rtnTotal = zen_db_prepare_input($_POST['finOpen']) . ',' . 
                        zen_db_prepare_input($_POST['finTAX']) . ',' . 
                        zen_db_prepare_input($_POST['finRestock']) . ',' . 
                        zen_db_prepare_input($_POST['finShipping']) . ',' . 
                        zen_db_prepare_input($_POST['finCOD']) . ',' . 
                        zen_db_prepare_input($_POST['finLowOrder']) . ',' . 
                        zen_db_prepare_input($_POST['ratDog']);

              while (!$returns->EOF) {
              $ratID = $returns->fields['return_id'];     
              $db->Execute(" UPDATE " . TABLE_ORDER_RETURN_MANAGER . " SET return_value = '" . $rtnTotal . "' WHERE return_id = '" . (int)$ratID . "'"); 
              $returns->MoveNext();
               }
               
               //add to order history so return value shows on the order page
               $comments = TEXT_RETURN_LINE . zen_db_prepare_input($_POST['ratDog']);
               $db->Execute("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('" . (int)$oID . "', 4, now(), -1, '" . $comments . "')");
               
            $messageStack->add_session('Return Total value saved.', 'success');
          zen_redirect(zen_href_link(FILENAME_RETURNS, zen_get_all_get_params(array('action')) . 'action=edit', 'NONSSL'));
         
        break;

      case 'rataddress':  
           $oID = $_POST['oID'];
            $returns = $db->Execute("select return_id from " . TABLE_ORDER_RETURN_MANAGER . " where orders_id = '" . (int)$oID . "'");
           
          $update_name  = zen_db_prepare_input(trim($_POST['update_name']));
          $update_telephone  = zen_db_prepare_input(trim($_POST['update_telephone']));
          $update_street_address  = zen_db_prepare_input(trim($_POST['update_street_address']));
          $update_city  = zen_db_prepare_input(trim($_POST['update_city']));
          $update_state  = zen_db_prepare_input(trim($_POST['update_state']));
          $update_postcode  = zen_db_prepare_input(trim($_POST['update_postcode']));
          $update_country  = zen_db_prepare_input(trim($_POST['update_country']));
          $update_email_address  = zen_db_prepare_input(trim($_POST['update_email_address']));
              
          while (!$returns->EOF) {
              $ratID = $returns->fields['return_id'];     
              $db->Execute(" UPDATE " . TABLE_ORDER_RETURN_MANAGER . " 
              SET customers_name = '" . $update_name . "',
              customers_email_address= '" . $update_email_address . "',
              return_telephone= '" . $update_telephone . "',
              return_street_address= '" . $update_street_address . "',
              return_city= '" . $update_city . "',
              return_state= '" . $update_state . "',
              return_postcode= '" . $update_postcode . "',
              return_country= '" . $update_country . "' 
              WHERE return_id = '" . (int)$ratID . "'"); 
              
              $returns->MoveNext();
               } 
               
               
           $messageStack->add_session('Return address saved.', 'success');
          zen_redirect(zen_href_link(FILENAME_RETURNS, zen_get_all_get_params(array('action')) . 'action=edit', 'NONSSL'));
          
        break;
      
      case 'sendRMA': 
            if (isset($_POST['oID'])) {
              $oID = zen_db_prepare_input(trim($_POST['oID']));
            } elseif (isset($_GET['oID'])) {
              $oID = zen_db_prepare_input(trim($_GET['oID']));
            }
            
           $returns = $db->Execute("select * from " . TABLE_ORDER_RETURN_MANAGER . " where orders_id = '" . (int)$oID . "'");
           
//customers_id, orders_status_id, date_added, comments, rma_type, return_value            
          $name  = $returns->fields['customers_name'];
          $telephone  = $returns->fields['return_telephone'];
          $email_address  = $returns->fields['customers_email_address'];
          $orderID  = (string)$returns->fields['orders_id'];
          
          //set a variable to display address used in shipping this order to customer
          $dogorder = "\n" . $returns->fields['return_street_address'] . "\n<br />" . 
                      $returns->fields['return_city'] . "\n<br />" . 
                      $returns->fields['return_state'] . "\n<br />" . 
                      $returns->fields['return_postcode'] . "\n<br />" . 
                      $returns->fields['return_country'] . "\n";




   //get and setup products in this return.
$howmany = $returns->RecordCount();
$y = 0;

  // Loop to get the values of individual products.
while(!$returns->EOF) {

     $proqty = $returns->fields['products_quantity'];
     $productname = $returns->fields['products_name'];
     $price = $currencies->format($returns->fields['products_price']);
     $action = $returns->fields['action'];
     $RMA = $returns->fields['rma_number'];

$email_item[$y] = "\n" . "<br />Item Name: " . $productname . "\n" .
              "<br />Item Price Each: " . $price . "\n" .
              "<br />Returning: " . $proqty . "\n" . 
              "<br />Action: " . $action . "\n" .
              "<br />RMA Number: " . $RMA . "\n<br />";

 $y++;
 $returns->MoveNext();
    } //end while
      	
         	$send_to_email = EMAIL_FROM;
		$send_to_name =  STORE_NAME;
		
   
    // Prepare Text-only portion of message
    $address_message = 
OFFICE_SENT_TO . $name . "\n<br />" . 
OFFICE_EMAIL .  $email_address . "\n<br />" .
ENTRY_TELEPHONE . $telephone . "\n<br />" .
ORDER_NO . $orderID . "\n<br />" .
EMAIL_FROM_ADDRESS . $dogorder . "\n<br />";

$howmany = count($email_item);
for ($i=0, $n=$howmany; $i<$n; $i++) {
$item_list .= $email_item[$i];
}

      $email_subject = EMAIL_TEXT_SUBJECT;
      $email_text .= EMAIL_TEXT;
      $email_text .= EMAIL_CONTACT ;
      $text_message = $email_text . "\n" . $address_message . "\n" . $item_list;
	
        // Prepare HTML-portion of message
      $html_msg['EMAIL_NAME'] = $name;
      $html_msg['EMAIL_MESSAGE_HTML'] = $email_text . "<br />" . EMAIL_SEPARATOR . "<br />" . $address_message . "<br />" . EMAIL_SEPARATOR . "<br />" . $item_list;
      $html_msg['EXTRA_INFO'] = '';
	 

 // Send message
//customer email
 zen_mail($name, $email_address, $email_subject, $text_message, STORE_OWNER, EMAIL_FROM, $html_msg, 'returns');

//Store owner email
if (ADMIN_EMAIL_COPY == 'true') {
   zen_mail(STORE_OWNER, EMAIL_FROM, EMAIL_TEXT_SUBJECT, $text_message, $name, $email_address, $html_msg, 'returns');
}
         $messageStack->add_session(TEXT_SUCCESS_EMAIL, 'success');
         zen_redirect(zen_href_link(FILENAME_RETURNS, zen_get_all_get_params(array('action')) . 'action=edit', 'NONSSL'));
       break;
        
      default:
       break;
    }
  }
/************ end switch section ***********/  	  
?>

<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
<style >
div.BMBox {
    background: #ffffff;
    margin-top: 1em;
}
.BMalert {
font-weight: bold;
border: 1px solid #666;
margin: 1em;
font-size: 1em;
text-align: center;
padding: 1em;
background:#f9eecc;
}
</style>
<script language="javascript" src="includes/menu.js"></script>
<script language="javascript" src="includes/general.js"></script>
<script type="text/javascript">
  <!--
  function init()
  {
    cssjsmenu('navbar');
    if (document.getElementById)
    {
      var kill = document.getElementById('hoverJS');
      kill.disabled = true;
    }
    
  }

function updatesum(theField) 
{
  var theForm = theField.form;
  var fldName = "finItemTotal|finOpen|finTAX|finRestock|finShipping|finCOD|finLowOrder".split('|');
  var theTotal = 0;
  var subtotal = 0;
  var ortotal = parseFloat(document.getElementById("dogOrder").value.replace(/([^\d\.\-])([^\w]*)/g,""));

  for(var i=0;i<fldName.length;i++){
    var theField = theForm[fldName[i]];
	var theValue = theField.value.replace(/([^\d\.\-])([^\w]*)/g,"");
	if(theValue>""){
	  theTotal = theTotal + (theValue*1);
	  theField.value = "$"+(theValue*1).toFixed(2);
	} else {
	  theField.value = "";
	}
  }
    
  theForm.finSubTotal.value = "$"+theTotal.toFixed(2);
  theForm.ratTotal.value = "$"+theTotal.toFixed(2);
  
  subtotal = ortotal - theTotal;
  theForm.ratDog.value = "$"+subtotal.toFixed(2);

}

function minmax(value, min, max) 
{
    if(parseInt(value) < min || isNaN(parseInt(value))) 
        return 0; 
    else if(parseInt(value) > max) 
        return max; 
    else return value;
}

//-->
</script>
</head>
<body onLoad="init(); updatesum(finOpen); update_zone(edit_return_address);">
<!-- header //-->
<?php
  require(DIR_WS_INCLUDES . 'header.php');
?>
<!-- header_eof //-->
<br />
<div>
 <div class="main" ><a href="<?php echo zen_href_link(FILENAME_RETURNS, 'action=mor'); ?>"><?php echo zen_image_button('button_remove.gif', 'Remove Returns Manager'); ?></a> <a href="<?php echo zen_href_link(FILENAME_RETURNS, 'action=ckupdate', 'NONSSL'); ?>"><?php echo zen_image_button('button_check_new_version.gif', 'Update Returns Manager'); ?></a> </div>     
  </div> 
  
<?php if ($action == 'mor') { 
  $action = '';
  ?>
<div class="BMalert"><?php echo TEXT_REMOVE_WARRING; ?>
<br /> <br />
<a href="<?php echo zen_href_link(FILENAME_RETURNS); ?>"> <?php echo zen_image_button('button_cancel.gif', IMAGE_CANCEL); ?></a> 
<a href="<?php echo zen_href_link(FILENAME_RETURNS, 'action=remove', 'NONSSL'); ?>"><?php echo zen_image_button('button_remove.gif', 'Remove Returns Manager'); ?></a>


</div>
  <?php  
    } elseif ($action == 'ckupdate') { 
  $action = '';
  ?>
<div class="BMalert"><br /> 
<?php echo TEXT_UPDATE_WARRING; ?>
<br />
<?php echo TEXT_UPDATE_DISCLAMER; ?>
<br /> <br /> 
       <a href="<?php echo zen_href_link(FILENAME_RETURNS) ?>"><?php echo zen_image_button('button_cancel.gif', IMAGE_CANCEL) ?></a> <a href="<?php echo zen_href_link(FILENAME_RETURNS, 'action=ckupd') ?>"><?php echo zen_image_button('button_check_new_version.gif', 'Check for Updated Returns Manager') ?></a>
       </div> 
       
<?php  }elseif ($action == 'remove') { 
              $action = '';
      
   $categoryid = array();
	$id_result = $db->Execute("SELECT configuration_group_id FROM ". TABLE_CONFIGURATION_GROUP . " WHERE configuration_group_title = 'Return Manager'");
	if (!$id_result->EOF) {
			$categoryid = $id_result->fields;
			$isit_installed .= 'Return Manager Configuration_Group ID = ' . $categoryid['configuration_group_id']. '<br>';
			$rm_config_id = $categoryid['configuration_group_id'];
			// kill config
			$db->Execute("DELETE FROM ".TABLE_CONFIGURATION." WHERE configuration_group_id = '" . $rm_config_id ."'");
                        $db->Execute("DELETE FROM ". TABLE_CONFIGURATION_GROUP . " WHERE configuration_group_id = '" . $rm_config_id ."'");
                        $isit_installed .= 'deleted Return Manager Configuration files!<br />';
                        // kill admin pages for ZC1.5.x only
                        if (function_exists('zen_deregister_admin_pages')) {  
                               zen_deregister_admin_pages('ReturnManager');
                               zen_deregister_admin_pages('configReturnMan');
                        $isit_installed .= 'deleted Return Manager Admin Pages!<br />';
                        }

                      $db->Execute("DELETE FROM " . TABLE_ORDERS_STATUS . " WHERE orders_status_name = 'RMA# Issued' LIMIT 1");
                      $db->Execute("DELETE FROM " . TABLE_ORDERS_STATUS . " WHERE orders_status_name = 'Cancel Item' LIMIT 1");

if ($sniffer->table_exists(TABLE_ORDER_RETURN_MANAGER)) $db->Execute("DROP TABLE " . TABLE_ORDER_RETURN_MANAGER );

          
//check for and remove the auto loader page so it wont install again
  if(file_exists(DIR_FS_ADMIN . DIR_WS_INCLUDES . 'functions/extra_functions/returns_functions.php')) {
         if(!unlink(DIR_FS_ADMIN . DIR_WS_INCLUDES . 'functions/extra_functions/returns_functions.php')) {
		$isit_installed .= 'Autoloader deleted<br />';
	};
    }

///done 
     echo $isit_installed . '<br /><br />Return Manager SQL and Menues have been deleted! Please delete all files! ' . ' <a href="' . zen_href_link(FILENAME_DEFAULT) .'"> ' . zen_image_button('button_go.gif', 'Exit this installer') . '</a><br />';
    exit;

    } else { 
//not done 
    $messageStack->add_session('Failed Finding Return Manager Configuration_Group ID!<br />No change made.', 'error');
    echo $isit_installed . '<br /><br />Read the help to help figure out what went wrong ' . ' <a href="' . zen_href_link(FILENAME_DEFAULT) .'"> ' . zen_image_button('button_go.gif', 'Exit this installer') . '</a><br />';
    	   
    }	
    


} elseif ($action == 'ckupd') {
               $action = '';
           
        $module_constant = 'RETURN_MANAGER_VERSION'; // This should be a UNIQUE name followed by _VERSION for convention
	$module_name = "Zen Return Manager v1.0"; // This should be a plain English or Other in a user friendly way
	$zencart_com_plugin_id = 2170; // from zencart.com plugins - Leave Zero not to check
	$current_version = RETURN_MANAGER_VERSION; //this should be the current installed version

  $configuration_group_id = '';
  $checklinknote = '';

    $config = $db->Execute("SELECT configuration_group_id FROM " . TABLE_CONFIGURATION . " WHERE configuration_key= '" . $module_name . "'");
    $configuration_group_id = $config->fields['configuration_group_id'];

// Version Checking 
$new_version_details = plugin_version_check_for_updates($zencart_com_plugin_id, $current_version);
    if ($new_version_details != FALSE) {
        echo '<div class="BMalert">Version ' . $new_version_details['latest_plugin_version']. ' of ' . $new_version_details['title'] . ' is available at <a href="' . $new_version_details['link'] . '" target="_blank">[Details]</a>';
    } else {
     echo '<div class="BMalert">No New Version for Return Manager is available or ID is set to 0.</div>';
     
    }
 } //end remove-update  ?>     
  
<table class="container-fluid" border="0" width="100%" cellspacing="2" cellpadding="2">
<!-- body_text //-->
  <tr>
    <td class="pageHeading"><?php echo ($action == 'edit' && $return_exists) ? HEADING_TITLE_DETAILS : HEADING_TITLE; ?> </td>
  </tr>

<?php $order_list_button = '<button type="button" class="btn btn-default" onclick="window.location.href=\'' . zen_href_link(FILENAME_RETURNS) . '\'"><i class="fa fa-th-list" aria-hidden="true"></i> ' . BUTTON_TO_LIST . '</button>'; //FILENAME_ORDERS  ?>



<?php  //edit return fields
  if ($action == 'edit'&& $return_exists) {
    if(!$createNew) {
    $returns = $db->Execute("select * from " . TABLE_ORDER_RETURN_MANAGER . " where return_id = '" . (int)$rID . "'");
    $oID = $returns->fields['orders_id'];     
    }else{
       $returns = $db->Execute("select * from " . TABLE_ORDER_RETURN_MANAGER . " where orders_id = '" . (int)$oID . "' LIMIT 1"); 
    }
    
    //finOpen,finTAX,finRestock,finShipping,finCOD,finLowOrder,ratDog   $0.00,$0.00,$20.00,$10.00,$0.00,$0.00,$80.00
   list($finOpen,$finTAX,$finRestock,$finShipping,$finCOD,$finLowOrder,$ratDog) = explode(",", $returns->fields['return_value']);

    $order = new order($oID);
    //$zco_notifier->notify('NOTIFY_ADMIN_ORDERS_EDIT_BEGIN', $oID, $order);
    if ($order->info['payment_module_code']) {
      if (file_exists(DIR_FS_CATALOG_MODULES . 'payment/' . $order->info['payment_module_code'] . '.php')) {
        require(DIR_FS_CATALOG_MODULES . 'payment/' . $order->info['payment_module_code'] . '.php');
        require(DIR_FS_CATALOG_LANGUAGES . $_SESSION['language'] . '/modules/payment/' . $order->info['payment_module_code'] . '.php');
        $module = new $order->info['payment_module_code'];
//        echo $module->admin_notification($oID);
      }
    }

?>
      <tr>
        <td width="100%" class="noprint">
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="row">
              <div class="col-xs-10 col-xs-offset-1 col-sm-6 col-sm-offset-3 col-md-5 col-md-offset-3 col-lg-4 col-lg-offset-4">
                <div class="input-group">
                  <div class="input-group-btn">
                    <?php //echo $prev_button; ?>
                    <?php echo $order_list_button ; ?>
                    <?php //echo $next_button; ?>
                  </div>
                  <div class="input-group-btn">
                    <button type="button" class="btn btn-default" onclick="javascript:history.back()"><i class="fa fa-undo" aria-hidden="true"></i> <?php echo IMAGE_BACK; ?></button>
                  </div>
              </div> 
            </td>
          </tr>
        </table> <!-- //-->
        </td>
      </tr>
      <tr>
        <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td colspan="3"><?php echo zen_draw_separator(); ?></td>
          </tr>
          <tr>
            <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main" valign="top"><strong><?php echo ENTRY_CUSTOMER_ADDRESS; ?></strong></td>
                <td class="main"><?php echo zen_address_format($order->customer['format_id'], $order->customer, 1, '', '<br />'); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo zen_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
              </tr>
              <tr>
                <td class="main"><strong><?php echo ENTRY_TELEPHONE_NUMBER; ?></strong></td>
                <td class="main"><a href="tel:<?php echo $order->customer['telephone']; ?>"><?php echo $order->customer['telephone']; ?></a></td>
              </tr>
              <tr>
                <td class="main"><strong><?php echo ENTRY_EMAIL_ADDRESS; ?></strong></td>
                <td class="main"><?php echo '<a href="mailto:' . $order->customer['email_address'] . '">' . $order->customer['email_address'] . '</a>'; ?></td>
              </tr>
              <tr>
                <td class="main"><strong><?php echo TEXT_INFO_IP_ADDRESS; ?></strong></td>
                <?php if ($order->info['ip_address'] != '') {
                  $lookup_ip = substr($order->info['ip_address'], 0, strpos($order->info['ip_address'], ' '));
                ?>
                <td class="main"><a href="http://www.dnsstuff.com/tools#whois|type=ipv4&&value=<?php echo $lookup_ip; ?>"  target="_blank"><?php echo $order->info['ip_address']; ?></a></td>
                <?php } else { ?>
                <td class="main"><?php echo TEXT_UNKNOWN; ?></td>
                <?php } ?>
              </tr>
              <tr>
                <td class="main"><strong><?php echo ENTRY_CUSTOMER; ?></strong></td>
                <td class="main"><?php echo '<a href="' . zen_href_link(FILENAME_CUSTOMERS, 'search=' . $order->customer['email_address'], 'SSL') . '" . >' . TEXT_CUSTOMER_LOOKUP . '</a>'; ?></td>
              </tr>
            </table></td>
            <td valign="top">       
            <table width="96%" border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main" valign="top" width="25%"><strong><?php echo 'Return ' . ENTRY_SHIPPING_ADDRESS; ?></strong></td>
                <td valign="top" width="75%">
                <?php echo zen_draw_form('edit_return_address', FILENAME_RETURNS, zen_get_all_get_params(array('action','rataddress')) . 'action=rataddress'); 
                echo zen_draw_hidden_field('oID', $oID); ?>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td class="main" align="right"><strong><?php echo 'Customer Name:&nbsp;'  ; ?></strong></td>
                  <?php $update_name = ($returns->fields['customers_name'] == '') ? $order->customer['name'] : $returns->fields['customers_name']; ?>
                  <td><input name="update_name" size="15" value="<?php echo $update_name; ?>"></td>
                </tr>
                <tr>
                  <td class="main" align="right"><strong><?php echo ENTRY_TELEPHONE_NUMBER; ?></strong></td>
                  <td>
                  <?php if($returns->fields['return_telephone'] == '') {
                  echo '<span  style="color:red;"><input name="update_telephone" size="15" value="' . $order->customer['telephone']  . '"></span>';
                  }else{
                  echo '<input name="update_telephone" size="15" value="' . $returns->fields['return_telephone']  . '">';
                  }    ?>
                  </td>
                </tr>
                <tr>
                  <td class="main" align="right"><strong> <?php echo 'Street Address:&nbsp;'; ?></strong></td>
                  <td>
                   <?php if($returns->fields['return_street_address'] == '') {
                  echo '<span  style="color:red;"><input name="update_street_address" size="15" value="' . $order->delivery['street_address']  . '"></span>';
                  }else{
                  echo '<input name="update_street_address" size="15" value="' . $returns->fields['return_street_address']  . '">';
                  }    ?>
                  </td>
                </tr>  
                 <tr>
                  <td class="main" align="right"><strong> <?php echo 'City:&nbsp;'; ?></strong></td>
                  <td>
                   <?php if($returns->fields['return_city'] == '') {
                  echo '<span  style="color:red;"><input name="update_city" size="15" value="' . $order->delivery['city']  . '"></span>';
                  }else{
                  echo '<input name="update_city" size="15" value="' . $returns->fields['return_city']  . '">';
                  }    ?>
                  </td>
                </tr>
                <tr>
                  <td class="main" align="right"><strong> <?php echo 'State:&nbsp;'; ?></strong></td>
                  <td>
                   <?php if($returns->fields['return_state'] == '') {
                  echo '<span  style="color:red;"><input name="update_state" size="15" value="' . $order->delivery['state']  . '"></span>';
                  }else{
                  echo '<input name="update_state" size="15" value="' . $returns->fields['return_state']  . '">';
                  }    ?>
                  </td>
                </tr>
                <tr>
                  <td class="main" align="right"><strong> <?php echo 'Postal Code:&nbsp;'; ?></strong></td>
                  <td>
                   <?php if($returns->fields['return_postcode'] == '') {
                  echo '<span  style="color:red;"><input name="update_postcode" size="15" value="' . $order->delivery['postcode']  . '"></span>';
                  }else{
                  echo '<input name="update_postcode" size="15" value="' . $returns->fields['return_postcode']  . '">';
                  }    ?>
                  </td>
                </tr>  
                <tr>
                  <td class="main" align="right"><strong> <?php echo 'Country:&nbsp;'; ?></strong></td>
                  <td><?php
        if(is_array($order->delivery['country']) && array_key_exists('id', $order->delivery['country'])) {
            echo zen_get_country_list('update_delivery_country', $order->delivery['country']['id']);
        }
        else { ?>
            <input name="update_country" size="15" value="<?php echo zen_db_output($order->delivery['country']); ?>">
        <?php } ?>
        </td>
                </tr>
                <tr>
                  <td class="main" align="right"><strong> <?php echo ENTRY_EMAIL_ADDRESS; ?></strong></td>
                  <td>
                   <?php if($returns->fields['customers_email_address'] == '') {
                  echo '<span  style="color:red;"><input name="update_email_address" size="20" value="' . $order->customer['email_address']  . '"></span>';
                  }else{
                  echo '<input name="update_email_address" size="20" value="' . $returns->fields['customers_email_address']  . '">';
                  }    ?>
                  </td>
                </tr>  
                <tr>
                  <td class="main" align="right"><strong><?php echo TEXT_UPDATE_FIRST; ?></strong> </td>
                  <td class="main" >&nbsp;&nbsp;&nbsp;<?php echo ($killButton == 'false') ? zen_image_submit('button_update.gif', IMAGE_UPDATE)  : TEXT_CREATE_FIRST;  ?>
                 </form>
                 </td>
                </tr> 
                </table>              
                </td>
              </tr>
            </table>
            </td>
            <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main" valign="top"><strong><?php echo ENTRY_BILLING_ADDRESS; ?></strong></td>
                <td class="main"><?php echo zen_address_format($order->billing['format_id'], $order->billing, 1, '', '<br />'); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo zen_draw_separator('pixel_trans.gif', '1', '10');?></td>
      </tr>
<!-- Begin Payment Block -->

      <tr>
        <td class="main"><strong><?php echo ENTRY_ORDER_ID . $oID; ?></strong></td>
      </tr>
      <tr>
     <td><table border="0" cellspacing="0" cellpadding="2">
        <tr>
           <td class="main"><strong><?php echo ENTRY_DATE_PURCHASED; ?></strong></td>
           <td class="main"><?php echo zen_date_long($order->info['date_purchased']); ?></td>
        </tr>
        <tr>
           <td class="main"><strong><?php echo ENTRY_PAYMENT_METHOD; ?></strong></td>
           <td class="main"><strong><?php echo zen_db_output($order->info['payment_method']); ?></strong></td>
        </tr>
    </table></td>
      </tr>

<!-- End Payment Block -->

      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr class="dataTableHeadingRow">
        <td class="dataTableHeadingContent" colspan="2" width="30%"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
        <td class="dataTableHeadingContent" align="center" width="5%"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
        <td class="dataTableHeadingContent" align="center" width="10%""><?php echo TABLE_HEADING_RMA_NUMBER; ?></td>
        <td class="dataTableHeadingContent" align="center" width="5%""><?php echo TABLE_HEADING_QTY; ?></td>
        <td class="dataTableHeadingContent" align="center" width="10%"><?php echo TABLE_HEADING_UNIT_PRICE; ?></td>
        <td class="dataTableHeadingContent" align="center" width="10%""><?php echo TABLE_HEADING_RC; ?></td>
        <td class="dataTableHeadingContent" align="center" width="10%"><?php echo TABLE_HEADING_QUANTITY; ?></td>
        <td class="dataTableHeadingContent" align="right" width="5%"><?php echo TABLE_HEADING_NEW . TABLE_HEADING_TOTAL_PRICE; ?></td>
        <td class="dataTableHeadingContent" align="right" width="5%"><?php echo TABLE_HEADING_OLD . TABLE_HEADING_TOTAL_PRICE; ?></td>
      </tr>
    <!-- Begin Products Listings Block -->
<?php

echo zen_draw_form('edit_quanty', FILENAME_RETURNS, zen_get_all_get_params(array('action','editqty')) . 'action=editqty'); 
echo zen_draw_hidden_field($oInfo->return_id);
   // $orders_products_id_mapping = eo_get_orders_products_id_mappings((int)$oID);
    $subreturntotal = 0;
    $returnTotal = 0;
    $rquant = 0;
    $cquant = 0;
   
    for($i=0; $i<sizeof($order->products); $i++) {
       // $orders_products_id = $orders_products_id_mapping[$i];
        $qtyMax = $order->products[$i]['qty'];
        $orders_products_id = $order->products[$i]['id'];
        
    $returns = $db->Execute("select * from " . TABLE_ORDER_RETURN_MANAGER . " 
                             where orders_id = '" . (int)$oID . "'
                             and products_id = '" . (int)$order->products[$i]['id'] . "'");
    
     $returnID = $returns->fields['return_id'][$i];    
?>
       
        <tr class="dataTableRow">
            <td class="dataTableContent" colspan="2"  valign="top" align="left"><?php echo $order->products[$i]['id'] . '&nbsp;&nbsp;&nbsp;&nbsp;' . zen_db_output($order->products[$i]['name']); ?>

<?php
  echo $product['attributeHiddenField'];
  if (isset($product['attributes']) && is_array($product['attributes'])) {
  echo '<div class="cartAttribsList">';
  echo '<ul>';
    reset($product['attributes']);
    foreach ($product['attributes'] as $option => $value) {
?>

<li><?php echo $value['products_options_name'] . TEXT_OPTION_DIVIDER . nl2br($value['products_options_values_name']); ?></li>

<?php
    }
  echo '</ul>';
  echo '</div>';

  }  
?>
 
        
            </td>
            <td class="dataTableContent" valign="top" align="center"><?php echo $order->products[$i]['model']; ?></td>
            <td class="dataTableContent" valign="top" align="center"><?php echo $returns->fields['rma_number']; ?></td>
            <td class="dataTableContent" valign="top" align="center">
            <?php echo zen_db_prepare_input($order->products[$i]['qty']); ?> &nbsp;&nbsp;&nbsp;&nbsp; X
            </td>
            <?php $ratTax = $order->products[$i]['tax']; ?>
            <td class="dataTableContent" align="center" valign="top"><?php echo number_format($order->products[$i]['final_price'], 2, '.', ''); ?></td>      

     
<?php                                
//return = 2  cancel = 1  not = 0
$rquant = $returns->fields['rma_type'];      

$ratqty = ($returns->fields['products_quantity'] != '') ? $returns->fields['products_quantity'] : 0;   
        
?>                        
            <td class="dataTableContent" valign="top" align="center"><?php 
           if($rquant == 2) {
             echo zen_draw_radio_field('rmatype_'.$orders_products_id, '2', true, 'id="reclusive-yes" ') . '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;' . zen_draw_radio_field('rmatype_'.$orders_products_id, '1', false, 'id="reclusive-no" ') ;
            }elseif($rquant == 1) {
             echo zen_draw_radio_field('rmatype_'.$orders_products_id, '2', false, 'id="reclusive-yes" ') . '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;' . zen_draw_radio_field('rmatype_'.$orders_products_id, '1', true, 'id="reclusive-no" ') ;
            } else {
             echo zen_draw_radio_field('rmatype_'.$orders_products_id, '2', false, 'id="reclusive-yes" ') . '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;' . zen_draw_radio_field('rmatype_'.$orders_products_id, '1', false, 'id="reclusive-no" ') ;
            }  /**/
           
            ?>
            </td>
            
            <td class="dataTableContent" valign="top" align="center"> -&nbsp;&nbsp; <input name="return_products[]" size="5" value="<?php echo $ratqty; ?>" onkeyup="this.value = minmax(this.value, 0, <?php echo $qtyMax; ?>)" type="text" />  = <?php echo zen_draw_hidden_field('ratProd_id[]',$orders_products_id);  echo zen_draw_hidden_field('qtyMax[]', $qtyMax); echo zen_draw_hidden_field('oID', $oID); ?></td>   

<?php

  $subitemtotal = ($order->products[$i]['final_price'] * ($order->products[$i]['qty'] - $ratqty)); 
  ?>                 
            <td class="dataTableContent" align="right" valign="top"><?php echo $GLOBALS['currencies']->format($subitemtotal, true, $order->info['currency'], $order->info['currency_value']); ?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
            
            <td class="dataTableContent" align="right" valign="top"><?php echo $GLOBALS['currencies']->format($order->products[$i]['final_price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']); ?></td>
        </tr><?php
      $subedittotal = $subedittotal + $subitemtotal;  
       
    } ?>
    <!-- End Products Listings Block -->

<!-- update quanty -->
    <tr>
    <td valign="top" align='right' colspan="7"><?php echo zen_image_submit('button_update.gif', IMAGE_UPDATE); ?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
    <td valign="top" align='right' colspan="8"> 
    <table border="0" cellspacing="0" cellpadding="2">
<?php  
 /** order total block  
  * $order->totals array (title, text, value, class)
  * ["title"]=>string(6) "Total:"  
  * ["text"]=>string(7) "$137.39" 
  * ["value"]=>string(8) "137.3874" 
  * ["class"]=>string(8) "ot_total" 
  * DEBUG  echo '<div>Updated Order Total: ' . $order_total['code'] . '</div><pre>'; var_dump($order->totals); echo '</pre>';
  */

    for ($i = 0, $n = sizeof($order->totals); $i < $n; $i++) {
      echo '              <tr>' . "\n" .
           '                <td align="right" class="'. str_replace('_', '-', $order->totals[$i]['class']) . '-Text">' . $order->totals[$i]['title'] . '</td>' . "\n" .
           '                <td align="right" class="'. str_replace('_', '-', $order->totals[$i]['class']) . '-Amount">' . $currencies->format($order->totals[$i]['value'], true, $order->info['currency'], $order->info['currency_value']) . '</td>' . "\n" .
           '              </tr>' . "\n";
           
           if($order->totals[$i]['class'] == 'ot_total')  $subreturntotal = $order->totals[$i]['value'];
          
    }
 
   ?>     
            </table></td>
    </tr></form>
    
    <!-- Begin Order Total Block -->
      <tr>
        <td align="right" colspan="9" ><?php echo zen_draw_form('edit_quanty', FILENAME_RETURNS, zen_get_all_get_params(array('action','editsave')) . 'action=editsave'); ?>
            <table align='right' border="0" cellspacing="0" cellpadding="2" width="100%">
            <tr>
            <td>
            <div><?php echo TEXT_RETURN_NOTES; ?></div>
            </td>              
           <td align='right'><h3><?php echo TITLE_EDIT_TOTALS; ?></h3>
            <table border="0" cellspacing="0" cellpadding="2">
            <!-- returns order total block -->

                   <?php //item total ?>
                <tr>
                    <td class="main" align="right"><strong><?php echo TEXT_SUB_TOTALS; ?></strong></td>
                    <td class="main" align="right"><strong><input name="finItemTotal" size="10" value="<?php echo $GLOBALS['currencies']->format($subedittotal); ?>" type="text" id="finItemTotal" readonly /></strong></td>
                   </tr> 
                   <?php //open box ?>  
                    <tr>
                    <td class="main" align="right"><?php echo TEXT_OPEN_FIELD; ?></td>
                    <td class="main" align="right"><input name="finOpen" size="10" value="<?php echo ($finOpen == '' ? '0.00' : $finOpen); ?>" type="text" id="finOpen" onChange="updatesum(this)" /></td>
                  </tr>
                  <?php // tax  
                    $newTax = ($subedittotal * ($ratTax / 100)); ?>
                    <tr>
                     <td align="right" class="main"><?php echo TEXT_TAX_FIELD . round($ratTax,1)   . ' %'; ?> </td>
                    <td class="main" align="right"><input name="finTAX" size="10" value="<?php echo ($finTAX == '' ? '0.00' : $finTAX); ?>" type="text" id="finTAX" onChange="updatesum(this)" /></td>
                    </tr>
                  <?php // restocking fee  ?>
                    <tr>
                     <td align="right" class="main"><?php echo TEXT_RESTOCK_FEE; ?> </td>
                    <td class="main" align="right"><input name="finRestock" size="10" value="<?php echo ($finRestock == '' ? '0.00' : $finRestock); ?>" type="text" id="finRestock" onChange="updatesum(this)" /></td>
                    </tr>
                    <?php // shipping fee ?>
                    <tr>
                    <td align="right"  class="main"><?php echo  TEXT_SHIPPING_FEE; ?> </td>
                    <td class="main" align="right"><input name="finShipping" size="10" value="<?php echo ($finShipping == '' ? '0.00' : $finShipping); ?>" type="text" id="finShipping" onChange="updatesum(this)" /></td>
                    </tr>
                    <?php // cod fee  ?>
                    <tr>
                    <td align="right" class="main"><?php echo TEXT_COD_FEE; ?></td>
                    <td class="main" align="right"><input name="finCOD" size="10" value="<?php echo ($finCOD == '' ? '0.00' : $finCOD); ?>" type="text" id="finCOD" onChange="updatesum(this)" /></td>
                    </tr>
                    <?php  //low order fee  ?>
                    <tr>
                    <td align="right" class="main"><?php echo TEXT_LOW_ORDER_FEE; ?> </td>
                    <td class="main" align="right"><input name="finLowOrder" size="10" value="<?php echo ($finLowOrder == '' ? '0.00' : $finLowOrder); ?>" type="text" id="finLowOrder" onChange="updatesum(this)" /></td>
                    </tr>
                     <?php // total   ?>
                    <tr>
                 <td class="main" align="right"><strong><?php echo TABLE_HEADING_TOTAL; ?></strong></td>
                 <td class="main" align="right"><strong><input readonly name="finSubTotal" size="10" value="<?php echo $GLOBALS['currencies']->format($returns->fields['return_subtotal']); ?>" id="finSubTotal" /></strong></td>
                  </tr>
                </table> 
           </td>
         
            <td align='right'></td>
            
         <td align='right'><h3><?php echo TITLE_RETURN; ?></h3>
            <table border="0" cellspacing="0" cellpadding="2">
            <!-- return total block -->  
            <tr>   
             <td class="main" align="right"></td>
             <td class="main" align="right"><strong><input name="dogOrder" size="10" readonly type="text" value="<?php echo $GLOBALS['currencies']->format($subreturntotal); ?>" id="dogOrder" style="text-align:right;" /></strong> </td>
            </tr>
            <tr>
            <td class="main" align="right"><strong><?php echo TEXT_LESS_FEES; ?></strong></td>
             <td class="main" align="right"><strong><input name="ratTotal" size="10" readonly value="<?php echo $GLOBALS['currencies']->format($returns->fields['return_subtotal']); ?>" id="ratTotal" style="text-align:right;" /></strong></td>
            </tr>
            <tr>
            <td class="main" align="right"><strong><?php echo TEXT_REFUND_TOTAL; ?></strong></td>
            <td class="main" align="right"><strong><input name="ratDog" size="10" readonly type="text"  value="0"  id="ratDog" style="text-align:right;" /> </strong></td>
            </tr>
             <tr>
             <td class="main" align="right"><?php echo zen_draw_hidden_field('oID', $oID); ?></td>
             <td class="main" align="right">&nbsp;&nbsp;&nbsp;<?php echo ($killButton == 'false') ? zen_image_submit('button_save.gif', BUTTON_SAVE_ALT)  : TEXT_CREATE_FIRST;  ?>
         </form>
           </td>           
            </tr>
            <tr>
            <td class="main" align="right"><strong><?php echo TEXT_REFUND_PAST_TOTAL; ?></strong></td>
            <td class="main" align="right"><strong><input name="ratOldDog" size="10" readonly type="text"  value="<?php echo ($ratDog == '' ? '0.00' : $ratDog); ?>"  id="ratOldDog" style="text-align:right;" /> </strong></td>
            </tr>
            </table>
             
            </td>
            </tr>
            </table>
        </td>
      </tr>
    <!-- End Order Total Block -->

                 
</td>
          </tr>
        </table></td>
       
      </tr>
      <tr>
        <td><br /><?php echo zen_draw_separator('pixel_black.gif', '100%', '2'); ?></td>
      </tr>
      <tr>
        <td class="pageHeading"><?php echo HEADING_TITLE_RETURN_DETAILS; ?></td>
      </tr>
      <tr>
        <td class="main"> 

<!-- Order Cancels -->
<?php   
$orders_cancel = $db->Execute("select return_id, orders_id, customers_id, orders_status_id, date_added, customers_name, customers_email_address, comments, products_id, products_name, products_price, products_quantity, rma_number, action, rma_type
                                    from " . TABLE_ORDER_RETURN_MANAGER . "
                                    where orders_id = '" . zen_db_input($oID) . "'
                                    and rma_type = 1
                                    order by date_added
                                    "); 

if ($orders_cancel->RecordCount() > 0) {
?>
        <table border="1" cellspacing="0" cellpadding="5">
          <tr>
          <td  class="pageHeading" colspan="10">You have Cancels in this order!</td>
          </tr>
          <tr>
            <td class="smallText" align="center"><strong><?php echo TABLE_HEADING_RETURN_ID; ?></strong></td>
            <td class="smallText" align="center"><strong><?php echo TABLE_HEADING_DATE_ADDED; ?></strong></td>
            <td class="smallText" align="center"><strong><?php echo TABLE_HEADING_RMA_NUMBER; ?></strong></td>
            <td class="smallText" align="center"><strong><?php echo TABLE_HEADING_STATUS; ?></strong></td>
            <td class="smallText" align="center"><strong><?php echo TABLE_HEADING_ACTION_REQ; ?></strong></td>
            <td class="smallText" align="center"><strong><?php echo TABLE_HEADING_CUSTOMER_COMMENTS; ?></strong></td>
            <td class="smallText" align="center"><strong><?php echo TABLE_HEADING_QUANTITY; ?></strong></td>  
          </tr>
<?php

      while (!$orders_cancel->EOF) {
        echo '          <tr>' . "\n" .
             '            <td class="smallText" align="center">' . $orders_cancel->fields['return_id'] . '</td>' . "\n" .
             '            <td class="smallText" align="center">' . zen_datetime_short($orders_cancel->fields['date_added']) . '</td>' . "\n" .
             '            <td class="smallText" align="center">' . $orders_cancel->fields['rma_number'] . '</td>' . "\n" .
             '            <td class="smallText">' . $orders_status_array[$orders_cancel->fields['orders_status_id']] . '</td>' . "\n" .
             '            <td class="smallText">' . $orders_cancel->fields['action'] . '</td>' . "\n" .
             '            <td class="smallText">' . nl2br(zen_db_output($orders_cancel->fields['comments'])) . '&nbsp;</td>' . "\n" .
             '            <td class="smallText">' . $orders_cancel->fields['products_quantity'] . '</td>' . "\n" .                   
             '          </tr>' . "\n";
        $orders_cancel->MoveNext();
      }                                    
    } else {

        echo '          <tr>' . "\n" .
             '            <td  class="pageHeading" colspan="5">' . TEXT_NO_RETURNS . '<br /></td>' . "\n" .
             '          </tr>' . "\n";
    }
?>
        </table>
        
 </td>
</tr>
 <tr>
<td class="main">        
<!-- Order Returns -->
<?php

$sizeOrder = sizeof($order->products);
$returnQty = $db->Execute("select rma_number, sum(products_quantity) as total from " . TABLE_ORDER_RETURN_MANAGER . " where orders_id = '" . (int)$orderID . "' and products_id = '" . (int)$order->products[$i]['id'] . "'");

$QTYleft = 0;
  if($sizeOrder == $returnQty->fields['total']) {
   $QTYleft = $sizeOrder - $returnQty->fields['total'];

   }
                                    
 $orders_returns = $db->Execute("select return_id, orders_id, customers_id, orders_status_id, date_added, customers_name, customers_email_address, comments, products_id, products_name, products_price, products_quantity, rma_number, action, rma_type
                                    from " . TABLE_ORDER_RETURN_MANAGER . "
                                    where orders_id = '" . zen_db_input($oID) . "'
                                    and rma_type = 2
                                    order by date_added");

if ($orders_returns->RecordCount() > 0) {
?>
        <table border="1" cellspacing="0" cellpadding="5">
          <tr>
          <td  class="pageHeading" colspan="10">You have Returns in this order!</td>
          </tr>
          <tr>
            <td class="smallText" align="center"><strong><?php echo TABLE_HEADING_RETURN_ID; ?></strong></td>
            <td class="smallText" align="center"><strong><?php echo TABLE_HEADING_DATE_ADDED; ?></strong></td>
            <td class="smallText" align="center"><strong><?php echo TABLE_HEADING_RMA_NUMBER; ?></strong></td>
            <td class="smallText" align="center"><strong><?php echo TABLE_HEADING_STATUS; ?></strong></td>
            <td class="smallText" align="center"><strong><?php echo TABLE_HEADING_ACTION_REQ; ?></strong></td>
            <td class="smallText" align="center"><strong><?php echo TABLE_HEADING_CUSTOMER_COMMENTS; ?></strong></td>
            <td class="smallText" align="center"><strong><?php echo TABLE_HEADING_QUANTITY; ?></strong></td>  
          </tr>
<?php

      while (!$orders_returns->EOF) {
        echo '          <tr>' . "\n" .
             '            <td class="smallText" align="center">' . $orders_returns->fields['return_id'] . '</td>' . "\n" .
             '            <td class="smallText" align="center">' . zen_datetime_short($orders_returns->fields['date_added']) . '</td>' . "\n" .
             '            <td class="smallText" align="center">' . $orders_returns->fields['rma_number'] . '</td>' . "\n" .
             '            <td class="smallText">' . $orders_status_array[$orders_returns->fields['orders_status_id']] . '</td>' . "\n" .
             '            <td class="smallText">' . $orders_returns->fields['action'] . '</td>' . "\n" .
             '            <td class="smallText">' . nl2br(zen_db_output($orders_returns->fields['comments'])) . '&nbsp;</td>' . "\n" .
             '            <td class="smallText">' . $orders_returns->fields['products_quantity'] . '</td>' . "\n" .        
             '          </tr>' . "\n";
       $returnTotal = $subtotalr + $returnTotal;
        $orders_returns->MoveNext();
      }                                    
    } else {
        echo '          <tr>' . "\n" .
             '            <td  class="pageHeading" colspan="5">' . TEXT_NO_RETURNS . '<br /></td>' . "\n" .
             '          </tr>' . "\n";
    }
?>
        </table>

        <br />
 
        </td>
      </tr>
    
    <tr>
         <td colspan="0" align="left" class="noprint"><?php echo ' <a href="' . zen_href_link(FILENAME_RETURNS, '&action=sendRMA&oID=' . (string)$oID, 'NONSSL') . '">' . zen_image_button('button_send_mail.gif', 'Email RMA') . '</a>&nbsp;&nbsp;&nbsp; <a href="' . zen_href_link(FILENAME_ORDERS, zen_get_all_get_params(array('action'))) . '">' . zen_image_button('button_orders.gif', IMAGE_ORDERS) . '</a>'; ?></td>
      </tr> 
<?php
// check if order has open gv
        $gv_check = $db->Execute("select order_id, unique_id
                                  from " . TABLE_COUPON_GV_QUEUE ."
                                  where order_id = '" . $_GET['oID'] . "' and release_flag='N' limit 1");
        if ($gv_check->RecordCount() > 0) {
          $goto_gv = '<a href="' . zen_href_link(FILENAME_GV_QUEUE, 'order=' . $_GET['oID']) . '">' . zen_image_button('button_gift_queue.gif',IMAGE_GIFT_QUEUE) . '</a>';
          echo '      <tr><td align="right"><table width="225"><tr>';
          echo '        <td align="center">';
          echo $goto_gv . '&nbsp;&nbsp;';
          echo '        </td>';
          echo '      </tr></table></td></tr>';
        }
?>
<?php
  } else { /********************** bof returns list ******************************************/
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="smallText"><?php echo TEXT_LEGEND . ' ' . zen_image(DIR_WS_IMAGES . 'icon_status_red.gif', TEXT_BILLING_SHIPPING_MISMATCH, 10, 10) . ' ' . TEXT_BILLING_SHIPPING_MISMATCH; ?>
          </td>
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
<?php
// Sort Listing
          switch ($_GET['list_order']) {
              case "id-asc":
              $disp_order = "c.customers_id";
              break;
              case "firstname":
              $disp_order = "c.customers_firstname";
              break;
              case "firstname-desc":
              $disp_order = "c.customers_firstname DESC";
              break;
              case "lastname":
              $disp_order = "c.customers_lastname, c.customers_firstname";
              break;
              case "lastname-desc":
              $disp_order = "c.customers_lastname DESC, c.customers_firstname";
              break;
              case "company":
              $disp_order = "a.entry_company";
              break;
              case "company-desc":
              $disp_order = "a.entry_company DESC";
              break;
              default:
              $disp_order = "c.customers_id DESC";
          }
?>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_ORDERS_ID; ?></td>
                <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_RMA; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CUSTOMERS; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_TOTAL_ITEMS_ORDERED; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_TOTAL_ITEMS; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_DATE_PURCHASED; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_CUSTOMER_COMMENTS; ?></td>
                <td class="dataTableHeadingContent noprint" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>

<?php

    $new_fields = ", o.customers_company, o.customers_email_address, o.customers_street_address, o.delivery_company, o.delivery_name, o.delivery_street_address, o.billing_company, o.billing_name, o.billing_street_address, o.payment_module_code, o.shipping_module_code, o.ip_address";

    $returns = ", orh.return_id, orh.orders_id, orh.customers_id, orh.orders_status_id, orh.date_added, orh.customers_name, orh.customers_email_address, orh.comments, orh.products_id, orh.products_name, orh.products_price, orh.products_quantity, orh.rma_number, orh.action";

    $orders_query_raw = "select o.orders_id, o.customers_id, o.customers_name, o.payment_method, o.shipping_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, s.orders_status_name, ot.text as order_total" .
$new_fields . $returns . "

              from (" . TABLE_ORDERS . " o )
              left join " . TABLE_ORDERS_STATUS . " s on (o.orders_status = s.orders_status_id and s.language_id = " . (int)$_SESSION['languages_id'] . ")
              left join " . TABLE_ORDER_RETURN_MANAGER . " orh on (o.orders_id = orh.orders_id)
              left join " . TABLE_ORDERS_TOTAL . " ot on (o.orders_id = ot.orders_id and ot.class = 'ot_total') ";



      //$status = 4;
      $orders_query_raw .= " WHERE o.orders_id = orh.orders_id";
      $orders_query_raw .= " GROUP by orh.orders_id ASC";  //orh.return_id orh.rma_number

// Split Page
// reset page when page is unknown
if (($_GET['page'] == '' or $_GET['page'] <= 1) and $_GET['oID'] != '') {
  $check_page = $db->Execute($orders_query_raw);
  $check_count=1;
  if ($check_page->RecordCount() > MAX_DISPLAY_SEARCH_RESULTS_ORDERS) {
    while (!$check_page->EOF) {
      if ($check_page->fields['orders_id'] == $_GET['oID']) {
        break;
      }
      $check_count++;
      $check_page->MoveNext();
    }
    $_GET['page'] = round((($check_count/MAX_DISPLAY_SEARCH_RESULTS_ORDERS)+(fmod_round($check_count,MAX_DISPLAY_SEARCH_RESULTS_ORDERS) !=0 ? .5 : 0)),0);
  } else {
    $_GET['page'] = 1;
  }
}

//    $orders_query_numrows = '';
    $orders_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS_ORDERS, $orders_query_raw, $orders_query_numrows);
    $orders = $db->Execute($orders_query_raw);
    
    while (!$orders->EOF) {
    if ((!isset($_GET['rID']) || (isset($_GET['rID']) && ($_GET['rID'] == $orders->fields['return_id']))) && !isset($oInfo)) {
        $oInfo = new objectInfo($orders->fields);
     }

     if (isset($oInfo) && is_object($oInfo) && ($orders->fields['return_id'] == $oInfo->return_id)) {
        echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link(FILENAME_RETURNS, zen_get_all_get_params(array('rID', 'action')) . 'rID=' . $oInfo->return_id . '&action=edit', 'NONSSL') . '\'">' . "\n";
      } else {
        echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link(FILENAME_RETURNS, zen_get_all_get_params(array('rID')) . 'rID=' . $orders->fields['return_id'], 'NONSSL') . '\'">' . "\n";
      }
      
?>
                <td class="dataTableContent" align="center"><?php echo $orders->fields['orders_id']; ?></td>
                <td class="dataTableContent" align="left" ><?php echo $orders->fields['rma_number']; ?></td>
                
                <td class="dataTableContent"><?php echo '<a href="' . zen_href_link(FILENAME_CUSTOMERS, 'cID=' . $orders->fields['customers_id'], 'NONSSL') . '">' . zen_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW . ' ' . TABLE_HEADING_CUSTOMERS) . '</a>&nbsp;' . $orders->fields['customers_name'] . ($orders->fields['customers_company'] != '' ? '<br />' . $orders->fields['customers_company'] : ''); ?></td>

<?php $orderQty = $db->Execute("select orders_id, sum(products_quantity) as total from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . (int)$orders->fields['orders_id'] . "'"); 
$sizeoforder = $orderQty->fields['total']; ?> 
<td class="dataTableContent" align="center"><?php echo (string)$sizeoforder; ?></td>

<?php $returnQty = $db->Execute("select orders_id, sum(products_quantity) as total from " . TABLE_ORDER_RETURN_MANAGER . " where orders_id = '" . (int)$orders->fields['orders_id'] . "'"); 
$sizeofitems = $returnQty->fields['total']; ?>                
                <td class="dataTableContent" align="center"><?php echo (string)$sizeofitems; //total number of items on this RMA ?></td> 
                <?php //TODO: change date based on return/cancel requested ?>
                <td class="dataTableContent" align="center"><?php echo zen_datetime_short($orders->fields['date_added']); ?></td>
              <?php //TODO: should only show returns/cancels and no others. ?>  
                <td class="dataTableContent" align="right"><?php echo ($orders->fields['orders_status_name'] != '' ? $orders->fields['orders_status_name'] : TEXT_INVALID_ORDER_STATUS); ?></td>
                <?php //TODO: change to show return/cancel comments only ?>
                <td class="dataTableContent" align="center"><?php echo (zen_get_returns_comments($orders->fields['orders_id'], $orders->fields['return_id']) == '' ? '' : zen_image(DIR_WS_IMAGES . 'icon_yellow_on.gif', TEXT_COMMENTS_YES, 16, 16)); ?></td>

                <td class="dataTableContent noprint" align="right">
                <?php echo '<a href="' . zen_href_link(FILENAME_RETURNS, zen_get_all_get_params(array('rID', 'action')) . 'rID=' . $orders->fields['return_id'] . '&action=edit', 'NONSSL') . '">' . zen_image(DIR_WS_IMAGES . 'icon_edit.gif', ICON_EDIT) . '</a>'; //edit button ?>
                <?php if (isset($oInfo) && is_object($oInfo) && ($orders->fields['return_id'] == $oInfo->return_id)) { echo zen_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . zen_href_link(FILENAME_RETURNS, zen_get_all_get_params(array('rID')) . 'rID=' . $orders->fields['return_id'], 'NONSSL') . '">' . zen_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } //selected button ?>&nbsp;</td>
              </tr>
<?php
      $orders->MoveNext();
    }
?>
              <tr>
                <td colspan="5"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $orders_split->display_count($orders_query_numrows, MAX_DISPLAY_SEARCH_RESULTS_ORDERS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ORDERS); ?></td>
                    <td class="smallText" align="right"><?php echo $orders_split->display_links($orders_query_numrows, MAX_DISPLAY_SEARCH_RESULTS_ORDERS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], zen_get_all_get_params(array('page', 'oID', 'action'))); ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
<?php  
/**************************  bof sidebox thingy *****************************/
  $heading = array();
  $contents = array();

      if (isset($oInfo) && is_object($oInfo)) {
        $heading[] = array('text' => '<strong>[' . $oInfo->orders_id . ']&nbsp;&nbsp;' . $oInfo->customers_email_address . '</strong>');
        
        $contents[] = array('align' => 'center', 'text' => '<a href="' . zen_href_link(FILENAME_RETURNS, zen_get_all_get_params(array('rID', 'action')) . 'rID=' . $oInfo->return_id . '&action=edit', 'NONSSL') . '">' . zen_image_button('button_details.gif', IMAGE_EDIT) . '</a> ');
        $zco_notifier->notify('NOTIFY_ADMIN_ORDERS_MENU_BUTTONS', $oInfo, $contents);

        $contents[] = array('text' => '<br />' . TEXT_DATE_ORDER_CREATED . ' ' . zen_date_short($oInfo->date_purchased));
        $contents[] = array('text' => TEXT_DATE_RETURN_CREATED . ' ' . zen_date_short($oInfo->date_added));

$datetime1 = zen_date_short($oInfo->date_added);
$now = date('m/d/Y');
$days_left =  zen_returns_date_diff($datetime1, $now);

        $contents[] = array('text' => TEXT_INFO_DAYS_AFTER . ' <b>' . (string)$days_left . ' Days</b>');
$days_rma = RMA_GRACE_PERIOD  -  $days_left;
        $contents[] = array('text' => TEXT_INFO_DAYS_RMA_LEFT . ' <b>' . (string)$days_rma . ' Days</b>');     
         
        $contents[] = array('text' => '<br />' . TEXT_INFO_PAYMENT_METHOD . ' '  . $oInfo->payment_method);
        $contents[] = array('text' => ENTRY_SHIPPING . ' '  . $oInfo->shipping_method);

        // indicate if comments exist
        $orders_history_query = $db->Execute("select orders_status_id, date_added, customer_notified, comments from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_id = '" . $oInfo->orders_id . "' and comments !='" . "'" );

        if ($orders_history_query->RecordCount() > 0) {
          $contents[] = array('align' => 'left', 'text' => '<br />' . TABLE_HEADING_COMMENTS);
        }

        $contents[] = array('text' => '<br />' . zen_image(DIR_WS_IMAGES . 'pixel_black.gif','','100%','3')); //items in full order
        
        $order = new order($oInfo->orders_id);
        $contents[] = array('text' => TABLE_HEADING_PRODUCTS . ': ' . sizeof($order->products) );
        for ($i=0; $i<sizeof($order->products); $i++) {
          $contents[] = array('text' => $order->products[$i]['qty'] . '&nbsp;x&nbsp;' . $order->products[$i]['name']);

          if (sizeof($order->products[$i]['attributes']) > 0) {
            for ($j=0; $j<sizeof($order->products[$i]['attributes']); $j++) {
              $contents[] = array('text' => '&nbsp;<i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . nl2br(zen_output_string_protected($order->products[$i]['attributes'][$j]['value'])) . '</i></nobr>' );
            }
          }
       /*   if ($i > MAX_DISPLAY_RESULTS_ORDERS_DETAILS_LISTING and MAX_DISPLAY_RESULTS_ORDERS_DETAILS_LISTING != 0) {
            $contents[] = array('align' => 'left', 'text' => TEXT_MORE);
            break;
          } */
        }

        $contents[] = array('text' => '<br />' . zen_image(DIR_WS_IMAGES . 'pixel_black.gif','','100%','3')); //item in returns/cancel

$returns_count = $db->Execute("select products_name, products_price, products_quantity, return_id, orders_id, action, rma_number from " . TABLE_ORDER_RETURN_MANAGER . " where orders_id = '" . $oInfo->orders_id . "'") ;


     
        while (!$returns_count->EOF) {      
         if ($returns_count->fields['return_id'] != '' && $returns_count->fields['orders_id'] == $oInfo->orders_id) {
        $contents[] = array('text' => $returns_count->fields['products_quantity'] . '&nbsp;x&nbsp;' . $returns_count->fields['products_name']);
        $contents[] = array('text' => 'Product Price:&nbsp;' . $returns_count->fields['products_price']);
        $contents[] = array('text' => 'RMA#: <b>' . $returns_count->fields['rma_number'] . '</b>' . ' Requested Action: <b>' . $returns_count->fields['action'] . '</b>');
        $contents[] = array('text' => '<br />' . zen_image(DIR_WS_IMAGES . 'pixel_black.gif','','100%','1'));
        
        } 
        
        $returns_count->MoveNext();
    }
        
       
      }

  
  
  $zco_notifier->notify('NOTIFY_ADMIN_ORDERS_MENU_BUTTONS_END', (isset($oInfo) ? $oInfo : array()), $contents);
//display the sidebox
  if ( (zen_not_null($heading)) && (zen_not_null($contents)) ) {
    echo '            <td class="noprint" width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
?>
          </tr>
        </table></td>
      </tr>
<?php
  } //eof returns list
?>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->
<!-- footer //-->
<?php
    require(DIR_WS_INCLUDES . 'footer.php');
?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>

