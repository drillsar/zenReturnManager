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
 * @version $Id: returns_filenames.php 1.0 09/02/2017 davewest $
 */

  define('FILENAME_RETURNS', 'returns');
  define('FILENAME_DEFINE_RETURNS', 'define_returns');
  define('FILENAME_CANCELME', 'cancelme');
  define('FILENAME_DEFINE_CANCELME', 'define_cancelme');
  define('FILENAME_POPUP_RETURNS', 'popup_returns');
    
  define('TABLE_ORDER_RETURN_MANAGER', DB_PREFIX . 'order_return_manager');
?>
