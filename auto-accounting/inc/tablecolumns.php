<?php
function wpa_set_status(){
	if(isset($_GET['wpa_set_status'])){
		wp_update_post(array('ID' => $_GET['post'], 'post_status' => ($_GET['wpa_set_status'] == 1 ? 'publish' : 'draft')));
		$bits = explode('wpa_set_status',$_SERVER['REQUEST_URI']);
		wp_redirect(rtrim($bits[0],'&'));
		exit();
	}elseif(isset($_GET['wpa_set_meta'])){
		update_post_meta($_GET['post'],$_GET['wpa_set_meta'],$_GET['wpa_meta_value']);
		$bits = explode('wpa_set_meta',$_SERVER['REQUEST_URI']);
		wp_redirect(rtrim($bits[0],'&'));
		exit();
	}
}
add_action('init','wpa_set_status');

add_filter('manage_edit-wpa_sales_meta_columns', 'wpa_columns_nodate');
add_filter('manage_edit-wpa_sales_meta_columns', 'wpa_columns_sales_lines');

add_filter('manage_edit-wpa_transaction_meta_columns', 'wpa_columns_nodate');
add_filter('manage_edit-wpa_transaction_meta_columns', 'wpa_columns_sales_meta');

add_filter('manage_edit-wpa_expense_type_columns', 'wpa_columns_nodate');

add_action('manage_wpa_sales_meta_posts_custom_column', 'wpa_columns_sales_lines_val', 10, 2);
add_action('manage_wpa_transaction_meta_posts_custom_column', 'wpa_columns_sales_lines_val', 10, 2);


function wpa_columns_nodate($columns) {
	add_filter('display_post_states','wpa_nodraft');
	unset($columns['date']);
	return $columns;
}

function wpa_columns_sales_lines($columns){
	$columns['show_ledger'] = 'Show in Ledger';
	$columns['add_to_total'] = 'Add to Total';
	return $columns;
}

function wpa_columns_sales_meta($columns){
	$columns['show_ledger'] = 'Show in Ledger';
	return $columns;
}

function wpa_nodraft($str){
	return false;
}

function wpa_columns_sales_lines_val($column_name, $id) {
	$post = get_post($id);
	switch($column_name){
		case 'show_ledger':
			echo '<a href="'.$_SERVER['REQUEST_URI'].'&wpa_set_status='.($post->post_status == 'publish' ? 0 : 1).'&post='.$post->ID.'">'.($post->post_status == 'publish' ? 'Yes' : 'No').'</a>';
		break;
		case 'add_to_total':
			$meta = get_post_meta($id, 'plus', true);
			echo '<a href="'.$_SERVER['REQUEST_URI'].'&wpa_set_meta=plus&wpa_meta_value='.($meta == 0 ? 1 : 0).'&post='.$post->ID.'">'. ($meta == 0 ? 'Subtract' : 'Add').'</a>';
		break;
	}
	
}   

?>