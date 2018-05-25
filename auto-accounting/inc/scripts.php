<?php
function wpa_admin_scripts( $hook ) {
    global $post;
    if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
        if (in_array($post->post_type, array('wpa_expense_type','wpa_vendors','wpa_sales_meta','wpa_transaction_meta'))) {     
            ?>
            <style type="text/css">
			#minor-publishing {
				display:none !important;
			}
			</style>
            <?php
        }
    }
}
add_action( 'admin_enqueue_scripts', 'wpa_admin_scripts', 10, 1 );
?>