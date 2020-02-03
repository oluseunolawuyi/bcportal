<?php require_once("../includes/admin-header.php"); require_once("../includes/resize-image.php"); ?>

<?php
$error = 1;
$name = tp_input("name");
$email = tp_input("email");

$profile = tp_input("profile");
$upload = tp_input("upload");
$edit = nr_input("edit");

$designation = tp_input("designation");
$telephone = tp_input("telephone");
$mobile = tp_input("mobile");
$contact_person = tp_input("contact_person");
$staff_id = tp_input("staff_id");
$address = tp_input("address");
$region = tp_input("region");
$city = tp_input("city");
$state = tp_input("state");
$state_of_origin = tp_input("state_of_origin");
$education = tp_input("education");

////////////// Upload image //////////////////////////////
if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($upload) && !empty($_FILES["ufile"]["tmp_name"])){ 

upload_single_image("ufile", "{$id}pic", "../images/users/", "250", "250");

$_SESSION["msg"] = "<div class='success'>Picture successfully updated.</div>";
redirect("{$directory}{$admin}profile");
}

////////////// Update Profile //////////////////////////////
if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($profile) && !empty($name) && !empty($email)){

$data_array = array(
"name" => $name,
"email" => $email,
"designation" => $designation,
"telephone" => $telephone,
"mobile" => $mobile,
"contact_person" => $contact_person,
"staff_id" => $staff_id,
"address" => $address,
"region" => $region,
"city" => $city,
"state" => $state,
"state_of_origin" => $state_of_origin,
"education" => $education
);
$act = $db->update($data_array, "reg_users", "id = '$edit'");

if($user_email != $email){
$db->query("UPDATE reg_users SET active = '0' WHERE id = '{$id}'");

$reg_id = $id;
$enc_email = sha1($email);

$to = "{$email}";
$subject = "Account Activation";
$message = "<p>Dear {$name},</p>
<p>Thank you for updating your profile on {$domain}.</p>{
<p>Please confirm your update request by following this link: {$directory}?a={$reg_id}&b={$enc_email}</p>
<p>You may also copy and paste this link in the address bar of your browser.</p>";
$foot_note .= "<p>If you did not complete a registration form on {$domain}, it means you are getting this message in error. Please delete it. No further action is necessary.</p>";
$message = message_template();
$headers = "{$gen_name} <no-reply@{$domain}>";
send_mail();

unset($_SESSION["login"]);
$_SESSION["msg"] = "<div class='success'>You are automatically logged out for e-mail update and your account has been deactivated. Kindly check your e-mail to activate your account. Then log in to continue.</div>";
redirect($directory);

}else if($act){

$error = 0;

$_SESSION["msg"] = "<div class='success'>Profile successfully updated.</div>";
redirect("{$directory}{$admin}profile");
}else{
echo "<div class='not-success'>Error occured.</div>";
}

}

if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($profile) && (empty($name) || empty($email))){
echo "<div class='not-success'>Not submitted! All the required fields (*) must be filled.</div>";
}else if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)){
echo "<div class='not-success'>Not submitted! Invalid  email format.</div>";
}

if(isset($_SESSION["msg"]) && !empty($_SESSION["msg"]) && empty($upload) && empty($profile)){
echo $_SESSION["msg"];
unset($_SESSION["msg"]);
}
?>

