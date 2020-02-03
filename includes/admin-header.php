<?php
// Date in the past
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

ini_set('session.gc_maxlifetime', 3600);
session_start();

require_once("../classes/db-class.php");
require_once("functions.php");

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
<title><?php echo (basename($_SERVER["PHP_SELF"],".php") == "index")?"Dashboard":title_link(basename($_SERVER["PHP_SELF"],".php")); ?> - <?php echo $full_gen_name; ?></title>
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
<a href="<?php directory(); ?>" class="float-left logo-link"><img src="images/risk-control-logo.jpg"></a>
<span>
<?php
$file_array = glob("../images/users/{$id}pic*.*");
$file_name = ($file_array)?"images/" . $file_array[0]:"images/post.jpg";
?><a href="<?php echo $admin; ?>profile"><img src="<?php echo $file_name; ?>" ><br>
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
if(check_admin("manage_admin_users") == 1 || check_admin("admin_analysis") == 1 || check_admin("manage_clients") == 1 || check_admin("bulk_client_upload") == 1 || check_admin("manage_agents") == 1 || check_admin("manage_bc_verification_types") == 1 || check_admin("manage_bc_education_types") == 1 || check_admin("manage_status_types") == 1 || check_admin("manage_recommendation_types") == 1 || check_admin("role_management") == 1){
?>
<a id="setup-menu" class="main-menu <?php echo (!empty(current_page("manage-admin-users")) || !empty(current_page("admin-analysis")) || !empty(current_page("manage-clients")) || !empty(current_page("bulk-clients-data-upload")) || !empty(current_page("manage-agents")) || !empty(current_page("bc-verification-types")) || !empty(current_page("recommendation-types")) || !empty(current_page("bc-education-types")) || !empty(current_page("status-types")) || !empty(current_page("role-management")))?"main-current":""; ?>"><i class="fa fa-cog" aria-hidden="true"></i> Setup</a>
<div id="setup-menu-div" class="sub-menu">
<?php if(check_admin("manage_admin_users") == 1){ ?>
<a href="<?php echo $admin; ?>manage-admin-users" class="<?php echo current_page("manage-admin-users"); ?>"><i class="fa fa-user"></i> Manage Admin Users</a>
<?php } if(check_admin("admin_analysis") == 1){ ?>
<a href="<?php echo $admin; ?>admin-analysis" class="<?php echo current_page("admin-analysis"); ?>"><i class="fa fa-user"></i> Admin Analysis</a>
<?php } if(check_admin("manage_clients") == 1){ ?>
<a href="<?php echo $admin; ?>manage-clients" class="<?php echo current_page("manage-clients"); ?>"><i class="fa fa-user"></i> Manage Clients</a>
<?php } if(check_admin("bulk_client_upload") == 1){ ?>
<a href="<?php echo $admin; ?>bulk-clients-data-upload" class="<?php echo current_page("bulk-clients-data-upload"); ?>"><i class="fa fa-user"></i> Bulk Clients Upload</a>
<?php } if(check_admin("manage_agents") == 1){ ?>
<a href="<?php echo $admin; ?>manage-agents" class="<?php echo current_page("manage-agents"); ?>"><i class="fa fa-user"></i> Manage Agents</a>
<?php } if(check_admin("manage_bc_verification_types") == 1){ ?>
<a href="<?php echo $admin; ?>bc-verification-types" class="<?php echo current_page("bc-verification-types"); ?>"><i class="fa fa-list"></i> BC Verification Types</a>
<?php } if(check_admin("manage_bc_education_types") == 1){ ?>
<a href="<?php echo $admin; ?>bc-education-types" class="<?php echo current_page("bc-education-types"); ?>"><i class="fa fa-list"></i> BC Education Types</a>
<?php } if(check_admin("manage_status_types") == 1){ ?>
<a href="<?php echo $admin; ?>status-types" class="<?php echo current_page("status-types"); ?>"><i class="fa fa-list"></i> Status Types</a>
<?php } if(check_admin("manage_recommendation_types") == 1){ ?>
<a href="<?php echo $admin; ?>recommendation-types" class="<?php echo current_page("recommendation-types"); ?>"><i class="fa fa-list"></i> Recommendations</a>
<?php } if(check_admin("role_management") == 1){ ?>
<a href="<?php echo $admin; ?>role-management" class="<?php echo current_page("role-management"); ?>"><i class="fa fa-list"></i> Role Management</a>
<?php }?>
</div>
<?php }?>

