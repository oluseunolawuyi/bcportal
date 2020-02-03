<?php 
ini_set('upload_max_filesize', '1000M');
//echo ini_get('upload_max_filesize'), ", " , ini_get('post_max_size');

ini_set("max_execution_time", 600); //300 seconds = 5 minutes

include_once("../includes/gen-header.php"); 

function detectCurrUserBrowser($a,$b,$c){
$msie = stripos($_SERVER["HTTP_USER_AGENT"], "msie") ? true : false;
if($msie){
$msiePosition = stripos($_SERVER["HTTP_USER_AGENT"], "msie");
$msiePositionNew = $msiePosition+5;
$versionNumber = substr($_SERVER["HTTP_USER_AGENT"],$msiePositionNew,1);
if($versionNumber <= $c){
echo $a;
}
else{
echo $b;
}
}
else{
echo $b;
}
}

if(!isset($_SESSION["login"])){
$_SESSION["msg"] = "<div class='success'>You are not logged in. Kindly log in to continue...</div>";
redirect($directory);
}

if(isset($_REQUEST["logout"])){
unset($_SESSION["login"]);
$_SESSION["msg"] = "<div class='success'>You are successfully logged out. Kindly log in to continue...</div>";

$db->query("UPDATE reg_users SET logged_in = '0', last_login = '{$date_time}' WHERE id = '{$id}'");

redirect($directory);
}

$last_login = in_table("last_login","reg_users","WHERE id = '$id'","last_login");
$blocked = in_table("blocked","reg_users","WHERE id = '$id'","blocked");

if($blocked == 1){
$_SESSION["msg"] = "<div class='not-success'>Hi {$user_name}! Your account is declined. Kindly contact the admin <a href='{$parent_domain}privates/contact-us'>HERE</a>.</div>";
redirect($directory);
}

$send_request = np_input("send_request");
$send_reminder = np_input("send_reminder");

$cover_letter_type = tp_input("cover_letter_type");
$request_id = np_input("request_id");
$reference_no = tp_input("reference_no");
$completion_date = tp_input("completion_date");
$client = tp_input("client");
$client_designation = tp_input("client_designation");
$client_department = tp_input("client_department");
$attention = tp_input("attention");
$re = tp_input("re");
$invoice_attachment = tp_input("invoice_attachment");
$signatory = tp_input("signatory");
$details_category = tp_input("details_category");

///////////////=== Save and Send Request ===///////////////
if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($cover_letter_type) && !empty($send_request)){

$data_array = array(
"cover_letter_type" => "'$cover_letter_type'",
"client" => "'$client'",
"completion_date" => "'$completion_date'",
"attention" => "'$attention'",
"reference_no" => "'$reference_no'",
"client_designation" => "'$client_designation'",
"client_department" => "'$client_department'",
"re" => "'$re'",
"invoice_attachment" => "'$invoice_attachment'",
"signatory" => "'$signatory'",
"details_category" => "'$details_category'",
"generated_by" => "'$id'",
"date_generated" => "'$date_time'"
);
$db->insert($data_array, "cover_letters");

$request_id = in_table("id","cover_letters","WHERE generated_by = '{$id}' AND date_generated = '{$date_time}'","id");
$client_email = in_table("email","reg_users","WHERE id = '{$client}'","email");
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");

$i=0;
foreach (glob("../gen-temp/{$id}-temp-*.*") as $filename) {
$i++;
$file_name_2_array = explode(".", $filename);
$file_extension = end($file_name_2_array);
rename_single_file($filename, "../pdf-reports-images/", "{$request_id}-{$id}-img-{$i}.{$file_extension}");
unlink($filename);
}

////////======Medge Files=============/////
require("../pdf/fpdf-merge.php");

$merge = new FPDF_Merge();
$merge->add("../pdf-reports/{$id}-temp-text-data.pdf");
$merge->add("../pdf-reports/{$id}-temp-image-data.pdf");
$merge->output("../pdf-reports/{$request_id}-{$id}-both-data.pdf");

///=====Send mail==================//
$to = $client_email;
$subject = "Verification Request [#{$request_id}]";
$message = "<p>Dear {$client_name},</p>
<p>Please find attached, the pdf file containing the verification details.</p>
<p>Thank you.</p>
<p>Warm regards.</p>";
$from = "no-reply@riskcontrolnigeria.com";
$from_name = $full_gen_name;
$files = array("../pdf-reports/{$request_id}-{$id}-both-data.pdf");
$html_content = message_template();
$send_att = multi_attach_mail($to,$subject,$html_content,$from,$from_name,$files);
if($send_att){
$_SESSION["msg"] = "<div class=\"success\">Verification request successfully sent</div>";
$file_array = glob("../pdf-reports/{$id}-temp-*.*");
if($file_array){
foreach($file_array as $filename){
unlink($filename);
}
}

}
///======================================================//
redirect("{$directory}{$admin}print-report");
}
///////////////////////////////////////////


///////////////=== Save and Send Reminder ===///////////////
if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($cover_letter_type) && !empty($send_reminder)){

$data_array = array(
"request_id" => "'$request_id'",
"cover_letter_type" => "'$cover_letter_type'",
"client" => "'$client'",
"completion_date" => "'$completion_date'",
"attention" => "'$attention'",
"reference_no" => "'$reference_no'",
"client_designation" => "'$client_designation'",
"client_department" => "'$client_department'",
"re" => "'$re'",
"invoice_attachment" => "'$invoice_attachment'",
"signatory" => "'$signatory'",
"details_category" => "'$details_category'",
"generated_by" => "'$id'",
"date_generated" => "'$date_time'"
);
$db->insert($data_array, "cover_letters");

$reminder_id = in_table("id","cover_letters","WHERE generated_by = '{$id}' AND date_generated = '{$date_time}'","id");
$client_email = in_table("email","reg_users","WHERE id = '{$client}'","email");
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");

$i=0;
foreach (glob("../gen-temp/{$id}-temp-*.*") as $filename) {
$i++;
$file_name_2_array = explode(".", $filename);
$file_extension = end($file_name_2_array);
rename_single_file($filename, "../pdf-reports-images/", "{$reminder_id}-{$id}-img-{$i}.{$file_extension}");
unlink($filename);
}

////////======Medge Files=============/////
require("../pdf/fpdf-merge.php");

$merge = new FPDF_Merge();
$merge->add("../pdf-reports/{$id}-temp-text-data.pdf");
$merge->add("../pdf-reports/{$id}-temp-image-data.pdf");
$merge->output("../pdf-reports/{$reminder_id}-{$id}-both-data.pdf");

///=====Send mail==================//
$to = $client_email;
$subject = "Verification Reminder #{$reminder_id} on Request #{$request_id}";
$message = "<p>Dear {$client_name},</p>
<p>Please find attached, the pdf file containing the verification details.</p>
<p>Thank you.</p>
<p>Warm regards.</p>";
$from = "no-reply@riskcontrolnigeria.com";
$from_name = $full_gen_name;
$files = array("../pdf-reports/{$reminder_id}-{$id}-both-data.pdf");
$html_content = message_template();
$send_att = multi_attach_mail($to,$subject,$html_content,$from,$from_name,$files);
if($send_att){
$_SESSION["msg"] = "<div class=\"success\">Verification reminder successfully sent</div>";
$file_array = glob("../pdf-reports/{$id}-temp-*.*");
if($file_array){
foreach($file_array as $filename){
unlink($filename);
}
}

}
///======================================================//
redirect("{$directory}{$admin}print-report");
}
///////////////////////////////////////////
?>

<!DOCTYPE html>
<html lang="en">
<head>
<base href="<?php directory(); ?>" target="_top">
<meta charset="UTF-8" />
<meta name="keywords" content="" />
<meta name="description" content="" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
<title><?php echo (basename($_SERVER["PHP_SELF"]) == "index.php")?"Dashboard":title_link(basename($_SERVER["PHP_SELF"],".php")); ?> - <?php echo $full_gen_name; ?></title>
<link rel="shortcut icon" href="images/favicon.png"/>
<link rel="stylesheet" href="css/font-awesome.css">
<link rel="stylesheet" href="css/portal.css">
<script src="js/jquery.js" type="text/javascript"></script>
<style>
<!--
body{
background:#fff;
}
html,body,img,div,span,a,table,tr,td,ul,ol,li,*{
font-family: Calibri;
font-size:15px;
color:#000;
}
div table thead tr th, div table tr th, div table tbody tr td, div table tr td{
text-align:left !important;
}

div table.top-print-report, div table.body-print-report{
border:1px solid #666;
}
div table.top-print-report *{
text-align:center !important;
}
div table.top-print-report th{
background-color:black;
font-size:20px;
color:#fff;
padding:15px;
}
div table.top-print-report th *{
color:#fff;
font-size:12px;
}
div table.top-print-report td{
padding:20px;
text-align:right !important;
}
div table.top-print-report td img{
height:30px;
}

div table.body-print-report td.outer, div table.body-dashed-report td.outer{
padding:20px;
}
div table.body-print-report table.inner *{
padding-left:0px;
}

/*=========================Background Checks Cover Letter============================*/
div table.body-dashed-report{
border:3px dotted #333;
}
div table.body-dashed-report *{
text-align:center !important;
}
div table.body-dashed-report h1{
font-size:25px;
font-weight:bold;
}
div table.body-dashed-report p, div table.body-dashed-report li{
font-size:18px;
}
div .header{
font-weight:bold;
margin-top:20px;
}
div table.body-dashed-report ol{
display:table; 
margin-left:auto; 
margin-right:auto;
}

/*=========================Certificate Cover Letter============================*/
div table.body-cert-report{
border:3px dotted #333;
}
div table.body-cert-report *{
font-size:16px;
}
div table.body-cert-report p.header, div table.body-cert-report p.header u{
font-weight:bold;
}
div table.body-cert-report table.inner *{
padding-left:0px;
}