<?php if(empty($edit) or (!empty($edit) && $error == 0)){ ?>

<div class="page-title">My Profile</div>

<?php
$result = $db->select("reg_users", "Where id = '{$id}'", "*", "");

if(count_rows($result) == 1){
$row = fetch_data($result);
$user_id = admin_id($id);
$designation = $row["designation"];
$telephone = $row["telephone"];
$mobile = $row["mobile"];
$contact_person = $row["contact_person"];
$staff_id = $row["staff_id"];
$address = $row["address"];
$region = $row["region"];
$city = $row["city"];
$state = $row["state"];
$state_of_origin = $row["state_of_origin"];
$education = $row["education"];
$confidentiality_agreement = $row["confidentiality_agreement"];
$admin_user = $row["admin"];
$super_admin = $row["super_admin"];
$client = $row["client"];
$agent = $row["agent"];

$role_id = $row["role_id"];
$role_assigned = (!empty($role_id))?"<span style=\"color:#2387a0; font-weight:bold;\">" . get_table_data("role_management", $role_id, "role") . "</span>":"<span style=\"color: #b20; font-weight:bold;\">Not Assigned</span>";
$role_assigned = (!empty($super_admin))?"<span style=\"color:#2387a0; font-weight:bold;\">SUPER ADMIN</span>":$role_assigned;

$date_registered = full_date($row["date_registered"]);
$active = ($row["active"] == 1)?"Active":"Not Active";
$date_time = full_date($row["date_time"]);
?>
<style>
<!--
div table thead tr th, div table tr th, div table tbody tr td, div table tr td{
text-align:left !important;
}
-->
</style>
<table class="table table-striped table-hover">

<tr><td style="width:160px;" class="gen-title">
<?php
$file_array = glob("../images/users/{$id}pic*.*");
$file_name = ($file_array)?"images/" . $file_array[0]:"images/post.jpg";
?>
<img src="<?php echo $file_name; ?>" >
</td><td>
<div class="col-sm-6">
<form action="<?php echo $admin; ?>profile" class="img-form" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">  
<input type="hidden" name="gh" value="1">
<input type="hidden" name="upload" value="1">                      
<p><b>Format: </b></p>
<p>.jpg, .gif, .png, .jpeg, Not more than 5MB<br /><br /></p>
<input type="file" name="ufile" id="ufile" required>
<label for="ufile" id="pic-label" class="btn gen-btn" ><i class="fa fa-upload" aria-hidden="true"></i> Change picture</label>
</form>
</div>
<div class="col-sm-6">
<p><b>User ID:</b> <?php echo $user_id; ?></p>
<p><b>Username:</b> <?php echo $username; ?></p>
<?php if(!empty($admin_user)){ ?>
<p><b>Role Assigned:</b> <?php echo $role_assigned; ?></p>
<?php } ?>
<p><b>Status:</b> <?php echo $active; ?></p>
<p><b>Jioned on:</b> <?php echo $date_registered; ?></p>
<p><b>Last Login:</b> <?php echo $date_time; ?></p>
</div>
</td></tr>
<tr><td class="gen-title"><i class="fa fa-user" aria-hidden="true"></i> Full Name</td><td><?php echo $user_name; ?></td></tr>
<tr><td class="gen-title"><i class="fa fa-envelope" aria-hidden="true"></i> Email</td><td><?php echo $user_email; ?></td></tr>
<?php if(!empty($admin_user)){ ?>
<tr><td class="gen-title"><i class="fa fa-user" aria-hidden="true"></i> Designation</td><td><?php echo $designation; ?></td></tr>
<?php } ?>
<tr><td class="gen-title"><i class="fa fa-phone" aria-hidden="true"></i> Telephone</td><td><?php echo $telephone; ?></td></tr>
<?php if(!empty($client)){ ?>
<tr><td class="gen-title"><i class="fa fa-phone" aria-hidden="true"></i> Mobile</td><td><?php echo $mobile; ?></td></tr>
<tr><td class="gen-title"><i class="fa fa-user" aria-hidden="true"></i> Contact Person</td><td><?php echo $contact_person; ?></td></tr>
<tr><td class="gen-title"><i class="fa fa-user" aria-hidden="true"></i> Staff ID</td><td><?php echo $staff_id; ?></td></tr>
<?php } ?>
<?php if(!empty($client) || !empty($agent)){ ?>
<tr><td class="gen-title"><i class="fa fa-map-marker" aria-hidden="true"></i> Address</td><td><?php echo (!empty($address))?$address . ", ":""; echo (!empty($region))?$region . ", ":""; echo (!empty($city))?$city . ", ":""; echo (!empty($state))?$state . ".":""; ?></td></tr>
<?php } ?>
<?php if(!empty($agent)){ ?>
<tr><td class="gen-title"><i class="fa fa-map-marker" aria-hidden="true"></i> State of Origin</td><td><?php echo $state_of_origin; ?></td></tr>
<tr><td class="gen-title"><i class="fa fa-university" aria-hidden="true"></i> Education (Highest Qualification)</td><td><?php echo $education; ?></td></tr>
<tr><td class="gen-title"><i class="fa fa-file-text" aria-hidden="true"></i> Confidentiality Agreement</td><td><?php echo $confidentiality_agreement; ?></td></tr>
<?php } ?>
</table>
<div class="bottom-edit"><a href="<?php echo $admin; ?>profile?edit=<?php echo $id; ?>" class="btn gen-btn float-right">Edit Profile</a></div>
<?php
}
} 
?>

