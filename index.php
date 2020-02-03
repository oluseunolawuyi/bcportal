<?php
// Date in the past
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

ini_set('session.gc_maxlifetime', 3600);
session_start();

require_once("classes/db-class.php");
require_once("includes/functions.php");

require_once("includes/mobile-detect.php");
$detect = new Mobile_Detect;

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

if(isset($_SESSION["login"]) && !empty($_SESSION["login"])){
redirect("{$directory}{$admin}");
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
<title>Home - <?php echo $full_gen_name; ?></title>

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
<script src="js/SmoothScroll.min.js"></script>
<script src="js/jarallax.js"></script>
<script src="js/jquery.jcarousellite.js"></script>
<script>
<!--
var img1 = new Image();
img1.src = "images/home-banner.jpg";
//-->
</script>

</head>
<?php detectCurrUserBrowser('<table width="100%"><tr><td>','',7); ?>
<body>

<div class="header-wrapper header-wrapper1" style="border-bottom:1px solid #ddd;">
<div class="header header1">
<a href="<?php directory(); ?>" class="float-left"><img src="images/risk-control-logo.jpg"></a>

<a href="https://riskcontrolnigeria.com" class="float-right"><img src="images/risk-control-logo.jpg"></a>
</div>
</div>

<div>
<div class="header-wrapper header-wrapper2">
<div class="header header2">

<div class="col-md-4">
</div>
<div class="col-md-4">

<div class="subscribe">

<?php 
$error = 1;
$login = np_input("login");
$reset = np_input("reset");
$update = np_input("update");
$username = tp_input("username");
$password = tp_input("password");
$password2 = $password;
$conf_pass = tp_input("conf_pass");
$a = nr_input("a");
$b = tr_input("b");
$a2 = nr_input("a2");
$b2 = tr_input("b2");

// Login
if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($login) && !empty($username) && !empty($password)){

$password = sha1($password);

$result = $db->select("reg_users", "Where username = '{$username}'", "*", "");

if(count_rows($result) == 1){

$row = fetch_data($result);

if($row["active"] == 0){
echo "<div class='not-success'>Your account is not active. Kindly check your e-mail to activate your accout OR contact the admin <a href='{$parent_domain}privates/contact-us'>HERE</a></div>";
}else if($row["password"] == $password && $row["blocked"] == 0){
$name = $row["name"];
$email = $row["email"];
$id = $row["id"];
$_SESSION["login"] = $id;

$db->query("UPDATE reg_users SET logged_in = '1', date_time = '{$date_time}' WHERE username = '{$username}'");

$activity = "Logged in to own account.";
$audit_data_array = array(
"user_id" => "'$id'",
"name" => "'$username'",
"email" => "'$email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$db->insert($audit_data_array, "audit_log");
$error = 0;

if(isset($_SESSION['prev_url']) && !empty($_SESSION['prev_url'])){
$prev_url = $_SESSION['prev_url'];
}else{
$prev_url = $directory;
}
?>
<form name="temp" action="<?php echo $admin; ?>" method="get"></form>
<script>
<!--
document.temp.submit();
//-->
</script>
<?php
}else if($row["password"] != $password && $row["blocked"] == 0){
echo "<div class='not-success'>Incorrect Password</div>";
}else if($row["blocked"] == 1){
echo "<div class='not-success'>Your account is declined. Kindly contact the admin <a href='{$parent_domain}privates/contact-us'>HERE</a></div>";
}

}else{
echo "<div class='not-success'>This username is not registered. Kindly contact the admin <a href='{$parent_domain}privates/contact-us'>HERE</a></div>";
}

}

// Reset Password
if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($reset) && !empty($username)){

$result = $db->select("reg_users", "Where username = '{$username}'", "*", "");

if(count_rows($result) == 1){

$row = fetch_data($result);
$name = $row["name"];
$email = $row["email"];
$reg_id = $row["id"];
$password = $row["password"];
$blocked = $row["blocked"];

if($blocked == 0){

$to = "{$email}";
$subject = "Password Reset";
$message = "<p>Dear {$name},</p>
<p>You have successfully reset your password.</p>
<p>Kindly update your new password by clicking on, or copying and pasting this link on your address bar: {$directory}?a2={$reg_id}&b2={$password}</p>";
$foot_note .= "<p>If you did not request for password reset, kindly ignore this mail.</p>";
$message = message_template();
$headers = "{$gen_name} <no-reply@{$domain}>";
send_mail();

$error = 0;
$_SESSION["msg"] = "<div class='success'>Successfully. Kindly check you mail for the next procedure.</div>";
redirect("{$directory}");
}else if($blocked == 1){
echo "<div class='not-success'>Hi {$name}! Your account is declined. Kindly contact the admin <a href='{$parent_domain}privates/contact-us'>HERE</a></div>";
}

}else{
echo "<div class='not-success'>This username is not registered. Kindly contact the admin <a href='{$parent_domain}privates/contact-us'>HERE</a></div>";
}

}