/*=========================General============================*/
table{
margin-bottom:2px;
}
button{
padding:5px;
}
hr{
border-top:1px solid #777;
width:100%;
margin-bottom:20px;
}
table tr td, table tr th, table tr .gen-title{
padding:10px;
padding-bottom:5px;
padding-top:5px;
}
table tr .gen-title{
border-bottom:1px solid #fff;
}
.subjects div{
margin:5px;
margin-bottom:10px;
}

.summary-table{
width:auto;
}
.summary-table td, .summary-table th{
border:1px solid #333;
}
.summary-table *{
text-align:center !important;
padding:2px;
font-size:11px !important;
}

.copyright2{
font-size:10px !important;
font-style:italic;
padding:10px;
padding-top:0px;
text-align:left !important;
}
.special-p{
margin-top:10px;
}
.letter-footer, .letter-footer *{
font-size:10px;
}

.about ul{
display:table;
width:100%;
list-style-type:square;
}
.about ul li{
display:table-cell;
font-weight:bold;
font-size:12px;
}
.about ul li i{
font-size:10px;
}

.float-right{
float:right;
}
.float-left{
float:left;
}
.align-left{
align:left;
}
.align-center{
align:center;
}
.align-right{
align:right;
}
-->
</style>
</head>
<?php detectCurrUserBrowser('<table width="100%"><tr><td>','',7); ?>
<body>

<?php
if(isset($_SESSION["msg"]) && !empty($_SESSION["msg"]) && empty($send_request) && empty($send_reminder)){
echo $_SESSION["msg"];
unset($_SESSION["msg"]);
}

check_admin("");

$reprint = nr_input("reprint");
$resend = nr_input("resend");

$view = nr_input("view");
$bc_letter = np_input("bc_letter");
$cv_letter = np_input("cv_letter");
$bulk_cv_letter = np_input("bulk_cv_letter");
$foreign_cv_letter = np_input("foreign_cv_letter");
$waec_cv_letter = np_input("waec_cv_letter");
$new_cv_letter = np_input("new_cv_letter");
$verification_request = np_input("verification_request");
$verification_reminder = np_input("verification_reminder");

$md_id = in_table("id","reg_users","WHERE md = '1'","id");
$md_signature_array = glob("../images/signatures/{$md_id}pic*.*");
$md_signature = ($md_signature_array)?"images/" . $md_signature_array[0]:"images/post.jpg";

/////============Print BC Report=============////
if(check_admin("print_bc_reports") == 1 && !empty($view)){
$result = $db->select("bc_reports", "WHERE id='$view'", "*");

if(count_rows($result) == 1){
$row = fetch_data($result);
$client = $row["client"];
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$client_email = in_table("email","reg_users","WHERE id = '{$client}'","email");
$client_address = in_table("address","reg_users","WHERE id = '{$client}'","address");
$client_region = in_table("region","reg_users","WHERE id = '{$client}'","region");
$client_city = in_table("city","reg_users","WHERE id = '{$client}'","city");
$client_state = in_table("state","reg_users","WHERE id = '{$client}'","state");
$batch = $row["batch"];
$subject = $row["subject"];
$recommendation = $row["recommendation"];
$start_date = ($row["start_date"] != "0000-00-00")?min_sub_date($row["start_date"]):"";
$end_date = ($row["end_date"] != "0000-00-00")?min_sub_date($row["end_date"]):"";
$status = $row["status"];
$status_text = ($status == "COMPLETED")?"Fully Completed":"Not Fully Completed";
$status_div = ($status == "COMPLETED")?"primary":"danger";
$sub_reports = in_table("COUNT(id) AS Total","bc_sub_reports","WHERE bc_report_id = '{$view}'","Total");
$report_date = sub_date($date);
?>

<div class="reply-content-wrapper" style="background:url(images/rcs-seal.jpg) repeat left top;">

<table class="top-print-report">
<tr><th>RISK CONTROL BACKGROUND SCREENING<br> <span><?php echo $report_date; ?></span></th></tr>
<tr><td><img src="images/risk-control-logo.jpg"></td></tr>
</table>

<table class="body-print-report">
<tr><td class="outer">

<table class="inner">
<tbody>
<tr><th style="width:110px;">Client:</th><td><?php echo $client_name; ?></td></tr>
<tr><th>Batch:</th><td><?php echo $batch; ?></td></tr>
<tr><th>Address:</th><td><?php echo (!empty($client_address))?$client_address . ", ":""; echo (!empty($client_region))?$client_region . ", ":""; echo (!empty($client_city))?$client_city . ", ":""; echo (!empty($client_state))?$client_state . ".":""; ?></td></tr>
<tr><th>Subject:</th><td><?php echo $subject; ?></td></tr>
<tr><th>Reference Code:</th><td><?php echo formatQty($view); ?></td></tr>
</tbody>
</table>

<br /><u><b>VERIFIED INFORMATION</b></u><br /><br />

<?php 
$result = $db->select("bc_sub_reports", "WHERE bc_report_id='{$view}'", "*", "ORDER BY verification_order_id ASC");
if(count_rows($result) > 0){ 
while($row = fetch_data($result)){
$sub_report_id = $row["id"];
$verification_type = $row["verification_type"];
$education = $row["education"];
$source = $row["source"];
$comment = $row["comment"];
?>
<table class="inner">
<tbody>
<tr><th style="width:125px;">Verification Type:</th><th><u><?php echo strtoupper($verification_type); ?></u> <?php echo $education; ?></th></tr>
<tr><th>Source:</th><td><i><?php echo $source; ?></i></td></tr>
<tr><th>Comment:</th><td><?php echo $comment; ?></td></tr>
</tbody>
</table>
<hr>
<?php
}
}
?>

<?php if(!empty($recommendation)){ ?>
<table class="inner">
<tbody>
<tr><th style="width:125px;">Recommendation:</th><td><?php echo $recommendation; ?></td></tr>
</tbody>
</table>
<?php } ?>

</td></tr>
</table>

</div>

<div class="copyright">All information gathered and presented by Risk Control is done legally and ethically, and Risk Control takes the utmost care to ensure that the information provided is correct and accurate. However, we cannot take responsibility for the accuracy of information provided by or through third parties.</div>

<?php
}else{
echo "<div class='not-success'>This BC report does not exist.</div>";
}
}

/////============BC Cover Letter=============////
if(check_admin("manage_cover_letters") == 1 && !empty($bc_letter)){

$client = np_input("client");
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$client_address = in_table("address","reg_users","WHERE id = '{$client}'","address");
$client_region = in_table("region","reg_users","WHERE id = '{$client}'","region");
$client_city = in_table("city","reg_users","WHERE id = '{$client}'","city");
$client_state = in_table("state","reg_users","WHERE id = '{$client}'","state");
$completion_date = tp_input("completion_date");
$completion_date = min_sub_date($completion_date);
$attention = tp_input("attention");
?>

<form action="<?php echo $admin; ?>process-data" class="cover-letter-form" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
<input type="hidden" name="cover_letter_type" value="1"> 
<input type="hidden" name="client" value="<?php echo $client; ?>">
<input type="hidden" name="completion_date" value="<?php echo tp_input("completion_date"); ?>">
<input type="hidden" name="attention" value="<?php echo $attention; ?>">

<div class="reply-content-wrapper" style="background:url(images/rcs-seal.jpg) repeat left top;">

<table class="body-dashed-report">
<tr><td class="outer">

<div class="align-center"><img src="images/risk-control-logo.jpg"></div>
<br><br>
<h1>Background Screening Report</h1>
<br>

<?php 
$c = 0;
$batch_category = "";
//////=============Batches and Subjects=============////////
if(isset($_POST["batch"]) && !empty($_POST["batch"]) && !empty($_POST["candidates"])){ 
?>
<p><ol type="1">
<?php
foreach($_POST["batch"] as $value){ 
$batch = test_input($value);
$candidates = test_input($_POST["candidates"][$c]);
$batch_category .= "{$batch}+*+*{$candidates}-/-/";
$candidates = $candidates . ",";
$candidates = explode(",",$candidates);
?>
<p class="header">BATCH <?php echo formatQty($batch); ?></p>

<?php
foreach($candidates as $val){
if(!empty($val)){
?>
<li style="text-align:left!important; padding-top:5px; padding-bottom:5px;"><?php echo $val; ?></li>
<?php
}
}

$c++;
}
?>
</ol></p>
<?php
}
//////=============Ends Batches and Subjects=============////////
?>
<input type="hidden" name="batch_category" value="<?php echo $batch_category; ?>">

<br>
<p>FOR</p>

<p class="header"><?php echo $client_name; ?></p>

<p><?php echo (!empty($client_address))?$client_address . ", ":""; echo (!empty($client_region))?$client_region . ", ":""; echo (!empty($client_city))?$client_city . ", ":""; echo (!empty($client_state))?$client_state . ".":""; ?></p><br>

<p class="header"><?php echo $completion_date; ?></p><br>

<?php if(!empty($attention)){ ?>
<p style="font-size:14px; font-weight:bold;">Attention: <?php echo $attention; ?></p>
<?php } ?>

<div class="copyright2" style="border-top: 2px dotted #000; padding-top:20px; padding-bottom:0px; font-style:normal; margin-left:-20px; margin-right:-20px; text-align:left!important;">All information gathered and presented by Risk Control is done legally and ethically, and Risk Control takes the utmost care to ensure that the information provided is correct and accurate. However, we cannot take responsibility for the accuracy of information provided by or through third parties.</div>

</td></tr>
</table>

<p class="align-center" style="font-size:10px;">Risk Control Services Nig. Ltd.<br>Risk Control Plaza, Plot 5, Dreamworld Africana Road, after Orchid Hotel 2nd Toll Gate,<br>Lekki - Epe Expressway, Lagos. Tel: +234 (1) 295 4283, +234 (1) 342 4709</p>

<?php if(empty($reprint)){ ?>
<a id="save-btn" onClick="javascript: $('.cover-letter-form').submit();" style="font-size:10px;">Save report</a>
<?php } ?>
</div>

</form>

<?php
}


