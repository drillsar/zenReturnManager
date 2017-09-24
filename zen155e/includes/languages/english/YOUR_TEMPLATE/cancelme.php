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
 * @version $Id: cancelme.php 1.0 09/02/2017 davewest $
 */

define('HEADING_TITLE', 'Cancellation Request');
define('NAVBAR_TITLE', 'Cancellation Request');

define('TEXT_SUCCESS', 'Your request was successfully submitted and the Cancellation processes has started, a confirmation email has been sent to your email address.<br /><br />For additional information, you may view our <a href="' . zen_href_link(FILENAME_SHIPPING, '', 'SSL') . '">Return Policy</a> or <a href="' . zen_href_link(FILENAME_CONTACT_US, '', 'SSL') . '">Contact Us</a> with any questions, comments or concerns that you may have.<br /><br />');

define('TEXT_SUCCESS_RMA_THANKS', 'Thank you for your request.<br /><br />');

define('EMAIL_SUBJECT', 'Cancellation Request');
define('EMAIL_GREET_NONE', ' %s' . "\n\n");
define('EMAIL_WELCOME', 'Thank you for contacting ' . STORE_NAME . '.' . "\n\n");

define('EMAIL_TEXT', 'Your request for a Cancellation was received..' . "\n\n");

define('EMAIL_CONTACT', 'For additional information, you may view our Return Policy.' . "\n");
define('EMAIL_SHIPPING_URL_BOF', '');
define('EMAIL_SHIPPING_URL_EOF', '');

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
define('ENTRY_ACTION_DEFAULT', 'Replacement');

define('TEXT_AD_BOX_RETURNS', 'Sorry to hear you want to cancel some or part of your order with us. Please give us some time to process the cancellation and refund you funds. If the item shipped already and we can\'t get the horse pulled back, you may have to return the item! We\'ll contact you if this happens.<br /><br />If you uncheck an item, that item well not be canceled at this time. ');

//EOF
