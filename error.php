<?php
// Date in the past
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

ini_set('session.gc_maxlifetime', 86400);
session_start();

require_once("classes/db-class.php");
require_once("includes/functions.php");

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
?>

<!DOCTYPE html>
<html lang="en-US" dir="ltr">
<head>
<base href="<?php directory(); ?>" target="_top">
<meta charset="UTF-8" />
<meta name="description" content="<?php echo $full_gen_name; ?>"/>
<meta name="robots" content="noodp"/>
<meta name="keywords" content="<?php echo $full_gen_name; ?>"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
<meta name="apple-mobile-web-app-capable" content="yes">
<title>Error - <?php echo $full_gen_name; ?></title>

<meta property="og:url" content="<?php directory(); ?>" /> 
<meta property="og:type" content="article" />
<meta property="og:title" content="<?php echo $full_gen_name; ?>" /> 
<meta property="og:description" content="<?php echo $full_gen_name; ?>" /> 
<meta property="og:image" content="<?php directory(); ?>images/gen-logo.png" />
<meta property="og:image:type" content="image/png" />
<meta property="og:image:width" content="210" />
<meta property="og:image:height" content="210" />

<link rel="shortcut icon" href="images/favicon.png"/>
<link type="text/css" rel="stylesheet" href="css/bootstrap.css" />
<link type="text/css" rel="stylesheet" href="css/font-awesome.css" />
<link type="text/css" rel="stylesheet" href="css/style.css" />
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/bootstrap.min.js" type="text/javascript"></script>

<style>
<!--
.error-header, .error-header *{
font-size:50px;
color:#900;
}
.error-message{
font-size:50px;
}
-->
</style>

</head>
<?php detectCurrUserBrowser('<table width="100%"><tr><td>','',7); ?>
<body>

<style>
<!--
.header2{
padding-bottom:50px;
}
-->
</style>
<div class="header-wrapper header-wrapper1">
<div class="header header1">
<a href="<?php directory(); ?>" class="float-left"><img src="images/risk-control-logo.jpg"></a>

<a href="https://riskcontrolnigeria.com" class="float-right"><img src="images/risk-control-logo.jpg"></a>
</div>
</div>

<div class="container both-border" style="min-height:500px; padding-top:50px;"> 

<div class="error-header"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Error</div>

<div class="error-message">Hello! We are sorry. Your request is not available.</div>

</div>

<?php require_once("includes/footer.php"); ?>