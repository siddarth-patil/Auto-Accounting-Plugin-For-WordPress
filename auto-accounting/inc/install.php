<?php
class wpAccountingInstall
{
	function install() {
	   global $wpdb;
	   
	   $dir_db_version = "1.0";
	   
	   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	   
	   $table_name = $wpdb->prefix . "wpaccounting_ledger";
		  
	   $sql = "CREATE TABLE $table_name (
			  ledger_id INT NOT NULL AUTO_INCREMENT,
			  ledger_date DATE NOT NULL,
			  type_id INT NOT NULL,
			  details TEXT,
			  amount DECIMAL(10,2),
			  
			  PRIMARY KEY (ledger_id),
			  INDEX ledger_date (ledger_date),
			  INDEX type_id (type_id)
			  
				);";
	
	   dbDelta($sql);	
	   
	   $table_name = $wpdb->prefix . "wpaccounting_meta";
		  
	   $sql = "CREATE TABLE $table_name (
			  meta_id INT NOT NULL AUTO_INCREMENT,
			  ledger_id INT NOT NULL,
			  meta_key VARCHAR(255),
			  meta_value VARCHAR(255),
			  PRIMARY KEY (meta_id),
			  INDEX ledger_id (ledger_id),
			  INDEX meta_key (meta_key)
				);";
	
	   dbDelta($sql);	
	   
	   add_option("dir_db_version", $dir_db_version);
	   
	   $default_types = array(
							  'Salary',
							  'Products',
							  'Supplies',
							  'Materials',
							  'Rent',
							  'Loan',
							  'General',
							  'Equipment',
							  'Transporation',
							  'Utilities',
							  'Donation',
							  'Marketing',
							  'Fees',
							  'Tax'
							  );
	   $check = get_posts(array('post_type' => 'wpa_expense_type','post_status' => 'any'));
	   if(count($check) == 0){
		   sort($default_types);
		   foreach($default_types as $v){
			   wp_insert_post(array('post_title' => $v,'post_type' => 'wpa_expense_type','post_status' => 'publish'));
		   }
	   }

	  
	   $check = get_posts(array('post_type' => 'wpa_transaction_meta','post_status' => 'any'));
	   if(count($check) == 0){
		   foreach($meta_options as $k => $v){
			   wp_insert_post(array('post_name' => $k, 'post_title' => $v,'post_type' => 'wpa_transaction_meta'));
		   }
	   }
	   
	   $sales_meta = array('cash' => array('Cash Slips','+'),
						   'check' => array('Checks','+'),
						   'credit_card' => array('Credit Cards','+'),
						   'tips_check' => array('Check Tips','-'),
						   'tips_credit_card' => array('Credit Card Tips','-')
						   );
	   $check = get_posts(array('post_type' => 'wpa_sales_meta','post_status' => 'any'));
	   if(count($check) == 0){
		   foreach($sales_meta as $k => $v){
			   $id = wp_insert_post(array('post_name' => $k, 'post_title' => $v[0],'post_type' => 'wpa_sales_meta'));
			   add_post_meta($id,'plus',($v[1] == '-' ? '0' : '1'), true);
		   }
	   }
	   
	   add_option('wpaccounting_currency','$');
	}
	
}
?>