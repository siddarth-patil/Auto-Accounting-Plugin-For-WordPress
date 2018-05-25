<?php
function wpaccounting_menu(){
	add_menu_page('Accounting Entry', 'Accounts Entry', 'administrator', ACCOUNTING_SLUG, 'wpaEntry', "
dashicons-editor-justify", "55"); 
	add_submenu_page( ACCOUNTING_SLUG, 'Income Statement', 'Balance Sheet', 'administrator', ACCOUNTING_STATEMENT,'wpaStatement'); 
	add_submenu_page( ACCOUNTING_SLUG, 'Ledger', 'Ledger', 'administrator', ACCOUNTING_LEDGER,'wpaLedger'); 

	add_submenu_page( ACCOUNTING_SLUG, 'Expense Types', 'Expense Types', 'administrator', '/edit.php?post_type=wpa_expense_type'); 

	add_submenu_page( ACCOUNTING_SLUG, 'Sales Lines', 'Sales Types', 'administrator', '/edit.php?post_type=wpa_sales_meta'); 
	add_submenu_page( ACCOUNTING_SLUG, 'Extra Sales Data', 'Extra Input Features', 'administrator', '/edit.php?post_type=wpa_transaction_meta'); 

	add_submenu_page( ACCOUNTING_SLUG, 'Accounting Settings', 'Basic Settings', 'administrator', ACCOUNTING_SETTINGS,'wpaSettings'); 
}

?>