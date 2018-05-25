<?php
//Our class extends the WP_List_Table class, so we need to make sure that it's there
if(!class_exists('WP_List_Table')){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class directory_ledger_table extends WP_List_Table {

	 var $item_options;
	 var $sales_meta;
	 var $sales_meta_titles;
	 var $transaction_meta;
	 
    function __construct(){
        global $status, $page;
		
		$this->sales_meta = get_posts(array('post_type' => 'wpa_sales_meta','posts_per_page' => -1,'orderby' => 'meta_value','meta_key' => 'plus','order' => 'DESC','post_status' => 'publish'));
		
		if($sales_meta = get_posts(array('post_type' => 'wpa_sales_meta','posts_per_page' => -1,'orderby' => 'meta_value','meta_key' => 'plus','order' => 'DESC','post_status' => 'any'))){
			foreach($sales_meta as $s){
				$this->sales_meta_titles[$s->post_name] = $s->post_title;
			}
		}
		
		$this->transaction_meta = get_posts(array('post_type' => 'wpa_transaction_meta','posts_per_page' => -1,'orderby' => 'title','post_status' => 'publish'));
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'entry',     //singular name of the listed records
            'plural'    => 'entries',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }
	
    function column_default($item, $column_name){
		global $wpdb;
		$value = $item->$column_name;
		
		$ray = array_merge($this->sales_meta, $this->transaction_meta);
		if(!empty($ray)){
			foreach($ray as $v){
				if($v->post_name == $column_name){
					if($var = $wpdb->get_var("SELECT meta_value FROM ".$wpdb->prefix . "wpaccounting_meta WHERE meta_key='".$v->post_name."' AND ledger_id='".$item->ledger_id."'")){
						$value = $var;
					}
				}
			}
		}
		
		if(!empty($this->sales_meta)){
			foreach($this->sales_meta as $v){
				if($column_name == $v->post_name){
					$value = WPA_CUR.number_format($value,2);
					if(get_post_meta($v->ID,'plus',true) == 0){
						$value = '('.$value.')';
					}
				}
			}
		}
		
		switch($column_name){
			case 'amount':
				$val = wpa_moneyFormat($value);
				if(stristr($item->type,'expense')){
					$val = '('.$val.')';
				}
				return $val;
			break;
			case 'action':
				return '<a href="'.add_query_arg('delete',$item->ledger_id,$_SERVER['REQUEST_URI']).'" class="confirm button-secondary">Delete Entry</a>';
			break;
			case 'type':
				if($item->type_id == 1){
					$value = '<input type="button" class="button expander" value="+"> '.$value;
					$value .= '<table cellspacing="0" cellpadding="1">';
					if($meta = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix . "wpaccounting_meta WHERE ledger_id='".$item->ledger_id."'")){
						foreach($meta as $m){
							if(isset($this->sales_meta_titles[$m->meta_key])){
								$value .= '<tr><td>'.$this->sales_meta_titles[$m->meta_key].'</td><td>'. wpa_moneyFormat($m->meta_value).'</td></tr>';
							}
						}
					}
					$value .= '</table>';
				}
				return $value;
			break;
			default:
				return $value;
			break;
		}
    }
    
    function get_columns(){
        $columns = array(
            'ledger_date'     => 'Date',
			'type' => 'Type',
			'amount' => 'Amount'
			
        );
		
		if(!empty($this->sales_meta)){
			foreach($this->sales_meta as $v){
				$columns[$v->post_name] = $v->post_title;
			}
		}
		
		if(!empty($this->transaction_meta)){
			foreach($this->transaction_meta as $v){
				$columns[$v->post_name] = $v->post_title;
			}
		}
		$columns['action'] = 'Options';
        return $columns;
    }
    
    function get_sortable_columns() {
        $sortable_columns = array();
		$col = $this->get_columns();
		foreach($col as $k => $c){
			$sortable_columns[$k] = array($k,true);
		}
        return $sortable_columns;
    }
    
    function prepare_items() {
        global $wpdb;
		
        $perpage = 50;
        
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

		$this->_column_headers = array($columns, $hidden, $sortable);
        
        
        $this->process_bulk_action();
        
		/* -- Preparing your query -- */
		$query = "SELECT l.ledger_id, l.ledger_date, l.amount, l.type_id, IF(l.type_id = 1, 'Income: Sales', CONCAT('Expense: ',(SELECT post_title FROM ".$wpdb->prefix."posts WHERE ID=l.type_id))) as type";
		$query .= " FROM ".$wpdb->prefix . "wpaccounting_ledger l ";
		$wheres = array();
		if(isset($_GET['filter_type']) && $_GET['filter_type'] != ''){
			switch($_GET['filter_type']){
				case 'sale':
					$wheres[] = "l.type_id=1";
				break;
				case 'exp':
					$wheres[] = "l.type_id<>1";
				break;
				default:
					$wheres[] = "l.type_id='".(int)$_GET['filter_type']."'";
				break;
			}
		}
		
		if(isset($_GET['start_date']) && $_GET['start_date'] != ''){
			$wheres[] = "l.ledger_date >='".date("Y-m-d 00:00:00",strtotime($_GET['start_date']))."'";
		}
		if(isset($_GET['end_date']) && $_GET['end_date'] != ''){
			$wheres[] = "l.ledger_date <='".date("Y-m-d 23:59:59",strtotime($_GET['end_date']))."'";
		}
		if(!empty($wheres)){
			$query .= " WHERE ".implode(' AND ',$wheres);
		}
		
		$orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : '';
		$order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : 'ASC';
		if($orderby == ''){
			$k = array_keys($sortable);
			$orderby = $k[0];
			$order = 'DESC';
		}
		$query .= ' ORDER BY ';
		if(!empty($orderby)){ $query.=$orderby.' '.$order; }
		
		$total_items = $wpdb->query($query); //return the total number of affected rows
		$wpdb->flush();
		
        $current_page = $this->get_pagenum();
		
		if(!empty($current_page) && !empty($perpage)){
			$offset=($current_page-1)*$perpage;
			$query.=' LIMIT '.(int)$offset.','.(int)$perpage;
		}
		
        $this->items = $wpdb->get_results($query);
        
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $perpage,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$perpage)   //WE have to calculate the total number of pages
        ) );
    }
	
	function bulk_actions(){
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'wpa.admin', plugins_url( '/js/admin.js', WPACCOUNTING_PLUGIN) );
		wp_enqueue_style( 'jquery.ui.theme', plugins_url( '/css/smoothness/jquery-ui.css', WPACCOUNTING_PLUGIN ) );
		wp_enqueue_style( 'wpa.admin', plugins_url( '/css/admin.css', WPACCOUNTING_PLUGIN ) );
		?>
		<script type="text/javascript">
		jQuery(document).ready(function(){
			jQuery('.datepicker').datepicker();
		});
		</script>
        <?php
		echo '<form action="'.basename($_SERVER['REQUEST_URI']).'" method="get">';
		foreach($_GET as $k => $v){
			if(!in_array($k,array('start_date','end_date','filter_type'))){
				echo '<input type="hidden" name="'.$k.'" value="'.$v.'">';
			}
		}
		echo '<p style="float:left;margin:3px 0px;">Filters: </p>';
		echo '<select name="filter_type" onchange="this.form.submit();">';
		echo '<option value="">All Types</option>';
		echo '<option value="sale" '.($_GET['filter_type'] == 'sale' ? 'SELECTED' : '').'>Sales</option>';
		echo '<option value="exp" '.($_GET['filter_type'] == 'exp' ? 'SELECTED' : '').'>Expenses</option>';
		$expenses = get_posts(array('post_type' => 'wpa_expense_type','posts_per_page' => -1,'orderby' => 'title','order' => 'ASC','post_status' => 'publish','post_parent' => 0));
		foreach($expenses as $exp){
			echo '<option value="'.$exp->ID.'"'.($exp->ID == $_GET['filter_type'] ? ' SELECTED' : '').'>&nbsp;|__'.$exp->post_title.'</option>';
			if($sub_expenses = get_posts(array('post_type' => 'wpa_expense_type','posts_per_page' => -1,'orderby' => 'title','order' => 'ASC','post_status' => 'publish','post_parent' => $exp->ID))){
				foreach($sub_expenses as $se){
					echo '<option value="'.$se->ID.'" '.($se->ID == $_GET['filter_type'] ? ' SELECTED' : '').'>&nbsp;&nbsp;&nbsp;&nbsp;|__'.$se->post_title.'</option>';
				}
			}
		}
		echo '</select>';
		echo '<p style="float:left;margin:3px 0px;">Start Date: </p>';
		echo '<input type="text" name="start_date" class="datepicker" value="'.(isset($_GET['start_date']) ? $_GET['start_date'] : '').'" id="start_end" style="float:left;width:100px;">';
		echo '<p style="float:left;margin:3px 0px;" style="float:left;">End Date: </p>';
		echo '<input type="text" name="end_date" class="datepicker" value="'.(isset($_GET['end_date']) ? $_GET['end_date'] : '').'" id="end_date" style="float:left;width:100px;">';
		echo '<input type="submit" name="" id="post-query-submit" class="button" value="Filter" style="float:left;" />';
		if(isset($_GET['filter_type']) || isset($_GET['end_date']) || isset($_GET['start_date'])){
			echo '<a href="'.admin_url('admin.php?page='.ACCOUNTING_LEDGER).'" class="button" style="float:left;margin-right:5px;">Reset</a>';
		}
		echo '<input type="button" name="" class="button expandall" value="- Hide All" style="float:left;" />';
		echo '</form>';
	}
	function process_bulk_action(){
		return;
	}
}
?>