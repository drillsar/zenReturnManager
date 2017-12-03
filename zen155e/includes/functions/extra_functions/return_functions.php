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
 * @version $Id: returns_functions.php 1.0 09/02/2017 davewest $
 */

// set up random charater set used in RMA number 
// used in tpl_returns_default.php  
function randomKey($length) {
    $pool = array_merge(range(0,9), range('A', 'Z'));  //range('a', 'z'),
    for($i=0; $i < $length; $i++) {
        $key .= $pool[mt_rand(0, count($pool) - 1)];
    }
    return $key;
}

function rma_pull_down_menu($name, $values, $default) {
//die($name . '---' . $values . '---' . $default);
//$default = '1';

$field = '<select id="postqt" name="' . zen_output_string($name) . '"';

    $field .= '>' . "\n";
	
    for ($i=1; $i<=$values; $i++) {
	
      $field .= '<option value="' . $i . '"';
      if ($default == $i) {
        $field .= ' selected="selected"';
      }

    $field .= '>' . $i . '</option>' . "\n";
	
	if ($i == MAX_QUANTITY_DISPLY) $i = $values;
    }
    $field .= '</select>' . "\n";
		  
    return $field;
}


// eof
