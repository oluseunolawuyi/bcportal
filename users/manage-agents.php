<?php if(!isset($_REQUEST["gh"])){ include_once("../includes/admin-header.php"); 
}else{ 
include_once("../includes/gen-header.php"); require_once("../includes/resize-image.php");
} ?>

<?php
admin_role_redirect("manage_agents");

//////////=============Add New User===================///////////////////////////////////////
$error = 1;

$activate = tr_input("activate");
$unblock = tr_input("unblock");
$block = tr_input("block");

$upload = np_input("upload");
$member_id = np_input("member_id");

$add = nr_input("add");
$edit = nr_input("edit");
$view = nr_input("view");

$name = tp_input("name");
$new_username = tp_input("username");
$password = tp_input("password");
$password2 = $password;
$re_password = tp_input("re_password");
$email = tp_input("email");
$re_email = tp_input("re_email");
$telephone = tp_input("telephone");
$address = tp_input("address");
$region = tp_input("region");
$city = tp_input("city");
$state = tp_input("state");
$state_of_origin = tp_input("state_of_origin");
$education = tp_input("education");
$confidentiality_agreement = tp_input("confidentiality_agreement");
$check_user = tp_input("check_user");

$conf_password = tp_input("conf_password");
$reset = tp_input("reset");

$uniq_id = "";

if(!empty($activate)){
$uniq_id = $activate;
}else if(!empty($unblock)){
$uniq_id = $unblock;
}else if(!empty($block)){
$uniq_id = $block;
}else if(!empty($reset)){
$uniq_id = $reset;
}else if(!empty($edit)){
$uniq_id = $edit;
}

$uniq_name = (!empty($uniq_id))?get_table_data("reg_users", $uniq_id, "name"):"";
$uniq_email = (!empty($uniq_id))?get_table_data("reg_users", $uniq_id, "email"):"";

////////////// Upload image //////////////////////////////
if(check_admin("change_agents_picture") == 1 && $_SERVER['REQUEST_METHOD'] == "POST" && !empty($upload) && !empty($member_id) && !empty($_FILES["ufile"]["tmp_name"])){ 

upload_single_image("ufile", "{$member_id}pic", "../images/users/", "250", "250");

echo "<div class='success'>Picture successfully updated.</div>";
}
////////////// Ends Upload image //////////////////////////////

if(check_admin("add_agents") == 1 && $_SERVER['REQUEST_METHOD'] == "POST" && !empty($add) && !empty($name) && !empty($new_username) && !empty($password) && !empty($re_password) && strlen($password) >= 5 && $password == $re_password && !empty($email) && !empty($re_email) && filter_var($email, FILTER_VALIDATE_EMAIL) && $email == $re_email && $check_user == $_SESSION["spam_checker"]){
	
$email = strtolower($email);
$password = sha1($password);

$email_exists = in_table("email", "reg_users", "WHERE email = '{$email}'", "email");

$result = $db->select("reg_users", "WHERE username = '{$new_username}'", "*", "");

if(!empty($email_exists)){

echo "<div class='not-success'>Email already exists. Log in instead.</div>";

}else if(count_rows($result) < 1){

$data_array = array(
"name" => "'$name'",
"username" => "'$new_username'",
"password" => "'$password'",
"email" => "'$email'",
"telephone" => "'$telephone'",
"address" => "'$address'",
"region" => "'$region'",
"city" => "'$city'",
"state" => "'$state'",
"state_of_origin" => "'$state_of_origin'",
"education" => "'$education'",
"confidentiality_agreement" => "'$confidentiality_agreement'",
"date_registered" => "'$date_time'",
"active" => "'1'",
"registered_by" => "'$id'",
"agent" => "'1'"
);
$act = $db->insert($data_array, "reg_users");

if($act){

$error = 0;

$activity = "Added a new user with the email: {$email}.";
$audit_data_array = array(
"user_id" => "'$id'",
"name" => "'$username'",
"email" => "'$user_email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$act = $db->insert($audit_data_array, "audit_log");

$to = "{$email}";
$subject = "New Account Creation";
$message = "<p>Dear {$name},</p>
<p>We are pleased to inform you that an account has been successfully created for you on {$gen_name}.</p>
<p>You may log in on {$domain} to manage your account with username: <b>{$new_username}</b> and password: <b>{$password2}</b> .</p>";
$message = message_template();
$headers = "{$gen_name} <no-reply@{$domain}>";
send_mail();

echo "<div class='success'>Account successfully created.</div>";
}else{
echo "<div class='not-success'>Error occured.</div>";
}

}else{

echo "<div class='not-success'>Username already exists.</div>";
}

}

if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($add) && (empty($name) || empty($new_username) || empty($email) || empty($re_email) || empty($password2) || empty($re_password) || empty($check_user))){
echo "<div class='not-success'>Not submitted! All * the fields are required.</div>";
}else if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($add) && !empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)){
echo "<div class='not-success'>Not submitted! Invalid  email format.</div>";
}else if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($add) && !empty($password2) && strlen($password2) < 5){
echo "<div class='not-success'>Not submitted! Password must be atleast 5 characters.</div>";
}else if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($add) && !empty($check_user) && $check_user != $_SESSION["spam_checker"]){
echo "<div class='not-success'>Not submitted! Incorrect check code.</div>";
}

