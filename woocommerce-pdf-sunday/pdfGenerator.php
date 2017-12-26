<?php
// Include the main TCPDF library (search for installation path).
require_once('TCPDF-master/tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);

$pdf->SetTitle( esc_attr( get_option('PDFtitle') ));
$pdf->SetSubject(esc_attr( get_option('PDFThanksDescription') ));


// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' ', PDF_HEADER_STRING, array(6,160,133), array(0,64,128));
$pdf->setFooterData(array(0,64,0), array(0,64,128));

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('dejavusans', '', 14, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();



// Set some content to print

$templateDirectory = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/woocommerce-pdf-sunday/woocommerceEmailTemplates';
require_once($templateDirectory . '/email-styles.php');

// save templates to variable
ob_start();
require_once($templateDirectory . '/email-header.php');
require_once($templateDirectory . '/email-order-details.php');
require_once($templateDirectory . '/email-customer-details.php');

if(get_option('PDFshowAddress') == 1){
echo "<p></p>";
require_once($templateDirectory . '/email-addresses.php');
}

require_once($templateDirectory . '/email-footer.php');

$html = ob_get_clean();

$pdf->writeHTMLCell(0, 0, '', '', $html );
// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output($_SERVER['DOCUMENT_ROOT'] .'/wp-content/plugins/woocommerce-pdf-sunday/pdf-arch/order-'. $order_id.'.pdf', 'F');

//============================================================+
// END OF FILE
//============================================================+