////////////////////////////////////////////////////
if(!empty($a) && !empty($b)){

$result = $db->select("reg_users", "Where id = '{$a}'", "*", "");

if(count_rows($result) == 1){
$row = fetch_data($result);
$enc_email = sha1($row["email"]);
$active = $row["active"];
$blocked = $row["blocked"];
$name = $row["name"];

if($blocked == 1){
echo "<div class='not-success'>Hi {$name}! Your account is declined. Kindly contact the admin <a href='{$parent_domain}privates/contact-us'>HERE</a>.</div>";
}else if($enc_email == $b && $active == 0){
$fid = $db->query("UPDATE reg_users SET active = '1' WHERE id = '{$a}'");
if($fid){
echo "<div class='success'>Congrat {$name}! Your account is now activated. Kindly log in.</div>";
}
}else if($enc_email == $b && $active == 1){
echo "<div class='not-success'>Hi {$name}! Your account was previously activated. Kindly log in.</div>";
}else if($enc_email != $b){
echo "<div class='not-success'>Invalid request.</div>";
}

}else{
echo "<div class='not-success'>Invalid request.</div>";
}

}

///////////////////////////////New Password////////////////////////
if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($update) && !empty($a2) && !empty($b2) && !empty($password) && !empty($conf_pass) && strlen($password) >= 5 && $password == $conf_pass ){

$new_password = sha1($password);
$username = in_table("username","reg_users","WHERE id='{$a2}'","username");
$user_email = in_table("email","reg_users","WHERE id='{$a2}'","email");

$result = $db->select("reg_users", "Where id = '{$a2}'", "*", "");

if(count_rows($result) == 1){
$row = fetch_data($result);
$password2 = $row["password"];
$blocked = $row["blocked"];
$name = $row["name"];

if($blocked == 1){
echo "<div class='not-success'>Hi {$name}! Your account is declined. Kindly contact the admin <a href='{$parent_domain}privates/contact-us'>HERE</a>.</div>";
}else if($password2 == $b2){

$data_array = array(
"password" => $new_password
);
$act = $db->update($data_array, "reg_users", "id = '{$a2}'");

if($act){
$activity = "Reset own password.";
$audit_data_array = array(
"user_id" => "'$a2'",
"name" => "'$username'",
"email" => "'$user_email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$db->insert($audit_data_array, "audit_log");

$_SESSION["msg"] = "<div class='success'>Password successfully updated. Kindly log in with your new password.</div>";
redirect("{$directory}");
}else{
echo "<div class='not-success'>Error occured.</div>";
}

}else if($password2 != $b2){
echo "<div class='not-success'>Invalid request.</div>";
}

}else{
echo "<div class='not-success'>Invalid request.</div>";
}

}


///////////////////////////////////////////////////////////////////////////////////////
if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($update) && (empty($password2) or empty($conf_pass))){
$_SESSION["notSuccess"] = "<div class='not-success'>Not submitted! All the fields are required.</div>";
}else if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($update) && !empty($password2) && strlen($password2) < 5){
$_SESSION["notSuccess"] = "<div class='not-success'>Not submitted! Password must be atleast 5 characters.</div>";
}else if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($update) && !empty($password2) && $password2 != $conf_pass){
$_SESSION["notSuccess"] = "<div class='not-success'>Not submitted! Passwords do not match.</div>";
}
/////////////////////////////////////////////////////////////