////////Update User's Profile//////////////////////
if(check_admin("edit_agents") == 1 && $_SERVER['REQUEST_METHOD'] == "POST" && !empty($edit) && !empty($name) && !empty($email)){

$data_array = array(
"name" => $name,
"email" => $email,
"telephone" => $telephone,
"address" => $address,
"region" => $region,
"city" => $city,
"state" => $state,
"state_of_origin" => $state_of_origin,
"education" => $education,
"confidentiality_agreement" => $confidentiality_agreement
);
$act = $db->update($data_array, "reg_users", "id = '$edit' AND agent = '1'");

if($act){

$activity = "Updated the profile of user #" . agent_id($edit) . ".";
$audit_data_array = array(
"user_id" => "'$id'",
"name" => "'$username'",
"email" => "'$user_email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$act = $db->insert($audit_data_array, "audit_log");

$error = 0;

$to = "{$uniq_email}";
$subject = "Profile Update";
$message = "<p>Dear {$name},</p>
<p>This is to notify you that your profile data has been modified by an admin user - {$username} ({$user_email}).</p>
<p>Thank you.</p>";
$message2 = $message;
$message = message_template();
$headers = "{$gen_name} <no-reply@{$domain}>";
send_mail(1);

$user_data_array = array(
"ticket_id" => "'$ticket_id'",
"sender_name" => "'$gen_name'",
"sender_email" => "'$gen_email'",
"recipient_name" => "'$uniq_name'",
"recipient_email" => "'$uniq_email'",
"subject" => "'$subject'",
"message" => "'$message2'",
"inbox" => "'1'",
"date_time" => "'$date_time'"
);
$db->insert($user_data_array, "admin_messages");

$admin_data_array = array(
"ticket_id" => "'$ticket_id'",
"sender_name" => "'$gen_name'",
"sender_email" => "'$gen_email'",
"recipient_name" => "'$uniq_name'",
"recipient_email" => "'$uniq_email'",
"subject" => "'$subject'",
"message" => "'$message2'",
"sent" => "'1'",
"date_time" => "'$date_time'"
);
$db->insert($admin_data_array, "admin_messages");

echo "<div class='success'>Profile successfully updated.</div>";
}else{
echo "<div class='not-success'>Error occured.</div>";
}

}

if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($edit) && (empty($name) or empty($email))){
echo "<div class='not-success'>Not submitted! All * the fields are required.</div>";
}

