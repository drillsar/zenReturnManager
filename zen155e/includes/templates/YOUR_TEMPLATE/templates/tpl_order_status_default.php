<?php
/**
 * Oreder Status
 *
 * Displays information related to a single specific order
 *
 * @package templateSystem
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: Integrated COWAA v1.0
 * @version $Id: tpl_order_status.php 1.0.1 11/27/2017 davewest $
 */
 
/**
 * Designed for and included with COWAA combined Order Status with cancellations and returns 
 * can be used with or without COWAA - Idea! Buy and Return are the two layers of a good shop
 * With cancellations and order status as part of a on-line shop
 * Combine all four with a admin back-end to compete with the big box stores.
 */
 
 //use the following difines if you want to turn off payment, products, shipping .. not sure why one would?
$KILL_PAYMENT = false;   //false = on true = off
$KILL_SHIPPING = false;  //false = on true = off
$KILL_PRODUCTS = false;  //false = on true = off
 
?> 
<div class="centerColumn" id="accountHistInfo">

<h1 id="orderHistoryHeading"><?php echo HEADING_TITLE; ?></h1>
<br />
<?php if ($messageStack->size('order_status') > 0) echo $messageStack->output('order_status'); ?>

<!-- bof grid-r -->         
<div class="ui-grid-r ui-responsive">
<!-- box ra -->
<div class="ui-block-ra"><div class="ui-body ui-body-d">

<?php echo zen_draw_form('order_status', zen_href_link(FILENAME_ORDER_STATUS, 'action=status', 'SSL'), 'post'); ?>
<legend><?php echo SUB_HEADING_TITLE; ?></legend>
<div class="margin-bottom-sm"><?php echo SUB_HEADING; ?></div>

<div class="input-group margin-bottom-sm">
<label class="inputLabel" for="order_id"><?php echo ENTRY_ORDER_NUMBER; ?></label><br />
<?php echo zen_draw_input_field('order_id', $orderID, ' class="form-ostatus required" id="order_id" required', 'number'); ?> 
</div>

<div class="input-group margin-bottom-sm">
<label class="inputLabel" for="email-address"><?php echo ENTRY_EMAIL; ?></label><br />
<?php echo zen_draw_input_field('email_address', $email_address, ' class="form-ostatus required" id="email_address"  placeholder="* (eg. dave@addme.com)" required ', 'email'); ?> 
</div>

<?php echo zen_draw_input_field('should_be_empty', '', ' size="40" id="CUAS" style="visibility:hidden; display:none;" autocomplete="off"'); ?>

<div class="buttonRow"><?php echo zen_image_submit(BUTTON_IMAGE_CONTINUE, BUTTON_FIND_ORDER_ALT); ?></div>
</form>
<br />
<div class="lookuporder"><?php echo TEXT_LOOKUP_INSTRUCTIONS; ?></div>

 </div></div>

<!-- box rb -->
<div class="ui-block-rb"><div class="ui-body ui-body-d">