/////============CV Cover Letter=============////
if(check_admin("manage_cover_letters") == 1 && !empty($cv_letter)){

$reference_no = tp_input("reference_no");
$completion_date = tp_input("completion_date");
$completion_date = (!empty($completion_date))?date_format(date_create($completion_date),"F j, Y"):"";
$client = np_input("client");
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$client_address = in_table("address","reg_users","WHERE id = '{$client}'","address");
$client_region = in_table("region","reg_users","WHERE id = '{$client}'","region");
$client_city = in_table("city","reg_users","WHERE id = '{$client}'","city");
$client_state = in_table("state","reg_users","WHERE id = '{$client}'","state");
$client_designation = tp_input("client_designation");
$attention = tp_input("attention");
$re = tp_input("re");
$names = tp_input("names");
$school = tp_input("school");
$year = tp_input("year");
$qualification = tp_input("qualification");
$grade = tp_input("grade");
$course = tp_input("course");
$transaction_ref = tp_input("transaction_ref");
$comment = tp_input("comment");
$report_source = tp_input("report_source");
$invoice_attachment = tp_input("invoice_attachment");
$signatory = np_input("signatory");
$signatory_name = in_table("name","reg_users","WHERE id = '{$signatory}'","name");
$designation = in_table("designation","reg_users","WHERE id = '{$signatory}'","designation");
$signature_array = glob("../images/signatures/{$signatory}pic*.*");
$signature_name = ($signature_array)?"images/" . $signature_array[0]:"images/post.jpg";
?>

<style>
<!--
div .header{
margin-top:10px;
}
hr{
border:1px #000 solid;
margin-top:5px;
margin-bottom:5px;
}
p.special-p, p.special-p *, .header, .header *, table tr *{
font-size:12px!important;
}
-->
</style>

<form action="<?php echo $admin; ?>process-data" class="cover-letter-form" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
<input type="hidden" name="cover_letter_type" value="2"> 

<input type="hidden" name="reference_no" value="<?php echo $reference_no; ?>">
<input type="hidden" name="completion_date" value="<?php echo tp_input("completion_date"); ?>">
<input type="hidden" name="client" value="<?php echo $client; ?>">
<input type="hidden" name="client_designation" value="<?php echo $client_designation; ?>">
<input type="hidden" name="attention" value="<?php echo $attention; ?>">
<input type="hidden" name="re" value="<?php echo $re; ?>">
<input type="hidden" name="invoice_attachment" value="<?php echo $invoice_attachment; ?>">
<input type="hidden" name="signatory" value="<?php echo $signatory; ?>">
<input type="hidden" name="names" value="<?php echo $names; ?>">
<input type="hidden" name="school" value="<?php echo $school; ?>">
<input type="hidden" name="year" value="<?php echo $year; ?>">
<input type="hidden" name="qualification" value="<?php echo $qualification; ?>">
<input type="hidden" name="grade" value="<?php echo $grade; ?>">
<input type="hidden" name="course" value="<?php echo $course; ?>">
<input type="hidden" name="transaction_ref" value="<?php echo $transaction_ref; ?>">
<input type="hidden" name="comment" value="<?php echo $comment; ?>">
<input type="hidden" name="report_source" value="<?php echo $report_source; ?>">

<div class="reply-content-wrapper" style="background:url(images/rcs-seal.jpg) repeat left top;">

<div class="align-right"><b>CERTIFIED: ISO 9001:2015</b></div>

<div class="align-center"><img src="images/risk-control-logo.jpg"></div>
<hr>
<div class="align-center about">
<ul><li><i class="fa fa-square"></i> Background Screening</li>
<li><i class="fa fa-square"></i> Electronic Security</li>
<li><i class="fa fa-square"></i> Forensics</li>
<li><i class="fa fa-square"></i> Anti-counterfeiting</li>
<li><i class="fa fa-square"></i> Consultancy</li>
<li><i class="fa fa-square"></i> Security Training</li></ul>
</div>

<table class="body-cert-report" style="border:0px; margin-top:0px;">
<tr><td class="outer">

<p class="header">Our Ref.: <?php echo $reference_no; ?></p>
<p class="special-p"><?php echo $completion_date; ?></p>

<p class="special-p">
<b><?php echo $client_designation; ?></b><br>
<?php echo $client_name; ?>
</p>

<p class="special-p">
<?php echo (!empty($client_address))?$client_address . ",<br>":""; ?>
<?php echo (!empty($client_region))?$client_region . ",<br>":""; ?>
<?php echo (!empty($client_city))?$client_city . ",<br>":""; ?>
<?php echo (!empty($client_state))?$client_state . ".":""; ?>
</p>

<p class="header special-p">ATTN: <?php echo $attention; ?></p>

<p class="header special-p" style="margin-top:8px;">Dear Sir,</p>

<p class="header special-p" style="margin-top:8px;"><u>RE: <?php echo $re; ?></u></p>

<p class="special-p">Your request on the above subject matter refers.</p>

<p class="special-p">The following attachment convey the result of our search</p>

<table class="inner special-p" style="width:100%;"><tbody>
<tr><td style="width:250px;">Name:</td><td><?php echo $names; ?></td></tr>
<tr><td>University:</td><td><?php echo $school; ?></td></tr>
<tr><td>Year:</td><td><?php echo $year; ?></td></tr>
<tr><td>Qualification:</td><td><?php echo $qualification; ?></td></tr>
<tr><td>Grade:</td><td><?php echo $grade; ?></td></tr>
<tr><td>Course:</td><td><?php echo $course; ?></td></tr>
<tr><td>Comment:</td><th><?php echo $comment; ?></th></tr>
<tr><td>Transaction Ref:</td><td><?php echo $transaction_ref; ?></td></tr>
</tbody></table>

<p>Attachment: <br />
<ol type="1" style="margin-left:30px;">
<?php if(!empty($report_source)){ ?>
<li><?php echo $report_source; ?></li>
<?php } ?>
<?php if(!empty($invoice_attachment)){ ?>
<li><?php echo $invoice_attachment; ?></li>
<?php } ?>
</ol></p>

<p class="special-p">Thank you.</p>

<p class="special-p">Yours faithfully, <br />
<b>For: RISK CONTROL SERVICES NIGERIA LIMITED</b></p>

<table class="inner" style="width:100%;"><tbody>
<tr style="height:20px;"><?php if(!empty($signatory)){ ?><td style="width:300px; padding-bottom:0px!important; vertical-align:bottom;"><!--<img src="<?php echo $signature_name; ?>">--></td><?php } ?><td style="padding-bottom:0px!important; vertical-align:bottom;"><!--<img src="<?php echo $md_signature; ?>">--></td></tr>
<tr><?php if(!empty($signatory)){ ?><td style="width:300px; padding-bottom:0px!important;"><?php echo $signatory_name; ?></td><?php } ?><td style="padding-bottom:0px!important;">Olufemi A. Ajayi</td></tr>
<tr><?php if(!empty($signatory)){ ?><th style="padding-top:0px!important; font-style:italic;"><?php echo $designation; ?></th><?php } ?><th style="padding-top:0px!important; font-style:italic;">CEO</th></tr>
</tbody></table>

</td></tr>
</table>

<div style="padding-left:10px; text-decoration:underline; font-weight:bold; font-size:10px;">DISCLAIMER</div>
<div class="copyright2" style="font-style:normal; text-align:left!important;">All information gathered and presented by Risk Control is done so legally and ethically. Risk Control takes the utmost care to ensure that the information we provide is correct. However, we are not able to take responsibility for the accuracy of information provided by or through third parties.</div>

<p class="align-center special-p letter-footer"><b>Head Office:</b> Risk Control Plaza, Plot 5, Dreamworld Africana Road, after Orchid Hotel 2nd Toll Gate, Lekki - Epe Expressway, Lagos.<br>
<b>Tel:</b> +234 1 295 4283, +234 1 342 4709 <b>Email:</b> info@riskcontrolnigeria.com <b>Website:</b> www.riskcontrolnigeria.com</p>

<?php if(empty($reprint)){ ?>
<a id="save-btn" onClick="javascript: $('.cover-letter-form').submit();" style="font-size:10px;">Save report</a>
<?php } ?>

</div>
</form>

<?php
}

