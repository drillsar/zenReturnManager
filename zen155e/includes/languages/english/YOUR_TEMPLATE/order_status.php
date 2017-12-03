<?php
/**
 * @package languageDefines
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: Integrated COWAA v1.0
 * @version $Id: order_status.php 1.0.1 11/27/2017 davewest $
 */

define('NAVBAR_TITLE', 'Order Tracking');
define('NAVBAR_TITLE_1', 'My Account');

//define('HEADING_TITLE', 'Return Authorization Request');
define('HEADING_TITLE', 'Order Tracking and History');

define('SUB_HEADING_TITLE', 'Look up a single order');
define('SUB_HEADING', 'Enter your order number to see what it\'s status is.');

define('HEADING_ORDER_NUMBER', 'Order #%s');
define('HEADING_ORDER_DATE', 'Order Date:');
define('HEADING_ORDER_TOTAL', 'Order Total:');
define('HEADING_LOGIN_BOX', 'Log in to view order history');

define('HEADING_PRODUCTS', 'Products');
define('HEADING_TAX', 'Tax');
define('HEADING_TOTAL', 'Total');
define('HEADING_QUANTITY', 'Qty.');

define('HEADING_SHIPPING_METHOD', 'Shipping Method');
define('HEADING_PAYMENT_METHOD', 'Payment Method');

define('HEADING_ORDER_HISTORY', 'Status History &amp; Comments');
define('TEXT_NO_COMMENTS_AVAILABLE', 'No comments available.');
define('TABLE_HEADING_STATUS_DATE', 'Date');
define('TABLE_HEADING_STATUS_ORDER_STATUS', 'Order Status');
define('TABLE_HEADING_STATUS_COMMENTS', 'Comments');
define('QUANTITY_SUFFIX', '&nbsp;ea.  ');
define('ORDER_HEADING_DIVIDER', '&nbsp;-&nbsp;');
define('TEXT_OPTION_DIVIDER', '&nbsp;-&nbsp;');

define('ENTRY_EMAIL', 'E-Mail Address:');
define('ENTRY_ORDER_NUMBER', 'Order Number:');

define('ERROR_INVALID_EMAIL', '<strong>You have entered an invalid e-mail address.</strong>');
define('ERROR_INVALID_ORDER', '<strong>You have entered an invalid order number.</strong');
define('ERROR_NO_MATCH', '<strong>No match found for your entry.</strong>');

define('TEXT_LOOKUP_INSTRUCTIONS', '<h3>INSTRUCTIONS</h3><p>To lookup the status of an order, <br />please enter the order number and<br />the e-mail address with which it was placed.</p>');

define('FOOTER_DOWNLOAD', 'You can also download your products at a later time at \'%s\'');

define('TEXT_ACCOUNT_INFO_RETURNS_BUTTON_HEADER', 'Submit a Returns Authorization Request');
define('TEXT_ACCOUNT_INFO_RETURNS_TEXT_LINK_HEADER', 'Returns');
define('TEXT_DEFINE_BUTTON_LINK2', 'Click here');
define('TEXT_DEFINE_BUTTON_LINK3', ' to create an RMA.');
define('FOOTER_DOWNLOAD_COWOA', 'You can download your products using the Order Status page until you reach max downloads or run out of time!');

define('TEXT_RETURN_GRACE_PERIOD_EXPIRED', 'The item(s) on this order extend past our <strong>' . RETURN_GRACE_PERIOD . ' Day</strong>' . '<a href="' . zen_href_link(FILENAME_SHIPPING, '', 'SSL') . '">' . ' Return Policy' . '</a>' . '. Please ' . '<a href="' . zen_href_link(FILENAME_CONTACT_US, '', 'SSL') . '">' . 'contact us' . '</a>' . ' for any further inquiries.');

define('TEXT_AD_BOX_ORDERS', 'Track your Order process easy and<br /> fast anytime from this page.');
define('TEXT_PASSWORD_FORGOTTEN', 'Forgot login password?');
define('TEXT_RETURN_CANCEL_INTRO', 'To submit a Return Authorization Request.<br />Check the box and select the number of items to return or cancel.<br />Cancel can only be used if the item has not shipped yet.');

define('TEXT_AD_MAIN_PAGE', 'Leave feedback by writing Product Reviews!');
define('TEXT_AD_TESTIMONIALS', 'Leave feedback by writing reviews! <br /> Leave store Feedback with Testimonials.');

//Order Table
define('TITLE_LINE_ITEMS', ' Number of Items: ');
define('TEXT_ORDER_DATE', 'Order Date');
define('TEXT_ORDER_NUMBER', 'Order Number');
define('TEXT_ORDER_TOTAL', 'Order Total');
define('TEXT_ORDER_SUB_TOTAL', 'Sub Total: ');
define('TEXT_TOTAL', 'Total: ');
define('TEXT_COMPLETED', 'Returned/Canceled<br />Completed');
define('TEXT_FREE_SHIPPING', 'Free Shipping');
define('TEXT_TESTIMONIALS', 'Leave us your comments <i class="fa fa-arrow-right"></i>');

//button name alts
define('BUTTON_FIND_ORDER_ALT', 'Find Order &#xf0a4;');
define('BUTTON_RETURN_ALT', 'Return Item &#xf25a;');
define('BUTTON_CANCEL_ALT', 'Cancel Item &#xf25a;');
// eof