//////////////////////////////////=====Block Or Unblock User============//////////////////////////////////////////
if(check_admin("edit_agents") == 1 && $_SERVER['REQUEST_METHOD'] == "GET" && (!empty($unblock) || !empty($block))){

$used_val = (!empty($unblock))?0:1;

$data_array = array(
"blocked" => $used_val
);
$act = $db->update($data_array, "reg_users", "id = '$uniq_id'");

if($act){
$activity = (!empty($unblock))?"Unblocked user #" . agent_id($uniq_id) . " to gain login access.":"Blocked user #" . agent_id($uniq_id) . " from logging in.";
$audit_data_array = array(
"user_id" => "'$id'",
"name" => "'$username'",
"email" => "'$user_email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$db->insert($audit_data_array, "audit_log");

///=====Send mail==================//
$to = "{$uniq_email}";
$subject = (!empty($unblock))?"Account Activation Notice":"Account Deactivation Notice";
$message = (!empty($block))?"<p>Dear {$uniq_name},</p><p>This is to notify you that your account has been deactivated due to some reasons. Therefore, you can not log in with your email({$uniq_email}). Kindly contact the customer service for account activation.</p>":"<p>Dear {$uniq_name},</p><p>This is to notify you that your account has been activated. Your email({$uniq_email}) has just been confirmed. You can always log in with your email ({$uniq_email}) and password.</p>";
$message2 = $message;
$message = message_template();
$headers = "{$gen_name} <no-reply@{$domain}>";

send_mail(1);

$user_data_array = array(
"ticket_id" => "'$ticket_id'",
"sender_name" => "'$gen_name'",
"sender_email" => "'$gen_email'",
"recipient_name" => "'$uniq_name'",
"recipient_email" => "'$uniq_email'",
"subject" => "'$subject'",
"message" => "'$message2'",
"inbox" => "'1'",
"date_time" => "'$date_time'"
);
$db->insert($user_data_array, "admin_messages");

$admin_data_array = array(
"ticket_id" => "'$ticket_id'",
"sender_name" => "'$gen_name'",
"sender_email" => "'$gen_email'",
"recipient_name" => "'$uniq_name'",
"recipient_email" => "'$uniq_email'",
"subject" => "'$subject'",
"message" => "'$message2'",
"sent" => "'1'",
"date_time" => "'$date_time'"
);
$db->insert($admin_data_array, "admin_messages");
///======================================================//

echo (!empty($unblock))?"<div class='success'>User successfully enabled.</div>":"<div class='success'>User successfully disabled.</div>";
}else{
echo "<div class='not-success'>Error. Unable to activate user.</div>";
}

}

//////////////////////////////////=====Activate User============//////////////////////////////////////////
if(check_admin("edit_agents") == 1 && $_SERVER['REQUEST_METHOD'] == "GET" && !empty($activate)){

$data_array = array(
"active" => 1
);
$act = $db->update($data_array, "reg_users", "id = '$activate'");

if($act){
$activity = "Activated a user - {$uniq_name}({$uniq_email}) with ID #" . agent_id($uniq_id) . ".";
$audit_data_array = array(
"user_id" => "'$id'",
"name" => "'$username'",
"email" => "'$user_email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$db->insert($audit_data_array, "audit_log");

///=====Send mail==================//
$to = "{$uniq_email}";
$subject = "Successful Account Activation";
$message = "<p>Dear {$uniq_name}, this is to notify you that your account has been activated. Your email ({$uniq_email}) has just been confirmed. You can always log in with your email ({$uniq_email}) and password.</p>";
$message2 = $message;
$message = message_template();
$headers = "{$gen_name} <no-reply@{$domain}>";

send_mail(1);

$user_data_array = array(
"ticket_id" => "'$ticket_id'",
"sender_name" => "'$gen_name'",
"sender_email" => "'$gen_email'",
"recipient_name" => "'$uniq_name'",
"recipient_email" => "'$uniq_email'",
"subject" => "'$subject'",
"message" => "'$message2'",
"inbox" => "'1'",
"date_time" => "'$date_time'"
);
$db->insert($user_data_array, "admin_messages");

$admin_data_array = array(
"ticket_id" => "'$ticket_id'",
"sender_name" => "'$gen_name'",
"sender_email" => "'$gen_email'",
"recipient_name" => "'$uniq_name'",
"recipient_email" => "'$uniq_email'",
"subject" => "'$subject'",
"message" => "'$message2'",
"sent" => "'1'",
"date_time" => "'$date_time'"
);
$db->insert($admin_data_array, "admin_messages");
///======================================================//

echo "<div class='success'>User successfully activated.</div>";
}else{
echo "<div class='not-success'>Error. Unable to activate user.</div>";
}

}

/////////////=================Reset Password for User=================/////////////
if(check_admin("edit_agents") == 1 && $_SERVER['REQUEST_METHOD'] == "POST" && !empty($reset) && !empty($password) && !empty($conf_password) && $password == $conf_password && strlen($password) >= 5){

$password = sha1($password);

$data_array = array(
"password" => $password
);
$act = $db->update($data_array, "reg_users", "id = '$reset'");

if($act){

$activity = "Reset password for user #" . agent_id($uniq_id) . ".";
$audit_data_array = array(
"user_id" => "'$id'",
"name" => "'$username'",
"email" => "'$user_email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$db->insert($audit_data_array, "audit_log");

$error = 0;

///=====Send mail==================//
$to = "{$uniq_email}";
$subject = "Successful Password Reset";
$message = "<p>Dear {$uniq_name}, this is to notify you that your password has been reset to <b>{$password2}</b> . You can now log in with your email ({$uniq_email}) and the new password.</p>";
$message2 = $message;
$message = message_template();
$headers = "{$gen_name} <no-reply@{$domain}>";

send_mail(1);

$user_data_array = array(
"ticket_id" => "'$ticket_id'",
"sender_name" => "'$gen_name'",
"sender_email" => "'$gen_email'",
"recipient_name" => "'$uniq_name'",
"recipient_email" => "'$uniq_email'",
"subject" => "'$subject'",
"message" => "'$message2'",
"inbox" => "'1'",
"date_time" => "'$date_time'"
);
$db->insert($user_data_array, "admin_messages");

$admin_data_array = array(
"ticket_id" => "'$ticket_id'",
"sender_name" => "'$gen_name'",
"sender_email" => "'$gen_email'",
"recipient_name" => "'$uniq_name'",
"recipient_email" => "'$uniq_email'",
"subject" => "'$subject'",
"message" => "'$message2'",
"sent" => "'1'",
"date_time" => "'$date_time'"
);
$db->insert($admin_data_array, "admin_messages");
///======================================================//

echo "<div class='success'>Password successfully updated for {$uniq_name} ({$uniq_email}).</div>";
}else{
echo "<div class='not-success'>Error occured.</div>";
}

}

if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($reset) && (empty($password2) or empty($conf_password))){
echo "<div class='not-success'>Not updated! All the fields are required.</div>";
}else if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($reset) && !empty($password2) && $password2 != $conf_password){
echo "<div class='not-success'>Not updated! Passwords do not match.</div>";
}else if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($reset) && !empty($password2) && strlen($password2) < 5){
echo "<div class='not-success'>Not updated! Password must be at least 5 digits.</div>";
}

////////////////////////////////////////////////////******************************//////////////

$new_name = (empty($activities) && isset($_POST["new_name"]) && !empty($_POST["new_name"]))?search_option("new_name"):"";
$new_email = (empty($activities) && isset($_POST["new_email"]) && !empty($_POST["new_email"]))?search_option("new_email"):"";
$no_of_rows = (empty($activities))?search_option("no_of_rows"):"";

