<?php
/**
 * tpl_main_page.php
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_main_page.php 2870 2006-01-21 21:36:02Z birdbrain $
 */


?>

<body id="popupReturns" class="centeredContent" >

<h3><?php //echo 'Order Number ' . $_SESSION['ordernum']; ?></h3>
<br class="clearBoth" />

<table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td align="center"><?php echo '<a href="' . HTTP_SERVER . DIR_WS_CATALOG . '">' . zen_image($template->get_template_dir(HEADER_LOGO_IMAGE, DIR_WS_TEMPLATE, $current_page_base,'images'). '/' . HEADER_LOGO_IMAGE, HEADER_ALT_TEXT) . '</a>'; ?></td>
      </tr>

      <tr>
<td align="center">Ship To: <?php if (RETURN_STORE_NAME_ADDRESS_SUCCESS == 'true') { 
echo '<address>' . nl2br(STORE_NAME_ADDRESS) . '</address>'; 
} else if (RETURN_STORE_NAME_ADDRESS_SUCCESS == 'false') { 
echo '<address>' . nl2br(RETURN_STORE_NAME_ADDRESS_DIFF) . '</address>'; 
} 
echo '</div>';?>
<hr />
From: <?php echo $_SESSION['retunaddress']; ?></td>
      </tr>

      <tr>
        <td align="center"><?php echo '<div id="returnRMA">' . TEXT_SUCCESS_RMA_ID . $_SESSION['retunrma'];?></td>
      </tr>

      <tr>
        <td align="center"><?php echo '<div class="buttonRow"><a href="javascript:window.print()">' . 'Print' . '</a></div>';?></td>
      </tr>

    </table>

</body>
