<?php

require_once APPPATH . 'ThirdParty/Pdfdocs.php';

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($papers['username']);
$pdf->SetTitle($papers['title']);
$pdf->SetSubject($papers['title']);
// $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
// $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 006', PDF_HEADER_STRING);
$pdf->SetHeaderData($ln='', $lw=0, $ht='', $hs='', $tc=array(255,255,255), $lc=array(255,255,255));

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
// $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
// $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
// $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

$pdf->SetMargins(5,5,5);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(0);

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

// set font
$pdf->SetFont('nanumbarungothicyethangul', '', 10);

foreach($sheets as $sheet) {

	$pdf->AddPage();

	$html = $sheet['content'];

	// output the HTML content
	$pdf->writeHTML($html, true, false, true, false, '');

	$pdf->lastPage();

}

// reset pointer to the last page

$title = str_replace(' ', '_', $papers['title']);
// ---------------------------------------------------------
//Close and output PDF document
$pdf->Output($title.'.pdf', 'D');
// $pdf->Output('example_006.pdf', 'I');