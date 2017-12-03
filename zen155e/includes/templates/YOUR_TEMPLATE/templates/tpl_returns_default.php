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
 * @version $Id: tpl_order_status.php 1.0.1 11/27/2017 davewest $
 */
?>

<div class="centerColumn" id="returnAuthorization">
<h1 id="returnAuthorizationHeading"><?php echo HEADING_TITLE; ?></h1>

<?php if ($messageStack->size('returns') > 0) echo $messageStack->output('returns'); ?>

<?php echo zen_draw_form('returns', zen_href_link(FILENAME_RETURNS, 'action=send', 'SSL'),'post','id="ninja"'); ?>

<?php if (isset($_GET['action']) && ($_GET['action'] == 'success')) { ?>

<br class="clearBoth" />

<div class="mainContent success">
<?php 
$dogorder = $_SESSION['retunaddress'];
$returnRMA = $_SESSION['retunrma'] ;
$order_number = $_SESSION['ordernum']; 
$proqty = $_SESSION['returnQTY'];

 echo '<div id="returnSuccess">' . TEXT_SUCCESS . TEXT_SUCCESS_RMA_REFERENCE . TEXT_SUCCESS_POPUP . TEXT_SUCCESS_RMA_REFERENCE1 . '<br /><br /><a href="javascript:popupWindow(\'' . zen_href_link(FILENAME_POPUP_RETURNS, 'action=success') . '\')">' . zen_image_button(BUTTON_IMAGE_SUBMIT, TEXT_SUCCESS_POPUP) . ' </a></div>';
 echo '<br /><br />';
 echo '<div id="returnAddressWrapper">' . TEXT_SUCCESS_RMA_ID . '<span id="returnRMA">' . $returnRMA . '</span>' . '<div id="returnAddress">' . TEXT_SUCCESS_RMA_RETURN_ADDRESS . '</div>'; 
 echo '<br /><br />';
     if (RETURN_STORE_NAME_ADDRESS_SUCCESS == 'true') { 
            echo '<address>' . nl2br(STORE_NAME_ADDRESS) . '</address>'; 
         } else if (RETURN_STORE_NAME_ADDRESS_SUCCESS == 'false') { 
           echo '<address>' . nl2br(RETURN_STORE_NAME_ADDRESS_DIFF) . '</address>'; 
         } 
 echo TEXT_SUCCESS_RMA_THANKS . '</div>'; 

        ?>
</div>
<div class="buttonRow back"><?php echo zen_back_link() . zen_image_button(BUTTON_IMAGE_BACK, BUTTON_BACK_ALT) . '</a>'; ?></div>

<?php } else {  ?> 

<?php if (DEFINE_RETURNS_STATUS >= '1' and DEFINE_RETURNS_STATUS <= '2') { ?>
<div id="returnAuthorizationMainContent">
<?php  
/**
 * require html_define for the Returns page
 */
require($define_page);
?>
</div>
<?php } ?>

<fieldset>
<legend><?php echo HEADING_TITLE; ?></legend>
<div class="boxcontainer">
<section>
<?php
switch (RETURN_NAME) {
    case 0:
        echo '<div class="input-group margin-bottom-sm"><label class="inputLabel" for="contactname">' . ENTRY_NAME . '</label>' . 
zen_draw_input_field('contactname', $name, ' class="form-control" id="contactname" placeholder="' . ENTRY_NAME . '" pattern="[a-zA-Z0-9 -]{4,}"') . '</div>';
        break;
    case 1:
        echo '<div class="input-group margin-bottom-sm"><label class="inputLabel" for="contactname">' . ENTRY_NAME . '</label>' . 
zen_draw_input_field('contactname', $name, ' class="form-control required" id="contactname" placeholder="' . ENTRY_NAME . '" pattern="[a-zA-Z0-9 -]{4,}" required') . '</div>';
        break;
    case 2:
        echo '';
        break;
}
?>

<?php

 echo '<div class="input-group margin-bottom-sm"><label class="inputLabel" for="email_address">' . ENTRY_EMAIL . '</label>' . zen_draw_input_field('email', $email_address, ' class="form-control" id="email_address" placeholder="' . ENTRY_EMAIL . '"  readonly', 'email') . '</div>';

?>



<?php
echo '<div class="input-group margin-bottom-sm"><label class="inputLabel" for="telephone">' . ENTRY_TELEPHONE . '</label>' .  zen_draw_input_field('telephone', $telephone, ' id="telephone" class="form-control phone" pattern="^[0-9]{3}-[0-9]{3}-[0-9]{4}$" placeholder="' . ENTRY_TELEPHONE_NUMBER_TEXT . '" required', 'tel') . '</div>';

?>

<div class="" id="back"><b><?php echo TEXT_CURRENT_ADDRESS; ?></b><br />
<?php echo $dogorder; ?>

</div>
<br class="clearBoth" />
<!-- begin: shipping address lines -->
<span style="cursor:pointer;" id="shippingbox" class="cssButton normal_button button" data-text-swap=" <?php echo TEXT_SHIPTO_BILLING; ?>"> <?php echo TEXT_SHIPTO_SHIPPING; ?></span> 
<br /><br />
<h3 class="shipping_address_group" ><?php echo TEXT_CREATE_SHIPPING; ?></h3>

<div class="shipping_address_group" style="display: none;">
<h3><?php echo TEXT_SETUP_SHIPPING; ?></h3>

<input name="shipto" id="shipto" value='no' type="hidden">

<br class="clearBoth" />
<?php
 echo '<div class="input-group margin-bottom-sm"><label class="inputLabel"  for="address">' . ENTRY_ADDRESS . '</label>' . zen_draw_input_field('address', $address, 'size="40" id="address"  class="form-control"') . '</div>';?>

<?php
echo '<div class="input-group margin-bottom-sm"><label class="inputLabel"  for="city">' . RETURNS_ENTRY_CITY . '</label>' . zen_draw_input_field('city', $city, 'size="40" id="city" class="form-control"') . '</div>';?>

<?php
echo '<div class="input-group margin-bottom-sm"><label class="inputLabel"  for="state">' . RETURNS_ENTRY_STATE . '</label>' . zen_draw_input_field('state', $state, 'size="40" id="state" class="form-control"') . '</div>';?>

<?php
 echo '<div class="input-group margin-bottom-sm"><label class="inputLabel"  for="postcode">' . ENTRY_POSTCODE . '</label>' . zen_draw_input_field('postcode', $postcode, 'size="40" id="postcode" class="form-control"') . '</div>';?>

<?php
 echo '<div class="input-group margin-bottom-sm"><label class="inputLabel"  for="country">' . RETURNS_ENTRY_COUNTRY . '</label>' . zen_draw_input_field('country', $country, 'size="40" id="country" class="form-control"') . '</div>';?>
</div>
<script type="text/javascript">
$( "#shippingbox" ).click(function() {
  $( ".shipping_address_group" ).toggle("slow");

    var el = $(this);
    if (el.text() == el.data('text-swap')) {
        el.text(el.data('text-original'));
         $("#shipto").val('no');
       /* closed box */
    } else {
        el.data('text-original', el.text());
        el.text(el.data('text-swap'));
         $("#shipto").val('yes');
         /* open box*/
    }
    
});

</script>
<!-- eof shipping --> 
</section>
<section>

 <?php
echo '<div class="input-group margin-bottom-sm"><label class="inputLabel"  for="order_number">' . ENTRY_ORDER_NUMBER . '</label>' . zen_draw_input_field('order_id', $orderID, 'class="form-control" id="order_number" readonly') . '</div>';
?>

<?php  echo '<input type="hidden" name="rma_number" value="'.$rma_number.'">'; ?> 

<?php  echo '<input type="hidden" name="coID" value="'.$CoID.'">'; ?> 
  
<!-- bof grid-d -->         
<div class="ui-grid-a ui-responsive">
<?php for ($i=0, $n=$howmany; $i<$n; $i++) { ?>
<fieldset style="border:1px solid #000000;border-radius:15px;">
<div class="ui-block-aa"><div class="ui-body ui-body-d">
<div id="orderback"><?php echo zen_draw_checkbox_field('notify[]', $prodID[$i], true, ' class="checky" id="notify-' . $i . '"') . '<br />Returning ' . $prodQTY[$i] . ' items!'; ?><br />
<?php  echo zen_draw_hidden_field('Prod_qty[]', $prodQTY[$i]); ?> 
</div>
</div></div>
<div class="ui-block-bb"><div class="ui-body ui-body-d">
<?php echo $image[$i]; ?>
</div></div>
<div class="ui-block-cc"><div class="ui-body ui-body-d">
<?php echo $product_info[$i]; ?>
</div></div>
</fieldset>
<?php } ?>
</div>

<br /><br />
<?php

foreach(explode(",", RETURN_ACTION_LIST_OPTIONS) as $k => $v) {
$entry_action_array[] = array('id' => $v, 'text' => preg_replace('/\<[^*]*/', '', $v));
}
?>

<div class="input-group margin-bottom-sm">
<label for="enquiry"><?php echo ENTRY_ACTION; ?></label>
<?php  echo zen_draw_pull_down_menu('action', $entry_action_array, '20', 'class="form-control required" id="subject" required=""') ;?>
</div>

<?php
switch (RETURN_REASON) {
    case 0:
        echo '<div class="input-group margin-bottom-sm"><label for="enquiry">' . ENTRY_REASON_TEXT . '</label>' . zen_draw_textarea_field('reason', '30', '7', $reason, 'id="reason" placeholder="' . ENTRY_ENQUIRY . '" wrap="virtual" class="form-control-aw"') . '</div>';
        break;
    case 1:
        echo '<div class="input-group margin-bottom-sm"><label for="enquiry">' . ENTRY_REASON_TEXT . '</label>' . zen_draw_textarea_field('reason', '30', '4', $reason, 'id="reason" placeholder="Tell us something" wrap="virtual" class="form-control-aw required" required=""') . '</div>';
        break;
}
?>

<?php echo zen_draw_input_field('should_be_empty', '', ' size="40" id="CUAS" style="visibility:hidden; display:none;" autocomplete="off"'); ?>

</section>
</div>
<h2 class="pasduck"><?php echo TEXT_AD_BOX_RETURNS; ?></h2>
</fieldset>

<br class="clearBoth" />

<div class="buttonRow forward"><?php echo zen_image_submit(BUTTON_IMAGE_SUBMIT, BUTTON_SEND_ALT, ' id="postme"'); ?></div>
<div class="buttonRow back"><?php echo zen_back_link() . zen_image_button(BUTTON_IMAGE_CANCEL, BUTTON_CANCEL_ALT) . '</a>'; ?></div>
<?php
  }
?>
</form>
</div>
<script type="text/javascript">
$(document).ready(function(){
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
