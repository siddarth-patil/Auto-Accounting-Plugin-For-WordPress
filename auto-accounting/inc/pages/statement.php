<?php
function wpaStatement(){
	global $wpdb;
	
	wp_enqueue_style( 'wpa.admin', plugins_url( '/css/admin.css', WPACCOUNTING_PLUGIN ) );
	
	if(!isset($_GET['date']) || $_GET['date'] == ''){
		$_GET['date'] = date("Y-m");
	}
	$d = $_GET['date'];
	if(strlen($d) == 4){
		$start = $d.'-01-01';
		$end = $d.'-12-31';
	}else{
		$start = $d.'-01';
		$end = $d.'-31';
	}
	$start .= ' 00:00:00';
	$end .= ' 23:59:59';
	?>
	 
    <div class='wrap'>

        <div id="icon-edit-pages" class="icon32"><br /></div><h2>Income Statement</h2>
        <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="get">
        <?php
		foreach($_GET as $k => $v){
			if($k != 'date'){
				echo '<input type="hidden" name="'.$k.'" value="'.$v.'">';
			}
		}
		?>
        <p><label for="date">Report Period: </label>
        	<select name="date" id="date" onChange="this.form.submit();">
            <?php
			for($i=0;$i<24;$i++){
				$x = strtotime("-".$i." Months");
				$d = date("Y-m",$x);
				echo '<option value="'.$d.'" '.($d == $_GET['date'] ? 'SELECTED' : '').'>'.date("F Y",$x).'</option>';
			}
			for($i=date("Y");$i>=(date("Y")-10);$i--){
				echo '<option value="'.$i.'" '.(strlen($_GET['date']) == 4 && $_GET['date'] == (int)$i ? 'SELECTED' : '').'>Annual '.$i.'</option>';
			}
			?>
            </select>
        </p>
        </form>
        <div class="statement">
        <div class="income">
        	<h3>Income</h3>
            <?php
			$total_sales = 0;
			$total_expense = 0;
			$subsales = array();
			$sales_meta = get_posts(array('post_type' => 'wpa_sales_meta','posts_per_page' => -1,'orderby' => 'meta_value','meta_key' => 'plus','order' => 'DESC','post_status' => 'any'));
			if($sales = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix . "wpaccounting_ledger WHERE type_id=1 AND ledger_date BETWEEN '".$start."' AND '".$end."'")){
				echo '<dl>';
				foreach($sales as $s){
					$total_sales += $s->amount;
					if($meta = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix . "wpaccounting_meta WHERE ledger_id='".$s->ledger_id."'")){
						foreach($meta as $m){
							$subsales[$m->meta_key] += (int)$m->meta_value;
						}
					}
				}
				
				if(!empty($subsales)){
					arsort($subsales);
					$sales_keys = array();
					if(!empty($sales_meta)){
						foreach($sales_meta as $s){
							$sales_keys[$s->post_name] = $s->post_title;
						}
					}
					
					foreach($subsales as $k => $v){
						if(isset($sales_keys[$k])){
							echo '<dt>'.$sales_keys[$k].':</dt><dd> '.wpa_moneyFormat($v).'</dd>';
						}
					}
				}else{
					echo '<dt>Total Sales:</dt><dd>'.wpa_moneyFormat($total_sales).'</dd>';
				}
				echo '</dl>';
			}else{
				echo '<p>No Sales Recorded</p>';
			}
			?>
            <br class="clear">
        </div>
        <div class="expenses">
        	<h3>Expenses</h3>
            <?php
			if($expenses = get_posts(array('post_type' => 'wpa_expense_type','posts_per_page' => -1,'orderby' => 'title','order' => 'ASC','post_status' => 'publish','post_parent' => 0))){
				echo '<dl>';
				foreach($expenses as $k => $e){
					$sum = $wpdb->get_var("SELECT SUM(amount) FROM ".$wpdb->prefix . "wpaccounting_ledger WHERE type_id='".$e->ID."' AND ledger_date BETWEEN '".$start."' AND '".$end."'");
					$total_expense += $sum;
					echo '<dt>'.$e->post_title.'</dt><dd>'.wpa_moneyFormat($sum).'</dd>';
					if($subexpenses = get_posts(array('post_type' => 'wpa_expense_type','posts_per_page' => -1,'orderby' => 'title','order' => 'ASC','post_status' => 'publish','post_parent' => $e->ID))){
						foreach($subexpenses as $se){
							$sum = $wpdb->get_var("SELECT SUM(amount) FROM ".$wpdb->prefix . "wpaccounting_ledger WHERE type_id='".$se->ID."' AND ledger_date BETWEEN '".$start."' AND '".$end."'");
							$total_expense += $sum;
							echo '<dt class="sub">'.$se->post_title.'</dt><dd class="sub">'.wpa_moneyFormat($sum).'</dd>';
						}
					}
				}
				echo '</dl>';
			}
			?>
            <br class="clear">
        </div>
        <div class="totals">
            <h3>Totals</h3>
        	<dl>
            	<dt>Gross Income:</dt>
                	<dd><?php echo wpa_moneyFormat($total_sales);?></dd>
                <?php
				$tax_percent = get_option('wpaccounting_tax');
				$calc_tax = 0;
				if(is_numeric($tax_percent) && $tax_percent > 0){
					//if(get_option('wpaccounting_tax_included') == 1){
					//	$pre_tax = $total_sales / (1 + ($tax_percent/100));
					//	$calc_tax = $total_sales - $pre_tax;
					//}else{
						$calc_tax = $total_sales * ($tax_percent/100);
					
				?>
            	<dt>Sales Tax (<?php echo $tax_percent;?>%):</dt>
                	<dd>- <?php echo wpa_moneyFormat($calc_tax);?></dd>
                <?php
				}
				?>
            	<dt>Gross Expenses:</dt>
                	<dd>- <?php echo wpa_moneyFormat($total_expense);?></dd>    
            	<dt>Net Income (Profit):</dt>
                	<dd><strong><?php echo wpa_moneyFormat($total_sales - $calc_tax - $total_expense);?></strong></dd>
            </dl>
            <br class="clear">
        </div>
        </div>
        <a class="print-link" href="javascript:window.print()">Print This Page</a>
 <style type="text/css">
 .print-link {
display:inline-block;
margin-bottom: 15px;
padding:8px 20px;
line-height:1em;
background-color: #005288;
color: #FFF !important;
font-size: 14px;
font-family:"Lucida Grande", 'LucidaSansRegular', Arial, Helvetica, sans-serif !important;
border-radius: 3px;
-moz-border-radius: 3px;
-webkit-border-radius: 3px;
text-shadow: 1px 1px 1px #111;
box-shadow: 1px 1px 1.2px #555555;
-moz-box-shadow: 1px 1px 1.2px #555555;
-webkit-box-shadow: 1px 1px 1.2px #555555;
transition: all 0.5s ease 0s;
-moz-transition: all 0.5s ease 0s;
-webkit-transition: all 0.5s ease 0s;
-ms-transition: all 0.5s ease 0s;
-0-transition: all 0.5s ease 0s;
}
.print-link:hover {
background-color: #C60;
color: #FFF;
text-decoration:none;
}
</style>
    </div>
    <?php
}
?>