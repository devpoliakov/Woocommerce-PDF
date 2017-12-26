<?php
/*
Plugin Name: Woocommerce orders PDF
Plugin URI: http://www.geolance.tech
Description: create PDF for Your orders
Version: 0.1.4
Author: Poliakov Yurii
Author URI: 
*/

// Hook for adding admin menus
add_action('admin_menu', 'PDF_add_pages');

// action function for above hook
function PDF_add_pages() {
$type_of_publication = 'all';
 add_menu_page("Dashboard of Messages", 
 'Your WC PDF configuration' ,
  'administrator',  __FILE__,  'PDF_options_page');

 //call register settings function
	add_action( 'admin_init', 'WCorder_PDF_plugin_settings' );
}

add_action( 'admin_enqueue_scripts', 'load_admin_dashboard' );
 function load_admin_dashboard() {
	wp_register_style( 'WCpdf-css', plugins_url('/css/style.css', __FILE__), array(), '1.7', 'all' );
	wp_enqueue_style( 'WCpdf-css' );
	
	wp_register_script( 'WCpdf-js', plugins_url ('/js/script.js',__FILE__ ), array(), '1.1', false );
    wp_enqueue_script( 'WCpdf-js' );

}

function WCorder_PDF_plugin_settings() {
	//register our settings
	register_setting( 'woocommerce-orders-pdf', 'PDFtitle' );
	register_setting( 'woocommerce-orders-pdf', 'PDFThanksDescription' );
	register_setting( 'woocommerce-orders-pdf', 'PDFshowAddress' );
}

function PDF_options_page(){
?>
<div class="wrap PDF-for-orders">
<h1><?php echo __("Woocommerce orders PDF") ?></h1>

<form method="post" action="options.php">
    <?php settings_fields( 'woocommerce-orders-pdf' ); ?>
    <?php do_settings_sections( 'woocommerce-orders-pdf' ); ?>
<div class="row">
<label for="PDFtitle"><?php echo __("Title of the Your document") ?></label>
<input type="text" id="PDFtitle" name="PDFtitle" value="<?php echo esc_attr( get_option('PDFtitle') ); ?>" />
</div>

<div class="row">    
<label for="PDFThanksDescription"><?php echo __("Description") ?></label>
<textarea id="PDFThanksDescription" name="PDFThanksDescription"><?php echo esc_attr( get_option('PDFThanksDescription') ); ?></textarea>
</div>

<div class="row">
<label for="PDFshowAddress"><?php echo __("Display address") ?></label>
<input type="checkbox" id="PDFshowAddress" name="PDFshowAddress" <?php checked( get_option('PDFshowAddress'), 1 ); ?> value="1" />
</div>
    
    <?php submit_button(); ?>

</form>
</div>
<?php
}
// last ID of order
	function get_last_order_id(){
    global $wpdb;
    $statuses = array_keys(wc_get_order_statuses());
    $statuses = implode( "','", $statuses );

    // Getting last Order ID (max value)
    $results = $wpdb->get_col( "
        SELECT MAX(ID) FROM {$wpdb->prefix}posts
        WHERE post_type LIKE 'shop_order'
        AND post_status IN ('$statuses')
    " );
    return reset($results);
}

// work with sending order
add_filter( 'woocommerce_email_attachments', 'attach_terms_conditions_pdf_to_email', 10, 3);
function attach_terms_conditions_pdf_to_email ( $attachments , $id, $object ) 

{


// get order id
$order_id = get_last_order_id();
$order = wc_get_order( $order_id );
$items = $order->get_items();


// get pdf lybrary
require_once('pdfGenerator.php');




	$your_pdf_path = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/woocommerce-pdf-sunday/pdf-arch/order-'. $order_id.'.pdf';
	$attachments[] = $your_pdf_path;
	return  $attachments[0];

}


?>