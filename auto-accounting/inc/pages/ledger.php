<?php
function wpaLedger(){
	global $wpdb;
	if(isset($_GET['delete'])){
		$wpdb->query("DELETE FROM ".$wpdb->prefix . "wpaccounting_ledger WHERE ledger_id='".(int)$_GET['delete']."'");
	}
	?>
    <div class='wrap'>
        <div id="icon-edit-pages" class="icon32"><br /></div><h2>Accounting Ledger</h2>
        <?php
        include_once(dirname(__FILE__).'/../tables/ledger.php');
        $wp_list_table = new directory_ledger_table();
        $wp_list_table->prepare_items();
        $wp_list_table->display();
		?>
    </div>
    <?php
}
?>