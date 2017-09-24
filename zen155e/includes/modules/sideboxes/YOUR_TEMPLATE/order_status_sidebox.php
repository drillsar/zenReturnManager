<?php
/**
 * Order Status SideBox
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $id: order_status_sidebox.php 3.5.3 6/1/2014 davewest $
 */

      require($template->get_template_dir('tpl_order_status_sidebox.php',DIR_WS_TEMPLATE, $current_page_base,'sideboxes'). '/tpl_order_status_sidebox.php');
      
      $content = '<div id="' . str_replace('_', '-', $box_id . 'Content') . 
                 '" class="sideBoxContent centeredContent">' . $content . '</div>';
    $title =  'ORDER TRACKING';
    $left_corner = false;
    $right_corner = false;
    $right_arrow = false;
    $title_link = false;
    require($template->get_template_dir($column_box_default, DIR_WS_TEMPLATE, $current_page_base,'common') . '/' . $column_box_default);

//EOF