/////============Bulk CV Cover Letter=============////
if(check_admin("manage_cover_letters") == 1 && !empty($bulk_cv_letter)){

$reference_no = tp_input("reference_no");
$completion_date = tp_input("completion_date");
$completion_date = (!empty($completion_date))?date_format(date_create($completion_date),"F j, Y"):"";
$client = np_input("client");
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$client_address = in_table("address","reg_users","WHERE id = '{$client}'","address");
$client_region = in_table("region","reg_users","WHERE id = '{$client}'","region");
$client_city = in_table("city","reg_users","WHERE id = '{$client}'","city");
$client_state = in_table("state","reg_users","WHERE id = '{$client}'","state");
$client_designation = tp_input("client_designation");
$attention = tp_input("attention");
$re = tp_input("re");
$invoice_attachment = tp_input("invoice_attachment");
$signatory = tp_input("signatory");
$signatory_name = in_table("name","reg_users","WHERE id = '{$signatory}'","name");
$designation = in_table("designation","reg_users","WHERE id = '{$signatory}'","designation");
$signature_array = glob("../images/signatures/{$signatory}pic*.*");
$signature_name = ($signature_array)?"images/" . $signature_array[0]:"images/post.jpg";
?>

<style>
<!--
div .header{
margin-top:10px;
}
hr{
border:1px solid #000;
margin-top:5px;
margin-bottom:5px;
}
-->
</style>

<form action="<?php echo $admin; ?>process-data" class="cover-letter-form" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
<input type="hidden" name="cover_letter_type" value="4"> 
<input type="hidden" name="reference_no" value="<?php echo $reference_no; ?>">
<input type="hidden" name="completion_date" value="<?php echo tp_input("completion_date"); ?>">
<input type="hidden" name="client" value="<?php echo $client; ?>">
<input type="hidden" name="client_designation" value="<?php echo $client_designation; ?>">
<input type="hidden" name="attention" value="<?php echo $attention; ?>">
<input type="hidden" name="re" value="<?php echo $re; ?>">
<input type="hidden" name="invoice_attachment" value="<?php echo $invoice_attachment; ?>">
<input type="hidden" name="signatory" value="<?php echo $signatory; ?>">

<div class="reply-content-wrapper" style="background:url(images/rcs-seal.jpg) repeat left top;">

<div class="align-right"><b>CERTIFIED: ISO 9001:2015</b></div>

<div class="align-center"><img src="images/risk-control-logo.jpg"></div>
<hr>
<div class="align-center about">
<ul><li><i class="fa fa-square"></i> Background Screening</li>
<li><i class="fa fa-square"></i> Electronic Security</li>
<li><i class="fa fa-square"></i> Forensics</li>
<li><i class="fa fa-square"></i> Anti-counterfeiting</li>
<li><i class="fa fa-square"></i> Consultancy</li>
<li><i class="fa fa-square"></i> Security Training</li></ul>
</div><br>

<table class="body-cert-report" style="border:0px;">
<tr><td class="outer">

<p class="header">Our Ref.: <?php echo $reference_no; ?></p>
<p style="margin-top:15px;"><?php echo $completion_date; ?></p>

<?php 
$c = 0;
$batch_category = "";
//////=============Summary=============////////
if(isset($_POST["batch_no"]) && !empty($_POST["batch_no"])){ 
?>
<p><table class="summary-table float-right">
<tr><th colspan="3">Summary</th></tr>
<tr><th>Batch No.</th><th>Received Job</th><th>In progress</th></tr>
<?php
foreach($_POST["batch_no"] as $value){ 
$batch_no = test_input($value);
$received_job = test_input($_POST["received_job"][$c]);
$in_progress = test_input($_POST["in_progress"][$c]);
$batch_category .= "{$batch_no}+*+*{$received_job}+*+*{$in_progress}-/-/";
if(!empty($batch_no)){
?>
<tr><td><?php echo $batch_no; ?></td><td><?php echo $received_job; ?></td><td><?php echo $in_progress; ?></td></tr>
<?php
}
$c++;
}
?>
</table></p>
<?php
}
//////=============Ends Summary=============////////
?>

<input type="hidden" name="batch_category" value="<?php echo $batch_category; ?>">

<p class="special-p"><b><?php echo $client_designation; ?></b><br>
<b><?php echo $client_name; ?></b><br>
<?php echo (!empty($client_address))?$client_address . ",<br>":""; ?>
<?php echo (!empty($client_region))?$client_region . ",<br>":""; ?>
<?php echo (!empty($client_city))?$client_city . ",<br>":""; ?>
<?php echo (!empty($client_state))?$client_state . ".":""; ?></p>

<p class="header">ATTN: <?php echo $attention; ?></p>

<p class="header">Dear Sir,</p>

<p class="header"><u>RE: <?php echo $re; ?></u></p>

<p class="special-p">Your request on the above subject matter refers.</p>

<p class="special-p">The following attachments convey the result of our search:</p>

<?php 
$list_category = "";
//////=============List Items=============////////
if(isset($_POST["list_items"]) && !empty($_POST["list_items"])){ 
?>
<p class="special-p"><ol type="1" style="margin-left:30px;">
<?php
foreach($_POST["list_items"] as $value){ 
$list_item = test_input($value);
$list_category .= "{$list_item}-/-/";
if(!empty($list_item)){
?>
<li><?php echo $list_item; ?></li>
<?php
}
}
?>
</ol></p>
<?php
}
//////=============Ends List Items=============////////
?>

<input type="hidden" name="list_category" value="<?php echo $list_category; ?>">

<?php if(!empty($invoice_attachment)){ ?>
<p class="special-p"><?php echo $invoice_attachment; ?></p>
<?php } ?>

<p class="special-p">Thank you.</p>

<p class="special-p">Yours faithfully, <br />
<b>For: RISK CONTROL SERVICES NIGERIA LIMITED</b></p>

<table class="inner" style="width:100%;"><tbody>
<tr style="height:40px;"><?php if(!empty($signatory)){ ?><td style="width:300px; padding-bottom:0px!important; vertical-align:bottom;"><!--<img src="<?php echo $signature_name; ?>">--></td><?php } ?><td style="padding-bottom:0px!important; vertical-align:bottom;"><!--<img src="<?php echo $md_signature; ?>">--></td></tr>
<tr><?php if(!empty($signatory)){ ?><td style="width:300px; padding-bottom:0px!important;"><?php echo $signatory_name; ?></td><?php } ?><td style="padding-bottom:0px!important;">Olufemi A. Ajayi</td></tr>
<tr><?php if(!empty($signatory)){ ?><th style="padding-top:0px!important; font-style:italic;"><?php echo $designation; ?></th><?php } ?><th style="padding-top:0px!important; font-style:italic;">CEO</th></tr>
</tbody></table>

</td></tr>
</table>

<div style="padding-left:10px; text-decoration:underline; font-weight:bold; font-size:10px;">DISCLAIMER</div>
<div class="copyright2" style="font-style:normal; text-align:left!important;">All information gathered and presented by Risk Control is done so legally and ethically. Risk Control takes the utmost care to ensure that the information we provide is correct. However, we are not able to take responsibility for the accuracy of information provided by or through third parties.</div>

<p class="align-center special-p letter-footer"><b>Head Office:</b> Risk Control Plaza, Plot 5, Dreamworld Africana Road, after Orchid Hotel 2nd Toll Gate, Lekki - Epe Expressway, Lagos.<br>
<b>Tel:</b> +234 1 295 4283, +234 1 342 4709 <b>Email:</b> info@riskcontrolnigeria.com <b>Website:</b> www.riskcontrolnigeria.com</p>

</div>
<?php if(empty($reprint)){ ?>
<a id="save-btn" onClick="javascript: $('.cover-letter-form').submit();" style="font-size:10px;">Save report</a>
<?php } ?>
</form>
<?php
}





