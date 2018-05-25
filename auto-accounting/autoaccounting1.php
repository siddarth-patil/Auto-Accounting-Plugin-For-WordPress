<?php
/*
Plugin Name: Auto Accounting1
Plugin URI: 
Description: Simple accounting system for recording income and expenses, and generate financial reports.
Version: 0.1
Author: Batch 8 | GIT
*/
$basedir = dirname(__FILE__);
define('WPACCOUNTING_PLUGIN',__FILE__);
define('WPACCOUNTING_BASE',$basedir.'/');

include_once(WPACCOUNTING_BASE.'inc/install.php');
register_activation_hook(__FILE__,array('wpAccountingInstall','install'));

add_action('admin_menu', 'wpaccounting_menu');

define('ACCOUNTING_SLUG','wpa_entry');
define('ACCOUNTING_LEDGER','wpa_ledger');
define('ACCOUNTING_SETTINGS','wpa_settings');
define('ACCOUNTING_STATEMENT','wpa_statement');

$currency = get_option('wpaccounting_currency');
define('WPA_CUR',$currency);

include_once(WPACCOUNTING_BASE.'inc/functions/general.php');

include_once(WPACCOUNTING_BASE.'inc/posttypes.php');

include_once(WPACCOUNTING_BASE.'inc/tablecolumns.php');

include_once(WPACCOUNTING_BASE.'inc/adminmenu.php');

include_once(WPACCOUNTING_BASE.'inc/scripts.php');

include_once(WPACCOUNTING_BASE.'inc/pages/entry.php');

include_once(WPACCOUNTING_BASE.'inc/pages/ledger.php');

include_once(WPACCOUNTING_BASE.'inc/pages/settings.php');

include_once(WPACCOUNTING_BASE.'inc/pages/statement.php');
?>