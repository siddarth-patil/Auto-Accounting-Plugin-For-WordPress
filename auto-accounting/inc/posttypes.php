<?php
function wpa_custom_post_types() {
	$labels = array(
    'name' => 'Vendors',
    'singular_name' => 'Vendor',
	'add_new' => __( 'Add Vendor' ),
	'add_new_item' => __( 'Add New Vendor' ),
	'edit_item' => __( 'Edit Vendor' ),
	'new_item' => __( 'New Vendor'),
	'view' => __( 'View Vendor'),
	'view_item' => __( 'View Vendor'),
	'search_items' => __( 'Search Vendors'),
	'not_found' => __( 'No Vendors found' ),
	'not_found_in_trash' => __( 'No Vendors found in trash' ),
	'parent' => __( 'Parent Vendor')
	);
	
    $args = array('public' => true, 
				  'show_ui' => true,
				  'show_in_nav_menus' => false,
				  'show_in_menu' => false,
				  'labels' => $labels,
				  'supports' => array( 'title')
				  );
    register_post_type( 'wpa_vendors', $args );
	
	$labels = array(
    'name' => 'Expense Types',
    'singular_name' => 'Expense',
	'add_new' => __( 'Add Expense' ),
	'add_new_item' => __( 'Add New Expense' ),
	'edit_item' => __( 'Edit Expense' ),
	'new_item' => __( 'New Expense'),
	'view' => __( 'View Expense'),
	'view_item' => __( 'View Expense'),
	'search_items' => __( 'Search Expenses'),
	'not_found' => __( 'No Expenses found' ),
	'not_found_in_trash' => __( 'No Expenses found in trash' ),
	'parent' => __( 'Parent Expense')
	);
	
    $args = array('hierarchical' => true,
				  'public' => true, 
				  'show_ui' => true,
				  'capability_type' => 'page',
				  'show_in_nav_menus' => false,
				  'show_in_menu' => false,
				  'labels' => $labels,
				  'supports' => array( 'title','page-attributes')
				  );
    register_post_type( 'wpa_expense_type', $args );
	
	
	$labels = array(
    'name' => 'Extra Sales Data',
    'singular_name' => 'Extra Sales Data',
	'add_new' => __( 'Add Data Field' ),
	'add_new_item' => __( 'Add New Data Field' ),
	'edit_item' => __( 'Edit Data Field' ),
	'new_item' => __( 'New Data Field'),
	'view' => __( 'View Data Field'),
	'view_item' => __( 'View Data Field'),
	'search_items' => __( 'Search Data Fields'),
	'not_found' => __( 'No Data Fields found' ),
	'not_found_in_trash' => __( 'No Data Fields found in trash' ),
	'parent' => __( 'Parent Data Field')
	);
	
    $args = array('public' => true, 
				  'show_ui' => true,
				  'show_in_nav_menus' => false,
				  'show_in_menu' => false,
				  'labels' => $labels,
				  'supports' => array( 'title')
				  );
    register_post_type( 'wpa_transaction_meta', $args );
	
	
	$labels = array(
    'name' => 'Sales Type',
    'singular_name' => 'Sales Type',
	'add_new' => __( 'Add Sales Type' ),
	'add_new_item' => __( 'Add New Sales Type' ),
	'edit_item' => __( 'Edit Sales Type' ),
	'new_item' => __( 'New Sales Type'),
	'view' => __( 'View Sales Type'),
	'view_item' => __( 'View Sales Type'),
	'search_items' => __( 'Search Sales Type'),
	'not_found' => __( 'No Sales Type found' ),
	'not_found_in_trash' => __( 'No Sales Type found in trash' ),
	'parent' => __( 'Parent Sales Line')
	);
	
    $args = array('public' => true, 
				  'show_ui' => true,
				  'show_in_nav_menus' => false,
				  'show_in_menu' => false,
				  'labels' => $labels,
				  'supports' => array( 'title')
				  );
    register_post_type( 'wpa_sales_meta', $args );
	
}

add_action('init', 'wpa_custom_post_types');
?>