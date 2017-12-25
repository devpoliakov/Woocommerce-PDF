<?php
/*
Plugin Name: Woocommerce PDF orders 
Plugin URI: http://www.geolance.tech
Description: create PDF for Your orders
Version: 0.1.4
Author: Poliakov Yurii
Author URI: 
*/



// main function include


// Hook for adding admin menus
add_action('admin_menu', 'PDF_add_pages');

// action function for above hook
function PDF_add_pages() {
$type_of_publication = 'all';
 add_menu_page("Dashboard of Messages", 
 'Your WC PDF configuration' ,
  'administrator',  __FILE__,  'PDF_options_page');
}

function PDF_options_page(){
/*
$latest_order_id = get_last_order_id(); // Last order ID
$order = wc_get_order( $latest_order_id ); // Get an instance of the WC_Order oject
$order_details = $order->get_data(); // Get the order data in an array
*/
//echo '<pre>'; print_r( $order_details ); echo '</pre>';
}
/**

*/
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

	// get description of the mail


$latest_order_id = get_last_order_id(); // Last order ID
$order = wc_get_order( $latest_order_id ); // Get an instance of the WC_Order oject
$order_details = $order->get_data(); // Get the order data in an array

//echo $latest_order_id;
/**

*/

	// get pdf lybrary
	require_once('fpdf181/fpdf.php');

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);



// Shipping address
$pdf->Write(19,'Product(s) details');
$pdf->Ln(15);
$pdf->SetFont('Arial','B',12);

foreach ($order_details['line_items'] as $key => $value) {	
	$pdf->Write(5,$value);
	$pdf->Ln(8);
}
$pdf->Ln(15);

// billing
$pdf->Write(19,'Billing address');
$pdf->Ln(15);
$pdf->SetFont('Arial','B',12);

foreach ($order_details['billing'] as $key => $value) {	
	$pdf->Write(5,$value);
	$pdf->Ln(8);
}
$pdf->Ln(15);

// Shipping address
$pdf->Write(19,'Shipping address');
$pdf->Ln(15);
$pdf->SetFont('Arial','B',12);

foreach ($order_details['shipping'] as $key => $value) {	
	$pdf->Write(5,$value);
	$pdf->Ln(8);
}




$pdf->Output('F',$_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/woocommerce-pdf-sunday/pdf-arch/order-'. $latest_order_id.'.pdf', true);


	$your_pdf_path = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/woocommerce-pdf-sunday/pdf-arch/order-'. $latest_order_id.'.pdf';
	$attachments[] = $your_pdf_path;
	return  $attachments[0];

}


?>