<a id="report-menu" class="main-menu <?php echo (!empty(current_page("manage-bc-reports")) || !empty(current_page("manage-cv-reports")) || !empty(current_page("bulk-bc-reports")) || !empty(current_page("bulk-cv-reports")) || !empty(current_page("manage-clients-reports")) || !empty(current_page("manage-all-clients-files")))?"main-current":""; ?>"><i class="fa fa-list" aria-hidden="true"></i> Reports</a>
<div id="report-menu-div" class="sub-menu">
<?php if(check_admin("manage_bc_reports") == 1){ ?>
<a href="<?php echo $admin; ?>manage-bc-reports" class="<?php echo current_page("manage-bc-reports"); ?>"><i class="fa fa-file-text"></i> Manage BC Reports</a>
<?php } if(check_admin("manage_cv_reports") == 1){ ?>
<a href="<?php echo $admin; ?>manage-cv-reports" class="<?php echo current_page("manage-cv-reports"); ?>"><i class="fa fa-file-text"></i> Manage CV Reports</a>
<?php } if(check_admin("manage_bulk_bc_reports") == 1){ ?>
<a href="<?php echo $admin; ?>bulk-bc-reports" class="<?php echo current_page("bulk-bc-reports"); ?>"><i class="fa fa-file-text"></i> Bulk BC Reports</a>
<?php } if(check_admin("manage_bulk_cv_reports") == 1){ ?>
<a href="<?php echo $admin; ?>bulk-cv-reports" class="<?php echo current_page("bulk-cv-reports"); ?>"><i class="fa fa-file-text"></i> Bulk CV Reports</a>
<?php } if(check_admin("manage_clients_reports") == 1){ ?>
<a href="<?php echo $admin; ?>manage-clients-reports" class="<?php echo current_page("manage-clients-reports"); ?>"><i class="fa fa-list"></i> Clients Reports</a>
<a href="<?php echo $admin; ?>manage-all-clients-files" class="<?php echo current_page("manage-all-clients-files"); ?>"><i class="fa fa-file-text" aria-hidden="true"></i> All Clients&#039; Files</a>
<?php } ?>
</div>

<a id="report-log-menu" class="main-menu <?php echo (!empty(current_page("bc-report-log")) || !empty(current_page("cv-report-log")))?"main-current":""; ?>"><i class="fa fa-list" aria-hidden="true"></i> Report Log</a>
<div id="report-log-menu-div" class="sub-menu">
<a href="<?php echo $admin; ?>bc-report-log" class="<?php echo current_page("bc-report-log"); ?>"><i class="fa fa-file-text"></i> BC Report Log</a>
<a href="<?php echo $admin; ?>cv-report-log" class="<?php echo current_page("cv-report-log"); ?>"><i class="fa fa-file-text"></i> CV Report Log</a>
</div>

<a id="status-log-menu" class="main-menu <?php echo (!empty(current_page("pending-verified-information")) || !empty(current_page("cv-pending-tasks")))?"main-current":""; ?>"><i class="fa fa-user" aria-hidden="true"></i> Pending Tasks</a>
<div id="status-log-menu-div" class="sub-menu">
<a href="<?php echo $admin; ?>pending-verified-information" class="<?php echo current_page("pending-verified-information"); ?>"><i class="fa fa-list"></i> Pending Verified Info.</a>
<a href="<?php echo $admin; ?>cv-pending-tasks" class="<?php echo current_page("cv-pending-tasks"); ?>"><i class="fa fa-list"></i> CV Pending Tasks</a>
</div> 

<?php if(check_admin("manage_cover_letters") == 1){ ?>
<a id="cover-letter-menu" class="main-menu <?php echo (!empty(current_page("bc-cover-letter")) || !empty(current_page("cv-cover-letter")) || !empty(current_page("new-cv-cover-letter")) || !empty(current_page("bulk-cv-cover-letter")) || !empty(current_page("waec-cv-cover-letter")) || !empty(current_page("foreign-cv-cover-letter")) || !empty(current_page("verification-requests")) || !empty(current_page("verification-reminders")))?"main-current":""; ?>"><i class="fa fa-file-text" aria-hidden="true"></i> Cover Letters</a>
<div id="cover-letter-menu-div" class="sub-menu">
<a href="<?php echo $admin; ?>bc-cover-letter" class="<?php echo current_page("bc-cover-letter"); ?>"><i class="fa fa-list"></i> BC Cover Letter</a>
<a href="<?php echo $admin; ?>cv-cover-letter" class="<?php echo current_page("cv-cover-letter"); ?>"><i class="fa fa-list"></i> CV Cover Letter 1</a>
<a href="<?php echo $admin; ?>new-cv-cover-letter" class="<?php echo current_page("new-cv-cover-letter"); ?>"><i class="fa fa-list"></i> CV Cover Letter 2</a>
<a href="<?php echo $admin; ?>bulk-cv-cover-letter" class="<?php echo current_page("bulk-cv-cover-letter"); ?>"><i class="fa fa-list"></i> Bulk CV Cover Letter</a>
<a href="<?php echo $admin; ?>waec-cv-cover-letter" class="<?php echo current_page("waec-cv-cover-letter"); ?>"><i class="fa fa-list"></i> WAEC CV Cover Letter</a>
<a href="<?php echo $admin; ?>foreign-cv-cover-letter" class="<?php echo current_page("foreign-cv-cover-letter"); ?>"><i class="fa fa-list"></i> Foreign Cover Letter</a>
<a href="<?php echo $admin; ?>verification-requests" class="<?php echo current_page("verification-requests"); ?>"><i class="fa fa-list"></i> Verification Requests</a>
<a href="<?php echo $admin; ?>verification-reminders" class="<?php echo current_page("verification-reminders"); ?>"><i class="fa fa-list"></i> Manage Reminders</a>
</div>
<?php } ?>

