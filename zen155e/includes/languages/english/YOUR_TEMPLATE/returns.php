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

define('HEADING_TITLE', 'Return Authorization Request');
define('NAVBAR_TITLE', 'Return Authorization Request');
define('TEXT_SUCCESS_RMA_ID', 'Your RMA# is: ');
define('TEXT_SUCCESS_RMA_RETURN_ADDRESS', 'Please ship all returns to this address:');

define('TEXT_SUCCESS', 'Your request was successfully submitted and a RMA# has been issued, a confirmation email has been sent to your email address.<br /><br />For additional information, you may view our <a href="' . zen_href_link(FILENAME_PAGE_2, '', 'SSL') . '">Return Policy</a> or <a href="' . zen_href_link(FILENAME_CONTACT_US, '', 'SSL') . '">Contact Us</a> with any questions, comments or concerns that you may have.<br /><br />');

define('TEXT_SUCCESS_RMA_REFERENCE', 'Your RMA# for this request is below, this RMA# is valid for ' . RMA_GRACE_PERIOD . ' days. Please make sure that the RMA# is visible on the package. We have also provided a'); 
define('TEXT_SUCCESS_POPUP', ' Returns Package Label '); 
define('TEXT_SUCCESS_RMA_REFERENCE1', 'that you can print out and use.');

define('TEXT_SUCCESS_RMA_THANKS', 'Thank you for your request.<br /><br />');

define('EMAIL_SUBJECT', 'Return Authorization Request');
define('EMAIL_GREET_NONE', ' %s' . "\n\n");
define('EMAIL_WELCOME', 'Thank you for contacting ' . STORE_NAME . '.' . "\n\n");

define('EMAIL_TEXT', 'Your request for a Return Authorization Number was received and a RMA# was issued. Please Remember that this RMA# is only valid for ' . RMA_GRACE_PERIOD . ' days. Please make sure that the RMA# is visible on the package or use the Returns Package Label that was provided upon submission of the Return Authorization Request.' . "\n\n");

define('EMAIL_CONTACT', 'For additional information, you may view our Return Policy.' . "\n");
define('EMAIL_SHIPPING_URL_BOF', '');
define('EMAIL_SHIPPING_URL_EOF', '');

define('TEXT_CURRENT_ADDRESS', 'Current FROM address: ');
define('ENTRY_NAME', 'Full Name: ');
define('ENTRY_EMAIL', 'Email Address: ');
define('ENTRY_TELEPHONE', 'Phone Number: ');
define('ENTRY_ADDRESS', 'Address: ');
define('RETURNS_ENTRY_CITY', 'City: ');
define('RETURNS_ENTRY_STATE', 'State: ');
define('ENTRY_POSTCODE', 'Post Code: ');
define('RETURNS_ENTRY_COUNTRY', 'Country: ');
define('ENTRY_ORDER_NUMBER', 'Order Number: ');
define('ENTRY_ACTION', 'Action Requested: ');
define('ENTRY_REASON', 'Reason for Return');
define('ENTRY_REASON_TEXT', 'Reason Text: ');

define('TEXT_AD_BOX_RETURNS', 'Return Authorization Number, or RMA# well only be valid for ' . RMA_GRACE_PERIOD . ' days. Please make sure that the RMA# is visible on the package or use the Returns Package Label that was provided upon submission of the Return Authorization Request.');

define('TEXT_SHIPTO_BILLING', 'To existing address');
define('TEXT_SHIPTO_SHIPPING', 'From a different address ');
define('TEXT_CREATE_SHIPPING','Your from address is used as your return address if something happens during shipping!');
define('TEXT_SETUP_SHIPPING', 'Add a different from address now instead of your existing address on this order!');

//button name alts
define('BUTTON_SEND_ALT', '&#xf1d8; Send Now');
//EOF