$where = "WHERE id > '0' AND agent = '1'";
$where .= (!empty($new_name))?" AND name LIKE '%{$new_name}%'":"";
$where .= (!empty($new_email))?" AND email LIKE '%{$new_email}%'":"";

$activities = tr_input("activities");
$table = (!empty($activities))?"audit_log":"reg_users";
$where = (!empty($activities))?"WHERE user_id = '{$activities}'":$where;
$result = $db->select("$table", "$where", "*", "ORDER BY id DESC");
$count = count_rows($result);

$per_view = (!empty($activities))?50:20;
$per_view = (!empty($no_of_rows) && empty($activities))?$no_of_rows:$per_view;
$page_link = "{$admin}manage-agents?pn=";
$link_suffix = (!empty($activities))?"&activities={$activities}":"";
$style_class = "general-link";
page_numbers();

$offset = ($per_view * $pn) - $per_view;

$result = $db->select("$table", "$where", "*", "ORDER BY id DESC", "LIMIT {$offset},{$per_view}");

if(empty($view) && empty($activities) && (empty($edit)||(!empty($edit)&&$error==0)) && (empty($add)||(!empty($add)&&$error==0)) ){
?>

<div class="page-title">Manage Agents <a href="<?php echo $admin; ?>manage-agents?add=1&pn=<?php echo $pn; ?>" class="btn gen-btn general-link float-right"><i class="fa fa-upload" aria-hidden="true"></i> New Agent</a></div>

<form action="<?php echo $admin; ?>manage-agents" class="img-form general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">  
<input type="hidden" name="gh" value="1">
<input type="hidden" name="upload" value="1">                      
<input type="hidden" name="pn" value="<?php echo $pn; ?>">                      
<input type="hidden" class="special-member" name="member_id" value="">                      
<input type="file" name="ufile" id="ufile" required>
</form>

<form action="<?php echo $admin; ?>manage-agents" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
<input type="hidden" name="gh" value="1"> 
<div class="search-dates">

<div class="col-md-3">
<label for="new_name">Name</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
<input type="text" name="new_name" id="new_name" class="form-control" placeholder="User&#039;s Name" value="<?php check_inputted("new_name", $new_name); ?>">
</div>
</div>

<div class="col-md-3">
<label for="new_email">Email</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-envelope" aria-hidden="true"></i></span>
<input type="email" name="new_email" id="new_email" class="form-control" placeholder="User&#039;s E-mail" value="<?php check_inputted("new_email", $new_email); ?>">
</div>
</div>

<div class="col-md-3">
<label for="no_of_rows">No. of Rows</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-list" aria-hidden="true"></i></span>
<input type="number" name="no_of_rows" id="no_of_rows" class="form-control only-no" placeholder="No. of rows" value="<?php check_inputted("no_of_rows", $per_view); ?>">
</div>
</div>

<div class="col-md-3">
<br />
<button type="submit" class="btn gen-btn"><i class="fa fa-search"></i> Search</button>
</div>

</div>
</form>

<?php
if($count > 0){
?>
<div class="overflow">
<table class="table table-striped table-hover">
<thead>
<tr class="gen-title">
<th>ID #</th>
<th style="width:50px">Picture</th>
<th>Username</th>
<th>Name</th>
<th>Email</th>
<th>Current Login</th>
<th>Status</th>
<th>Activities</th>
<th>Profile</th>
<?php if(check_admin("change_agents_picture") == 1){ ?>
<th>Image</th>
<?php } ?>
</tr>
</thead>
<tbody>
<?php
while($row = fetch_data($result)){
$get_id = $row["id"];
$user_id = agent_id($get_id);
$new_username = $row["username"];
$name = $row["name"];
$email = $row["email"];
$date_time = ($row["date_time"] != "0000-00-00 00:00:00")?min_full_date($row["date_time"]):"Never logged in";
$active = $row["active"];
$blocked = $row["blocked"];
$status = "";
if($blocked == 1){
$status = "Blocked";
}else if($active == 1){
$status = "Active";
}else if($active == 0){
$status = "Not active";
}

$file_array = glob("../images/users/{$get_id}pic*.jpg");
$file_name = ($file_array)?"images/" . $file_array[0]:"images/post.jpg";
?>
<tr>
<td><?php echo $user_id; ?></td>
<td><img src="<?php echo $file_name; ?>" class="img-rounded"></td>
<td><?php echo $new_username; ?></td>
<td><?php echo $name; ?></td>
<td><?php echo $email; ?></td>
<td><?php echo $date_time; ?></td>
<td><?php echo $status; ?></td>
<td><a href="<?php echo $admin; ?>manage-agents?activities=<?php echo $get_id; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link" title="View <?php echo $name; ?>&#039;s activities"><i class="fa fa-history" aria-hidden="true"></i> View Log</a></td>
<td><a href="<?php echo $admin; ?>manage-agents?view=<?php echo $get_id; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link" title="View <?php echo $name; ?>&#039;s profile"><i class="fa fa-eye" aria-hidden="true"></i> View</a></td>
<?php if(check_admin("change_agents_picture") == 1){ ?>
<td><label for="ufile" id="<?php echo $get_id; ?>" class="btn gen-btn change-picture-label" title="Change <?php echo $name; ?>&#039;s profile picture. Format: .jpg, .gif, .png, .jpeg, Not more than 5MB"><i class="fa fa-upload" aria-hidden="true"></i> Change</label></td>
<?php } ?>
</tr>
<?php 
}
?>
</tbody>
</table>
</div>
<?php
echo ($last_page>1)?"<div class=\"page-nos\">" . $center_pages . "</div>":"";
}else{
echo "<div class='not-success'>No users found.</div>";
}

}

