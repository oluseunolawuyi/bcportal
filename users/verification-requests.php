<?php 
if(!isset($_REQUEST["gh"])){ include_once("../includes/admin-header.php"); 
}else{ 
include_once("../includes/gen-header.php");
}

//error_reporting(E_ALL); ini_set('display_errors', 1); 

admin_role_redirect("manage_cover_letters");

$table = "cover_letters";

/////////////////////////////////////////////////////////////////////////////////////////
if(isset($_POST["delete"]) && isset($_POST["del"])){
$i = 0;
if(is_array($_POST["del"])){
foreach ($_POST["del"] as $k => $c) {
if($c != ""){ 
$c = testQty($c);
$generated_by = in_table("generated_by",$table,"WHERE id = '{$c}'","generated_by");
$act = $db->delete($table, " id='{$c}'");

$file_array = glob("../pdf-reports/{$c}-{$generated_by}-*.*");
if($file_array){
foreach($file_array as $filename){
unlink($filename);
}
}

$file_array = glob("../pdf-reports-images/{$c}-*");
if($file_array){
foreach($file_array as $filename){
unlink($filename);
}
}

$i++;			
}else{
continue;
}
}

if($act){

$activity = "Deleted {$i} Verification request(s).";
$audit_data_array = array(
"user_id" => "'$id'",
"name" => "'$username'",
"email" => "'$user_email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$db->insert($audit_data_array, "audit_log");

echo "<div class='success'>{$i} verification request(s) successfully deleted.</div>";
}else{
echo "<div class='not-success'>Error. Unable to delete verification request(s).</div>";
}

}else{
echo "<div class='not-success'>Atleast one field must be selected.</div>";
}
}
///////////////////////////////////////////////////////////////////////

$edit = nr_input("edit");
$edit_images = nr_input("edit_images");
$add = nr_input("add");
$pn = nr_input("pn");
$view = nr_input("view");
$manage_images = nr_input("manage_images");
$download = nr_input("download");
$allow_check = np_input("allow_check");
$resend = np_input("resend");
$delete_image = tp_input("delete_image");

////////////////============ Update CV Cover Letter =============///////////////
$reference_no = tp_input("reference_no");
$completion_date = tp_input("completion_date");
$client = np_input("client");
$client_designation = tp_input("client_designation");
$client_department = tp_input("client_department");
$attention = tp_input("attention");
$re = tp_input("re");
$invoice_attachment = tp_input("invoice_attachment");
$signatory = tp_input("signatory");

$table_content = array();

$c = 0;
$details_category = "";
if(isset($_POST["names"]) && !empty($_POST["names"])){
foreach($_POST["names"] as $value){ 
$names = test_input($value);
$course = test_input($_POST["course"][$c]);
$grade = test_input($_POST["grade"][$c]);
$year_of_graduation = test_input($_POST["year_of_graduation"][$c]);
$matric_no = test_input($_POST["matric_no"][$c]);
$table_content[] = array($c+1, $names, $course, $grade, $year_of_graduation, $matric_no);
$details_category .= "{$names}+*+*{$course}+*+*{$grade}+*+*{$year_of_graduation}+*+*{$matric_no}-/-/";
$c++;
}
}

//////========Download PDF Report==========//
if(!empty($download)){
$generated_by = in_table("generated_by",$table,"WHERE id = '{$download}'","generated_by");
$_SESSION["message"] = "../pdf-reports/{$download}-{$generated_by}-both-data.pdf";
redirect("{$directory}{$admin}verification-requests?pn={$pn}");
}

//////////////==========Delete Single Image===========//////////////////
if($_SERVER["REQUEST_METHOD"] == "POST" && !empty($delete_image) && !empty($manage_images) && !empty($pn) && !empty($client) && !empty($completion_date) && !empty($attention) && !empty($details_category)){

$generated_by = in_table("generated_by",$table,"WHERE id = '{$manage_images}'","generated_by");
$completion_date = (!empty($completion_date))?date_format(date_create($completion_date),"jS F, Y"):"";
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$client_email = in_table("email","reg_users","WHERE id = '{$client}'","email");
$client_address = in_table("address","reg_users","WHERE id = '{$client}'","address");
$client_region = in_table("region","reg_users","WHERE id = '{$client}'","region");
$client_city = in_table("city","reg_users","WHERE id = '{$client}'","city");
$client_state = in_table("state","reg_users","WHERE id = '{$client}'","state");
$signatory_name = in_table("name","reg_users","WHERE id = '{$signatory}'","name");
$designation = in_table("designation","reg_users","WHERE id = '{$signatory}'","designation");
$signature_array = glob("../images/signatures/{$signatory}pic*.*");
$signature_name = ($signature_array)?"images/" . $signature_array[0]:"../images/post.jpg";

//////===========Starts Update PDF File====///////
require("../pdf/fpdf.php");
require("../pdf/fpdf-extension.php");

class NPDF extends PDF{

// Page footer
function Footer()
{
    // Arial italic 8
    $this->SetFont('Arial','',8);
    // Page number
	if($this->PageNo() == 1){
    // Position at 1.5 cm from bottom
    $this->SetXY(0,-15);
	$this->SetFillColor(0, 0, 0);
	$this->SetTextColor(255, 255, 255);
	$this->Cell(210, 15, "Risk Control Services Nig. Ltd. ... Protecting Your Assets", 0, 0, 'C', true);
	
    $this->SetFont('Arial','B',6);
	$this->SetXY(150,-31);
	$this->SetFillColor(255, 255, 255);
	$this->SetTextColor(0, 0, 0);
	$this->MultiCell(45, 2, "\nDIRECTORS:\n", 0, "L", true);
    $this->SetFont('Arial','',6);
	$this->SetXY(150,-25);
	$this->MultiCell(45, 1.7, "Mr. Tokunbo Talabi (CHAIRMAN)\n
	Mr. Olufemi Ajayi (MD/CEO)\n
	Mrs. Nnena Uwakwe\n
	Alh. Jamilu Jibrin\n
	Dr. (Mrs.) O. Olowu\n
	Mrs. Hasanatu Ado\n ", 0, "L", true);
	
    //$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	}
}

}

$pdf = new NPDF();

$pdf->AliasNbPages();

$pdf->SetAutoPageBreak(true,31);
$pdf->AddPage("P", "A4");
$pdf->Header("CONFIDENTIAL");
$pdf->SetFont('Arial','',8);
$pdf->SetTextColor(0,0,0);

$pdf->Image("../images/pdf-header-lines.png",68,10.3,0,0,"","");

// Title
$pdf->WriteHTML("<b>Risk Control Plaza</b>");
$pdf->Ln(4);
$pdf->WriteHTML("<b>Plot 5, Dream World Africana Road</b>");
$pdf->Ln(4);
$pdf->WriteHTML("<b>After Orchid Hotels, off 2nd Toll Gate,</b>");
$pdf->Ln(4);
$pdf->WriteHTML("<b>Lekki-Epe Expressway</b>");
$pdf->Ln(4);
$pdf->WriteHTML("<b>Lagos.</b>");
$pdf->Ln(4);
$pdf->WriteHTML("<b>Tel: 01-2954283</b>");
$pdf->Ln(4);
$pdf->WriteHTML("Email: info@riskcontrolnigeria.com");
$pdf->Ln(4);
$pdf->Cell(10);
$pdf->Cell(55,4,"investigation@riskcontrolnigeria.com");
$pdf->Ln(4);
$pdf->WriteHTML("Website: www.riskcontrolnigeria.com");
$pdf->Ln(8);

$pdf->SetFont('Arial','',10);

$pdf->WriteHTML("<b>Our Ref.: {$reference_no}</b>");

$pdf->Ln(8);
$pdf->WriteHTML($completion_date);
if(!empty($client_designation)){
$pdf->Ln(4);
$pdf->WriteHTML("{$client_designation},");
}
if(!empty($client_department)){
$pdf->Ln(4);
$pdf->WriteHTML("{$client_department},");
}
if(!empty($client_address)){
$pdf->Ln(4);
$pdf->WriteHTML("{$client_address},");
}
if(!empty($client_region)){
$pdf->Ln(4);
$pdf->WriteHTML("{$client_region},");
}
if(!empty($client_city)){
$pdf->Ln(4);
$pdf->WriteHTML("{$client_city},");
}
if(!empty($client_state)){
$pdf->Ln(4);
$pdf->WriteHTML("{$client_state}.");
}
$pdf->Ln(8);

$pdf->WriteHTML("{$attention},");
$pdf->Ln(8);

$pdf->WriteHTML("<u><b>RE: {$re}</b></u>");
$pdf->Ln(8);

$pdf->WriteHTML("We request that you kindly confirm the results of the candidates listed below:");
$pdf->Ln(8);

/////////////////==========Ends Update PDF File==============//////////////////

$lineheight = 5;
$table_header = array();
$pdf->SetFont('Arial','B',9);
$table_header[] = array("S/N", "NAMES", "COURSE", "GRADE", "YEAR OF GRAD.", "MATRIC NO.");
$widths = array(7,52,52,25,18,38);
$pdf->plot_table($widths, $lineheight, $table_header);

$pdf->SetFont('Arial','',8);
$widths = array(7,52,52,25,18,38);
$pdf->plot_table($widths, $lineheight, $table_content);
$pdf->Ln(3);

$pdf->SetFont("Arial","",10);

if(!empty($invoice_attachment)){
$pdf->WriteHTML($invoice_attachment);
$pdf->Ln(6);
}

$pdf->WriteHTML("We thank you for your usual cooperation.");
$pdf->Ln(10);

$pdf->WriteHTML("Yours Faithfully,");
$pdf->Ln(5);
$pdf->WriteHTML("<b>For: RISK CONTROL SERVICES NIGERIA LIMITED</b>");
if(!empty($signatory)){ 
$pdf->Ln(5);
$pdf->Cell(50, 10, $pdf->Image($signature_array[0], $pdf->GetX(), $pdf->GetY()), 0, 0, 'L', false);
$pdf->Ln(10);
$pdf->WriteHTML($signatory_name);
$pdf->Ln(5);
$pdf->WriteHTML("<b>{$designation}</b>");
}
$pdf->Ln(5);

$pdf->Output("F","../pdf-reports/{$manage_images}-{$generated_by}-text-data.pdf");

////////======Image Files=============/////
$pdf = new PDF();
$pdf->AliasNbPages();

unlink($delete_image);

$document_group = glob("../pdf-reports-images/{$manage_images}-{$generated_by}-img-*.*");
natsort($document_group);
if($document_group){
foreach($document_group as $file_name){
$pdf->AddPage("P", "A4");
$pdf->Image($file_name,10,30);
}
}
$pdf->Output("F","../pdf-reports/{$manage_images}-{$generated_by}-image-data.pdf");

require("../pdf/fpdf-merge.php");

$merge = new FPDF_Merge();
$merge->add("../pdf-reports/{$manage_images}-{$generated_by}-text-data.pdf");
$merge->add("../pdf-reports/{$manage_images}-{$generated_by}-image-data.pdf");
$merge->output("../pdf-reports/{$manage_images}-{$generated_by}-both-data.pdf");
unlink("../pdf-reports/{$manage_images}-{$generated_by}-text-data.pdf");
unlink("../pdf-reports/{$manage_images}-{$generated_by}-image-data.pdf");

$_SESSION["msg"] = "<div class='success'>Image file successfully deleted.</div>";
redirect("{$directory}{$admin}verification-requests?pn={$pn}&manage_images={$manage_images}");
}

//////////////==========Resend Email===========//////////////////
if($_SERVER["REQUEST_METHOD"] == "POST" && !empty($resend) && !empty($client)){

$generated_by = in_table("generated_by",$table,"WHERE id = '{$resend}'","generated_by");
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$client_email = in_table("email","reg_users","WHERE id = '{$client}'","email");

///=====Send mail==================//
$to = $client_email;
$subject = "Verification Request [#{$resend}]";
$message = "<p>Dear {$client_name},</p>
<p>Please find attached, the pdf file containing the verification details.</p>
<p>Thank you.</p>
<p>Warm regards.</p>";
$from = "no-reply@riskcontrolnigeria.com";
$from_name = $full_gen_name;
$files = array("../pdf-reports/{$resend}-{$generated_by}-both-data.pdf");
$html_content = message_template();
if(multi_attach_mail($to,$subject,$html_content,$from,$from_name,$files)){
unlink("../pdf-reports/{$resend}-{$generated_by}-both-data.pdf");
echo "<div class=\"success\">Verification request successfully sent</div>";
}
///======================================================//

}

/////////////========================Update Request=====================/////////
if($_SERVER["REQUEST_METHOD"] == "POST" && !empty($edit) && !empty($client) && !empty($completion_date) && !empty($attention) && !empty($details_category)){

$data_array = array(
"client" => $client,
"completion_date" => $completion_date,
"attention" => $attention,
"reference_no" => $reference_no,
"client_designation" => $client_designation,
"client_department" => $client_department,
"re" => $re,
"invoice_attachment" => $invoice_attachment,
"signatory" => $signatory,
"details_category" => $details_category,
"updated_by" => $id,
"date_updated" => $date_time
);

$act = $db->update($data_array, $table, "id = '$edit'");

$error = 0;

$generated_by = in_table("generated_by",$table,"WHERE id = '{$edit}'","generated_by");
$completion_date = (!empty($completion_date))?date_format(date_create($completion_date),"jS F, Y"):"";
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$client_email = in_table("email","reg_users","WHERE id = '{$client}'","email");
$client_address = in_table("address","reg_users","WHERE id = '{$client}'","address");
$client_region = in_table("region","reg_users","WHERE id = '{$client}'","region");
$client_city = in_table("city","reg_users","WHERE id = '{$client}'","city");
$client_state = in_table("state","reg_users","WHERE id = '{$client}'","state");
$signatory_name = in_table("name","reg_users","WHERE id = '{$signatory}'","name");
$designation = in_table("designation","reg_users","WHERE id = '{$signatory}'","designation");
$signature_array = glob("../images/signatures/{$signatory}pic*.*");
$signature_name = ($signature_array)?"images/" . $signature_array[0]:"../images/post.jpg";

//////===========Starts Update PDF File====///////
require("../pdf/fpdf.php");
require("../pdf/fpdf-extension.php");

class NPDF extends PDF{

// Page footer
function Footer()
{
    // Arial italic 8
    $this->SetFont('Arial','',8);
    // Page number
	if($this->PageNo() == 1){
    // Position at 1.5 cm from bottom
    $this->SetXY(0,-15);
	$this->SetFillColor(0, 0, 0);
	$this->SetTextColor(255, 255, 255);
	$this->Cell(210, 15, "Risk Control Services Nig. Ltd. ... Protecting Your Assets", 0, 0, 'C', true);
	
    $this->SetFont('Arial','B',6);
	$this->SetXY(150,-31);
	$this->SetFillColor(255, 255, 255);
	$this->SetTextColor(0, 0, 0);
	$this->MultiCell(45, 2, "\nDIRECTORS:\n", 0, "L", true);
    $this->SetFont('Arial','',6);
	$this->SetXY(150,-25);
	$this->MultiCell(45, 1.7, "Mr. Tokunbo Talabi (CHAIRMAN)\n
	Mr. Olufemi Ajayi (MD/CEO)\n
	Mrs. Nnena Uwakwe\n
	Alh. Jamilu Jibrin\n
	Dr. (Mrs.) O. Olowu\n
	Mrs. Hasanatu Ado\n ", 0, "L", true);
	
    //$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	}
}

}

$pdf = new NPDF();

$pdf->AliasNbPages();

$pdf->SetAutoPageBreak(true,31);
$pdf->AddPage("P", "A4");
$pdf->Header("CONFIDENTIAL");
$pdf->SetFont('Arial','',8);
$pdf->SetTextColor(0,0,0);

$pdf->Image("../images/pdf-header-lines.png",68,10.3,0,0,"","");

// Title
$pdf->WriteHTML("<b>Risk Control Plaza</b>");
$pdf->Ln(4);
$pdf->WriteHTML("<b>Plot 5, Dream World Africana Road</b>");
$pdf->Ln(4);
$pdf->WriteHTML("<b>After Orchid Hotels, off 2nd Toll Gate,</b>");
$pdf->Ln(4);
$pdf->WriteHTML("<b>Lekki-Epe Expressway</b>");
$pdf->Ln(4);
$pdf->WriteHTML("<b>Lagos.</b>");
$pdf->Ln(4);
$pdf->WriteHTML("<b>Tel: 01-2954283</b>");
$pdf->Ln(4);
$pdf->WriteHTML("Email: info@riskcontrolnigeria.com");
$pdf->Ln(4);
$pdf->Cell(10);
$pdf->Cell(55,4,"investigation@riskcontrolnigeria.com");
$pdf->Ln(4);
$pdf->WriteHTML("Website: www.riskcontrolnigeria.com");
$pdf->Ln(8);

$pdf->SetFont('Arial','',10);

$pdf->WriteHTML("<b>Our Ref.: {$reference_no}</b>");

$pdf->Ln(8);
$pdf->WriteHTML($completion_date);
if(!empty($client_designation)){
$pdf->Ln(4);
$pdf->WriteHTML("{$client_designation},");
}
if(!empty($client_department)){
$pdf->Ln(4);
$pdf->WriteHTML("{$client_department},");
}
if(!empty($client_address)){
$pdf->Ln(4);
$pdf->WriteHTML("{$client_address},");
}
if(!empty($client_region)){
$pdf->Ln(4);
$pdf->WriteHTML("{$client_region},");
}
if(!empty($client_city)){
$pdf->Ln(4);
$pdf->WriteHTML("{$client_city},");
}
if(!empty($client_state)){
$pdf->Ln(4);
$pdf->WriteHTML("{$client_state}.");
}
$pdf->Ln(8);

$pdf->WriteHTML("{$attention},");
$pdf->Ln(8);

$pdf->WriteHTML("<u><b>RE: {$re}</b></u>");
$pdf->Ln(8);

$pdf->WriteHTML("We request that you kindly confirm the results of the candidates listed below:");
$pdf->Ln(8);

/////////////////==========Ends Update PDF File==============//////////////////

$lineheight = 5;
$table_header = array();
$pdf->SetFont('Arial','B',9);
$table_header[] = array("S/N", "NAMES", "COURSE", "GRADE", "YEAR OF GRAD.", "MATRIC NO.");
$widths = array(7,52,52,25,18,38);
$pdf->plot_table($widths, $lineheight, $table_header);

$pdf->SetFont('Arial','',8);
$widths = array(7,52,52,25,18,38);
$pdf->plot_table($widths, $lineheight, $table_content);
$pdf->Ln(3);

$pdf->SetFont("Arial","",10);

if(!empty($invoice_attachment)){
$pdf->WriteHTML($invoice_attachment);
$pdf->Ln(6);
}

$pdf->WriteHTML("We thank you for your usual cooperation.");
$pdf->Ln(10);

$pdf->WriteHTML("Yours Faithfully,");
$pdf->Ln(5);
$pdf->WriteHTML("<b>For: RISK CONTROL SERVICES NIGERIA LIMITED</b>");
if(!empty($signatory)){ 
$pdf->Ln(5);
$pdf->Cell(50, 10, $pdf->Image($signature_array[0], $pdf->GetX(), $pdf->GetY()), 0, 0, 'L', false);
$pdf->Ln(10);
$pdf->WriteHTML($signatory_name);
$pdf->Ln(5);
$pdf->WriteHTML("<b>{$designation}</b>");
}
$pdf->Ln(5);

$pdf->Output("F","../pdf-reports/{$edit}-{$generated_by}-text-data.pdf");

////////======Image Files=============/////
$pdf = new PDF();
$pdf->AliasNbPages();

$document_group = glob("../pdf-reports-images/{$edit}-{$generated_by}-img-*.*");
natsort($document_group);
if($document_group){
foreach($document_group as $file_name){
$pdf->AddPage("P", "A4");
$pdf->Image($file_name,10,30);
}
}
$pdf->Output("F","../pdf-reports/{$edit}-{$generated_by}-image-data.pdf");

require("../pdf/fpdf-merge.php");

$merge = new FPDF_Merge();
$merge->add("../pdf-reports/{$edit}-{$generated_by}-text-data.pdf");
$merge->add("../pdf-reports/{$edit}-{$generated_by}-image-data.pdf");
$merge->output("../pdf-reports/{$edit}-{$generated_by}-both-data.pdf");
unlink("../pdf-reports/{$edit}-{$generated_by}-text-data.pdf");
unlink("../pdf-reports/{$edit}-{$generated_by}-image-data.pdf");

if($act){
$error = 0;
echo "<div class='success'>Verification request successfully updated.</div>";
}else{
echo "<div class='not-success'>Error. Unable to update verification request.</div>";
}
}
///////////////////////////////////////////////////////////////////////////////

/////////////========================Update Request Images=====================/////////
if($_SERVER["REQUEST_METHOD"] == "POST" && !empty($edit_images) && !empty($client) && !empty($completion_date) && !empty($attention) && !empty($details_category)){

$generated_by = in_table("generated_by",$table,"WHERE id = '{$edit_images}'","generated_by");
$completion_date = (!empty($completion_date))?date_format(date_create($completion_date),"jS F, Y"):"";
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$client_email = in_table("email","reg_users","WHERE id = '{$client}'","email");
$client_address = in_table("address","reg_users","WHERE id = '{$client}'","address");
$client_region = in_table("region","reg_users","WHERE id = '{$client}'","region");
$client_city = in_table("city","reg_users","WHERE id = '{$client}'","city");
$client_state = in_table("state","reg_users","WHERE id = '{$client}'","state");
$signatory_name = in_table("name","reg_users","WHERE id = '{$signatory}'","name");
$designation = in_table("designation","reg_users","WHERE id = '{$signatory}'","designation");
$signature_array = glob("../images/signatures/{$signatory}pic*.*");
$signature_name = ($signature_array)?"images/" . $signature_array[0]:"../images/post.jpg";

//////===========Starts Update PDF File====///////
require("../pdf/fpdf.php");
require("../pdf/fpdf-extension.php");

class NPDF extends PDF{

// Page footer
function Footer()
{
    // Arial italic 8
    $this->SetFont('Arial','',8);
    // Page number
	if($this->PageNo() == 1){
    // Position at 1.5 cm from bottom
    $this->SetXY(0,-15);
	$this->SetFillColor(0, 0, 0);
	$this->SetTextColor(255, 255, 255);
	$this->Cell(210, 15, "Risk Control Services Nig. Ltd. ... Protecting Your Assets", 0, 0, 'C', true);
	
    $this->SetFont('Arial','B',6);
	$this->SetXY(150,-31);
	$this->SetFillColor(255, 255, 255);
	$this->SetTextColor(0, 0, 0);
	$this->MultiCell(45, 2, "\nDIRECTORS:\n", 0, "L", true);
    $this->SetFont('Arial','',6);
	$this->SetXY(150,-25);
	$this->MultiCell(45, 1.7, "Mr. Tokunbo Talabi (CHAIRMAN)\n
	Mr. Olufemi Ajayi (MD/CEO)\n
	Mrs. Nnena Uwakwe\n
	Alh. Jamilu Jibrin\n
	Dr. (Mrs.) O. Olowu\n
	Mrs. Hasanatu Ado\n ", 0, "L", true);
	
    //$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	}
}

}

$pdf = new NPDF();

$pdf->AliasNbPages();

$pdf->SetAutoPageBreak(true,31);
$pdf->AddPage("P", "A4");
$pdf->Header("CONFIDENTIAL");
$pdf->SetFont('Arial','',8);
$pdf->SetTextColor(0,0,0);

$pdf->Image("../images/pdf-header-lines.png",68,10.3,0,0,"","");

// Title
$pdf->WriteHTML("<b>Risk Control Plaza</b>");
$pdf->Ln(4);
$pdf->WriteHTML("<b>Plot 5, Dream World Africana Road</b>");
$pdf->Ln(4);
$pdf->WriteHTML("<b>After Orchid Hotels, off 2nd Toll Gate,</b>");
$pdf->Ln(4);
$pdf->WriteHTML("<b>Lekki-Epe Expressway</b>");
$pdf->Ln(4);
$pdf->WriteHTML("<b>Lagos.</b>");
$pdf->Ln(4);
$pdf->WriteHTML("<b>Tel: 01-2954283</b>");
$pdf->Ln(4);
$pdf->WriteHTML("Email: info@riskcontrolnigeria.com");
$pdf->Ln(4);
$pdf->Cell(10);
$pdf->Cell(55,4,"investigation@riskcontrolnigeria.com");
$pdf->Ln(4);
$pdf->WriteHTML("Website: www.riskcontrolnigeria.com");
$pdf->Ln(8);

$pdf->SetFont('Arial','',10);

$pdf->WriteHTML("<b>Our Ref.: {$reference_no}</b>");

$pdf->Ln(8);
$pdf->WriteHTML($completion_date);
if(!empty($client_designation)){
$pdf->Ln(4);
$pdf->WriteHTML("{$client_designation},");
}
if(!empty($client_department)){
$pdf->Ln(4);
$pdf->WriteHTML("{$client_department},");
}
if(!empty($client_address)){
$pdf->Ln(4);
$pdf->WriteHTML("{$client_address},");
}
if(!empty($client_region)){
$pdf->Ln(4);
$pdf->WriteHTML("{$client_region},");
}
if(!empty($client_city)){
$pdf->Ln(4);
$pdf->WriteHTML("{$client_city},");
}
if(!empty($client_state)){
$pdf->Ln(4);
$pdf->WriteHTML("{$client_state}.");
}
$pdf->Ln(8);

$pdf->WriteHTML("{$attention},");
$pdf->Ln(8);

$pdf->WriteHTML("<u><b>RE: {$re}</b></u>");
$pdf->Ln(8);

$pdf->WriteHTML("We request that you kindly confirm the results of the candidates listed below:");
$pdf->Ln(8);

/////////////////==========Ends Update PDF File==============//////////////////

$lineheight = 5;
$table_header = array();
$pdf->SetFont('Arial','B',9);
$table_header[] = array("S/N", "NAMES", "COURSE", "GRADE", "YEAR OF GRAD.", "MATRIC NO.");
$widths = array(7,52,52,25,18,38);
$pdf->plot_table($widths, $lineheight, $table_header);

$pdf->SetFont('Arial','',8);
$widths = array(7,52,52,25,18,38);
$pdf->plot_table($widths, $lineheight, $table_content);
$pdf->Ln(3);

$pdf->SetFont("Arial","",10);

if(!empty($invoice_attachment)){
$pdf->WriteHTML($invoice_attachment);
$pdf->Ln(6);
}

$pdf->WriteHTML("We thank you for your usual cooperation.");
$pdf->Ln(10);

$pdf->WriteHTML("Yours Faithfully,");
$pdf->Ln(5);
$pdf->WriteHTML("<b>For: RISK CONTROL SERVICES NIGERIA LIMITED</b>");
if(!empty($signatory)){ 
$pdf->Ln(5);
$pdf->Cell(50, 10, $pdf->Image($signature_array[0], $pdf->GetX(), $pdf->GetY()), 0, 0, 'L', false);
$pdf->Ln(10);
$pdf->WriteHTML($signatory_name);
$pdf->Ln(5);
$pdf->WriteHTML("<b>{$designation}</b>");
}
$pdf->Ln(5);

$pdf->Output("F","../pdf-reports/{$edit_images}-{$generated_by}-text-data.pdf");
//$pdf->Output("F","../pdf-reports/{$edit_images}-{$generated_by}-text-data.pdf");

////////======Image Files=============/////
$pdf = new PDF();
$pdf->AliasNbPages();

if(!empty($_FILES["request_file"])){ 
include_once("../includes/resize-image.php");

$i=0;
foreach($_FILES["request_file"]["tmp_name"] as $val){ 
$file_name = $_FILES["request_file"]["name"][$i]; 
$file_temp_name = $_FILES["request_file"]["tmp_name"][$i];
$info   = getimagesize($file_temp_name);
$file_size = $_FILES["request_file"]["size"][$i];
$file_error_message = $_FILES["request_file"]["error"][$i];
$file_name_2_array = explode(".", $file_name);
$file_extension = end($file_name_2_array);
$i++;

if (!$file_temp_name) {
    echo "<div class=\"not-success\">ERROR: Please browse for a file before clicking the upload button.</div>";
    exit();
} 
else if($file_size > 20971520) {
    echo "<div class=\"not-success\">ERROR: Your file was larger than 20 Megabytes in size.</div>";
    unlink($file_temp_name);
    exit();
}
else if (!preg_match("/.(gif|GIF|jpg|JPG|png|PNG|jpeg|JPEG|tif|TIF)$/i", $file_name) ) {
     echo "<div class=\"not-success\">ERROR: Your image was not .gif, .jpg, .jpeg, .png or .tif.</div>";
     unlink($file_temp_name);
     exit();
}
else if ($file_error_message == 1) {
    echo "<div class=\"not-success\">ERROR: An error occured while processing the file. Try again.</div>";
    exit();
}
else if ($info[2] != 1 && $info[2] != 2 && $info[2] != 3 && $info[2] != 7 && $info[2] != 8) {
     echo "<div class=\"not-success\">ERROR: Your image was not .gif, .jpg, .jpeg, .png or .tif.</div>";
     exit();
}

$document_group_array = glob("../pdf-reports-images/{$edit_images}-{$generated_by}-img-*.*");
natsort($document_group_array);
$last_on_document_group = end($document_group_array);
$file_full_name = explode(".", $last_on_document_group);
$file_full_name = $file_full_name[2];
$usable_part = explode("-", $file_full_name);
$usable_part = end($usable_part);
$usable_part = $usable_part + 1;

$file_name = "../pdf-reports-images/{$edit_images}-{$generated_by}-img-{$usable_part}.{$file_extension}";
$move_file = move_uploaded_file($file_temp_name, $file_name);
if ($move_file != true) {
    echo "<div class=\"not-success\">ERROR: File not uploaded. Try again.</div>";
    unlink($file_temp_name);
    exit();
}else{

$target_file = $file_name;
$resized_file = $file_name;
image_resize($target_file, $resized_file, $file_extension, 710, 950);

}

}
}

$document_group = glob("../pdf-reports-images/{$edit_images}-{$generated_by}-img-*.*");
natsort($document_group);
if($document_group){
foreach($document_group as $file_name){
$pdf->AddPage("P", "A4");
$pdf->Image($file_name,10,30);
}
}
$pdf->Output("F","../pdf-reports/{$edit_images}-{$generated_by}-image-data.pdf");

require("../pdf/fpdf-merge.php");

$merge = new FPDF_Merge();
$merge->add("../pdf-reports/{$edit_images}-{$generated_by}-text-data.pdf");
$merge->add("../pdf-reports/{$edit_images}-{$generated_by}-image-data.pdf");
$merge->output("../pdf-reports/{$edit_images}-{$generated_by}-both-data.pdf");
unlink("../pdf-reports/{$edit_images}-{$generated_by}-text-data.pdf");
unlink("../pdf-reports/{$edit_images}-{$generated_by}-image-data.pdf");

$error = 0;
$_SESSION["msg"] = "<div class='success'>Verification request images successfully updated.</div>";
redirect("{$directory}{$admin}verification-requests?pn={$pn}&manage_images={$manage_images}");
}
///////////////////////////////////////////////////////////////////////////////


$search_client = search_option("search_client");
$search_completion_date = search_option("search_completion_date");
$request_id = search_option("request_id");
$no_of_rows = search_option("no_of_rows");
$search_start_date = search_option("search_start_date");
$search_end_date = search_option("search_end_date");
$generated_by_me = search_option("generated_by_me", $allow_check);

$where = "WHERE cover_letter_type = '7'";
$where .= (!empty($search_client))?" AND client = '{$search_client}'":"";
$where .= (!empty($search_completion_date))?" AND completion_date = '{$search_completion_date}'":"";
$where .= (!empty($request_id))?" AND id = '{$request_id}'":"";
if(!empty($search_start_date) && !empty($search_end_date)){	
$where .= " AND date_generated BETWEEN '{$search_start_date} 00:00:00' AND '{$search_end_date} 23:59:59'";
}else if(!empty($search_start_date)){
$where .= " AND date_generated >= '{$search_start_date} 00:00:00'";
}else if(!empty($search_end_date)){
$where .= " AND date_generated <= '{$search_end_date} 23:59:59'";
}
$where .= (!empty($generated_by_me))?" AND generated_by = '{$id}'":"";

$result = $db->select($table, $where, "*", "ORDER BY id DESC");

$per_view = 20;
$per_view = (!empty($no_of_rows))?$no_of_rows:$per_view;
$page_link = "{$admin}verification-requests?pn=";
$link_suffix = "";
$style_class = "general-link";
page_numbers();

if(isset($_SESSION["msg"]) && !empty($_SESSION["msg"]) && empty($edit_images) && empty($delete_image)){
echo $_SESSION["msg"];
unset($_SESSION["msg"]);
}

if(isset($_SESSION["message"]) && !empty($_SESSION["message"]) && empty($download)){
echo "<iframe src=\"{$admin}download-report?document=" . $_SESSION["message"] . "\" style=\"width:1px; height:1px;\"></iframe>";
unset($_SESSION["message"]);
}

if(empty($view) && (empty($edit_images)||(!empty($edit_images)&&$error==0)) && (empty($edit)||(!empty($edit)&&$error==0)) && (empty($add)||(!empty($add)&&$error==0)) && empty($manage_images) ){
?>

<div class="page-title">Verification Requests <a href="format/verification-data.xlsx" class="btn gen-btn float-right" style="margin-left:10px;"><i class="fa fa-download" aria-hidden="true"></i> Download Verification Format</a><a href="<?php echo $admin; ?>verification-requests?add=1" class="btn gen-btn general-link float-right">New Verification Request</a></div>

<form action="<?php echo $admin; ?>verification-requests" name="search_form" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
<input type="hidden" name="gh" value="1"> 
<input type="hidden" name="allow_check" value="1"> 
<div class="search-dates">

<div class="col-md-4">
<label for="search_client">Agent</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
<select name="search_client" id="search_client" title="Select an agent" class="form-control js-example-basic-single" style="width:100%">
<option value="">**Select an agent**</option>
<?php 
$result2 = $db->select($table, $where, "DISTINCT client", "ORDER BY client ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$client_id = $row2["client"];
$client_name = in_table("name","reg_users","WHERE id = '{$client_id}'","name");
$client_email = in_table("email","reg_users","WHERE id = '{$client_id}'","email");
echo "<option value='{$client_id}'";
check_selected("search_client", $client_id, $search_client); 
echo ">{$client_name} ({$client_email})</option>";
}
}
?>
</select>
</div>
</div>

<div class="col-md-3">
<label for="search_completion_date">Completion Date</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
<input type="text" name="search_completion_date" id="search_completion_date" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php check_inputted("search_completion_date", $search_completion_date); ?>">
</div>
</div>

<div class="col-md-3">
<label for="request_id">Request ID</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-code" aria-hidden="true"></i></span>
<input type="text" name="request_id" id="request_id" class="form-control only-no" placeholder="E.g. 12" value="<?php check_inputted("request_id", $request_id); ?>">
</div>
</div>

<div class="col-md-2">
<label for="no_of_rows">No. of Rows</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-list" aria-hidden="true"></i></span>
<input type="text" name="no_of_rows" id="no_of_rows" class="form-control only-no" placeholder="E.g. 10" value="<?php check_inputted("no_of_rows", $per_view); ?>">
</div>
</div>

<div class="col-md-3">
<label for="search_start_date">Gen. Start Date</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
<input type="text" name="search_start_date" id="search_start_date" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php check_inputted("search_start_date", $search_start_date); ?>">
</div>
</div>

<div class="col-md-3">
<label for="search_end_date">Gen. End Date</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
<input type="text" name="search_end_date" id="search_end_date" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php check_inputted("search_end_date", $search_end_date); ?>">
</div>
</div>


<div class="col-md-4">
<br />
<div class="form-group input-group">
<span class="input-group-addon"><input type="checkbox" name="generated_by_me" id="generated_by_me" value="<?php echo $id; ?>" <?php echo (!empty($generated_by_me))?"checked":""; ?>></span>
<label for="generated_by_me"> &nbsp;&nbsp; Generated by me</label>
</div>
</div>

<div class="col-md-2">
<br />
<button type="submit" class="btn gen-btn"><i class="fa fa-search"></i> Search</button>
</div> 

</div>
</form>

<?php
$d = 0;

$offset = ($per_view * $pn) - $per_view;

$result = $db->select($table, $where, "*", "ORDER BY id DESC", "LIMIT {$offset},{$per_view}");

if(count_rows($result) > 0){
?>
<form action="<?php echo $admin; ?>verification-requests" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data" style="overflow-x:auto;">
<input type="hidden" name="pn" value="<?php echo $pn; ?>"> 
<input type="hidden" name="gh" value="1"> 
<input type="hidden" name="delete" value="1"> 
<table class="table table-striped table-hover">
<thead>
<tr class="gen-title">
<th>Request ID</th>
<th>Date Generated</th>
<th>Agent</th>
<th>Completion Date</th>
<th style="width:70px;">Option</th>
<th style="width:70px;">Details</th>
<th style="width:70px;">Images</th>
<th style="width:70px;">Action</th>
<th style="width:30px;"><input type="checkbox" name="sel_all" id="delG" class="sel-group" value=""></th>
</tr>
</thead>
<tbody>
<?php
while($row = fetch_data($result)){
$letter_id = $row["id"];
$client = $row["client"];
$attention = $row["attention"];
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$client_email = in_table("email","reg_users","WHERE id = '{$client}'","email");
$client_details = $client_name . "<br>" . break_long($client_email, "(", ")");
$completion_date = ($row["completion_date"] != "0000-00-00")?min_sub_date($row["completion_date"]):"";
$date_generated = ($row["date_generated"] != "0000-00-00 00:00:00")?min_full_date($row["date_generated"]):"";
?>
<tr>
<td><?php echo $letter_id; ?></td>
<td><?php echo $date_generated; ?></td>
<td><?php echo $client_details; ?></td>
<td><?php echo $completion_date; ?></td>
<td><a href="<?php echo $admin; ?>verification-requests?edit=<?php echo $letter_id; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link" title="Edit verification request #<?php echo $letter_id; ?>"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></td>
<td><a href="<?php echo $admin; ?>verification-requests?view=<?php echo $letter_id; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link" title="View verification request #<?php echo $letter_id; ?>"><i class="fa fa-eye" aria-hidden="true"></i> View</a></td>
<td><a href="<?php echo $admin; ?>verification-requests?manage_images=<?php echo $letter_id; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn" title="Manage verification request #<?php echo $letter_id; ?> images"><i class="fa fa-file-image-o" aria-hidden="true"></i> Manage</a></td>
<td><a href="<?php echo $admin; ?>verification-requests?download=<?php echo $letter_id; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn" title="Download verification request #<?php echo $letter_id; ?>"><i class="fa fa-download" aria-hidden="true"></i> Download</a></td>
<td><input type="checkbox" name="del[<?php echo $d; ?>]" id="del<?php echo $d; ?>" class="delG" value="<?php echo $letter_id; ?>"></td>
</tr>
<?php 
$d++;
}
?>
<tr><td colspan="9"><input class="sub-del" type="submit" value=" "><button type="button" class="btn del-btn gen-btn float-right"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete selected request(s)</button></td></tr>
</tbody>
</table>
</form>
<?php
echo ($last_page>1)?"<div class=\"page-nos\">" . $center_pages . "</div>":"";
}else{
echo "<div class='not-success'>No verification requests found at the moment.</div>";
}

}

/////============View Images=============////
if(!empty($manage_images)){
?>
<div class="back">
<a href="<?php echo $admin; ?>verification-requests?pn=<?php echo $pn; ?>" class="btn gen-btn"><i class="fa fa-arrow-left"></i> Back to view verification requests</a>
</div>

<div class="page-title">View Verification Request Images</div><br>
<?php
$result = $db->select($table, "WHERE id='$manage_images'", "*");

if(count_rows($result) == 1){
$row = fetch_data($result);

$client = $row["client"];
$completion_date = ($row["completion_date"] != "0000-00-00")?$row["completion_date"]:"";
$attention = $row["attention"];
$reference_no = $row["reference_no"];
$client_department = $row["client_department"];
$client_designation = $row["client_designation"];
$re = $row["re"];
$invoice_attachment = $row["invoice_attachment"];
$signatory = $row["signatory"];
$details_category = $row["details_category"];$client = $row["client"];
$completion_date = ($row["completion_date"] != "0000-00-00")?$row["completion_date"]:"";
$attention = $row["attention"];
$reference_no = $row["reference_no"];
$client_department = $row["client_department"];
$client_designation = $row["client_designation"];
$re = $row["re"];
$invoice_attachment = $row["invoice_attachment"];
$signatory = $row["signatory"];
$details_category = $row["details_category"];
$generated_by = $row["generated_by"];

$file_array = glob("../pdf-reports-images/{$manage_images}-{$generated_by}-img-*");
natsort($file_array);
if($file_array){
echo "<div>";
$count_img = 0;
foreach($file_array as $file_name){
$count_img++;
?>
<div class="col-md-9"><br>
<img src="pdf-reports-images/<?php echo $file_name; ?>">
</div>
<div class="col-md-3"><br>
<form action="<?php echo $admin; ?>verification-requests" class="form-<?php echo $count_img; ?>" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
<input type="hidden" name="manage_images" value="<?php echo $manage_images; ?>"> 
<input type="hidden" name="pn" value="<?php echo $pn; ?>"> 
<input type="hidden" name="delete_image" value="<?php echo $file_name; ?>"> 

<input type="hidden" name="reference_no" value="<?php echo $reference_no; ?>">
<input type="hidden" name="completion_date" value="<?php echo $completion_date; ?>">
<input type="hidden" name="client_designation" value="<?php echo $client_designation; ?>" >
<input type="hidden" name="client_department" value="<?php echo $client_department; ?>" >
<input type="hidden" name="client" value="<?php echo $client; ?>" >
<input type="hidden" name="attention" value="<?php echo $attention; ?>" >
<input type="hidden" name="re" value="<?php echo $re; ?>" >
<?php 
if(!empty($details_category)){ 
$details_category_array = explode("-/-/",$details_category);

foreach($details_category_array as $value){
if(!empty($value)){
$value_array = explode("+*+*",$value);
$names = $value_array[0];
$course = $value_array[1];
$grade = $value_array[2];
$year_of_graduation = $value_array[3];
$matric_no = $value_array[4];
?>
<input type="hidden" name="names[]" value="<?php echo $names; ?>" >
<input type="hidden" name="course[]" value="<?php echo $course; ?>" >
<input type="hidden" name="grade[]" value="<?php echo $grade; ?>" >
<input type="hidden" name="year_of_graduation[]" value="<?php echo $year_of_graduation; ?>" >
<input type="hidden" name="matric_no[]" value="<?php echo $matric_no; ?>" >
<?php
}
}

}
?>
<input type="hidden" name="invoice_attachment" value="<?php echo $invoice_attachment; ?>" >
<input type="hidden" name="signatory" value="<?php echo $signatory; ?>" >

<button type="button" class="btn gen-btn image-btn" lang="<?php echo $count_img; ?>" id="form-<?php echo $count_img; ?>">Delete image #<?php echo $count_img; ?></button>
</form>
</div>
<?php
}
echo "</div>";
}
?>
<form action="<?php echo $admin; ?>verification-requests" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
<input type="hidden" name="manage_images" value="<?php echo $manage_images; ?>"> 
<input type="hidden" name="pn" value="<?php echo $pn; ?>"> 
<input type="hidden" name="edit_images" value="<?php echo $manage_images; ?>"> 

<input type="hidden" name="reference_no" value="<?php echo $reference_no; ?>">
<input type="hidden" name="completion_date" value="<?php echo $completion_date; ?>">
<input type="hidden" name="client_designation" value="<?php echo $client_designation; ?>" >
<input type="hidden" name="client_department" value="<?php echo $client_department; ?>" >
<input type="hidden" name="client" value="<?php echo $client; ?>" >
<input type="hidden" name="attention" value="<?php echo $attention; ?>" >
<input type="hidden" name="re" value="<?php echo $re; ?>" >
<?php 
if(!empty($details_category)){ 
$details_category_array = explode("-/-/",$details_category);

foreach($details_category_array as $value){
if(!empty($value)){
$value_array = explode("+*+*",$value);
$names = $value_array[0];
$course = $value_array[1];
$grade = $value_array[2];
$year_of_graduation = $value_array[3];
$matric_no = $value_array[4];
?>
<input type="hidden" name="names[]" value="<?php echo $names; ?>" >
<input type="hidden" name="course[]" value="<?php echo $course; ?>" >
<input type="hidden" name="grade[]" value="<?php echo $grade; ?>" >
<input type="hidden" name="year_of_graduation[]" value="<?php echo $year_of_graduation; ?>" >
<input type="hidden" name="matric_no[]" value="<?php echo $matric_no; ?>" >
<?php
}
}

}
?>
<input type="hidden" name="invoice_attachment" value="<?php echo $invoice_attachment; ?>" >
<input type="hidden" name="signatory" value="<?php echo $signatory; ?>" >

<table class="table table-striped table-hover list-items">

<tr>
<th class="gen-title" style="width:30px">S/N</th>
<th class="gen-title">Attachment(s)</th>
<th class="gen-title" style="width:30px"><i class="fa fa-minus"></i></th>
</tr>

<tr class="sub-row" id="row1">
<td class="sub-cell" id="td1">1</td>
<td><input type="file" name="request_file[]" id="request_file" class="form-control" value=""></td>
<td></td>
</tr>

</table>
                     
<div class="submit-div col-sm-12">
<button type="button" class="btn add-new-list-item gen-btn float-left"><i class="fa fa-plus"></i> Add</button>

<button class="btn gen-btn float-right"><i class="fa fa-upload"></i> Upload Imges</button>
</div>

</form>

<script>
<!--

var c = 1;

$(".add-new-list-item").click(function(){
c++;
$(".list-items").append("<tr class=\"sub-row\" id=\"row" + c + "\"><td class=\"sub-cell\" id=\"td" + c + "\">" + c + "</td><td><input type=\"file\" name=\"request_file[]\" id=\"request_file\" class=\"form-control\" value=\"\"></td><td><button type=\"button\" class=\"btn gen-btn del-sub-cat\" lang=\"row" + c + "\" onclick=\"javascript: delete_sub(this.lang);\"><i class=\"fa fa-minus\"></i></button></td></tr>");
});

function delete_sub(what){
document.getElementById(what).outerHTML = "";
var sub_row = document.getElementsByClassName("sub-row");
var sub_cell = document.getElementsByClassName("sub-cell");
var del_sub_cat = document.getElementsByClassName("del-sub-cat");
var i;
for(i = 0; i < sub_row.length; i++){
c = i+1;
d = i-1;
sub_row[i].id = "row" + c;
sub_cell[i].id = "td" + c;
sub_cell[i].innerHTML = c;
if(i > 0){
del_sub_cat[d].lang = "row" + c;
}
}
}


$(".image-btn").click(function(){
var this_btn_id = $(this).attr("id");
var this_btn_lang = $(this).attr("lang");

swal({
  title: "Deletion Confirmation",
  text: "Are you sure you want to delete image #" + this_btn_lang,
  type: "warning",
  showCancelButton: true,
  confirmButtonColor: "#DD6B55",
  confirmButtonText: "Yes",
  closeOnConfirm: true
},
function(isConfirm){
  if (isConfirm) {  
$("." + this_btn_id).submit();
  } else {
return false;
  }
});

});
//-->
</script>

<?php
}
}

/////============View Report=============////
if(!empty($view) && (empty($edit) || (!empty($edit) && $error == 0))){
$result = $db->select($table, "WHERE id='$view'", "*");

if(count_rows($result) == 1){
$row = fetch_data($result);
$client = $row["client"];
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$client_email = in_table("email","reg_users","WHERE id = '{$client}'","email");
$client_details = $client_name . " (" . $client_email . ")";
$completion_date = ($row["completion_date"] != "0000-00-00")?sub_date($row["completion_date"]):"";
$reference_no = $row["reference_no"];
$client_designation = $row["client_designation"];
$client_department = $row["client_department"];
$attention = $row["attention"];
$re = $row["re"];
$invoice_attachment = $row["invoice_attachment"];
$signatory = $row["signatory"];
$signatory_name = in_table("name","reg_users","WHERE id = '{$signatory}'","name");
$signatory_name = (!empty($signatory_name))?$signatory_name:"";
$signatory_email = in_table("email","reg_users","WHERE id = '{$signatory}'","email");
$signatory_email = (!empty($signatory_email))?$signatory_email:"";
$details_category = $row["details_category"];
$generated_by = $row["generated_by"];
$generated_by_name = in_table("name","reg_users","WHERE id = '{$generated_by}'","name");
$generated_by_email = in_table("email","reg_users","WHERE id = '{$generated_by}'","email");
$generated_by_details = $generated_by_name . " (" . $generated_by_email . ")";
$date_generated = ($row["date_generated"] != "0000-00-00 00:00:00")?full_date($row["date_generated"]):"";
$updated_by = $row["updated_by"];
$updated_by_name = in_table("name","reg_users","WHERE id = '{$updated_by}'","name");
$updated_by_email = in_table("email","reg_users","WHERE id = '{$updated_by}'","email");
$updated_by_details = (!empty($updated_by))?$updated_by_name . " (" . $updated_by_email . ")":"";
$date_updated = ($row["date_updated"] != "0000-00-00 00:00:00")?full_date($row["date_updated"]):"";
?>
<style>
<!--
div table thead tr th, div table tr th, div table tbody tr td, div table tr td{
dtext-align:left !important;
}
.details-table *{
text-align:left !important;
}
-->
</style>
<div class="reply-content-wrapper ">

<div class="back"><a href="<?php echo $admin; ?>verification-requests?pn=<?php echo $pn; ?>" class="btn gen-btn general-link"><i class="fa fa-arrow-left"></i> Back to verification requests</a></div>

<div class="view-wrapper ">

<div class="view-header ">
<div class="header-img"><img src="images/post.jpg" ></div>
<div class="header-content">
<div class="view-title">Agent:  <b><?php echo $client_details; ?></b> &nbsp;&nbsp; Request ID: <b><?php echo $view; ?></b></div>
<div class="view-title-details">Generated on <b><?php echo $date_generated; ?></b> by <b><?php echo $generated_by_details; ?></b></div>
</div>
</div>

<div class="view-content">

<table class="table table-striped table-hover details-table"><tbody>
<tr><th style="width:170px;">Agent Designation:</th><td><?php echo $client_designation; ?></td><th style="width:150px;">Agent Dept.:</th><td><?php echo $client_department; ?></td></tr>
<tr><th>Ref. No.:</th><td><?php echo $reference_no; ?></td><th>Salutation:</th><td><?php echo $attention; ?></td></tr>
<tr><th>RE:</th><td><?php echo $re; ?></td><th>Invoice Attachment:</th><td><?php echo $invoice_attachment; ?></td></tr>
<?php if(!empty($signatory)){ ?>
<tr><th>Completion Date:</th><td><?php echo $completion_date; ?></td><th>Signatory Details:</th><td><?php echo "$signatory_name ({$signatory_email})"; ?></td></tr>
<?php } ?>

<?php if(!empty($updated_by)){ ?>
<tr><th>Last Update:</th><td><?php echo $date_updated; ?></td><th>Updated By:</th><td><?php echo $updated_by_details; ?></td></tr>
<?php } ?>
</tbody>
</table>

<?php 
$c = 0;
if(!empty($details_category)){ 
$details_category_array = explode("-/-/",$details_category);
?>
<div class="body-header">Candidates</div>

<table class="table table-striped table-hover">
<thead>
<tr>
<th>S/N</th>
<th>NAMES</th>
<th>COURSE</th>
<th>GRADE</th>
<th>YEAR OF GRAD.</th>
<th>MATRIC NO.</th>
</tr>
</thead>
<tbody>
<?php
foreach($details_category_array as $value){
if(!empty($value)){
$c++;
$value_array = explode("+*+*",$value);
$names = $value_array[0];
$course = $value_array[1];
$grade = $value_array[2];
$year_of_graduation = $value_array[3];
$matric_no = $value_array[4];
?>
<tr>
<td><?php echo $c; ?></td>
<td><?php echo $names; ?></td>
<td><?php echo $course; ?></td>
<td><?php echo $grade; ?></td>
<td><?php echo $year_of_graduation; ?></td>
<td><?php echo $matric_no; ?></td>
</tr>
<?php
}
}
?>
</tbody></table>
<?php
}
?>

<div>
<form action="<?php echo $admin; ?>verification-requests" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
<input type="hidden" name="gh" value="1"> 
<input type="hidden" name="view" value="<?php echo $view; ?>"> 
<input type="hidden" name="pn" value="<?php echo $pn; ?>"> 
<input type="hidden" name="resend" value="<?php echo $view; ?>"> 
<input type="hidden" name="client" value="<?php echo $client; ?>"> 

<button type="submit" class="btn gen-btn float-left"><i class="fa fa-envelope"></i> Resend mail</button> 
<a href="<?php echo $admin; ?>verification-requests?edit=<?php echo $view; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link float-right" title="Edit this cover letter"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
</form>
</div>

</div>

</div>
</div>

<?php
}else{
echo "<div class='not-success'>This verification request does not exist.</div>";
}
}

////==============Add or Edit Cover Letter=============//////
if((!empty($add) || !empty($edit)) && $error == 1){

$client = $completion_date = $attention = $reference_no = $client_department = $client_designation = $re = $invoice_attachment = $signatory = $details_category = "";
if(!empty($edit)){
$result = $db->select("cover_letters", "WHERE id='$edit'", "*", "");
if(count_rows($result) == 1){
$row = fetch_data($result);
$client = $row["client"];
$completion_date = ($row["completion_date"] != "0000-00-00")?$row["completion_date"]:"";
$attention = $row["attention"];
$reference_no = $row["reference_no"];
$client_department = $row["client_department"];
$client_designation = $row["client_designation"];
$re = $row["re"];
$invoice_attachment = $row["invoice_attachment"];
$signatory = $row["signatory"];
$details_category = $row["details_category"];
}
}

$field_name = (!empty($edit))?"edit":"add";
$field_value = (!empty($edit))?$edit:1;
$action_title = (!empty($edit))?"Edit":"Add New";
?>

<div class="back">
<a href="<?php echo $admin; ?>verification-requests?view=<?php echo $edit; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link"><i class="fa fa-arrow-left"></i> Back to view verification request</a>
</div>

<div class="page-title"><?php echo $action_title; ?> Verification Request</div>

<?php if(!empty($edit)){ ?>

<form action="<?php echo $admin; ?>verification-requests" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data" style="border:1px solid #eee;">
<input type="hidden" name="gh" value="1"> 
<input type="hidden" name="edit" value="<?php echo $edit; ?>"> 
<input type="hidden" name="pn" value="<?php echo $pn; ?>"> 
<input type="hidden" name="view" value="<?php echo $edit; ?>"> 

<?php }else{ ?>

<form action="<?php echo $admin; ?>print-report" method="post" runat="server" autocomplete="off" enctype="multipart/form-data" target="_blank">
<input type="hidden" name="verification_request" value="1"> 
<?php } ?>

<div class="col-sm-12">
<label for="reference_no">Reference</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-code"></i></span>
<input type="text" name="reference_no" id="reference_no" class="form-control" placeholder="Our ref." value="<?php check_inputted("reference_no", $reference_no); ?>">
</div>
</div>

<div class="col-sm-12">
<label for="completion_date">Completion Date*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
<input type="text" name="completion_date" id="completion_date" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php check_inputted("completion_date", $completion_date); ?>" required>
</div>
</div>

<div class="col-sm-12">
<label for="client_designation">Agent Designation</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user"></i></span>
<input type="text" name="client_designation" id="client_designation" class="form-control" placeholder="E.g. The Registrar" value="<?php check_inputted("client_designation", $client_designation); ?>" >
</div>
</div>

<div class="col-sm-12">
<label for="client_department">Agent Department</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user"></i></span>
<input type="text" name="client_department" id="client_department" class="form-control" placeholder="E.g. Exams and Records" value="<?php check_inputted("client_department", $client_department); ?>" >
</div>
</div>

<div class="col-sm-12">
<label for="client">Agent*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user"></i></span>
<select name="client" id="client" title="Select an agent" class="form-control js-example-basic-single" style="width:100%" required>
<option value="">**Select an agent**</option>
<?php 
$result2 = $db->select("reg_users", "WHERE agent = '1'", "DISTINCT *", "ORDER BY id ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$client_id = $row2["id"];
$client_name = $row2["name"];
$client_email = $row2["email"];
echo "<option value='{$client_id}'";
check_selected("client", $client_id, $client); 
echo ">{$client_name} ({$client_email})</option>";
}
}
?>
</select>
</div>
</div>

<div class="col-sm-12">
<label for="attention">Salutation*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-bullhorn"></i></span>
<input type="text" name="attention" id="attention" class="form-control" placeholder="E.g. Dear Sir" value="<?php check_inputted("attention", $attention); ?>" required>
</div>
</div>

<div class="col-sm-12">
<label for="re">RE*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-globe"></i></span>
<input type="text" name="re" id="re" class="form-control" placeholder="RE" value="<?php check_inputted("re", $re); ?>" required>
</div>
</div>

<table class="table table-striped table-hover right-summary-list">

<tr>
<th class="gen-title" style="width:30px">S/N</th>
<th class="gen-title">Names</th>
<th class="gen-title">Course</th>
<th class="gen-title">Grade</th>
<th class="gen-title">Year of Grad</th>
<th class="gen-title">Matric No.</th>
<th class="gen-title" style="width:30px"><i class="fa fa-minus"></i></th>
</tr>

<?php 
$a = 0;
if(!empty($details_category)){ 
$details_category_array = explode("-/-/",$details_category);

foreach($details_category_array as $value){
if(!empty($value)){
$a++;
$value_array = explode("+*+*",$value);
$names = $value_array[0];
$course = $value_array[1];
$grade = $value_array[2];
$year_of_graduation = $value_array[3];
$matric_no = $value_array[4];
?>

<tr class="sub-right-summary-row" id="right-summary-row<?php echo $a; ?>">
<td class="sub-right-summary-cell" id="right-summary-td<?php echo $a; ?>"><?php echo $a; ?></td>
<td><input type="text" name="names[]" id="names" class="form-control" placeholder="Full Name" value="<?php echo $names; ?>"></td>
<td><input type="text" name="course[]" id="course" class="form-control" placeholder="E.g. ACCOUNTING" value="<?php echo $course; ?>"></td>
<td><input type="text" name="grade[]" id="grade" class="form-control" placeholder="E.g. DISTINCTION" value="<?php echo $grade; ?>"></td>
<td><input type="text" name="year_of_graduation[]" id="year_of_graduation" class="form-control" placeholder="E.g. 2015" value="<?php echo $year_of_graduation; ?>"></td>
<td><input type="text" name="matric_no[]" id="matric_no" class="form-control" placeholder="E.g. 20145525151516" value="<?php echo $matric_no; ?>"></td>
<td>
<?php 
if($a > 1){
?>
<button type="button" class="btn gen-btn del-sub-right-summary-cat" lang="right-summary-row<?php echo $a; ?>" onclick="javascript: delete_right_summary(this.lang);"><i class="fa fa-minus"></i></button>
<?php
}
?>
</td>
</tr>
<?php
}
}

}else{ 
$a++;
?>
<tr class="sub-right-summary-row" id="right-summary-row<?php echo $a; ?>">
<td class="sub-right-summary-cell" id="right-summary-td<?php echo $a; ?>"><?php echo $a; ?></td>
<td><input type="text" name="names[]" id="names" class="form-control" placeholder="Full Name" value=""></td>
<td><input type="text" name="course[]" id="course" class="form-control" placeholder="E.g. ACCOUNTING" value=""></td>
<td><input type="text" name="grade[]" id="grade" class="form-control" placeholder="E.g. DISTINCTION" value=""></td>
<td><input type="text" name="year_of_graduation[]" id="year_of_graduation" class="form-control" placeholder="E.g. 2015" value=""></td>
<td><input type="text" name="matric_no[]" id="matric_no" class="form-control" placeholder="E.g. 20145525151516" value=""></td>
</tr>
<?php } ?>

</table>
<div class="col-sm-12">
<button type="button" class="btn add-new-right-summary gen-btn float-left"><i class="fa fa-plus"></i> Add</button>
</div>

<p>&nbsp;</p>

<?php if(!empty($add)){ ?>
<div class="col-sm-12">
<div class="form-group input-group">
<span class="input-group-addon"><label for="ufile_id"><i class="fa fa-paperclip"></i> Attach Excel File (Format: .xlsx)</label></span>
<input type="file" name="ufile" id="ufile_id" class="form-control">
</div>
</div>
<?php } ?>

<div class="col-sm-12">
<label for="invoice_attachment">Invoice Attachment</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-file-text"></i></span>
<input type="text" name="invoice_attachment" id="invoice_attachment" class="form-control" placeholder="Note on file attachment" value="<?php echo (!empty($invoice_attachment))?check_inputted("invoice_attachment", $invoice_attachment):"Attached are copies of the credentials submitted by the candidates for your urgent response please."; ?>">
</div>
</div>

<div class="col-sm-12">
<label for="signatory">Signatory</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user"></i></span>
<select name="signatory" id="signatory" title="Select a signatory" class="form-control">
<option value="">**Select a signatory**</option>
<?php 
$result2 = $db->select("reg_users", "WHERE admin = '1' AND signature = '1'", "*", "ORDER BY name ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$signatory_id = $row2["id"];
$signatory_name = $row2["name"];
echo "<option value='{$signatory_id}'";
check_selected("signatory", $signatory_id, $signatory); 
echo ">{$signatory_name}</option>";
}
}
?>
</select>
</div>
</div>

<?php if(empty($edit)){ ?>

<table class="table table-striped table-hover list-items">

<tr>
<th class="gen-title" style="width:30px">S/N</th>
<th class="gen-title">Attachment(s)</th>
<th class="gen-title" style="width:30px"><i class="fa fa-minus"></i></th>
</tr>

<tr class="sub-row" id="row1">
<td class="sub-cell" id="td1">1</td>
<td><input type="file" name="request_file[]" id="request_file" class="form-control" value=""></td>
<td></td>
</tr>

</table>

<?php } ?>                     

<p>&nbsp;</p>
                     
<div class="submit-div col-sm-12">
<button type="button" class="btn add-new-list-item gen-btn float-left"><i class="fa fa-plus"></i> Add</button>

<button class="btn gen-btn float-right" name="update">
<?php if(!empty($add)){ ?>
<i class="fa fa-file-text"></i> Generate Letter
<?php }else{ ?>
<i class="fa fa-upload"></i> Save Letter
<?php } ?>
</button>
</div>

</form>


<script>
<!--

var a = <?php echo $a; ?>;
var c = 1;

$(".add-new-right-summary").click(function(){
a++;
$(".right-summary-list").append("<tr class=\"sub-right-summary-row\" id=\"right-summary-row" + a + "\"><td class=\"sub-right-summary-cell\" id=\"right-summary-td" + a + "\">" + a + "</td><td><input type=\"text\" name=\"names[]\" id=\"names\" class=\"form-control\" placeholder=\"Full Name\" value=\"\"></td><td><input type=\"text\" name=\"course[]\" id=\"course\" class=\"form-control\" placeholder=\"E.g. ACCOUNTING\" value=\"\"></td><td><input type=\"text\" name=\"grade[]\" id=\"grade\" class=\"form-control\" placeholder=\"E.g. DISTINCTION\" value=\"\"></td><td><input type=\"text\" name=\"year_of_graduation[]\" id=\"year_of_graduation\" class=\"form-control\" placeholder=\"E.g. 2015\" value=\"\"></td><td><input type=\"text\" name=\"matric_no[]\" id=\"matric_no\" class=\"form-control\" placeholder=\"E.g. 20145525151516\" value=\"\"></td><td><button type=\"button\" class=\"btn gen-btn del-sub-right-summary-cat\" lang=\"right-summary-row" + a + "\" onclick=\"javascript: delete_right_summary(this.lang);\"><i class=\"fa fa-minus\"></i></button></td></tr>");
});

$(".add-new-list-item").click(function(){
c++;
$(".list-items").append("<tr class=\"sub-row\" id=\"row" + c + "\"><td class=\"sub-cell\" id=\"td" + c + "\">" + c + "</td><td><input type=\"file\" name=\"request_file[]\" id=\"request_file\" class=\"form-control\" value=\"\"></td><td><button type=\"button\" class=\"btn gen-btn del-sub-cat\" lang=\"row" + c + "\" onclick=\"javascript: delete_sub(this.lang);\"><i class=\"fa fa-minus\"></i></button></td></tr>");
});

function delete_right_summary(what){
document.getElementById(what).outerHTML = "";
var sub_summary_row = document.getElementsByClassName("sub-right-summary-row");
var sub_summary_cell = document.getElementsByClassName("sub-right-summary-cell");
var del_sub_summary_cat = document.getElementsByClassName("del-sub-right-summary-cat");
var i;
for(i = 0; i < sub_summary_row.length; i++){
a = i+1;
b = i-1;
sub_summary_row[i].id = "right-summary-row" + a;
sub_summary_cell[i].id = "right-summary-td" + a;
sub_summary_cell[i].innerHTML = a;
if(i > 0){
del_sub_summary_cat[b].lang = "right-summary-row" + a;
}
}
}

function delete_sub(what){
document.getElementById(what).outerHTML = "";
var sub_row = document.getElementsByClassName("sub-row");
var sub_cell = document.getElementsByClassName("sub-cell");
var del_sub_cat = document.getElementsByClassName("del-sub-cat");
var i;
for(i = 0; i < sub_row.length; i++){
c = i+1;
d = i-1;
sub_row[i].id = "row" + c;
sub_cell[i].id = "td" + c;
sub_cell[i].innerHTML = c;
if(i > 0){
del_sub_cat[d].lang = "row" + c;
}
}
}
//-->
</script>

<?php } ?>

<script>
<!--
var conf_text = "verification request";
//-->
</script>

<script src="js/general-form.js"></script>

<?php if(!isset($_REQUEST["gh"])){ ?>

</div>
</div>

</div>

<?php require_once("../includes/portal-footer.php"); } ?>