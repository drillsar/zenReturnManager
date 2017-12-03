<?php
/**
 * Order Cancel
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

<?php if ($messageStack->size('cancelme') > 0) echo $messageStack->output('cancelme'); ?>

<?php echo zen_draw_form ('cancelme', zen_href_link(FILENAME_CANCELME, 'action=send', 'SSL'),'post',' id="ninja"'); ?>

<?php  if (isset($_GET['action']) && ($_GET['action'] == 'success')) { ?>

<br class="clearBoth" />

<div class="mainContent success">
<?php 
$dogorder = $_SESSION['retunaddress'];
$order_number = $_SESSION['ordernum']; 
$proqty = $_SESSION['returnQTY'];

 echo '<br /><h2>' . TEXT_SUCCESS . '</h2>';

 echo '<br /><h2>' . TEXT_SUCCESS_RMA_THANKS . '</h2></div>'; 
        ?>
</div>
<div class="buttonRow back"><?php echo zen_back_link() . zen_image_button(BUTTON_IMAGE_BACK, BUTTON_BACK_ALT) . '</a>'; ?></div>

<?php } else {  ?> 

<?php if (DEFINE_CANCELME_STATUS >= '1' and DEFINE_CANCELME_STATUS <= '2') { ?>
<div id="returnAuthorizationMainContent">
<?php  
/**
 * require html_define for the cancelme page
 */
require($define_page);
?>
</div>
<?php } ?>

<fieldset>
<legend><?php echo HEADING_TITLE; ?></legend>
<div class="boxcontainer">
<section>
<h3>Do we have your Name and Phone correct!</h3>
<?php echo '<div class="input-group margin-bottom-sm"><label class="inputLabel" for="contactname">' . ENTRY_NAME . '</label><br />' . 
zen_draw_input_field('contactname', $name, ' class="form-control required" id="contactname" placeholder="' . ENTRY_NAME . '" pattern="[a-zA-Z0-9 -]{4,}" required') . '</div>'; ?>

<?php echo '<div class="input-group margin-bottom-sm"><label class="inputLabel" for="telephone">' . ENTRY_TELEPHONE . '</label><br />' .  zen_draw_input_field('telephone', $telephone, ' id="telephone" class="form-control phone" pattern="^[0-9]{3}-[0-9]{3}-[0-9]{4}$" placeholder="' . ENTRY_TELEPHONE_NUMBER_TEXT . '" required', 'tel') . '</div>'; ?>

<div class="input-group margin-bottom-sm">
<h2>Order address:<br />
<?php echo $dogorder; ?>

</h2>
</div>
<fieldset>
<h2 class="pasduck"><?php echo TEXT_AD_BOX_RETURNS; ?></h2>
</fieldset>
</section>
<section>
<h2 id="orderHistoryDetailedOrder"><?php echo $email_address . ' -- ' . ENTRY_ORDER_NUMBER . ' ' . $orderID; ?></h2>

<?php  echo '<input type="hidden" name="email" value="'.$email_address.'">'; ?> 
<?php  echo '<input type="hidden" name="order_id" value="'.$orderID.'">'; ?> 
<?php  echo '<input type="hidden" name="coID" value="'.$CoID.'">'; ?> 
  
<!-- bof grid-d -->         
<div class="ui-grid-a ui-responsive">
<?php for ($i=0, $n=$howmany; $i<$n; $i++) { ?>
<fieldset style="border:1px solid #000000;border-radius:15px;">
<div class="ui-block-aa"><div class="ui-body ui-body-d">
<div id="orderback"><?php echo zen_draw_checkbox_field('notify[]', $prodID[$i], true, ' class="checky" id="notify-' . $i . '"') . '<br />Canceling ' . $prodQTY[$i] . ' items!'; ?><br />
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

foreach(explode(",", CANCEL_ACTION_LIST_OPTIONS) as $k => $v) {
$entry_action_array[] = array('id' => $v, 'text' => preg_replace('/\<[^*]*/', '', $v));
}
?>

<div class="input-group margin-bottom-sm">
<label for="enquiry"><?php echo ENTRY_ACTION; ?></label><br />
<?php  echo zen_draw_pull_down_menu('action', $entry_action_array, '20', 'class="form-control required" id="subject" required=""') ;?>
</div>

<?php
switch (RETURN_REASON) {
    case 0:
        echo '<div class="input-group margin-bottom-sm"><label for="enquiry">' . ENTRY_REASON_TEXT . '</label><br />' . zen_draw_textarea_field('reason', '30', '7', $reason, 'id="reason" placeholder="Tell us something" wrap="virtual" class="form-control-aw"') . '</div>';
        break;
    case 1:
        echo '<div class="input-group margin-bottom-sm"><label for="enquiry">' . ENTRY_REASON_TEXT . '</label><br />' . zen_draw_textarea_field('reason', '30', '4', $reason, 'id="reason" placeholder="Tell us something" wrap="virtual" class="form-control-aw required" required=""') . '</div>';
        break;
}
?>

<?php echo zen_draw_input_field('should_be_empty', '', ' size="40" id="CUAS" style="visibility:hidden; display:none;" autocomplete="off"'); ?>

</section>
</div>

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