/////====================== View Activities===============////////
if(!empty($activities)){
$users_name = get_table_data("reg_users", $activities, "name");
$users_email = get_table_data("reg_users", $activities, "email");
?>
<div class="back"><a href="<?php echo $admin; ?>manage-agents?pn=<?php echo $pn ?>" class="btn gen-btn general-link"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to agents list</a></div>
<?php
if($count > 0){
$row = fetch_data($result);
$this_id = $row["id"];
$get_id = $row["user_id"];
$user_id = agent_id($get_id);
$name = $row["name"];
$email = $row["email"];

$file_array = glob("../images/users/{$get_id}pic*.jpg");
$file_name = ($file_array)?"images/" . $file_array[0]:"images/post.jpg";
?>
<style>
<!--
div table thead tr th, div table tr th, div table tbody tr td, div table tr td{
text-align:left !important;
}
-->
</style>
<div class="overflow">
<table class="table table-striped table-hover">
<thead>
<tr class="gen-title">
<th style="width:80px"><?php echo $user_id; ?></th>
<th style="width:120px"><img src="<?php echo $file_name; ?>" class="img-rounded"></th>
<th><?php echo "{$name} ({$email})"; ?></th>
</tr>
<tr>
<th colspan="2" class="center"><b>Date and Time</b></th>
<th><b>Activities</b></th>
</tr>
</thead>
<tbody>
<?php
$result = $db->select("$table", "$where", "*", "ORDER BY id DESC");

while($row = fetch_data($result)){
$activity = $row["activity"];
$activity_date = min_full_date($row["date_time"]);
?>
<tr>
<td colspan="2" class="center"><?php echo $activity_date; ?></td>
<td><?php echo $activity; ?></td>
</tr>
<?php 
}
?>
</tbody>
</table>
</div>
<?php
echo ($last_page>1)?"<div class=\"page-nos\">" . $center_pages . "</div>":"";
}else{
echo "<div class='not-success'>No activities found for {$users_name} ({$users_email}).</div>";
}

}