/////============New Bulk CV Cover Letter Tabulated=============////
if(check_admin("manage_cover_letters") == 1 && !empty($new_cv_letter)){

$reference_no = tp_input("reference_no");
$completion_date = tp_input("completion_date");
$completion_date = (!empty($completion_date))?date_format(date_create($completion_date),"F j, Y"):"";
$client = np_input("client");
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$client_address = in_table("address","reg_users","WHERE id = '{$client}'","address");
$client_region = in_table("region","reg_users","WHERE id = '{$client}'","region");
$client_city = in_table("city","reg_users","WHERE id = '{$client}'","city");
$client_state = in_table("state","reg_users","WHERE id = '{$client}'","state");
$client_designation = tp_input("client_designation");
$attention = tp_input("attention");
$re = tp_input("re");
$invoice_attachment = tp_input("invoice_attachment");
$signatory = tp_input("signatory");
$signatory_name = in_table("name","reg_users","WHERE id = '{$signatory}'","name");
$designation = in_table("designation","reg_users","WHERE id = '{$signatory}'","designation");
$signature_array = glob("../images/signatures/{$signatory}pic*.*");
$signature_name = ($signature_array)?"images/" . $signature_array[0]:"images/post.jpg";
?>

<style>
<!--
div .header{
margin-top:10px;
}
hr{
border:1px solid #000;
margin-top:5px;
margin-bottom:5px;
}
.inner2 th, .inner2 td{
border:1px solid #000;
padding:4px!important;
}
p.special-p, p.special-p *, .header, .header *, table:not(.summary-table) tr *{
font-size:12px!important;
}
-->
</style>

<form action="<?php echo $admin; ?>process-data" class="cover-letter-form" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
<input type="hidden" name="cover_letter_type" value="3"> 
<input type="hidden" name="reference_no" value="<?php echo $reference_no; ?>">
<input type="hidden" name="completion_date" value="<?php echo tp_input("completion_date"); ?>">
<input type="hidden" name="client" value="<?php echo $client; ?>">
<input type="hidden" name="client_designation" value="<?php echo $client_designation; ?>">
<input type="hidden" name="attention" value="<?php echo $attention; ?>">
<input type="hidden" name="re" value="<?php echo $re; ?>">
<input type="hidden" name="invoice_attachment" value="<?php echo $invoice_attachment; ?>">
<input type="hidden" name="signatory" value="<?php echo $signatory; ?>">

<div class="reply-content-wrapper" style="background:url(images/rcs-seal.jpg) repeat left top;">

<div class="align-right"><b>CERTIFIED: ISO 9001:2015</b></div>

<div class="align-center"><img src="images/risk-control-logo.jpg"></div>
<hr>
<div class="align-center about">
<ul><li><i class="fa fa-square"></i> Background Screening</li>
<li><i class="fa fa-square"></i> Electronic Security</li>
<li><i class="fa fa-square"></i> Forensics</li>
<li><i class="fa fa-square"></i> Anti-counterfeiting</li>
<li><i class="fa fa-square"></i> Consultancy</li>
<li><i class="fa fa-square"></i> Security Training</li></ul>
</div><br>

<table class="body-cert-report" style="border:0px;">
<tr><td class="outer">

<p class="header">Our Ref.: <?php echo $reference_no; ?></p>
<p style="margin-top:15px;"><?php echo $completion_date; ?></p>

<?php 
$c = 0;
$batch_category = "";
//////=============Right Summary=============////////
if(isset($_POST["batch_no"]) && !empty($_POST["batch_no"])){ 
?>
<p><table class="summary-table float-right">
<tr><th colspan="3">Summary</th></tr>
<tr><th>Batch No.</th><th>Received Job</th><th>In progress</th></tr>
<?php
foreach($_POST["batch_no"] as $value){ 
$batch_no = test_input($value);
$received_job = test_input($_POST["received_job"][$c]);
$in_progress = test_input($_POST["in_progress"][$c]);
$batch_category .= "{$batch_no}+*+*{$received_job}+*+*{$in_progress}-/-/";
if(!empty($batch_no)){
?>
<tr><td><?php echo $batch_no; ?></td><td><?php echo $received_job; ?></td><td><?php echo $in_progress; ?></td></tr>
<?php
}
$c++;
}
?>
</table></p>
<?php
}
//////=============Ends Right Summary=============////////
?>
<input type="hidden" name="batch_category" value="<?php echo $batch_category; ?>">

<p class="special-p"><b><?php echo $client_designation; ?></b><br>
<b><?php echo $client_name; ?></b><br>
<?php echo (!empty($client_address))?$client_address . ",<br>":""; ?>
<?php echo (!empty($client_region))?$client_region . ",<br>":""; ?>
<?php echo (!empty($client_city))?$client_city . ",<br>":""; ?>
<?php echo (!empty($client_state))?$client_state . ".":""; ?></p>

<p class="header">ATTN: <?php echo $attention; ?></p>

<p class="header">Dear Sir,</p>

<p class="header"><u>RE: <?php echo $re; ?></u></p>

<p class="special-p">Your letter on the above subject matter refers.</p>

<p class="special-p">The following attachments convey the result of our search:</p>

<?php 
$c = 0;
$details_category = "";
//////=============Summary=============////////
if(isset($_POST["names"]) && !empty($_POST["names"])){ 
?>
<p><table class="inner inner2">
<tr><th>S/N</th><th>NAMES</th><th>INSTITUTION</th><th>COURSE</th><th>GRADE</th><th>SESSION</th><th>COMMENT</th><th>TRANSACTION REF</th></tr>
<?php
foreach($_POST["names"] as $value){ 
$names = test_input($value);
$institution = test_input($_POST["institution"][$c]);
$qualification = test_input($_POST["qualification"][$c]);
$course = test_input($_POST["course"][$c]);
$grade = test_input($_POST["grade"][$c]);
$session = test_input($_POST["session"][$c]);
$comment = test_input($_POST["comment"][$c]);
$transaction_ref = test_input($_POST["transaction_ref"][$c]);
$details_category .= "{$names}+*+*{$institution}+*+*{$qualification}+*+*{$course}+*+*{$grade}+*+*{$session}+*+*{$comment}+*+*{$transaction_ref}-/-/";

if(!empty($names)){
?>
<tr><td><?php echo $c + 1; ?></td><td><?php echo $names; ?></td><td><?php echo $institution; ?></td><td><?php echo $qualification; if(!empty($qualification) && !empty($course)){ echo " ({$course})"; }else{ echo $course; } ?></td><td><?php echo $grade; ?></td><td><?php echo $session; ?></td><td><?php echo $comment; ?></td><td><?php echo $transaction_ref; ?></td></tr>
<?php
}
$c++;
}
?>
</table></p>
<?php
}
//////=============Ends Summary=============////////
?>
<input type="hidden" name="details_category" value="<?php echo $details_category; ?>">
<?php
$list_category = "";
//////=============List Items=============////////
if(isset($_POST["list_items"]) && !empty($_POST["list_items"])){ 
?>
<p class="special-p"><ol type="1" style="margin-left:30px;">
<?php
foreach($_POST["list_items"] as $value){ 
$list_item = test_input($value);
$list_category .= "{$list_item}-/-/";
if(!empty($list_item)){
?>
<li><?php echo $list_item; ?></li>
<?php
}
}
?>
</ol></p>
<?php
}
//////=============Ends List Items=============////////
?>
<input type="hidden" name="list_category" value="<?php echo $list_category; ?>">

<?php if(!empty($invoice_attachment)){ ?>
<p class="special-p"><?php echo $invoice_attachment; ?></p>
<?php } ?>

<p class="special-p">Thank you.</p>

<p class="special-p">Yours faithfully, <br />
<b>For: RISK CONTROL SERVICES NIGERIA LIMITED</b></p>

<table class="inner" style="width:100%;"><tbody>
<tr style="height:30px;"><?php if(!empty($signatory)){ ?><td style="width:300px; padding-bottom:0px!important; vertical-align:bottom;"><!--<img src="<?php echo $signature_name; ?>">--></td><?php } ?><td style="padding-bottom:0px!important; vertical-align:bottom;"><!--<img src="<?php echo $md_signature; ?>">--></td></tr>
<tr><?php if(!empty($signatory)){ ?><td style="width:300px; padding-bottom:0px!important;"><?php echo $signatory_name; ?></td><?php } ?><td style="padding-bottom:0px!important;">Olufemi A. Ajayi</td></tr>
<tr><?php if(!empty($signatory)){ ?><th style="padding-top:0px!important; font-style:italic;"><?php echo $designation; ?></th><?php } ?><th style="padding-top:0px!important; font-style:italic;">CEO</th></tr>
</tbody></table>

</td></tr>
</table>

<div style="padding-left:10px; text-decoration:underline; font-weight:bold; font-size:10px;">DISCLAIMER</div>
<div class="copyright2" style="font-style:normal; text-align:left!important;">All information gathered and presented by Risk Control is done so legally and ethically. Risk Control takes the utmost care to ensure that the information we provide is correct. However, we are not able to take responsibility for the accuracy of information provided by or through third parties.</div>

<p class="align-center special-p letter-footer"><b>Head Office:</b> Risk Control Plaza, Plot 5, Dreamworld Africana Road, after Orchid Hotel 2nd Toll Gate, Lekki - Epe Expressway, Lagos.<br>
<b>Tel:</b> +234 1 295 4283, +234 1 342 4709 <b>Email:</b> info@riskcontrolnigeria.com <b>Website:</b> www.riskcontrolnigeria.com</p>

</div>
<?php if(empty($reprint)){ ?>
<a id="save-btn" onClick="javascript: $('.cover-letter-form').submit();" style="font-size:10px;">Save report</a>
<?php } ?>
</form>
<?php
}








/////============Foreign CV Cover Letter=============////
if(check_admin("manage_cover_letters") == 1 && !empty($foreign_cv_letter)){

$completion_date = tp_input("completion_date");
$completion_date = (!empty($completion_date))?date_format(date_create($completion_date),"F j, Y"):"";
$client = np_input("client");
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$provided_by = tp_input("provided_by");
$subject = tp_input("subject");
$institution = tp_input("institution");
$award_date = tp_input("award_date");
$qualification = tp_input("qualification");
$grade = tp_input("grade");
$course = tp_input("course");
$status = tp_input("status");
?>
<style>
<!--
table.inner tr td, table.inner tr th{
font-size:13px;
}
table.inner2 tr td, table.inner2 tr th{
padding:2px;
padding-right:10px;
}
hr{
border:1px #000 solid;
margin-bottom:0px;
}
.diff-font *{
font-family:Consolas;
font-size:14px !important;
}
div .header{
margin-top:10px;
}
p{
font-size:14px !important;
}
-->
</style>

<form action="<?php echo $admin; ?>process-data" class="cover-letter-form" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
<input type="hidden" name="cover_letter_type" value="6"> 
<input type="hidden" name="completion_date" value="<?php echo tp_input("completion_date"); ?>">
<input type="hidden" name="client" value="<?php echo $client; ?>">
<input type="hidden" name="provided_by" value="<?php echo $provided_by; ?>">
<input type="hidden" name="subject" value="<?php echo $subject; ?>">
<input type="hidden" name="institution" value="<?php echo $institution; ?>">
<input type="hidden" name="award_date" value="<?php echo $award_date; ?>">
<input type="hidden" name="qualification" value="<?php echo $qualification; ?>">
<input type="hidden" name="grade" value="<?php echo $grade; ?>">
<input type="hidden" name="course" value="<?php echo $course; ?>">
<input type="hidden" name="status" value="<?php echo $status; ?>">

<div class="reply-content-wrapper" style="background:url(images/rcs-seal.jpg) repeat left top;">

<div class="align-center"><img src="images/foreign-report-background.png"></div>

<div style="margin:auto; width:82%;">

<p class="header align-center" style="font-family:'Bodoni MT Black'; font-size:9px;"><?php echo $client_name; ?></p>

<p class="align-right"><?php echo $completion_date; ?></p>

<p class="header align-center" style="margin-top:0px; padding-bottom:0px;">CONFIRMATION OF FOREIGN CERTIFICATE</p>

<p class="align-center"><?php echo $institution; ?></p>

<p class="align-center"><?php echo $subject; ?></p>

<table class="body-cert-report" style="border:2px solid #000;">
<tr><td class="outer">

<p class="header">The relevant authority of the above named institution was contacted in the name of Subject.</p>

<p class="header">Below is the result:</p><br>

<table class="inner" style="width:100%;"><tbody>
<tr><th style="width:250px;">Subject:<br><br></th><td><?php echo $subject; ?><br><br></td></tr>
<tr><th>Institution:<br><br></th><td><?php echo $institution; ?><br><br></td></tr>
<tr><th>DATA PROVIDED BY:</th><td><?php echo $provided_by; ?></td></tr>
</tbody></table><br>

<p class="diff-font"><span>INFORMATION VERIFIED</span></p>
<hr>

<table class="inner inner2 diff-font" style="width:100%;"><tbody>
<tr><td style="width:250px;"><?php echo str_pad("Name On School&#039;s Records ",35,"."); ?>:</td><td><?php echo $subject; ?></td></tr>
<tr><td><?php echo str_pad("Award Obtained ",30,"."); ?>:</td><td><?php echo $qualification; ?></td></tr>
<tr><td><?php echo str_pad("Major ",30,"."); ?>:</td><td><?php echo $course; ?></td></tr>
<tr><td><?php echo str_pad("Grade ",30,"."); ?>:</td><td><?php echo $grade; ?></td></tr>
<tr><td><?php echo str_pad("Award Date ",30,"."); ?>:</td><td><?php echo $award_date; ?></td></tr>
<tr><td><?php echo str_pad("Status ",30,"."); ?>:</td><th><?php echo $status; ?></th></tr>
</tbody></table><br>

<div style="height:80px;"></div>

<p><i style="font-size:13px;">No further information was available from the school, please.</i></p>

</td></tr>
</table>
<br><br>
<div style="padding-left:10px; font-size:11px; text-decoration:underline; font-weight:bold;">DISCLAIMER</div>
<div class="copyright2" style="font-size:11px; text-align:left;">All information gathered and presented by Risk Control is done so legally and ethically. Risk Control takes the utmost care to ensure that the information we provide is correct. However, we are not able to take responsibility for the accuracy of information provided by or through third parties.</div>

</div>

</div>

<?php if(empty($reprint)){ ?>
<a id="save-btn" onClick="javascript: $('.cover-letter-form').submit();" style="font-size:10px;">Save report</a>
<?php } ?>
</form>

<?php
}



