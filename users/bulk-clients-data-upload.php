<?php require_once("../includes/admin-header.php"); require_once("../includes/simplexlsx.class.php"); ?>

<?php
admin_role_redirect("bulk_client_upload");

$error = $act = "";

$upload = tp_input("upload");

////////////// Upload image //////////////////////////////
if(check_admin("bulk_client_upload") == 1 && $_SERVER['REQUEST_METHOD'] == "POST" && !empty($upload) && !empty($_FILES["ufile"]["tmp_name"])){ 

$file_name = $_FILES["ufile"]["name"]; 
$file_temp_name = $_FILES["ufile"]["tmp_name"];
$file_error_message = $_FILES["ufile"]["error"];
$file_name_2_array = explode(".", $file_name);
$file_extension = end($file_name_2_array);

////======================= Access File ===============/////
if (!$file_temp_name) {
    echo "<div class=\"not-success\">ERROR: Please browse for a file before clicking the upload button.</div><button class=\"btn gen-btn\"  onclick=\"javascript:location.reload();\"><i class=\"fa fa-refresh\"></i> Try Again</button>";
    exit();
} 
else if (!preg_match("/.(xlsx|XLSX)$/i", $file_name) ) {
     echo "<div class=\"not-success\">ERROR: Your file was not .xlsx .</div><button class=\"btn gen-btn\"  onclick=\"javascript:location.reload();\"><i class=\"fa fa-refresh\"></i> Try Again</button>";
     unlink($file_temp_name);
     exit();
}
else if ($file_error_message == 1) {
    echo "<div class=\"not-success\">ERROR: An error occured while processing the file. Try again.</div><button class=\"btn gen-btn\"  onclick=\"javascript:location.reload();\"><i class=\"fa fa-refresh\"></i> Try Again</button>";
    exit();
}
///////////////////===================================================/////////////
$file_name = "clients-data" . $ticket_id . $rand_no . ".xlsx";

foreach (glob("clients-data*.xlsx") as $filename) {
unlink($filename);
}

if(move_uploaded_file($file_temp_name, $file_name)){

////////===================== Read File ====================///////
if(file_exists($file_name)){

if ($xlsx = SimpleXLSX::parse($file_name)) {
$file_rows_array = $xlsx->rows();

////////===================== Starts Read Each Line ====================///////
	
for($i = 0; $i < count($file_rows_array); $i++){
  
$name = $file_rows_array[$i][0];
$new_username = test_input($file_rows_array[$i][1]);
$password2 = test_input($file_rows_array[$i][2]);
$email = test_input($file_rows_array[$i][3]);
$telephone = test_input($file_rows_array[$i][4]);
$mobile = test_input($file_rows_array[$i][5]);
$contact_person = test_input($file_rows_array[$i][6]);
$staff_id = test_input($file_rows_array[$i][7]);
$address = test_input($file_rows_array[$i][8]);	
$region = test_input($file_rows_array[$i][9]);	
$city = test_input($file_rows_array[$i][10]);	
$state = test_input($file_rows_array[$i][11]);	

if($i > 0){
if(!empty($name) && !empty($new_username) && !empty($password2) && !empty($email)){	
 
$email = strtolower($email);
$password = sha1($password2);
$email_exists = in_table("email", "reg_users", "WHERE email = '{$email}'", "email");
$result = $db->select("reg_users", "WHERE username = '{$new_username}'", "*", "");
if(!empty($email_exists) && count_rows($result) > 0){
$error .= "Username: <b>{$new_username}</b>, Email: <b>{$email}</b><br>";
}else if(!empty($email_exists)){
$error .= "Email: <b>{$email}</b><br>";
}else if(count_rows($result) > 0){
$error .= "Username: <b>{$new_username}</b><br>";
}else if(count_rows($result) < 1){

$data_array = array(
"name" => $name,
"username" => $new_username,
"password" => $password,
"email" => $email,
"telephone" => $telephone,
"mobile" => $mobile,
"contact_person" => $contact_person,
"staff_id" => $staff_id,
"address" => $address,
"region" => $region,
"city" => $city,
"state" => $state,
"date_registered" => $date_time,
"active" => 1,
"registered_by" => $id,
"client" => 1
);
$act = $db->insert2($data_array, "reg_users");

$to = "{$email}";
$subject = "New Account Creation";
$message = "<p>Dear {$name},</p>
<p>We are pleased to inform you that an account has been successfully created for you on {$gen_name}.</p>
<p>You may log in on {$domain} to manage your account with username: <b>{$new_username}</b> and password: <b>{$password2}</b> .</p>";
$message = message_template();
$headers = "{$gen_name} <no-reply@{$domain}>";
send_mail();
}


}
}


}

if($act){
$activity = "Uploaded new bulk clients&#039; data.";
$audit_data_array = array(
"user_id" => "'$id'",
"name" => "'$username'",
"email" => "'$user_email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$db->insert($audit_data_array, "audit_log");

$_SESSION["msg"] = "<div class='success'>Bulk clients&#039; data successfully uploaded at " . full_date($date_time) . ".</div>";
$_SESSION["msg"] .= (!empty($error))?"<div class='not-success'>Upload of some data was not successful for existing on the database, detailed as follows:<br>{$error}</div>":"";
redirect("{$directory}{$admin}bulk-clients-data-upload");
}else{
echo "<div class='not-success'>Upload of some data was not successful for existing on the database, detailed as follows:<br>{$error}</div>";
}

////////===================== Ends Read Each Line ====================///////

}else{
echo SimpleXLSX::parse_error();
}

}else{
echo "<div class='not-success'>Error occured while reading the file. Please upload again.</div>";
}
////////////////////////////=========================================////////////

}
}

if(isset($_SESSION["msg"]) && !empty($_SESSION["msg"]) && empty($upload)){
echo $_SESSION["msg"];
unset($_SESSION["msg"]);
}
?>

<div class="page-title">Bulk Clients&#039; Data Upload <a href="format/bulk-clients-data-upload.xlsx" class="btn gen-btn float-right">Download Data Format</a></div>

<form action="<?php echo $admin; ?>bulk-clients-data-upload" class="img-form" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">  
<input type="hidden" name="gh" value="1">
<input type="hidden" name="upload" value="1">                      
<p><b>Format:</b> .xlsx<br /><br /></p>
<input type="file" name="ufile" id="ufile" required>
<label for="ufile" id="pic-label" class="btn gen-btn" ><i class="fa fa-upload" aria-hidden="true"></i> Upload data</label>
</form>

<script src="js/general-form.js"></script>

</div>
</div>

</div>

<?php require_once("../includes/portal-footer.php"); ?>