//=======================View User's Profile==============================//
if(!empty($view) && (empty($edit) || (!empty($edit) && $error == 0))){
$result = $db->select("reg_users", "WHERE id='$view' AND agent = '1'", "*", "");

if(count_rows($result) == 1){
$row = fetch_data($result);
$get_id = $row["id"];
$user_id = agent_id($get_id);
$new_username = $row["username"];
$name = $row["name"];
$email = $row["email"];
$telephone = $row["telephone"];
$address = $row["address"];
$region = $row["region"];
$city = $row["city"];
$state = $row["state"];
$state_of_origin = $row["state_of_origin"];
$education = $row["education"];
$confidentiality_agreement = $row["confidentiality_agreement"];
$date_registered = full_date($row["date_registered"]);
$registered_by_id = $row["registered_by"];
$registered_by_name = ($registered_by_id > 0)?get_table_data("reg_users", $registered_by_id, "name"):"";
$registered_by_email = ($registered_by_id > 0)?get_table_data("reg_users", $registered_by_id, "email"):"";
$registered_by = ($registered_by_id > 0)?"{$registered_by_name} ({$registered_by_email})":"Self";
$logged_in = ($row["logged_in"] == 1)?"Yes":"No";
$active = $row["active"];
$blocked = $row["blocked"];
$status = "";
if($blocked == 1){
$status = "Blocked";
}else if($active == 1){
$status = "Active";
}else if($active == 0){
$status = "Not active";
}
$date_time = ($row["date_time"] != "0000-00-00 00:00:00")?full_date($row["date_time"]):"Never logged in";
$last_login = ($row["last_login"] != "0000-00-00 00:00:00")?full_date($row["last_login"]):"Not Available";

$file_array = glob("../images/users/{$get_id}pic*.jpg");
$file_name = ($file_array)?"images/" . $file_array[0]:"images/post.jpg";
?>

<style>
<!--
div table thead tr th, div table tr th, div table tbody tr td, div table tr td{
text-align:left !important;
}
.table .reset-row, .table .bonus-row{
display:none;
}
-->
</style>

<div class="back"><a href="<?php echo $admin; ?>manage-agents?pn=<?php echo $pn ?>" class="btn gen-btn general-link float-left"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to agents list</a></div>
<table class="table table-striped table-hover">
<tr><td class="gen-title" colspan="2"><img src="<?php echo $file_name; ?>" class="img-rounded"></td></tr>
<tr><td style="width:175px;" class="gen-title"><i class="fa fa-user" aria-hidden="true"></i> User ID</td><td><?php echo $user_id; ?></td></tr>
<tr><td class="gen-title"><i class="fa fa-user" aria-hidden="true"></i> Username</td><td><?php echo $new_username; ?></td></tr>
<tr><td class="gen-title"><i class="fa fa-user" aria-hidden="true"></i> Full Name</td><td><?php echo $name; ?></td></tr>
<tr><td class="gen-title"><i class="fa fa-envelope" aria-hidden="true"></i> Email</td><td><?php echo $email; ?></td></tr>
<tr><td class="gen-title"><i class="fa fa-phone" aria-hidden="true"></i> Telephone</td><td><?php echo $telephone; ?></td></tr>
<tr><td class="gen-title"><i class="fa fa-map-marker" aria-hidden="true"></i> Address</td><td><?php echo (!empty($address))?$address . ", ":""; echo (!empty($region))?$region . ", ":""; echo (!empty($city))?$city . ", ":""; echo (!empty($state))?$state . ".":""; ?></td></tr>
<tr><td class="gen-title"><i class="fa fa-map-marker" aria-hidden="true"></i> State of Origin</td><td><?php echo $state_of_origin; ?></td></tr>
<tr><td class="gen-title"><i class="fa fa-university" aria-hidden="true"></i> Education</td><td><?php echo $education; ?></td></tr>
<tr><td class="gen-title"><i class="fa fa-file-text" aria-hidden="true"></i> Confidentiality Agreement</td><td><?php echo $confidentiality_agreement; ?></td></tr>
<tr><td class="gen-title"><i class="fa fa-calendar" aria-hidden="true"></i> Date Registered</td><td><?php echo $date_registered; ?></td></tr>
<tr><td class="gen-title"><i class="fa fa-user" aria-hidden="true"></i> Registered by</td><td><?php echo $registered_by; ?></td></tr>
<tr><td class="gen-title"><i class="fa fa-sign-in" aria-hidden="true"></i> Logged In</td><td><?php echo $logged_in; ?></td></tr>
<tr><td class="gen-title"><i class="fa fa-calendar" aria-hidden="true"></i> Last Login</td><td><?php echo $last_login; ?></td></tr>
<tr><td class="gen-title"><i class="fa fa-calendar" aria-hidden="true"></i> Current Login</td><td><?php echo $date_time; ?></td></tr>
<tr><td class="gen-title"><i class="fa fa-user" aria-hidden="true"></i> Account Status</td><td><?php echo $status; ?></td></tr>
<tr><td class="gen-title"><i class="fa fa-cog" aria-hidden="true"></i> Action</td><td><?php 
if(check_admin("edit_agents") == 1){

if($active == 0){ ?> <a href="<?php echo $admin; ?>manage-agents?view=<?php echo $id; ?>&activate=<?php echo $get_id; ?>&pn=<?php echo $pn ?>" class="btn gen-btn general-link"><i class="fa fa-check" aria-hidden="true"></i> Activate user</a> <?php }

if($blocked == 0){ ?><a href="<?php echo $admin; ?>manage-agents?view=<?php echo $get_id; ?>&block=<?php echo $get_id; ?>&pn=<?php echo $pn ?>" class="btn gen-btn general-link"><i class="fa fa-times" aria-hidden="true"></i> Block user</a> <?php } 

if($blocked == 1){ ?> <a href="<?php echo $admin; ?>manage-agents?view=<?php echo $get_id; ?>&unblock=<?php echo $get_id; ?>&pn=<?php echo $pn ?>" class="btn gen-btn general-link"><i class="fa fa-check" aria-hidden="true"></i> Unblock user</a> <?php } ?> 

<a onClick="javascript:$('.reset-row').slideToggle();" class="btn gen-btn"><i class="fa fa-lock" aria-hidden="true"></i> Reset password <i class="fa fa-angle-down" aria-hidden="true"></i></a>

<a href="<?php echo $admin; ?>manage-agents?edit=<?php echo $get_id; ?>&pn=<?php echo $pn ?>" class="btn gen-btn general-link"><i class="fa fa-pencil" aria-hidden="true"></i> Edit Profile</a>

<?php } ?>
</td></tr>
<tr><td colspan="2">
<?php if(check_admin("edit_agents") == 1){ ?>

<form action="<?php echo $admin; ?>manage-agents" class="general-form reset-row" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data" style="width:100%;">  
<div class="gen-title">Change <?php echo $name; ?>&#039;s Password</div>    

<input type="hidden" name="gh" value="1">
<input type="hidden" name="reset" value="<?php echo $get_id; ?>">
<input type="hidden" name="view" value="<?php echo $view; ?>">
<input type="hidden" name="pn" value="<?php echo $pn; ?>">

<div>
<label for="password">New Password (atleast 5 characters)</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-lock"></i></span>
<input type="password" name="password" id="password" class="form-control" placeholder="Password to log in" required value="<?php check_inputted("password"); ?>">
</div>
</div>

<div>
<label for="conf_pass">Retype Password</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-lock"></i></span>
<input type="password" name="conf_password" id="conf_password" class="form-control" placeholder="Retype password" required value="<?php check_inputted("conf_password"); ?>">
</div>
</div>
                     
<div class="submit-div">
<button class="btn gen-btn float-right"><i class="fa fa-upload"></i> Update</button>
</div>
</form>

<?php } ?>
</td></tr>
</table>

<?php
}else{
echo "<div class='not-success'>This user does not exist.</div>";
}
}