/////============WAEC CV Cover Letter=============////
if(check_admin("manage_cover_letters") == 1 && !empty($waec_cv_letter)){

$completion_date = tp_input("completion_date");
$completion_date = (!empty($completion_date))?date_format(date_create($completion_date),"F j, Y"):"";
$client = np_input("client");
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$confirmation_type = tp_input("confirmation_type");
$provided_by = tp_input("provided_by");
$subject = tp_input("subject");
$institution = tp_input("institution");
$year = tp_input("year");
$centre = tp_input("centre");
$candidate_number = tp_input("candidate_number");
$status = tp_input("status");
?>
<style>
<!--
table.inner tr td, table.inner tr th{
font-size:13px;
}
table.inner2 tr td, table.inner2 tr th{
padding:2px;
padding-right:10px;
}
hr{
border:1px #000 solid;
margin-bottom:0px;
}
.diff-font *{
font-family:Consolas;
font-size:14px !important;
}
div .header{
margin-top:10px;
}
p{
font-size:14px !important;
}
-->
</style>

<form action="<?php echo $admin; ?>process-data" class="cover-letter-form" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
<input type="hidden" name="cover_letter_type" value="5"> 
<input type="hidden" name="completion_date" value="<?php echo tp_input("completion_date"); ?>">
<input type="hidden" name="client" value="<?php echo $client; ?>">
<input type="hidden" name="confirmation_type" value="<?php echo $confirmation_type; ?>">
<input type="hidden" name="provided_by" value="<?php echo $provided_by; ?>">
<input type="hidden" name="subject" value="<?php echo $subject; ?>">
<input type="hidden" name="institution" value="<?php echo $institution; ?>">
<input type="hidden" name="year" value="<?php echo $year; ?>">
<input type="hidden" name="centre" value="<?php echo $centre; ?>">
<input type="hidden" name="candidate_number" value="<?php echo $candidate_number; ?>">
<input type="hidden" name="status" value="<?php echo $status; ?>">

<div class="reply-content-wrapper" style="background:url(images/rcs-seal.jpg) repeat left top;">

<div class="align-center"><img src="images/foreign-report-background.png"></div>

<div>

<p class="header align-center" style="font-family:'Bodoni MT Black'; font-size:9px;"><?php echo $client_name; ?></p>

<p class="align-right"><?php echo $completion_date; ?></p>

<p class="header align-center" style="margin-top:0px; padding-bottom:0px;">CONFIRMATION OF <?php echo $confirmation_type; ?> CERTIFICATE</p>

<p class="align-center"><?php echo $institution; ?></p>

<p class="align-center"><?php echo $subject; ?></p>

<table class="body-cert-report" style="border:2px solid #000;">
<tr><td class="outer">

<p class="header">The relevant authority of the above named institution was contacted in the name of Subject.</p>

<p class="header">Below is the result:</p><br>

<table class="inner" style="width:100%;"><tbody>
<tr><th style="width:250px;">Subject:<br><br></th><td><?php echo $subject; ?><br><br></td></tr>
<tr><th>Institution:<br><br></th><td><?php echo $institution; ?><br><br></td></tr>
<tr><th>DATA PROVIDED BY:</th><td><?php echo $provided_by; ?></td></tr>
</tbody></table><br>

<p class="diff-font"><span>INFORMATION VERIFIED</span></p>
<hr>

<table class="inner inner2 diff-font" style="width:100%;"><tbody>
<tr><td style="width:250px;"><?php echo str_pad("Name On WAEC&#039;s Records ",35,"."); ?>:</td><td><?php echo $subject; ?></td></tr>
<tr><td><?php echo str_pad("Centre ",30,"."); ?>:</td><td><?php echo $centre; ?></td></tr>
<tr><td><?php echo str_pad("Candidate Number ",30,"."); ?>:</td><td><?php echo $candidate_number; ?></td></tr>
<tr><td><?php echo str_pad("Candidate's result ",30,"."); ?>:</td><td>

<?php 
$c = 0;
$course_category = "";
//////=============Summary=============////////
if(isset($_POST["course"]) && !empty($_POST["course"]) && !empty($_POST["grade"])){ 
?>
<table style="width:100%">
<?php
foreach($_POST["course"] as $value){ 
$course = test_input($value);
$grade = test_input($_POST["grade"][$c]);
if(!empty($course) && !empty($grade)){
$course_category .= "{$course}+*+*{$grade}-/-/";
?>
<tr><th><?php echo $course; ?></th><th> - &nbsp;&nbsp; <?php echo $grade; ?></th></tr>
<?php
}
$c++;
}
?>
</table>
<?php
}
//////=============Ends Summary=============////////
?>
<input type="hidden" name="course_category" value="<?php echo $course_category; ?>">
</td></tr>
<tr><td><?php echo str_pad("Year ",30,"."); ?>:</td><td><?php echo $year; ?></td></tr>
<tr><td><?php echo str_pad("Status ",30,"."); ?>:</td><th><?php echo $status; ?></th></tr>
</tbody></table>

<div style="height:15px;"></div>

<p><i style="font-size:13px;">No further information was available from <?php echo $confirmation_type; ?>, please.</i></p>

</td></tr>
</table>
<br>
<div style="padding-left:10px; font-size:11px; text-decoration:underline; font-weight:bold;">DISCLAIMER</div>
<div class="copyright2" style="font-size:11px; text-align:left;">All information gathered and presented by Risk Control is done so legally and ethically. Risk Control takes the utmost care to ensure that the information we provide is correct. However, we are not able to take responsibility for the accuracy of information provided by or through third parties.</div>

</div>

</div>

<?php if(empty($reprint)){ ?>
<a id="save-btn" onClick="javascript: $('.cover-letter-form').submit();" style="font-size:10px;">Save report</a>
<?php } ?>
</form>

<?php
}

