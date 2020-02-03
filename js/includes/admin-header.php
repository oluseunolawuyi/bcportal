<?php
// Date in the past
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

ini_set('session.gc_maxlifetime', 86400);
session_start();

require_once("../classes/db-class.php");
require_once("functions.php");

if(!isset($_SESSION["login"])){
redirect("{$directory}{$privates}login.php");
$_SESSION["msg"] = "<div class='success'>You are not logged in. Kindly log in to continue...</div>";
}

if(isset($_REQUEST["logout"])){
unset($_SESSION["login"]);
$_SESSION["msg"] = "<div class='success'>You are successfully logged out. Kindly log in to continue...</div>";

$db->query("UPDATE reg_users SET logged_in = '0', last_login = '{$date_time}' WHERE id = '{$id}'");

redirect("{$directory}{$privates}login.php");
}

$last_login = in_table("last_login","reg_users","WHERE id = '$id'","last_login");
$blocked = in_table("blocked","reg_users","WHERE id = '$id'","blocked");

if($blocked == 1){
redirect("{$directory}{$privates}login.php");
$_SESSION["msg"] = "<div class='not-success'>Hi {$user_name}! Your account is declined. Kindly contact the admin <a href='{$privates}contact-us.php'>HERE</a>.</div>";
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
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/font-awesome.css">
<link rel="stylesheet" href="css/portal.css">
<link rel="stylesheet" href="css/jquery-ui.css">
<link rel="stylesheet" href="css/select2.min.css">
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery-ui.js"></script>
<script src="js/select2.min.js"></script>
</head>
<?php detectCurrUserBrowser('<table width="100%"><tr><td>','',7); ?>
<body>
<div class="header-wrapper" id="bodyDiv">
<div class="header">
<a href="<?php directory(); ?>" class="logo-link"><img src="images/logo2.png"></a>
<span>
<?php
$file_array = glob("../images/users/{$id}pic*.*");
$file_name = ($file_array)?"images/" . $file_array[0]:"images/post.jpg";
?><a href="<?php echo $admin; ?>profile.php"><img src="<?php echo $file_name; ?>" ><br>
<i class="fa fa-user" aria-hidden="true"></i> <?php echo $user_name; ?></a>
</span>
<button class="collapse"><span></span><span></span><span></span></button>
</div>
</div>

<div class="portal-wrapper">

<div class="portal-nav portal-content">

<a href="<?php echo $admin; ?>" class="main-menu <?php echo current_page("index"); ?>"><i class="fa fa-dashboard" aria-hidden="true"></i> Dashboard</a>

<?php
if($is_admin == 1){
?>
<a id="setup-menu" class="main-menu <?php echo (!empty(current_page("manage-users")) || !empty(current_page("newsletter-subscribers")) || !empty(current_page("currencies-setup")) || !empty(current_page("currencies-activation")) || !empty(current_page("manage-plans")) || !empty(current_page("manage-sub-plans")) || !empty(current_page("manage-bonuses")) || !empty(current_page("manage-penalties")) || !empty(current_page("referral-setup")))?"main-current":""; ?>"><i class="fa fa-cog" aria-hidden="true"></i> Setup</a>
<div id="setup-menu-div" class="sub-menu">
<a href="<?php echo $admin; ?>manage-users.php" class="<?php echo current_page("manage-users"); ?>"><i class="fa fa-user"></i> Manage Users</a>
<a href="<?php echo $admin; ?>newsletter-subscribers.php" class="<?php echo current_page("newsletter-subscribers"); ?>"><i class="fa fa-users" aria-hidden="true"></i> Subscribers</a>
<a href="<?php echo $admin; ?>currencies-setup.php" class="<?php echo current_page("currencies-setup"); ?>"><i class="fa fa-money"></i> Currencies Setup</a>
<a href="<?php echo $admin; ?>currencies-activation.php" class="<?php echo current_page("currencies-activation"); ?>"><i class="fa fa-money"></i> Currencies Activation</a>
<a href="<?php echo $admin; ?>manage-plans.php" class="<?php echo current_page("manage-plans"); ?>"><i class="fa fa-tag" aria-hidden="true"></i> Manage Plans</a>
<a href="<?php echo $admin; ?>manage-sub-plans.php" class="<?php echo current_page("manage-sub-plans"); ?>"><i class="fa fa-tag" aria-hidden="true"></i> Manage Sub-Plans</a>
<a href="<?php echo $admin; ?>manage-bonuses.php" class="<?php echo current_page("manage-bonuses"); ?>"><i class="fa fa-money" aria-hidden="true"></i> Manage Bonuses</a>
<a href="<?php echo $admin; ?>manage-penalties.php" class="<?php echo current_page("manage-penalties"); ?>"><i class="fa fa-money" aria-hidden="true"></i> Manage Penalties</a>
<a href="<?php echo $admin; ?>referral-setup.php" class="<?php echo current_page("referral-setup"); ?>"><i class="fa fa-reply" aria-hidden="true"></i> Referral Setup</a>
</div>

<a id="logs-menu" class="main-menu <?php echo (!empty(current_page("all-transactions-log")) || !empty(current_page("all-deposits-history")) || !empty(current_page("all-withdrawals-history")) || !empty(current_page("all-pending-withdrawals")) || !empty(current_page("all-referred-users")) || !empty(current_page("all-referral-commissions")))?"main-current":""; ?>"><i class="fa fa-list" aria-hidden="true"></i> Logs</a>
<div id="logs-menu-div" class="sub-menu">
<a href="<?php echo $admin; ?>all-transactions-log.php" class="<?php echo current_page("all-transactions-log"); ?>"><i class="fa fa-tasks"></i> Transaction Log</a>
<a href="<?php echo $admin; ?>all-deposits-history.php" class="<?php echo current_page("all-deposits-history"); ?>"><i class="fa fa-history" aria-hidden="true"></i> Deposit History</a>
<a href="<?php echo $admin; ?>all-withdrawals-history.php" class="<?php echo current_page("all-withdrawals-history"); ?>"><i class="fa fa-history" aria-hidden="true"></i> Withdrawal History</a>
<a href="<?php echo $admin; ?>all-pending-withdrawals.php" class="<?php echo current_page("all-pending-withdrawals"); ?>"><i class="fa fa-refresh" aria-hidden="true"></i> Pending Withdrawals</a>
<a href="<?php echo $admin; ?>all-referred-users.php" class="<?php echo current_page("all-referred-users"); ?>"><i class="fa fa-reply" aria-hidden="true"></i> Referred Users</a>
<a href="<?php echo $admin; ?>all-referral-commissions.php" class="<?php echo current_page("all-referral-commissions"); ?>"><i class="fa fa-money" aria-hidden="true"></i> Referral Commissions</a>
</div>

<a id="gen-messages-menu" class="main-menu <?php echo (!empty(current_page("general-inbox")) || !empty(current_page("auto-sent-messages")) || !empty(current_page("newsletters")) || !empty(current_page("new-message")))?"main-current":""; ?>"><i class="fa fa-envelope" aria-hidden="true"></i> General Messages</a>
<div id="gen-messages-menu-div" class="sub-menu">
<a href="<?php echo $admin; ?>general-inbox.php" class="<?php echo current_page("general-inbox"); ?>"><i class="fa fa-inbox" aria-hidden="true"></i> General Inbox</a>
<a href="<?php echo $admin; ?>auto-sent-messages.php" class="<?php echo current_page("auto-sent-messages"); ?>"><i class="fa fa-send" aria-hidden="true"></i> Auto-Sent Msgs.</a>
<a href="<?php echo $admin; ?>newsletter.php" class="<?php echo current_page("newsletter"); ?>"><i class="fa fa-envelope" aria-hidden="true"></i> Newsletters</a>
<a href="<?php echo $admin; ?>new-message.php" class="<?php echo current_page("new-message"); ?>"><i class="fa fa-envelope-o" aria-hidden="true"></i> New Message</a>
</div>
<?php
}
?>

<?php 
if($is_admin == 0){
?>
<a id="tansactions-menu" class="main-menu <?php echo (!empty(current_page("make-deposit")) || !empty(current_page("deposit-history")) || !empty(current_page("make-withdrawal")) || !empty(current_page("pending-withdrawals")) || !empty(current_page("withdrawal-history")) || !empty(current_page("transaction-log")) || !empty(current_page("view-bonuses")) || !empty(current_page("view-penalties")) || !empty(current_page("referred-users")) || !empty(current_page("referral-commissions")))?"main-current":""; ?>"><i class="fa fa-money" aria-hidden="true"></i> Tansactions</a>
<div id="tansactions-menu-div" class="sub-menu">
<a href="<?php echo $admin; ?>make-deposit.php" class="<?php echo current_page("make-deposit"); ?>"><i class="fa fa-arrow-circle-down" aria-hidden="true"></i> Make Deposit</a>
<a href="<?php echo $admin; ?>deposit-history.php" class="<?php echo current_page("deposit-history"); ?>"><i class="fa fa-history" aria-hidden="true"></i> Deposit History</a>
<a href="<?php echo $admin; ?>make-withdrawal.php" class="<?php echo current_page("make-withdrawal"); ?>"><i class="fa fa-arrow-circle-up" aria-hidden="true"></i> Make Withdrawal</a>
<a href="<?php echo $admin; ?>pending-withdrawals.php" class="<?php echo current_page("pending-withdrawals"); ?>"><i class="fa fa-refresh" aria-hidden="true"></i> Pending Withdrawals</a>
<a href="<?php echo $admin; ?>withdrawal-history.php" class="<?php echo current_page("withdrawal-history"); ?>"><i class="fa fa-history" aria-hidden="true"></i> Withdrawal History</a>
<a href="<?php echo $admin; ?>transaction-log.php" class="<?php echo current_page("transaction-log"); ?>"><i class="fa fa-tasks" aria-hidden="true"></i> Transaction Log</a>
<a href="<?php echo $admin; ?>view-bonuses.php" class="<?php echo current_page("view-bonuses"); ?>"><i class="fa fa-money" aria-hidden="true"></i> View Bonuses</a>
<a href="<?php echo $admin; ?>view-penalties.php" class="<?php echo current_page("view-penalties"); ?>"><i class="fa fa-money" aria-hidden="true"></i> View Penalties</a>
<a href="<?php echo $admin; ?>referred-users.php" class="<?php echo current_page("referred-users"); ?>"><i class="fa fa-reply" aria-hidden="true"></i> Referred Users</a>
<a href="<?php echo $admin; ?>referral-commissions.php" class="<?php echo current_page("referral-commissions"); ?>"><i class="fa fa-money" aria-hidden="true"></i> Referral Commissions</a>
</div>
<?php
}
?>

<a id="general-menu" class="main-menu <?php echo (!empty(current_page("inbox")) || !empty(current_page("sent-messages")) || !empty(current_page("profile")) || !empty(current_page("reset-password")))?"main-current":""; ?>"><i class="fa fa-diamond" aria-hidden="true"></i> General</a>
<div id="general-menu-div" class="sub-menu">
<a href="<?php echo $admin; ?>inbox.php" class="<?php echo current_page("inbox"); ?>"><i class="fa fa-inbox" aria-hidden="true"></i> Inbox</a>
<a href="<?php echo $admin; ?>sent-messages.php" class="<?php echo current_page("sent-messages"); ?>"><i class="fa fa-send" aria-hidden="true"></i> Sent Messages</a>
<a href="<?php echo $admin; ?>profile.php" class="<?php echo current_page("profile"); ?>"><i class="fa fa-user" aria-hidden="true"></i> Profile</a>
<a href="<?php echo $admin; ?>reset-password.php" class="<?php echo current_page("reset-password"); ?>"><i class="fa fa-lock" aria-hidden="true"></i> Reset Password</a>
</div>

<a class="main-menu" onClick="javascript:my_confirm('Logout Confirmation','Are you sure you want to log out?','<?php echo $directory . $admin; ?>?logout=1');"><i class="fa fa-sign-out"></i> Log Out</a>
</div>

<div class="portal-body portal-content">
<div class="<?php echo (basename($_SERVER["PHP_SELF"],".php") == "index")?"portal-body-wrapper":"body-content form-div"; ?>">