//=======================Add New Agent==============================//
if(check_admin("add_agents") == 1 && !empty($add) && $error == 1){ ?>

<style>
<!--
.check-code{
color:#f00;
}
-->
</style>

<div><a href="<?php echo $admin; ?>manage-agents?pn=<?php echo $pn; ?>" class="btn gen-btn general-link float-left"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to agents list</a></div>

<form action="<?php echo $admin; ?>manage-agents" method="post" class="general-form" id="form-div" runat="server" autocomplete="off" enctype="multipart/form-data">
<div class="body-header">Add a <span>New Agent</span></div>    

<div class="required">All * fields are required.</div>

<input type="hidden" name="gh" value="1">
<input type="hidden" name="add" value="1">
<input type="hidden" name="pn" value="<?php echo $pn; ?>">

<div class="col-sm-6">
<label for="name">Full Name*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
<input type="text" name="name" id="name" class="form-control" placeholder="User&#039;s Full Name" required value="<?php check_inputted("name"); ?>">
</div>
</div>

<div class="col-sm-6">
<label for="username">Username*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
<input type="text" name="username" id="username" class="form-control" placeholder="User&#039;s Username" required value="<?php check_inputted("username"); ?>">
</div>
</div>

<div class="col-sm-6">
<label for="password">Password <b>(atleast 5 characters)</b>*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-lock" aria-hidden="true"></i></span>
<input type="password" name="password" id="password" class="form-control" placeholder="User&#039;s Password" required value="<?php check_inputted("password"); ?>">
</div>
</div>

<div class="col-sm-6">
<label for="re_password">Retype Password <b>(atleast 5 characters)</b>*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-lock" aria-hidden="true"></i></span>
<input type="password" name="re_password" id="re_password" class="form-control" placeholder="Retype User&#039;s Password" required value="<?php check_inputted("re_password"); ?>">
</div>
</div>

<div class="col-sm-6">
<label for="email">Email*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-envelope-o" aria-hidden="true"></i></span>
<input type="email" name="email" id="email" class="form-control" placeholder="User&#039;s E-mail Address" required value="<?php check_inputted("email"); ?>">
</div>
</div>

<div class="col-sm-6">
<label for="re_email">Retype Email*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-envelope-o" aria-hidden="true"></i></span>
<input type="email" name="re_email" id="re_email" class="form-control" placeholder="Retype User&#039;s E-mail Address" required value="<?php check_inputted("re_email"); ?>">
</div>
</div>

<div class="col-sm-6">
<label for="telephone">Telephone</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></span>
<input type="text" name="telephone" id="telephone" class="form-control" placeholder="Telephone no" value="<?php check_inputted("telephone"); ?>">
</div>
</div>


<div class="col-sm-6">
<label for="address">Address (Street no. and name)</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-map-marker" aria-hidden="true"></i></span>
<input type="text" name="address" id="address" class="form-control" placeholder="E.g. Plat 7, Igbayilola Steet" value="<?php check_inputted("address"); ?>" />
</div>
</div>

<div class="col-sm-6">
<label for="region">Region</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-map-marker" aria-hidden="true"></i></span>
<input type="text" name="region" id="region" class="form-control" placeholder="E.g. Orile Iganmu" value="<?php check_inputted("region"); ?>" />
</div>
</div>

<div class="col-sm-6">
<label for="city">City</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-map-marker" aria-hidden="true"></i></span>
<input type="text" name="city" id="city" class="form-control" placeholder="E.g. Surulere" value="<?php check_inputted("city"); ?>" />
</div>
</div>

<div class="col-sm-6">
<label for="state">State</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-map-marker" aria-hidden="true"></i></span>
<input type="text" name="state" id="state" class="form-control" placeholder="E.g. Lagos" value="<?php check_inputted("state"); ?>" />
</div>
</div>

<div class="col-sm-6">
<label for="state_of_origin">State of Origin</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-map-marker" aria-hidden="true"></i></span>
<input type="text" name="state_of_origin" id="state_of_origin" class="form-control" placeholder="State of Origin" value="<?php check_inputted("state_of_origin"); ?>">
</div>
</div>

<div class="col-sm-6">
<label for="education">Education (Highest Qualification)</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-university" aria-hidden="true"></i></span>
<input type="text" name="education" id="education" class="form-control" placeholder="E.g. Primary" value="<?php check_inputted("education"); ?>">
</div>
</div>

<div class="col-sm-6">
<label for="confidentiality_agreement">Confidentiality Agreement</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-file-text" aria-hidden="true"></i></span>
<select name="confidentiality_agreement" id="confidentiality_agreement" class="form-control">
<option value=""> - - Select an option - - </option>
<option value="Yes" <?php check_selected("confidentiality_agreement", "Yes"); ?>>Yes</option>
<option value="No" <?php check_selected("confidentiality_agreement", "No"); ?>>No</option>
</select>
</div>
</div>

<div class="col-sm-6">
<label for="check_user">Type this check code below: <span class="check-code"><?php $_SESSION["spam_checker"] = rand(1000,9999); echo $_SESSION["spam_checker"]; ?></span></label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-lock"></i></span>
<input type="text" name="check_user" id="check_user" class="form-control only-no"  maxlength="4" placeholder="Type the check code here" required value="">
</div>
</div>
                     
<div class="col-sm-6 submit-div">
<button class="btn gen-btn float-right"><i class="fa fa-upload"></i> Save</button>
</div>
</form>

<?php
}