<?php if(!empty($edit) && $error == 1){ 
$edit = testQty($_REQUEST["edit"]);
$result = $db->select("reg_users", "Where id = '{$edit}'", "*", "");

if(count_rows($result) == 1){
$row = fetch_data($result);
$designation = $row["designation"];
$telephone = $row["telephone"];
$mobile = $row["mobile"];
$contact_person = $row["contact_person"];
$staff_id = $row["staff_id"];
$address = $row["address"];
$region = $row["region"];
$city = $row["city"];
$state = $row["state"];
$state_of_origin = $row["state_of_origin"];
$education = $row["education"];
$admin_user = $row["admin"];
$client = $row["client"];
$agent = $row["agent"];
?>

<div class="back"><a href="<?php echo $admin; ?>profile" class="btn gen-btn"><i class="fa fa-arrow-left"></i> Back to profile</a></div>

<form action="<?php echo $admin; ?>profile" method="post" runat="server" autocomplete="off" enctype="multipart/form-data" style="overflow-x:auto;">  
<div class="gen-title">Edit Your Profile</div>    
<input type="hidden" name="edit" value="<?php echo $edit; ?>">
<input type="hidden" name="profile" value="1">
     
<div class="col-sm-12">
<label for="name">Full Name*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
<input type="text" name="name" id="name" class="form-control" placeholder="Your Full Name" required value="<?php check_inputted("name", $user_name); ?>">
</div>
</div>

<div class="col-sm-12">
<label for="email">Email*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-envelope-o" aria-hidden="true"></i></span>
<input type="email" name="email" id="email" class="form-control" placeholder="Your E-mail Address" required value="<?php check_inputted("email", $user_email); ?>">
</div>
</div>

<?php if(!empty($admin_user)){ ?>

<div class="col-sm-12">
<label for="designation">Designation</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
<input type="text" name="designation" id="designation" class="form-control" placeholder="E.g. Supervisor" value="<?php check_inputted("designation", $designation); ?>">
</div>
</div>

<?php } ?>

<div class="col-sm-12">
<label for="telephone">Telephone</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></span>
<input type="text" name="telephone" id="telephone" class="form-control" placeholder="Telephone no" value="<?php check_inputted("telephone", $telephone); ?>">
</div>
</div>

<?php if(!empty($client)){ ?>

<div class="col-sm-12">
<label for="mobile">Mobile No.</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></span>
<input type="text" name="mobile" id="mobile" class="form-control" placeholder="Mobile no" value="<?php check_inputted("mobile", $mobile); ?>">
</div>
</div>

<div class="col-sm-12">
<label for="contact_person">Contact Person</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
<input type="text" name="contact_person" id="contact_person" class="form-control" placeholder="Contact person" value="<?php check_inputted("contact_person", $contact_person); ?>">
</div>
</div>

<div class="col-sm-12">
<label for="staff_id">Staff ID</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
<input type="text" name="staff_id" id="staff_id" class="form-control" placeholder="Staff ID" value="<?php check_inputted("staff_id", $staff_id); ?>">
</div>
</div>

<?php } ?>

<?php if(!empty($client) || !empty($agent)){ ?>

<div class="col-sm-12">
<label for="address">Address (Street no. and name)</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-map-marker" aria-hidden="true"></i></span>
<input type="text" name="address" id="address" class="form-control" placeholder="E.g. Plat 7, Igbayilola Steet" value="<?php check_inputted("address", $address); ?>" />
</div>
</div>

<div class="col-sm-12">
<label for="region">Region</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-map-marker" aria-hidden="true"></i></span>
<input type="text" name="region" id="region" class="form-control" placeholder="E.g. Orile Iganmu" value="<?php check_inputted("region", $region); ?>" />
</div>
</div>

<div class="col-sm-12">
<label for="city">City</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-map-marker" aria-hidden="true"></i></span>
<input type="text" name="city" id="city" class="form-control" placeholder="E.g. Surulere" value="<?php check_inputted("city", $city); ?>" />
</div>
</div>

<div class="col-sm-12">
<label for="state">State</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-map-marker" aria-hidden="true"></i></span>
<input type="text" name="state" id="state" class="form-control" placeholder="E.g. Lagos" value="<?php check_inputted("state", $state); ?>" />
</div>
</div>

<?php } ?>

<?php if(!empty($agent)){ ?>

<div class="col-sm-12">
<label for="state_of_origin">State of Origin</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-map-marker" aria-hidden="true"></i></span>
<input type="text" name="state_of_origin" id="state_of_origin" class="form-control" placeholder="State of Origin" value="<?php check_inputted("state_of_origin", $state_of_origin); ?>">
</div>
</div>

<div class="col-sm-12">
<label for="education">Education (Highest Qualification)</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-university" aria-hidden="true"></i></span>
<input type="text" name="education" id="education" class="form-control" placeholder="E.g. Primary" value="<?php check_inputted("education", $education); ?>">
</div>
</div>

<?php } ?>
                    
<div class="submit-div col-sm-12">
<button class="btn gen-btn float-right" name="update"><i class="fa fa-upload"></i> Update</button>
</div>
</form>
<?php
}
} 
?>

<script src="js/general-form.js"></script>

</div>
</div>

</div>

<?php require_once("../includes/portal-footer.php"); ?>