<?php if(check_admin("manage_general_messages") == 1){ ?>
<a id="gen-messages-menu" class="main-menu <?php echo (!empty(current_page("general-inbox")) || !empty(current_page("auto-sent-messages")) || !empty(current_page("general-sent-messages")) || !empty(current_page("new-message")))?"main-current":""; ?>"><i class="fa fa-envelope" aria-hidden="true"></i> General Messages</a>
<div id="gen-messages-menu-div" class="sub-menu">
<a href="<?php echo $admin; ?>general-inbox" class="<?php echo current_page("general-inbox"); ?>"><i class="fa fa-inbox" aria-hidden="true"></i> General Inbox</a>
<a href="<?php echo $admin; ?>auto-sent-messages" class="<?php echo current_page("auto-sent-messages"); ?>"><i class="fa fa-send" aria-hidden="true"></i> Auto-Sent Msgs.</a>
<a href="<?php echo $admin; ?>general-sent-messages" class="<?php echo current_page("general-sent-messages"); ?>"><i class="fa fa-envelope" aria-hidden="true"></i> Gen. Sent Msgs.</a>
<a href="<?php echo $admin; ?>new-message" class="<?php echo current_page("new-message"); ?>"><i class="fa fa-envelope-o" aria-hidden="true"></i> New Message</a>
</div>
<?php
}

}
?>

<?php
if($is_client == 1 || $is_agent == 1){
?>
<a id="client-agent-menu" class="main-menu <?php echo (!empty(current_page("client-bc-reports")) || !empty(current_page("client-cv-reports")) || !empty(current_page("client-pdf-reports")) || !empty(current_page("all-clients-files")) || !empty(current_page("agent-bc-reports")) || !empty(current_page("agent-cv-reports")))?"main-current":""; ?>"><i class="fa fa-bullhorn" aria-hidden="true"></i> Reports</a>
<div id="client-agent-menu-div" class="sub-menu">
<?php
if($is_client == 1){
?>
<a href="<?php echo $admin; ?>client-bc-reports" class="<?php echo current_page("client-bc-reports"); ?>"><i class="fa fa-list" aria-hidden="true"></i> BC Reports</a>
<a href="<?php echo $admin; ?>client-cv-reports" class="<?php echo current_page("client-cv-reports"); ?>"><i class="fa fa-list" aria-hidden="true"></i> CV Reports</a>
<a href="<?php echo $admin; ?>client-pdf-reports" class="<?php echo current_page("client-pdf-reports"); ?>"><i class="fa fa-list" aria-hidden="true"></i> Reports/Requests</a>
<a href="<?php echo $admin; ?>all-clients-files" class="<?php echo current_page("all-clients-files"); ?>"><i class="fa fa-file-text" aria-hidden="true"></i> All My Files</a>
<?php
}
if($is_agent == 1){
?>
<a href="<?php echo $admin; ?>agent-bc-reports" class="<?php echo current_page("agent-bc-reports"); ?>"><i class="fa fa-list" aria-hidden="true"></i> BC Reports</a>
<a href="<?php echo $admin; ?>agent-cv-reports" class="<?php echo current_page("agent-cv-reports"); ?>"><i class="fa fa-list" aria-hidden="true"></i> CV Reports</a>
<?php
}
?>
</div>
<?php
}
?>

<a id="general-menu" class="main-menu <?php echo (!empty(current_page("inbox")) || !empty(current_page("sent-messages")) || !empty(current_page("profile")) || !empty(current_page("reset-password")))?"main-current":""; ?>"><i class="fa fa-diamond" aria-hidden="true"></i> General</a>
<div id="general-menu-div" class="sub-menu">
<a href="<?php echo $admin; ?>inbox" class="<?php echo current_page("inbox"); ?>"><i class="fa fa-inbox" aria-hidden="true"></i> Inbox</a>
<a href="<?php echo $admin; ?>sent-messages" class="<?php echo current_page("sent-messages"); ?>"><i class="fa fa-send" aria-hidden="true"></i> Sent Messages</a>
<a href="<?php echo $admin; ?>profile" class="<?php echo current_page("profile"); ?>"><i class="fa fa-user" aria-hidden="true"></i> Profile</a>
<a href="<?php echo $admin; ?>reset-password" class="<?php echo current_page("reset-password"); ?>"><i class="fa fa-lock" aria-hidden="true"></i> Reset Password</a>
</div>

<a class="main-menu" onClick="javascript:my_confirm('Logout Confirmation','Are you sure you want to log out?','<?php echo $directory . $admin; ?>?logout=1');"><i class="fa fa-sign-out"></i> Log Out</a>
</div>

<div class="portal-body portal-content">
<div class="<?php echo (basename($_SERVER["PHP_SELF"],".php") == "index")?"portal-body-wrapper":"body-content form-div"; ?>">