if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($login) && (empty($username) or empty($password2))){
echo "<div class='not-success'>All the feilds are required.</div>";
}
if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($reset) && empty($username)){
echo "<div class='not-success'>Email is required.</div>";
}

if(isset($_SESSION["msg"]) && !empty($_SESSION["msg"]) && $_SERVER['REQUEST_METHOD'] != "POST"){
echo $_SESSION["msg"];
$_SESSION["msg"] = NULL;
unset($_SESSION["msg"]);
session_destroy();
}

if(empty($a2) && empty($b2)){
?>

<form  action="<?php directory(); ?>" class="login-form" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">

<input type="hidden" name="login" value="1">
<div class="subscription-details">LOGIN</div>

<div class="form-group input-group">
<span class="input-group-addon"><i class="fa"><label for="username">Username</label></i></span>
<input type="text" name="username" id="username" class="form-control" value="" placeholder="Your username" required value="<?php check_inputted("username"); ?>">
</div>

<div class="form-group input-group">
<span class="input-group-addon"><i class="fa"><label for="password">Password</label></i></span>
<input type="password" name="password" id="password" class="form-control" value="" placeholder="Your password" required value="<?php check_inputted("password"); ?>">
</div>
<div>
<a class="toggle-forms float-left" onClick="javascript:$('.login-form').slideToggle();$('.forgot-form').slideToggle();">Forgot Password?</a>
<button class="float-right"><i class="fa fa-sign-in" aria-hidden="true"></i> Login</button>
</div>	
</form>

<form action="<?php directory(); ?>" class="forgot-form" style="display:none;" method="post" enctype="multipart/form-data">  
<div class="subscription-details">Change Your Password</div>    
<input type="hidden" name="reset" value="1">

<div class="form-group input-group">
<span class="input-group-addon"><i class="fa"><label for="username">Username</label></i></span>
<input type="text" name="username" id="username" class="form-control" value="" placeholder="Your username" required value="<?php check_inputted("username"); ?>">
</div>
                     
<div>
<a class="toggle-forms float-left" onClick="javascript:$('.forgot-form').slideToggle();$('.login-form').slideToggle();">Login</a>
<button class="float-right"><i class="fa fa-lock"></i> Reset Password</button>
</div>
</form>

<?php } 
if(!empty($a2) && !empty($b2)){

$result = $db->select("reg_users", "Where id = '{$a2}'", "*", "");

if(count_rows($result) == 1){
$row = fetch_data($result);
$password2 = $row["password"];
$blocked = $row["blocked"];
$name = $row["name"];

if($blocked == 1){
echo "<div class='not-success'>Hi {$name}! Your account is declined. Kindly contact the admin <a href='{$parent_domain}privates/contact-us'>HERE</a>.</div>";
}else if($password2 == $b2){
?>
 
<form action="<?php directory(); ?>" class="reset-form" method="post" enctype="multipart/form-data">  
<div class="subscription-details">Hi <?php echo $name; ?>, Your New Password</div>    

<input type="hidden" name="a2" id="a2" required value="<?php echo $a2; ?>">
<input type="hidden" name="b2" id="b2" required value="<?php echo $b2; ?>">
<input type="hidden" name="update" value="1">

<div>
<label for="password" class="special-label">New Password <i>(atleast 5 characters)</i></label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-lock"></i></span>
<input type="password" name="password" id="password" class="form-control" placeholder="Your password for login" required value="<?php check_inputted("password"); ?>">
</div>
</div>

<div>
<label for="conf_pass" class="special-label">Retype Password</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-lock"></i></span>
<input type="password" name="conf_pass" id="conf_pass" class="form-control" placeholder="Retype your password" required value="<?php check_inputted("conf_pass"); ?>">
</div>
</div>
                     
<div>
<button class="float-right"><i class="fa fa-upload"></i> Update</button>
</div>
</form>

<?php  
}else if($password2 != $b2){
echo "<div class='not-success'>Invalid request.</div>";
}

}else{
echo "<div class='not-success'>Invalid request.</div>";
}

}
?>

</div>


</div>

</div>
</div>
</div>

<script src="js/general-form.js"></script>


<?php require_once("includes/footer.php"); ?>