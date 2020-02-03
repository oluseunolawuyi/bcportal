<?php
// Date in the past
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

ini_set('session.gc_maxlifetime', 86400);
session_start();

require_once("../classes/db-class.php");
require_once("functions.php");

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
<meta name="description" content="<?php echo title_link(basename($_SERVER["PHP_SELF"],".php")) . " - " . $full_gen_name; ?>"/>
<meta name="robots" content="noodp"/>
<meta name="keywords" content="<?php echo title_link(basename($_SERVER["PHP_SELF"],".php")) . " - " . $full_gen_name; ?>"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
<meta name="apple-mobile-web-app-capable" content="yes">
<title><?php echo title_link(basename($_SERVER["PHP_SELF"],".php")) . " - " . $full_gen_name; ?></title>

<meta property="og:url" content="<?php directory(); ?>" /> 
<meta property="og:type" content="article" />
<meta property="og:title" content="<?php echo title_link(basename($_SERVER["PHP_SELF"],".php")) . " - " . $full_gen_name; ?>" /> 
<meta property="og:description" content="<?php echo title_link(basename($_SERVER["PHP_SELF"],".php")) . " - " . $full_gen_name; ?>" /> 
<meta property="og:image" content="<?php directory(); ?>images/gen-logo.png" />
<meta property="og:image:type" content="image/png" />
<meta property="og:image:width" content="210" />
<meta property="og:image:height" content="210" />

<link rel="shortcut icon" href="images/favicon.png"/>
<link type="text/css" rel="stylesheet" href="css/bootstrap.css" />
<link type="text/css" rel="stylesheet" href="css/font-awesome.css" />
<link type="text/css" rel="stylesheet" href="css/style.css" />
<link type="text/css" rel="stylesheet" href="css/owl.carousel.css" />
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/bootstrap.min.js" type="text/javascript"></script>
<script src="js/jquery.jcarousellite.js"></script>
<script src="js/owl.carousel.js"></script>
</head>
<?php detectCurrUserBrowser('<table width="100%"><tr><td>','',7); ?>
<body>

<div class="header-wrapper header-wrapper1" id="bodyDiv">
<div class="header header1">
<?php if(isset($_SESSION["login"])){  ?>
<a onClick="javascript:my_confirm('Logout Confirmation','Are you sure you want to log out?','<?php echo $directory . $admin; ?>?logout=1');"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</a>
<a href="<?php echo $admin; ?>profile.php"><i class="fa fa-user" aria-hidden="true"></i> My Profile</a>
<?php }else{  ?>
<a href="<?php echo $privates; ?>login.php"><i class="fa fa-sign-in" aria-hidden="true"></i> Login</a>
<a href="<?php echo $privates; ?>register.php"><i class="fa fa-laptop" aria-hidden="true"></i> Register</a>
<?php } ?>
</div>
</div>

<div class="header-wrapper header-wrapper2">
<div class="header header2">
<a href="<?php directory(); ?>"><img src="images/logo2.png"></a>

<button class="collapse"><span></span><span></span><span></span></button>
<ul>
<li><a href="<?php directory(); ?>"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
<li><a href="<?php echo $privates; ?>about-us.php" class="<?php echo current_page("about-us"); ?>"><i class="fa fa-university" aria-hidden="true"></i> About Us</a></li>
<li><a href="<?php echo $privates; ?>terms-and-conditions.php" class="<?php echo current_page("terms-and-conditions"); ?>"><i class="fa fa-list" aria-hidden="true"></i> Terms & Conditions</a></li>
<li><a href="<?php echo $privates; ?>contact-us.php" class="<?php echo current_page("contact-us"); ?>"><i class="fa fa-phone" aria-hidden="true"></i> Contact Us</a></li>
</ul>
</div>
</div>