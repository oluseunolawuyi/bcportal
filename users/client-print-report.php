<?php 
ini_set('upload_max_filesize', '1000M');
//echo ini_get('upload_max_filesize'), ", " , ini_get('post_max_size');

ini_set("max_execution_time", 600); //300 seconds = 5 minutes

include_once("../includes/gen-header.php"); 

if(empty($is_client)){ 
redirect($directory . $admin);
}

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

div table.body-print-report td.outer{
padding:20px;
}
div table.body-print-report table.inner *{
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
if(!empty($is_client) && !empty($view)){
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
?>

</body>
<?php
$db->disconnect();
detectCurrUserBrowser('</td></tr></table>','',7); ?>
</html>