//=======================Edit Agent Data==============================//
if(check_admin("edit_agents") == 1 && !empty($edit) && $error == 1){ 

$result = $db->select("reg_users", "Where id = '{$edit}'", "*", "");

if(count_rows($result) == 1){
$row = fetch_data($result);
$name = $row["name"];
$email = $row["email"];
$telephone = $row["telephone"];
$address = $row["address"];
$region = $row["region"];
$city = $row["city"];
$state = $row["state"];
$state_of_origin = $row["state_of_origin"];
$education = $row["education"];
$confidentiality_agreement = $row["confidentiality_agreement"];
?>

<div><a href="<?php echo $admin; ?>manage-agents?view=<?php echo $edit; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link float-left"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to agent&#039;s profile</a></div>

<form action="<?php echo $admin; ?>manage-agents" method="post" class="general-form" id="form-div" runat="server" autocomplete="off" enctype="multipart/form-data">
<div class="body-header">Edit <span>Agent&#039;s Profile</span></div>    

<div class="required">All * fields are required.</div>

<input type="hidden" name="gh" value="1">
<input type="hidden" name="view" value="<?php echo $edit; ?>">
<input type="hidden" name="edit" value="<?php echo $edit; ?>">
<input type="hidden" name="pn" value="<?php echo $pn; ?>">

<div class="col-sm-6">
<label for="name">Full Name*</label>
<div class="form-group input-group"> 
<span class="input-group-addon"><i class="fa fa-user"></i></span>
<input type="text" name="name" id="name" class="form-control" placeholder="Type user&#039;s full name" value="<?php check_inputted("name", $name); ?>" required>
</div>
</div>

<div class="col-sm-6">
<label for="email">Email*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-envelope"></i></span>
<input type="email" name="email" id="email" class="form-control" placeholder="Type user&#039;s email" required value="<?php check_inputted("email", $email); ?>">
</div>
</div>

<div class="col-sm-6">
<label for="telephone">Telephone</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></span>
<input type="text" name="telephone" id="telephone" class="form-control" placeholder="Telephone no" value="<?php check_inputted("telephone", $telephone); ?>">
</div>
</div>

<div class="col-sm-6">
<label for="address">Address (Street no. and name)</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-map-marker" aria-hidden="true"></i></span>
<input type="text" name="address" id="address" class="form-control" placeholder="E.g. Plat 7, Igbayilola Steet" value="<?php check_inputted("address", $address); ?>" />
</div>
</div>

<div class="col-sm-6">
<label for="region">Region</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-map-marker" aria-hidden="true"></i></span>
<input type="text" name="region" id="region" class="form-control" placeholder="E.g. Orile Iganmu" value="<?php check_inputted("region", $region); ?>" />
</div>
</div>

<div class="col-sm-6">
<label for="city">City</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-map-marker" aria-hidden="true"></i></span>
<input type="text" name="city" id="city" class="form-control" placeholder="E.g. Surulere" value="<?php check_inputted("city", $city); ?>" />
</div>
</div>

<div class="col-sm-6">
<label for="state">State</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-map-marker" aria-hidden="true"></i></span>
<input type="text" name="state" id="state" class="form-control" placeholder="E.g. Lagos" value="<?php check_inputted("state", $state); ?>" />
</div>
</div>

<div class="col-sm-6">
<label for="state_of_origin">State of Origin</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-map-marker" aria-hidden="true"></i></span>
<input type="text" name="state_of_origin" id="state_of_origin" class="form-control" placeholder="State of Origin" value="<?php check_inputted("state_of_origin", $state_of_origin); ?>">
</div>
</div>

<div class="col-sm-6">
<label for="education">Education (Highest Qualification)</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-university" aria-hidden="true"></i></span>
<input type="text" name="education" id="education" class="form-control" placeholder="E.g. Primary" value="<?php check_inputted("education", $education); ?>">
</div>
</div>

<div class="col-sm-6">
<label for="confidentiality_agreement">Confidentiality Agreement</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-file-text" aria-hidden="true"></i></span>
<select name="confidentiality_agreement" id="confidentiality_agreement" class="form-control">
<option value=""> - - Select an option - - </option>
<option value="Yes" <?php check_selected("confidentiality_agreement", "Yes", $confidentiality_agreement); ?>>Yes</option>
<option value="No" <?php check_selected("confidentiality_agreement", "No", $confidentiality_agreement); ?>>No</option>
</select>
</div>
</div>

<div class="col-sm-12 submit-div">
<button class="btn gen-btn float-right"><i class="fa fa-upload"></i> Update</button>
</div>
</form>

<?php
}
}
?>

<script src="js/general-form.js"></script>

<?php  if(!isset($_REQUEST["gh"])){?>

</div>
</div>

</div>
<?php require_once("../includes/portal-footer.php"); } ?>