<?php if($order) { ?>

<!-- box b -->
<div class=""><div class="ui-body ui-body-d">
<!-- title line -->
<h2>
<div class="boxcontainer ">
<aside><?php echo $email_address; ?></aside>
<aside><?php echo sprintf(HEADING_ORDER_NUMBER, $_POST['order_id']); ?></aside>
<aside><?php echo TITLE_LINE_ITEMS . sizeof($order->products); ?></aside>
</div>
</h2>

<div class="ui-grid-b ui-responsive">

<!-- dd -->
<div class="ui-block-dd"><div class="ui-body ui-body-d">
<div class="orderHeading"><h2><?php echo TEXT_ORDER_DATE; ?></h2></div>
<br />
<h2><?php echo zen_date_long($order->info['date_purchased']); ?></h2>
</div></div>

<!-- ee -->
<div class="ui-block-ee"><div class="ui-body ui-body-d">
<div class="orderHeading"><h2><?php echo TEXT_ORDER_NUMBER; ?></h2></div>
<br />
<h2><?php echo sprintf($_POST['order_id']); ?></h2>
</div></div>

<!-- ff -->
<div class="ui-block-ff"><div class="ui-body ui-body-d">
<div class="orderHeading"><h2><?php echo TEXT_ORDER_TOTAL; ?></h2></div>
<br />
  <?php $i=2; ?>
<h2><?php echo TEXT_ORDER_SUB_TOTAL . $order->totals[0]['text'] . '<br />' . TEXT_TOTAL . $order->totals[2]['text']; ?></h2>
</div></div>

<!-- gg -->
<div class="ui-block-gg"><div class="ui-body ui-body-d">
<div class="orderHeading"><h2><?php echo TABLE_HEADING_STATUS_ORDER_STATUS; ?></h2></div>
<br />
<h2><?php echo  $order_status; ?></h2>
</div></div>

<?php
// check order status expired date
$compareDate = ORDER_STATUS_COMPARE;
$getDate = ("select date_added from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_id = '" . $orderID . "' and orders_status_id = '" . $compareDate . "'");
$comparing = $db->Execute($getDate);
$currentDate = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")-RETURN_GRACE_PERIOD, date("Y") ));

if ($comparing->fields['date_added'] <= $currentDate && zen_not_null($comparing->fields['date_added'])) {
echo '<br class="clearBoth" /><br />';
echo '<h2>' . TEXT_RETURN_GRACE_PERIOD_EXPIRED . '</h2>';

$KILL_PAYMENT = true;  
$KILL_SHIPPING = true;  
$KILL_PRODUCTS = true;

} ?>

<?php if($KILL_PRODUCTS == false) { ?>
<?php if ($killreturns == false) {  
echo zen_draw_form('returns_status', zen_href_link(FILENAME_RETURNS, '', 'SSL'), 'post', ' id="ninja"'); 
 } elseif ($docancel == true) {  
echo zen_draw_form('returns_status', zen_href_link(FILENAME_CANCELME, '', 'SSL'), 'post', ' id="ninja"'); 
 }   ?> 
<?php for ($i=0, $n=sizeof($order->products); $i<$n; $i++) { ?>
<!-- items -->
<div class="ui-block-dd"><div class="ui-body ui-body-d">
<br class="clearBoth" />
<?php if($order->products[$i]['product_is_free'] != 1) {

$productrma = $db->Execute("select orh.orders_status_id, orh.rma_number, orh.products_quantity, orh.products_id, os.orders_status_name from  " . TABLE_ORDER_RETURN_MANAGER . " orh  left join " . TABLE_ORDERS_STATUS . " os ON (orh.orders_status_id = os.orders_status_id)  where orh.products_id = '" . (int)$order->products[$i]['id'] . "' and orh.orders_id = '" . (int)$orderID . "'");


$return_query = $db->Execute("select rma_number, sum(products_quantity) as total from " . TABLE_ORDER_RETURN_MANAGER . " where orders_id = '" . (int)$orderID . "' and products_id = '" . (int)$order->products[$i]['id'] . "'");

$QTYleft = 0;
  if($order->products[$i]['qty'] != $return_query->fields['total']) {
   $QTYleft = $order->products[$i]['qty'] - $return_query->fields['total'];
   if($QTYleft < 0) $QTYleft = 0;
  }else{
  $QTYleft = 0;
   }

$productis = $db->Execute("select products_type, products_virtual from " . TABLE_PRODUCTS . " where products_id = " . (int)$order->products[$i]['id']);
 
if (($productrma->fields['products_id'] != $order->products[$i]['id']) && ($productis->fields['products_virtual'] == 0)) {
 echo zen_draw_checkbox_field('notify[]', $order->products[$i]['id'], false, ' class="checky" id="notify-' . $i . '"') . zen_draw_input_field('Prod_qty[]', $order->products[$i]['qty'], ' class="input-number"  min="1" max="' . $order->products[$i]['qty'] . '"', 'number');
 } elseif (($productis->fields['products_virtual'] == 1) && ($order_status != 'Pending')) {
 echo '<h3><a href="' . zen_href_link(FILENAME_CONTACT_US, '', 'SSL') . '">' . BOX_INFORMATION_CONTACT . '</a></h3>';
 } elseif ($QTYleft >= 1) {
 echo zen_draw_checkbox_field('notify[]', $order->products[$i]['id'], false, ' class="checky" id="notify-' . $i . '"') . zen_draw_input_field('Prod_qty[]', $QTYleft, ' class="input-number"  min="1" max="' . $QTYleft . '"', 'number');
 } else {
 echo '<h3>' . TEXT_COMPLETED . '</h3>';
 }

} ?>
<br />

<br class="clearBoth" />
</div></div>
<div class="ui-block-ee"><div class="ui-body ui-body-d">
<br class="clearBoth" />
<?php  $productimage = $db->Execute("select products_image from  " . TABLE_PRODUCTS . "  where products_id = '" . (int)$order->products[$i]['id'] . "'"); ?>
<div><?php echo zen_image('images/' . $productimage->fields['products_image'], addslashes($order->products[$i]['name']), 50, 50)  ;?></div>
</div></div>
<div class="ui-block-ff"><div class="ui-body ui-body-d">
<br class="clearBoth" /><br />
<div><?php echo '<a href="' . zen_href_link(zen_get_info_page($order->products[$i]['id']), 'products_id=' . (string)$order->products[$i]['id']) . '">' . $order->products[$i]['name'] . '</a><br />' . $order->products[$i]['qty'] . ' in Order.' ;?></div>
</div></div>
<div class="ui-block-gg"><div class="ui-body ui-body-d">
<br class="clearBoth" />
<div id="productReviewLink" class="buttonRow"><?php echo '<a href="' . zen_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, '&products_id='.$order->products[$i]['id'] . '&reviews_id=' .$order->products[$i]['id']) . '">' . zen_image_button(BUTTON_IMAGE_WRITE_REVIEW, BUTTON_WRITE_REVIEW_ALT) . '</a>'; ?></div>
<br />
</div></div>
<?php }  //eof for ?>
<br class="clearBoth" />

<?php  echo zen_draw_hidden_field('order_id', $orderID); ?> 
<?php  echo zen_draw_hidden_field('email_address', $email_address); ?> 
<?php  echo zen_draw_hidden_field('coID', $custorder_id); ?> 

<?php if ($killreturns == false) { ?> 
<div id="confirmButton" class="buttonrow back">
<?php echo zen_image_submit(BUTTON_IMAGE_CONTINUE, BUTTON_RETURN_ALT, ' id="postme"'); ?>
</div> 
<?php } elseif ($docancel == true) { ?> 
<div id="confirmButton" class="buttonrow back">
<?php echo zen_image_submit(BUTTON_IMAGE_CONTINUE, BUTTON_CANCEL_ALT, ' id="postme"'); ?>
</div> 
<?php } ?> 
<!--<div id="confirmButton" class="buttonrow back"><input id="postme" class="cssButton submit_button button  button_continue" value="Re-Order &#xf25a;" type="submit"></div>-->
<br /><br />
</form>

<script type="text/javascript">
$(document).ready(function(){
                $("#postme").attr("disabled","disabled");
                $("#postme").css({opacity:0,cursor:'default'});
                    $(".checky").click(function(){
                        if($(".checky").is(":checked")){
                         $("#postme").removeAttr("disabled"); 
                         $("#postme").css({opacity:1,cursor:'pointer'}); 
                         }
                        else{
                            $("#postme").attr("disabled","disabled");
                            $("#postme").css({opacity:0,cursor:'default'});
                            }
                    });
                })
</script>
<br class="clearBoth" />

</div>

 <?php
  /**
   * Used to display any downloads associated with the cutomers account
   */
    if (DOWNLOAD_ENABLED == 'true') require($template->get_template_dir('tpl_modules_downloads.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_downloads.php');
  ?>
<?php }  ?>

<div class="ui-grid-b ui-responsive">

<div class="ui-block-dd"><div class="ui-body ui-body-d">
  <?php if($KILL_SHIPPING == false) { ?>
  <div id="myAccountShipInfo" class=" ">
  <?php if (zen_not_null($order->info['shipping_method'])) { ?>
  <h4><?php echo HEADING_SHIPPING_METHOD; ?></h4>
  <div><?php echo $order->info['shipping_method']; ?></div>
  <?php } else {  ?>
  <div><?php echo TEXT_FREE_SHIPPING; ?></div>
  <?php
      }
  ?>
  </div>
<?php }  ?>
</div></div>

<div class="ui-block-ee"><div class="ui-body ui-body-d">
<?php if($KILL_PAYMENT == false) { ?>
  <div id="myAccountPaymentInfo" class="">
  <h4><?php echo HEADING_PAYMENT_METHOD; ?></h4>
  <div><?php echo $order->info['payment_method']; ?></div>
  </div>
  <?php } ?>
</div></div>

<div class="ui-block-ff"><div class="ui-body ui-body-d">
<br class="clearBoth" />
<?php if ((DISPLAY_COMMENTS == 'true') && ($statuses->fields['comments'] != '')) { 
echo '<div class="lookuporder"><b>' . $statuses->fields['comments'] . '</b></div>';
 } elseif (DISPLAY_ADD_TESTIMONIAL_LINK == 'true') { //from the testimonial mod if installed then it works
 echo '<h2>' . TEXT_TESTIMONIALS . '</h2>';
 }?>
</div></div>

<div class="ui-block-gg"><div class="ui-body ui-body-d">
<?php  if (DISPLAY_ADD_TESTIMONIAL_LINK == 'true') { //from the testimonial mod if installed then it works
  echo '<div class="buttonRow"><br /><a href="' . zen_href_link(FILENAME_TESTIMONIALS_ADD, '', 'SSL') . '">' . zen_image_button(BUTTON_IMAGE_TESTIMONIALS, BUTTON_TESTIMONIALS_ADD_ALT) . '</a></div>';
 } ?>
</div></div>
</div>
<h3><?php echo TEXT_RETURN_CANCEL_INTRO; ?></h3>
</div></div>

<?php }else{  //eof order status ?>

<?php if(COWOA_NOACCOUNT_ONLY != "true") { ?>
<div class="ui-grid-r ui-responsive">
<!-- bof login box -->
<div class="ui-block-ma"><div class="ui-body ui-body-d">
<fieldset >
<div class="input-group margin-bottom-sm">
<?php  if (DISPLAY_ADD_TESTIMONIAL_LINK == 'true') {
  echo '<h1>' . TEXT_AD_TESTIMONIALS . '</h1><br />';
  echo '<div class="buttonRow"><br /><a href="' . zen_href_link(FILENAME_TESTIMONIALS_ADD, '', 'SSL') . '">' . zen_image_button(BUTTON_IMAGE_TESTIMONIALS, BUTTON_TESTIMONIALS_ADD_ALT) . '</a></div>';
 }else{  ?>
<h1><?php echo TEXT_AD_MAIN_PAGE; ?></h1>
<?php } ?>
</div>
</fieldset>
</div></div>

<div class="ui-block-mb"><div class="ui-body ui-body-d">

<h2 class="pasduck"><?php echo TEXT_AD_BOX_ORDERS; ?></h2>
<div class="pasduck"><?php echo zen_image(DIR_WS_TEMPLATE . 'images/TrackOrder.png', 'Track Order', '50%'); //replace this image with yours ?></div>  

</div></div>
</div>
<?php }else{ ?>
<fieldset>
<h2 class="pasduck"><?php echo TEXT_AD_BOX_ORDERS; ?></h2>
<div class="pasduck"><?php echo zen_image(DIR_WS_TEMPLATE . 'images/TrackOrder.png', 'Track Order', '50%'); //replace this image with yours ?></div>  
</fieldset>
<?php } 
 } ?>
</div></div>
</div>

</div>
<script type="text/javascript">
$(document).ready(function () {
        $.noop()
	$("#showHide").click(function () {
  		
	var el = $(this);
    if ($("#password").attr("type")=="text") {
        el.data('text-original', el.text());
        el.text(el.data('text-swap'));
        $("#password").attr("type", "password");
    } else {
        el.text(el.data('text-original'));
         $("#password").attr("type", "text");
    }
		
	});
});

    
</script>
