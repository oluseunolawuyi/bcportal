<?php include_once('../includes/admin-header.php');  ?> 

<?php
admin_role_redirect("manage_general_messages");

$error = 1;
$recipient = tp_input("recipient");
$username = tp_input("username");
$subject2 = tp_input("subject");
$subject = html_entity_decode($subject2);
$message = tp_input("message");
$mail = tp_input("newsletter");

if(check_admin("manage_general_messages") == 1 && $_SERVER['REQUEST_METHOD'] == "POST" && !empty($mail) && !empty($recipient) && !empty($subject2) && !empty($message)){

$result = $email = "";

/////========== A Single User ==========//////
if($recipient == "single" && !empty($username)){
$result = $db->select("reg_users", "WHERE username = '$username'", "*");
if(count_rows($result) > 0){
$row = fetch_data($result);
$email = $row["email"] . ", ";
}
}

/////========== Active Users ==========//////    
if($recipient == "all" || $recipient == "admin" || $recipient == "clients" || $recipient == "agents"){
$where = "";
if($recipient == "admin"){
$where = " AND admin = '1'";
}else if($recipient == "clients"){
$where = " AND client = '1'";
}else if($recipient == "agents"){
$where = " AND agent = '1'";
}
$result = $db->select("reg_users", "WHERE active = '1' {$where}", "*");
if(count_rows($result) > 0){
while($row = fetch_data($result)){
$email .= $row["email"] . ", ";
}
}
}


if(!empty($email)){
$email = substr($email,0,-2);
$sep_pos = strpos($email, ",");
$email_array = ($sep_pos > 0)?explode(", ",$email):"";
$email_array = ($sep_pos > 0)?array_unique($email_array):"";

$message = "<p>Dear +*/-+*/-,</p><p>{$message}</p>";
$message2 = $message;
$message = html_entity_decode($message);
$message3 = message_template();
$headers2 = "{$gen_name} <no-reply@{$domain}>";

//////////=============== Send Mail ===============/////////////
if($sep_pos > 0){
foreach($email_array as $value){
if(!empty($value)){
$receivers_name = $message = $to = $headers = "";
$receivers_name = in_table("name","reg_users","WHERE email = '{$value}'","name");
$message = str_replace("+*/-+*/-", $receivers_name, $message3);
$to = "{$value}";
$headers = $headers2;
$act = send_mail();
$message4 = str_replace("+*/-+*/-", "$receivers_name", $message2);
$admin_data_array = array(
"ticket_id" => "'$ticket_id'",
"sender_name" => "'$gen_name'",
"sender_email" => "'$gen_email'",
"recipient_name" => "'$receivers_name'",
"recipient_email" => "'$value'",
"subject" => "'$subject2'",
"message" => "'$message4'",
"inbox" => "'1'",
"date_time" => "'$date_time'"
);
$act = $db->insert($admin_data_array, "admin_messages");
}
}
}else{
$receivers_name = $message = $to = $headers = "";
$receivers_name = in_table("name","reg_users","WHERE email = '{$email}'","name");
$message = str_replace("+*/-+*/-", $receivers_name, $message3);
$to = "{$email}";
$headers = $headers2;
$act = send_mail();
$message4 = str_replace("+*/-+*/-", "$receivers_name", $message2);
$admin_data_array = array(
"ticket_id" => "'$ticket_id'",
"sender_name" => "'$gen_name'",
"sender_email" => "'$gen_email'",
"recipient_name" => "'$receivers_name'",
"recipient_email" => "'$email'",
"subject" => "'$subject2'",
"message" => "'$message4'",
"inbox" => "'1'",
"date_time" => "'$date_time'"
);
$act = $db->insert($admin_data_array, "admin_messages");
}
////////==============================================///////////

if($act){

$message4 = $receivers_name = "";
if($recipient == "single"){
$receivers_name = in_table("name","reg_users","WHERE email = '{$email}'","name");
$message4 = str_replace("+*/-+*/-", $receivers_name, $message2);
}else if($recipient == "all"){
$receivers_name = "All Active Users";
$message4 = str_replace("+*/-+*/-", $receivers_name, $message2);
}else if($recipient == "admin"){
$receivers_name = "All Admin Users";
$message4 = str_replace("+*/-+*/-", $receivers_name, $message2);
}else if($recipient == "clients"){
$receivers_name = "All Clients";
$message4 = str_replace("+*/-+*/-", $receivers_name, $message2);
}else if($recipient == "agents"){
$receivers_name = "All Agents";
$message4 = str_replace("+*/-+*/-", $receivers_name, $message2);
}

$user_data_array = array(
"recipient" => "'$receivers_name'",
"subject" => "'$subject2'",
"message" => "'$message4'"
);
$db->insert($user_data_array, "messages");

$_SESSION["msg"] = "<div class='success'>Mail successfully sent to $receivers_name.</div>";
redirect("{$directory}{$admin}new-message");
}else{
echo "<div class='not-success'>Not successful! Unable to send mail.</div>";
}

}else{
echo "<div class='not-success'>Not sent. No records found.</div>";
}
}

if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($mail) && (empty($recipient) or empty($subject2) or empty($message))){
echo "<div class='not-success'>Not successful! All the * fields are required.</div>";
}

if(isset($_SESSION["msg"]) && empty($mail)){
echo $_SESSION["msg"];
unset($_SESSION["msg"]);
}
?>

<div class="page-title">Send a Mail to Users</div>

<form action="<?php echo $admin; ?>new-message" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">  

<input type="hidden" name="mail" value="1">

<div>
<label for="recipient">Recipient*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user"></i></span>
<select name="recipient" id="recipient" class="form-control js-example-basic-single" required>
<option value=""> ** Select a recipient ** </option>
<option value="single" <?php check_selected("recipient", "single"); ?>>One user (type a username below)</option>
<option value="all" <?php check_selected("recipient", "all"); ?>>All Active Users</option>
<option value="admin" <?php check_selected("recipient", "admin"); ?>>Only Admin Users</option>
<option value="clients" <?php check_selected("recipient", "clients"); ?>>Only Clients</option> 
<option value="agents" <?php check_selected("recipient", "agents"); ?>>Only Agents</option>
</select>
</div>
</div>

<div>
<label for="username">Username</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user"></i></span>
<input type="text" name="username" id="username" class="form-control" placeholder="Username if to a user" value="<?php check_inputted("username"); ?>">
</div>
</div>

<div>
<label for="subject">Subject*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-file-text"></i></span>
<input type="text" name="subject" id="subject" class="form-control" placeholder="Subject of the message" value="<?php check_inputted("subject"); ?>" required>
</div>
</div>

<div>
<label for="message">Message*</label>
<textarea class="ckeditor" name="message" id="message" required placeholder="Type your message here" rows="3" cols="40" style="overflow: auto" ><?php check_inputted("message"); ?></textarea>
</div>
                  
<div class="submit-div">
<button class="btn gen-btn float-right" name="update"><i class="fa fa-upload"></i> Send</button>
</div>
</form>

</div>
</div>

</div>

<script src="js/text_plugin/ckeditor.js"></script>
<script src="js/general-form.js"></script>

<?php require_once("../includes/portal-footer.php"); ?>