/////============Verfication Requests=============////
if(check_admin("manage_cover_letters") == 1 && !empty($verification_request)){

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

$reference_no = tp_input("reference_no");
$completion_date = tp_input("completion_date");
$completion_date = (!empty($completion_date))?date_format(date_create($completion_date),"jS F, Y"):"";
$client = np_input("client");
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$client_address = in_table("address","reg_users","WHERE id = '{$client}'","address");
$client_region = in_table("region","reg_users","WHERE id = '{$client}'","region");
$client_city = in_table("city","reg_users","WHERE id = '{$client}'","city");
$client_state = in_table("state","reg_users","WHERE id = '{$client}'","state");
$client_designation = tp_input("client_designation");
$client_department = tp_input("client_department");
$attention = tp_input("attention");
$re = tp_input("re");
$invoice_attachment = tp_input("invoice_attachment");
$signatory = tp_input("signatory");
$signatory_name = in_table("name","reg_users","WHERE id = '{$signatory}'","name");
$designation = in_table("designation","reg_users","WHERE id = '{$signatory}'","designation");
$signature_array = glob("../images/signatures/{$signatory}pic*.*");
$signature_name = (file_exists($signature_array[0]))?"images/" . $signature_array[0]:"../images/post.jpg";

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
?>

<style>
<!--
div .header{
margin-top:10px;
}
hr{
border:1px solid #000;
margin-top:5px;
margin-bottom:5px;
}
.inner2 th, .inner2 td{
border:1px solid #000;
padding:4px!important;
}
p.special-p, p.special-p *, .header, .header *, table:not(.summary-table) tr *{
font-size:12px!important;
}
-->
</style>

<form action="<?php echo $admin; ?>print-report" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
<input type="hidden" name="cover_letter_type" value="7"> 
<input type="hidden" name="reference_no" value="<?php echo $reference_no; ?>">
<input type="hidden" name="completion_date" value="<?php echo tp_input("completion_date"); ?>">
<input type="hidden" name="client" value="<?php echo $client; ?>">
<input type="hidden" name="client_designation" value="<?php echo $client_designation; ?>">
<input type="hidden" name="client_department" value="<?php echo $client_department; ?>">
<input type="hidden" name="attention" value="<?php echo $attention; ?>">
<input type="hidden" name="re" value="<?php echo $re; ?>">
<input type="hidden" name="invoice_attachment" value="<?php echo $invoice_attachment; ?>">
<input type="hidden" name="signatory" value="<?php echo $signatory; ?>">

<div class="reply-content-wrapper" style="background:url(images/rcs-seal.jpg) repeat left top;">

<table class="body-cert-report" style="border:0px;">
<tr><td class="outer">

<p class="header">Our Ref.: <?php echo $reference_no; ?></p>
<p style="margin-top:15px;"><?php echo $completion_date; ?></p>

<p class="special-p"><b><?php echo $client_name; ?></b><br>
<b><?php echo $client_designation; ?></b><br>
<?php echo (!empty($client_department))?$client_department . ",<br>":""; ?>
<?php echo (!empty($client_address))?$client_address . ",<br>":""; ?>
<?php echo (!empty($client_region))?$client_region . ",<br>":""; ?>
<?php echo (!empty($client_city))?$client_city . ",<br>":""; ?>
<?php echo (!empty($client_state))?$client_state . ".":""; ?></p>

<p class="header"><?php echo $attention; ?>,</p>

<p class="header"><u>RE: <?php echo $re; ?></u></p>

<p class="special-p">We request that you kindly confirm the results of the candidates listed below:</p>

<?php 
$details_category = "";
?>
<p><table class="inner inner2">
<tr><th>S/N</th><th>NAMES</th><th>COURSE</th><th>GRADE</th><th>YEAR OF GRAD.</th><th>MATRIC NO.</th></tr>
<?php
//////=============Summary=============////////
if(isset($_POST["names"]) && !empty($_POST["names"])){ 

$lineheight = 5;
$table = array();
$pdf->SetFont('Arial','B',9);
$table[] = array("S/N", "NAMES", "COURSE", "GRADE", "YEAR OF GRAD.", "MATRIC NO.");
$widths = array(7,52,52,25,18,38);
$pdf->plot_table($widths, $lineheight, $table);

$table = array();
$pdf->SetFont('Arial','',8);

$sn=0;
foreach($_POST["names"] as $value){ 
$names = test_input($value);
$course = test_input($_POST["course"][$sn]);
$grade = test_input($_POST["grade"][$sn]);
$year_of_graduation = test_input($_POST["year_of_graduation"][$sn]);
$matric_no = test_input($_POST["matric_no"][$sn]);
if(!empty($names)){
$details_category .= "{$names}+*+*{$course}+*+*{$grade}+*+*{$year_of_graduation}+*+*{$matric_no}-/-/";
$sn++;
$table[] = array($sn, $names, $course, $grade, $year_of_graduation, $matric_no);
?>
<tr><td><?php echo $sn; ?></td><td><?php echo $names; ?></td><td><?php echo $course; ?></td><td><?php echo $grade; ?></td><td><?php echo $year_of_graduation; ?></td><td><?php echo $matric_no; ?></td></tr>
<?php
}
}

}

////////////// Upload Excel File //////////////////////////////  
if(isset($_FILES["ufile"]["tmp_name"]) && !empty($_FILES["ufile"]["tmp_name"])){ 

$file_name = $_FILES["ufile"]["name"]; 
$file_temp_name = $_FILES["ufile"]["tmp_name"];
$file_error_message = $_FILES["ufile"]["error"];
$file_name_2_array = explode(".", $file_name);
$file_extension = end($file_name_2_array);

////======================= Access File ===============/////
if (!$file_temp_name) {
    echo "<div class=\"not-success\">ERROR: Please browse for a file before clicking the upload button.</div>";
    exit();
} 
else if (!preg_match("/.(xlsx|XLSX)$/i", $file_name) ) {
     echo "<div class=\"not-success\">ERROR: Your file was not .xlsx .</div>";
     unlink($file_temp_name);
     exit();
}
else if ($file_error_message == 1) {
    echo "<div class=\"not-success\">ERROR: An error occured while processing the file. Try again.</div>";
    exit();
}
///////////////////===================================================/////////////
$file_name = "verification-{$id}-{$rand_no}.xlsx";

if(move_uploaded_file($file_temp_name, $file_name)){

////////===================== Read File ====================///////

if(file_exists($file_name)){

require_once("../includes/simplexlsx.class.php");

if ($xlsx = SimpleXLSX::parse($file_name)) {
$file_rows_array = $xlsx->rows();
	
for($i = 0; $i < count($file_rows_array); $i++){
  
$names = test_input($file_rows_array[$i][0]);
$course = test_input($file_rows_array[$i][1]);
$grade = test_input($file_rows_array[$i][2]);
$year_of_graduation = test_input($file_rows_array[$i][3]);
$matric_no = test_input($file_rows_array[$i][4]);

if($i > 0){
if(!empty($names) && !empty($course) && !empty($grade) && !empty($year_of_graduation)){	
$sn++;
$details_category .= "{$names}+*+*{$course}+*+*{$grade}+*+*{$year_of_graduation}+*+*{$matric_no}-/-/";
$table[] = array($sn, $names, $course, $grade, $year_of_graduation, $matric_no);
?>
<tr><td><?php echo $sn; ?></td><td><?php echo $names; ?></td><td><?php echo $course; ?></td><td><?php echo $grade; ?></td><td><?php echo $year_of_graduation; ?></td><td><?php echo $matric_no; ?></td></tr>
<?php
}
}

}

}else{
echo SimpleXLSX::parse_error();
}

unlink($file_name);
}else{
echo "<div class='not-success'>Error occured while reading the file. Please upload again.</div>";
}
////////////////////////////=========================================////////////

}
?>
</table></p>
<?php

$widths = array(7,52,52,25,18,38);
$pdf->plot_table($widths, $lineheight, $table);
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

}
//////=============Ends Summary=============////////
?>
<input type="hidden" name="details_category" value="<?php echo $details_category; ?>">

<?php if(!empty($invoice_attachment)){ ?>
<p class="special-p"><?php echo $invoice_attachment; ?></p>
<?php } ?>

<p class="special-p">We thank you for your usual cooperation.</p>

<p class="special-p">Yours faithfully, <br />
<b>For: RISK CONTROL SERVICES NIGERIA LIMITED</b></p>

<table class="inner" style="width:100%;"><tbody>
<tr style="height:30px;"><?php if(!empty($signatory)){ ?><td style="width:300px; padding-bottom:0px!important; vertical-align:bottom;"><img src="<?php echo $signature_name; ?>"></td><?php } ?></tr>
<tr><?php if(!empty($signatory)){ ?><td style="width:300px; padding-bottom:0px!important;"><?php echo $signatory_name; ?></td><?php } ?></tr>
<tr><?php if(!empty($signatory)){ ?><th style="padding-top:0px!important; font-style:italic;"><?php echo $designation; ?></th><?php } ?></tr>
</tbody></table>

</td></tr>
</table>

<?php 
$pdf->Output("F","../pdf-reports/{$id}-temp-text-data.pdf");

////////======Image Files=============/////
$pdf = new PDF();
$pdf->AliasNbPages();

if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_FILES["request_file"])){ 
include_once("../includes/resize-image.php");

$file_num = count($_FILES["request_file"]);

foreach (glob("../gen-temp/{$id}-temp-*.*") as $filename) {
unlink($filename);
}

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
else if (!preg_match("/.(gif|GIF|jpg|JPG|png|PNG|jpeg|JPEG|tif|TIF|tiff|TIFF)$/i", $file_name) ) {
     echo "<div class=\"not-success\">ERROR: Your image b2 was not .gif, .jpg, .jpeg, .png or .tif.</div>";
     unlink($file_temp_name);
     exit();
}
else if ($file_error_message == 1) {
    echo "<div class=\"not-success\">ERROR: An error occured while processing the file. Try again.</div>";
    exit();
}
else if ($info[2] != 1 && $info[2] != 2 && $info[2] != 3 && $info[2] != 7 && $info[2] != 8) {
     echo "<div class=\"not-success\">ERROR: Your image 1 was not .gif, .jpg, .jpeg, .png or .tif.</div>";
     exit();
}

$file_name = "../gen-temp/{$id}-temp-" . rand(1000,9999) . ".{$file_extension}";
$move_file = move_uploaded_file($file_temp_name, $file_name);
if ($move_file != true) {
    echo "<div class=\"not-success\">ERROR: File not uploaded. Try again.</div>";
    unlink($file_temp_name);
    exit();
}else{

$target_file = $file_name;
$resized_file = $file_name;
image_resize($target_file, $resized_file, $file_extension, 710, 950);

echo "<div><img src=\"images/{$file_name}\"></div><br>";

$pdf->AddPage("P", "A4");
$pdf->Image($file_name,10,30);
}

}

$pdf->Output("F","../pdf-reports/{$id}-temp-image-data.pdf");

}
?>

</div>
<div>

<input type="hidden" name="send_request" value="1">
<button type="submit" class="btn gen-btn float-right">Send request</button>

</div>
</form>
<?php
}















/////============Verfication Reminder=============////
if(check_admin("manage_cover_letters") == 1 && !empty($verification_reminder)){

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

$request_id = tp_input("request_id");
$reference_no = tp_input("reference_no");
$completion_date = tp_input("completion_date");
$completion_date = (!empty($completion_date))?date_format(date_create($completion_date),"jS F, Y"):"";
$client = np_input("client");
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$client_address = in_table("address","reg_users","WHERE id = '{$client}'","address");
$client_region = in_table("region","reg_users","WHERE id = '{$client}'","region");
$client_city = in_table("city","reg_users","WHERE id = '{$client}'","city");
$client_state = in_table("state","reg_users","WHERE id = '{$client}'","state");
$client_designation = tp_input("client_designation");
$client_department = tp_input("client_department");
$attention = tp_input("attention");
$re = tp_input("re");
$invoice_attachment = tp_input("invoice_attachment");
$signatory = tp_input("signatory");
$signatory_name = in_table("name","reg_users","WHERE id = '{$signatory}'","name");
$designation = in_table("designation","reg_users","WHERE id = '{$signatory}'","designation");
$signature_array = glob("../images/signatures/{$signatory}pic*.*");
$signature_name = (file_exists($signature_array[0]))?"images/" . $signature_array[0]:"../images/post.jpg";

$pdf = new NPDF();

$pdf->AliasNbPages();

$pdf->SetAutoPageBreak(true,31);
$pdf->AddPage("P", "A4");
$pdf->Header("REMINDER");
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

$pdf->WriteHTML("We write to remind you of the verification in favour of the candidates below:");
$pdf->Ln(8);
?>

<style>
<!--
div .header{
margin-top:10px;
}
hr{
border:1px solid #000;
margin-top:5px;
margin-bottom:5px;
}
.inner2 th, .inner2 td{
border:1px solid #000;
padding:4px!important;
}
p.special-p, p.special-p *, .header, .header *, table:not(.summary-table) tr *{
font-size:12px!important;
}
-->
</style>

<form action="<?php echo $admin; ?>print-report" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
<input type="hidden" name="cover_letter_type" value="8"> 
<input type="hidden" name="request_id" value="<?php echo $request_id; ?>">
<input type="hidden" name="reference_no" value="<?php echo $reference_no; ?>">
<input type="hidden" name="reference_no" value="<?php echo $reference_no; ?>">
<input type="hidden" name="completion_date" value="<?php echo tp_input("completion_date"); ?>">
<input type="hidden" name="client" value="<?php echo $client; ?>">
<input type="hidden" name="client_designation" value="<?php echo $client_designation; ?>">
<input type="hidden" name="client_department" value="<?php echo $client_department; ?>">
<input type="hidden" name="attention" value="<?php echo $attention; ?>">
<input type="hidden" name="re" value="<?php echo $re; ?>">
<input type="hidden" name="invoice_attachment" value="<?php echo $invoice_attachment; ?>">
<input type="hidden" name="signatory" value="<?php echo $signatory; ?>">

<div class="reply-content-wrapper" style="background:url(images/rcs-seal.jpg) repeat left top;">

<table class="body-cert-report" style="border:0px;">
<tr><td class="outer">

<p class="header">Request ID: <?php echo $request_id; ?></p>
<p class="header" style="margin-top:15px;">Our Ref.: <?php echo $reference_no; ?></p>
<p style="margin-top:15px;"><?php echo $completion_date; ?></p>

<p class="special-p"><b><?php echo $client_name; ?></b><br>
<b><?php echo $client_designation; ?></b><br>
<?php echo (!empty($client_department))?$client_department . ",<br>":""; ?>
<?php echo (!empty($client_address))?$client_address . ",<br>":""; ?>
<?php echo (!empty($client_region))?$client_region . ",<br>":""; ?>
<?php echo (!empty($client_city))?$client_city . ",<br>":""; ?>
<?php echo (!empty($client_state))?$client_state . ".":""; ?></p>

<p class="header"><?php echo $attention; ?>,</p>

<p class="header"><u>RE: <?php echo $re; ?></u></p>

<p class="special-p">We write to remind you of the verification in favour of the candidates below:</p>

<?php 
$details_category = "";
?>
<p><table class="inner inner2">
<tr><th>S/N</th><th>NAMES</th><th>COURSE</th><th>GRADE</th><th>YEAR OF GRAD.</th><th>MATRIC NO.</th></tr>
<?php
//////=============Summary=============////////
if(isset($_POST["names"]) && !empty($_POST["names"])){ 

$lineheight = 5;
$table = array();
$pdf->SetFont('Arial','B',9);
$table[] = array("S/N", "NAMES", "COURSE", "GRADE", "YEAR OF GRAD.", "MATRIC NO.");
$widths = array(7,52,52,25,18,38);
$pdf->plot_table($widths, $lineheight, $table);

$table = array();
$pdf->SetFont('Arial','',8);

$sn=0;
foreach($_POST["names"] as $value){ 
$names = test_input($value);
$course = test_input($_POST["course"][$sn]);
$grade = test_input($_POST["grade"][$sn]);
$year_of_graduation = test_input($_POST["year_of_graduation"][$sn]);
$matric_no = test_input($_POST["matric_no"][$sn]);
if(!empty($names)){
$sn++;
$details_category .= "{$names}+*+*{$course}+*+*{$grade}+*+*{$year_of_graduation}+*+*{$matric_no}-/-/";
$table[] = array($sn, $names, $course, $grade, $year_of_graduation, $matric_no);
?>
<tr><td><?php echo $sn; ?></td><td><?php echo $names; ?></td><td><?php echo $course; ?></td><td><?php echo $grade; ?></td><td><?php echo $year_of_graduation; ?></td><td><?php echo $matric_no; ?></td></tr>
<?php
}
}

}

////////////// Upload Excel File //////////////////////////////  
if(isset($_FILES["ufile"]["tmp_name"]) && !empty($_FILES["ufile"]["tmp_name"])){ 

$file_name = $_FILES["ufile"]["name"]; 
$file_temp_name = $_FILES["ufile"]["tmp_name"];
$file_error_message = $_FILES["ufile"]["error"];
$file_name_2_array = explode(".", $file_name);
$file_extension = end($file_name_2_array);

////======================= Access File ===============/////
if (!$file_temp_name) {
    echo "<div class=\"not-success\">ERROR: Please browse for a file before clicking the upload button.</div>";
    exit();
} 
else if (!preg_match("/.(xlsx|XLSX)$/i", $file_name) ) {
     echo "<div class=\"not-success\">ERROR: Your file was not .xlsx .</div>";
     unlink($file_temp_name);
     exit();
}
else if ($file_error_message == 1) {
    echo "<div class=\"not-success\">ERROR: An error occured while processing the file. Try again.</div>";
    exit();
}
///////////////////===================================================/////////////
$file_name = "verification-{$id}-{$rand_no}.xlsx";

if(move_uploaded_file($file_temp_name, $file_name)){

////////===================== Read File ====================///////
if(file_exists($file_name)){

require_once("../includes/simplexlsx.class.php");

if ($xlsx = SimpleXLSX::parse($file_name)) {
$file_rows_array = $xlsx->rows();
	
for($i = 0; $i < count($file_rows_array); $i++){
  
$names = test_input($file_rows_array[$i][0]);
$course = test_input($file_rows_array[$i][1]);
$grade = test_input($file_rows_array[$i][2]);
$year_of_graduation = test_input($file_rows_array[$i][3]);
$matric_no = test_input($file_rows_array[$i][4]);

if($i > 0){
if(!empty($names) && !empty($course) && !empty($grade) && !empty($year_of_graduation)){	
$sn++;
$details_category .= "{$names}+*+*{$course}+*+*{$grade}+*+*{$year_of_graduation}+*+*{$matric_no}-/-/";
$table[] = array($sn, $names, $course, $grade, $year_of_graduation, $matric_no);
?>
<tr><td><?php echo $sn; ?></td><td><?php echo $names; ?></td><td><?php echo $course; ?></td><td><?php echo $grade; ?></td><td><?php echo $year_of_graduation; ?></td><td><?php echo $matric_no; ?></td></tr>
<?php
}
}

}

}else{
echo SimpleXLSX::parse_error();
}

unlink($file_name);

}else{
echo "<div class='not-success'>Error occured while reading the file. Please upload again.</div>";
}
////////////////////////////=========================================////////////

}
?>
</table></p>
<?php

$widths = array(7,52,52,25,18,38);
$pdf->plot_table($widths, $lineheight, $table);
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

}
//////=============Ends Summary=============////////
?>
<input type="hidden" name="details_category" value="<?php echo $details_category; ?>">

<?php if(!empty($invoice_attachment)){ ?>
<p class="special-p"><?php echo $invoice_attachment; ?></p>
<?php } ?>

<p class="special-p">We thank you for your usual cooperation.</p>

<p class="special-p">Yours faithfully, <br />
<b>For: RISK CONTROL SERVICES NIGERIA LIMITED</b></p>

<table class="inner" style="width:100%;"><tbody>
<tr style="height:30px;"><?php if(!empty($signatory)){ ?><td style="width:300px; padding-bottom:0px!important; vertical-align:bottom;"><img src="<?php echo $signature_name; ?>"></td><?php } ?></tr>
<tr><?php if(!empty($signatory)){ ?><td style="width:300px; padding-bottom:0px!important;"><?php echo $signatory_name; ?></td><?php } ?></tr>
<tr><?php if(!empty($signatory)){ ?><th style="padding-top:0px!important; font-style:italic;"><?php echo $designation; ?></th><?php } ?></tr>
</tbody></table>

</td></tr>
</table>

<?php 
$pdf->Output("F","../pdf-reports/{$id}-temp-text-data.pdf");

////////======Image Files=============/////
$pdf = new PDF();
$pdf->AliasNbPages();

if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_FILES["request_file"])){ 
include_once("../includes/resize-image.php");

$file_num = count($_FILES["request_file"]);

foreach (glob("../gen-temp/{$id}-temp-*.*") as $filename) {
unlink($filename);
}

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

$file_name = "../gen-temp/{$id}-temp-" . rand(1000,9999) . ".{$file_extension}";
$move_file = move_uploaded_file($file_temp_name, $file_name);
if ($move_file != true) {
    echo "<div class=\"not-success\">ERROR: File not uploaded. Try again.</div>";
    unlink($file_temp_name);
    exit();
}else{

$target_file = $file_name;
$resized_file = $file_name;
image_resize($target_file, $resized_file, $file_extension, 710, 950);

echo "<div><img src=\"images/{$file_name}\"></div><br>";

$pdf->AddPage("P", "A4");
$pdf->Image($file_name,10,30);
}

}

$pdf->Output("F","../pdf-reports/{$id}-temp-image-data.pdf");

}
?>

</div>
<div>

<input type="hidden" name="send_reminder" value="1">
<button type="submit" class="btn gen-btn float-right">Send reminder</button>

</div>
</form>
<?php
}
?>

<script>
<!--
$(document).ready(function () {

///////////////////////////////
$("body").find(".cover-letter-form").on("submit", function(e) {
e.preventDefault();  
var formdata = new FormData(this);
var page_url = $(this).attr("action");
var page_result = "save-btn";
var this_name = $(this).attr("name");
var this_lang = $(this).attr("lang");
var this_title = $(this).attr("title");

document.getElementById("save-btn").innerHTML = "Saving...";
$.ajax({
url: page_url,
type: "POST",
data: formdata,
mimeTypes:"multipart/form-data",
contentType: false,
cache: false,
processData: false,
success: function(data){
document.getElementById("save-btn").innerHTML = "Saved";
document.getElementById("save-btn").onclick = "";
},error: function(){
alert("Error occured!");
document.getElementById("save-btn").innerHTML = "Try again";
}
});

});

//////////////////////
});
//-->
</script>

</body>
<?php
$db->disconnect();
detectCurrUserBrowser('</td></tr></table>','',7); ?>
</html>