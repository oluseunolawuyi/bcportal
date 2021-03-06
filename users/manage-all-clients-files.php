<?php if(!isset($_REQUEST["gh"])){ include_once("../includes/admin-header.php"); 
}else{ 
include_once("../includes/gen-header.php");
} ?>

<?php
admin_role_redirect("manage_clients_reports");

$table = "all_clients_reports";

/////////////////////////////////////////////////////////////////////////////////////////
if(isset($_POST["delete"]) && isset($_POST["del"])){
$i = 0;
if(is_array($_POST["del"])){
foreach ($_POST["del"] as $k => $c) {
if($c != ""){ 
$c = testQty($c);

$client_id = in_table("client",$table,"WHERE id = '{$c}'","client");
$client_name = in_table("name","reg_users","WHERE id = '{$client_id}'","name");
$client_email = in_table("email","reg_users","WHERE id = '{$client_id}'","email");
$client_details = "$client_name ({$client_email})";

$act = $db->delete($table, " id='{$c}' AND direction='1'");

$file_array = glob("../all-clients-reports/{$c}report*.*");
if($file_array){
foreach($file_array as $filename){
unlink($filename);
}
}

$i++;			
}else{
continue;
}
}

if($act){

$activity = "Deleted {$i} file(s) uploaded for {$client_details}.";
$audit_data_array = array(
"user_id" => "'$id'",
"name" => "'$username'",
"email" => "'$user_email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$db->insert($audit_data_array, "audit_log");

echo "<div class='success'>{$i} file(s) for {$client_details} successfully deleted.</div>";
}else{
echo "<div class='not-success'>Error. Unable to delete file(s).</div>";
}

}else{
echo "<div class='not-success'>Atleast one field must be selected.</div>";
}
}
///////////////////////////////////////////////////////////////////////


//////////=============Add New User===================///////////////////////////////////////

$client = np_input("client");
$verified_info = np_input("verified_info");
$candidate_name = tp_input("candidate_name");
$add = nr_input("add");
$download = tp_input("download");

$client_name = (!empty($client))?get_table_data("reg_users", $client, "name"):"";
$client_email = (!empty($client))?get_table_data("reg_users", $client, "email"):"";

////////////// Report Upload //////////////////////////////
if(check_admin("manage_clients_reports") == 1 && $_SERVER['REQUEST_METHOD'] == "POST" && !empty($add) && !empty($client) && !empty($verified_info) && !empty($candidate_name) && !empty($_FILES["ufile"]["tmp_name"])){ 

$i = $file_count = 0;

////=================Starts Upload Files==============////
if(!empty($_FILES["ufile"])){ 
include_once("../includes/resize-image.php");

$det_file = glob("../gen-temp/{$id}-client-temp-*.*");
if($det_file){
foreach($det_file as $value){
unlink($value);
}
}

foreach($_FILES["ufile"]["tmp_name"] as $val){ 
if(!empty($_FILES["ufile"]["tmp_name"][$i])){ 

$file_name = $_FILES["ufile"]["name"][$i]; 
$file_temp_name = $_FILES["ufile"]["tmp_name"][$i];
$info   = getimagesize($file_temp_name);
$file_size = $_FILES["ufile"]["size"][$i];
$file_error_message = $_FILES["ufile"]["error"][$i];
$file_name_2_array = explode(".", $file_name);
$file_extension = end($file_name_2_array);
$i++;

if (!$file_temp_name) {
    echo "<div class=\"not-success\">ERROR: Please browse for a file before clicking the upload button.</div>";
    exit();
} 
else if($file_size > 20971520) {
    echo "<div class=\"not-success\">ERROR: Your file #{$i} was larger than 20 Megabytes in size.</div>";
    unlink($file_temp_name);
    exit();
}
else if (!preg_match("/.(gif|GIF|jpg|JPG|png|PNG|jpeg|JPEG|tif|TIF|pdf|PDF|doc|DOC|docx|DOCX|xls|XLS|xlsx|XLSX)$/i", $file_name) ) {
     echo "<div class=\"not-success\">ERROR: Your file #{$i} was not .gif, .jpg, .jpeg, .png, .tif, doc, docx, xls or xlsx.</div>";
     unlink($file_temp_name);
     exit();
}
else if ($file_error_message == 1) {
    echo "<div class=\"not-success\">ERROR: An error occured while processing the file #{$i}. Try again.</div>";
    exit();
}
else if (  
(($file_extension=="gif" || $file_extension=="jpg" || $file_extension=="jpeg" || $file_extension=="png") &&
$info[2] != 1 && $info[2] != 2 && $info[2] != 3) 
|| ($file_extension=="tif" && $info[2] != 7 && $info[2] != 8)
  ) {
     echo "<div class=\"not-success\">ERROR: Your file #{$i} was not .gif, .jpg, .jpeg, .png or .tif.</div>";
     exit();
}

$file_name = "../gen-temp/{$id}-client-temp-" . rand(1000,9999) . ".{$file_extension}";

$move_file = move_uploaded_file($file_temp_name, $file_name);
if ($move_file != true) {
    echo "<div class=\"not-success\">ERROR: File #{$i} not uploaded. Try again.</div>";
    unlink($file_temp_name);
    exit();
}else{
if($file_extension=="gif" || $file_extension=="jpg" || $file_extension=="jpeg" || $file_extension=="png"){
$target_file = $file_name;
$resized_file = $file_name;
image_resize($target_file, $resized_file, $file_extension, 710, 950);
}
}

$file_count++;

}
}
}

////=================Ends Upload Files==============////

////=======Starts Save Date and Files=======//////////
if($file_count > 0){
	
$data_array = array(
"client" => "'$client'",
"direction" => "'1'",
"candidate_name" => "'$candidate_name'",
"verified_info" => "'$verified_info'",
"date_time" => "'$date_time'"
);
$db->insert($data_array, $table);

$report_id = in_table("id", $table, "WHERE client = '$client' AND verified_info = '$verified_info' AND date_time = '$date_time'", "id");

////=======Starts Save Files=======//////////
$file_array = glob("../gen-temp/{$id}-client-temp-*.*");
foreach($file_array as $value){
$new_file_extension = explode(".", $value);
$new_file_extension = end($new_file_extension);
if(file_exists($value)){
if(copy($value, "../all-clients-reports/{$report_id}report" . rand(1000,9999) . ".{$new_file_extension}")){
unlink($value);
}
}
}
////=======Ends Save Files=======//////////

$activity = "Uploaded new file(s) for $client_name ({$client_email}).";
$audit_data_array = array(
"user_id" => "'$id'",
"name" => "'$username'",
"email" => "'$user_email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$act = $db->insert($audit_data_array, "audit_log");

$to = $client_email;
$subject = "New File(s) Upload";
$message = "<p>Dear {$client_name},</p>
<p>We are pleased to inform you that a new report has been uploaded for you.</p>
<p>Kindly log in on {$domain} to view the report.</p>";

$admin_data_array = array(
"ticket_id" => "'$ticket_id'",
"sender_name" => "'$gen_name'",
"sender_email" => "'$gen_email'",
"recipient_name" => "'$client_name'",
"recipient_email" => "'$client_email'",
"subject" => "'$subject'",
"message" => "'$message'",
"inbox" => "'1'",
"date_time" => "'$date_time'"
);
$act = $db->insert($admin_data_array, "admin_messages");

$message = message_template();
$headers = "{$gen_name} <no-reply@{$domain}>";
send_mail();

$error = 0;

echo "<div class='success'>Report successfully uploaded.</div>";

}
////=======Ends Save Date and Files=======//////////


}
////////////// Ends Report Upload //////////////////////////////

if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($add) && (empty($client) || empty($verified_info) || empty($candidate_name) || empty($_FILES["ufile"]["tmp_name"]))){
echo "<div class='not-success'>Not submitted! All * the fields are required.</div>";
}

//////////====================Download Report==========//////
if(check_admin("manage_clients_reports") == 1 && !empty($download)){
$file_name = glob($download);
$file_name = (file_exists($file_name[0]))?$file_name[0]:"";
if(!empty($file_name)){
echo "<iframe src=\"{$admin}download-report?document={$file_name}\" style=\"width:1px; height:1px;\"></iframe>";
}
}

////////////////////////////////////////////////////******************************//////////////

$search_type = search_option("search_type");
$search_client = search_option("search_client");
$search_verified_info = search_option("search_verified_info");
$reference_code = search_option("reference_code");
$keywords = search_option("keywords");
$search_start_date = search_option("search_start_date");
$search_end_date = search_option("search_end_date");
$no_of_rows = search_option("no_of_rows");

$where = "WHERE id > '0'";
$where .= (!empty($search_client))?" AND client = '$search_client'":"";
$where .= (!empty($search_verified_info))?" AND verified_info = '$search_verified_info'":"";
$where .= (!empty($search_type))?" AND direction = '{$search_type}'":"";
$where .= (!empty($reference_code))?" AND id = '{$reference_code}'":"";
$where .= (!empty($keywords))?" AND candidate_name LIKE '%{$keywords}%'":"";
$where .= (!empty($search_start_date) && !empty($search_end_date))?" AND date_time BETWEEN '{$search_start_date} 00:00:00' AND '{$search_end_date} 23:59:59'":"";
$where .= (!empty($search_start_date) && empty($search_end_date))?" AND date_time >= '{$search_start_date} 00:00:00'":"";
$where .= (empty($search_start_date) && !empty($search_end_date))?" AND date_time <= '{$search_end_date} 23:59:59'":"";

$result = $db->select($table, $where, "*", "ORDER BY id DESC");
$count = count_rows($result);

$per_view = 20;
$per_view = (!empty($no_of_rows))?$no_of_rows:$per_view;
$page_link = "{$admin}manage-all-clients-files?pn=";
$link_suffix = "";
$style_class = "general-link";
page_numbers();

$offset = ($per_view * $pn) - $per_view;

$result = $db->select($table, $where, "*", "ORDER BY id DESC", "LIMIT {$offset},{$per_view}");

if(empty($add)||(!empty($add)&&$error==0)){
?>

<div class="page-title">Manage All Clients&#039; Files <a href="<?php echo $admin; ?>manage-all-clients-files?add=1&pn=<?php echo $pn; ?>" class="btn gen-btn general-link float-right"><i class="fa fa-upload" aria-hidden="true"></i> New File</a></div>

<form action="<?php echo $admin; ?>manage-all-clients-files" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
<input type="hidden" name="gh" value="1"> 
<div class="search-dates">

<div class="col-md-3">
<label for="search_client">Client</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
<select name="search_client" id="search_client" title="Select a client" class="form-control js-example-basic-single" style="width:100%">
<option value="">**Select a client**</option>
<?php 
$result2 = $db->select($table, "", "DISTINCT client", "ORDER BY client ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$client_id = $row2["client"];
$client_name = in_table("name","reg_users","WHERE id = '{$client_id}'","name");
$client_email = in_table("email","reg_users","WHERE id = '{$client_id}'","email");
echo "<option value='{$client_id}'";
check_selected("search_client", $client_id, $search_client); 
echo ">{$client_name} ({$client_email})</option>";
}
}
?>
</select>
</div>
</div>

<div class="col-md-3">
<label for="search_verified_info">Verified Info.</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-tag" aria-hidden="true"></i></span>
<select name="search_verified_info" id="search_verified_info" class="form-control js-example-basic-single" style="width:100%">
<option value="">**Select a verified info.**</option>
<?php 
$result2 = $db->select($table, "", "DISTINCT verified_info", "ORDER BY verified_info ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$verified_info_id = $row2["verified_info"];
$verified_info_title = in_table("item","uploadable_items","WHERE id = '{$verified_info_id}'","item");
echo "<option value='{$verified_info_id}'";
check_selected("search_verified_info", $verified_info_id, $search_verified_info); 
echo ">{$verified_info_title}</option>";
}
}
?>
</select>
</div>
</div>

<div class="col-md-3">
<label for="search_type">Type</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-file-text" aria-hidden="true"></i></span>
<select name="search_type" id="search_type" title="Select an option" class="form-control js-example-basic-single" style="width:100%">
<option value="">**Select an option**</option>
<option value="1" <?php check_selected("search_type", 1, $search_type); ?>>Sent Files</option>
<option value="2" <?php check_selected("search_type", 2, $search_type); ?>>Recieved Files</option>
</select>
</div>
</div>

<div class="col-md-3">
<label for="reference_code">Ref. Code</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-code" aria-hidden="true"></i></span>
<input type="text" name="reference_code" id="reference_code" class="form-control only-no" placeholder="E.g. 15" value="<?php check_inputted("reference_code", $reference_code); ?>">
</div>
</div>

<div class="col-md-4">
<label for="keywords">Keywords</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-file-text" aria-hidden="true"></i></span>
<input type="text" name="keywords" id="keywords" class="form-control" placeholder="Candidate&#039;s name" value="<?php check_inputted("keywords", $keywords); ?>">
</div>
</div>

<div class="col-md-2">
<label for="no_of_rows">No. of Rows</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-list" aria-hidden="true"></i></span>
<input type="number" name="no_of_rows" id="no_of_rows" class="form-control only-no" placeholder="No. of rows" value="<?php check_inputted("no_of_rows", $per_view); ?>">
</div>
</div>

<div class="col-md-3">
<label for="search_start_date">Start Date</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
<input type="text" name="search_start_date" id="search_start_date" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php check_inputted("search_start_date", $search_start_date); ?>">
</div>
</div>

<div class="col-md-3">
<label for="search_end_date">End Date</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
<input type="text" name="search_end_date" id="search_end_date" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php check_inputted("search_end_date", $search_end_date); ?>">
</div>
</div>

<div class="col-md-12">
<button type="submit" class="btn gen-btn float-right"><i class="fa fa-search"></i> Search</button>
</div>

</div>
</form>

<?php
if($count > 0){
?>
<form action="<?php echo $admin; ?>manage-all-clients-files" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data" style="overflow-x:auto;">
<input type="hidden" name="pn" value="<?php echo $pn; ?>"> 
<input type="hidden" name="gh" value="1"> 
<input type="hidden" name="delete" value="1"> 
<table class="table table-striped table-hover">
<thead>
<tr class="gen-title">
<th>Ref. Code</th>
<th>Client Details</th>
<th>Candidate</th>
<th>Verified Info.</th>
<th>Date Uploaded</th>
<th>Type</th>
<th>Download</th>
<th style="width:30px;"><input type="checkbox" name="sel_all" id="delG" class="sel-group" value=""></th>
</tr>
</thead>
<tbody>
<?php
$d = 0;
while($row = fetch_data($result)){
$get_id = $row["id"];	
$client_id = $row["client"];
$client_name = in_table("name","reg_users","WHERE id = '{$client_id}'","name");
$client_email = in_table("email","reg_users","WHERE id = '{$client_id}'","email");
$client_email = break_long($client_email, "", "");
$client_details = "{$client_name}<br>{$client_email}";
$candidate_name = $row["candidate_name"];
$verified_info = $row["verified_info"];
$verified_info = in_table("item","uploadable_items","WHERE id = '{$verified_info}'","item");
$direction_type = $row["direction"];
$direction = ($direction_type == 2)?"<div class=\"btn btn-primary\"><i class=\"fa fa-arrow-down\" aria-hidden=\"true\"></i> Received</div>":"<div class=\"btn btn-success\"><i class=\"fa fa-arrow-up\" aria-hidden=\"true\"></i> Sent</div>";
$date_time = ($row["date_time"] != "0000-00-00 00:00:00")?min_full_date($row["date_time"]):"";
?>
<tr>
<td><?php echo $get_id; ?></td>
<td><?php echo $client_details; ?></td>
<td><?php echo $candidate_name; ?></td>
<td><?php echo $verified_info; ?></td>
<td><?php echo $date_time; ?></td>
<td><?php echo $direction; ?></td>
<td>
<select title="Select a file to download" class="download form-control" style="width:100%">
<option value="">**Select a file**</option>
<?php 
$load_files = glob("../all-clients-reports/{$get_id}report*.*");
if($load_files){
$i=0;
foreach($load_files as $value){
$i++;
$new_file_ext = explode(".", $value);
$new_file_ext = end($new_file_ext);
$new_file_ext = strtoupper($new_file_ext);
echo "<option value='{$value}'>File {$i} - {$new_file_ext}</option>";
}
}
?>
</select>
</td>
<td><?php if($direction_type == 1){ ?><input type="checkbox" name="del[<?php echo $d; ?>]" id="del<?php echo $d; ?>" class="delG" value="<?php echo $get_id; ?>"><?php } ?></td>
</tr>
<?php 
$d++;
}
?>
<tr><td colspan="8">
<input class="sub-del" type="submit" value=" "><button type="button" class="btn del-btn gen-btn float-right"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete selected file(s)</button>
</td></tr>
</tbody>
</table>
</div>

<script>
<!--
/////=====Download Files=====//////
$(".download").change(function(){

var this_val = $(this).val();
if(this_val != ""){
$(".general-fade").show();
$.post("<?php echo $admin; ?>manage-all-clients-files", {gh : 1, download : this_val}, function(data){ 
$(".form-div").html(data);
$(".general-fade").hide();
 }).error(function() { 
sweetAlert("Notice", "Error occured!", "error");
$(".general-fade").hide();
 });
}

});
//-->
</script>

<?php
echo ($last_page>1)?"<div class=\"page-nos\">" . $center_pages . "</div>":"";
}else{
echo "<div class='not-success'>No files found.</div>";
}

}

//=======================Add New Report==============================//
if(check_admin("manage_clients_reports") == 1 && !empty($add) && $error == 1){ ?>

<style>
<!--
.gen-btn.del-sub-cat{
padding:5px!important;
padding-top:0px!important;
padding-bottom:0px!important;
}
.ufile{
border:0px;
}
.upload-label{
overflow:hidden; 
width:100%;
}
-->
</style>

<div><a href="<?php echo $admin; ?>manage-all-clients-files?pn=<?php echo $pn; ?>" class="btn gen-btn general-link float-left"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to clients&#039; files</a></div>

<form action="<?php echo $admin; ?>manage-all-clients-files" method="post" class="general-form" id="form-div" runat="server" autocomplete="off" enctype="multipart/form-data">
<div class="body-header">Add <span>New File(s)</span></div>    

<div class="required">All * fields are required.</div>

<input type="hidden" name="gh" value="1">
<input type="hidden" name="add" value="1">
<input type="hidden" name="pn" value="<?php echo $pn; ?>">

<div class="col-sm-6">
<label for="client">Client*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
<select name="client" id="client" title="Select a client" class="form-control js-example-basic-single" style="width:100%" required>
<option value="">**Select a client**</option>
<?php 
$result2 = $db->select("reg_users", "WHERE client = '1'", "DISTINCT *", "ORDER BY id ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$client_id = $row2["id"];
$client_name = $row2["name"];
$client_email = $row2["email"];
echo "<option value='{$client_id}'";
check_selected("client", $client_id); 
echo ">{$client_name} ({$client_email})</option>";
}
}
?>
</select>
</div>
</div>

<div class="col-sm-6">
<label for="verified_info">Verified Info.*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
<select name="verified_info" id="verified_info" class="form-control js-example-basic-single" style="width:100%" required>
<option value="">**Select a verified info.**</option>
<?php 
$result2 = $db->select("uploadable_items", "WHERE user_type='1'", "DISTINCT *", "ORDER BY id ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$item_id = $row2["id"];
$item_title = $row2["item"];
echo "<option value='{$item_id}'";
check_selected("verified_info", $item_id); 
echo ">{$item_title}</option>";
}
}
?>
</select>
</div>
</div>

<div class="col-sm-6">
<label for="candidate_name">Candidate&#039;s Name</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
<input type="text" name="candidate_name" id="candidate_name" class="form-control" placeholder="Enter full name of candidate" value="<?php check_inputted("candidate_name"); ?>">
</div>
</div>

<div class="col-sm-6">
<label for="ufile1" class="upload-label"><b>Format:</b> .pdf, .doc, .docx, .jpg, .jpeg, .tif, doc, docx, xls or xlsx*</label>
<div class="form-group input-group">
<input type="file" name="ufile[]" for="ufile1" class="form-control ufile" required>
</div>
</div>

<div class="col-sm-12 files-upload"></div>
                     
<div class="col-sm-12 submit-div">
<button type="button" class="btn add-new-file gen-btn float-left"><i class="fa fa-plus"></i> Add</button>
<button type="submit" class="btn gen-btn float-right"><i class="fa fa-upload"></i> Save</button>
</div>
</form>

<script>
<!--
var c = 1;

$(".add-new-file").click(function(){
c++;
$(".files-upload").append("<div class=\"col-sm-6\" id=\"ufile-div-" + c + "\"><label for=\"ufile" + c + "\" class=\"upload-label\"><b>Format:</b> .pdf, .doc, .docx, .jpg, .jpeg, .tif, doc, docx, xls or xlsx* <button type=\"button\" class=\"btn gen-btn del-sub-cat float-right\" lang=\"ufile-div-" + c + "\" onclick=\"javascript: delete_sub(this.lang);\"><i class=\"fa fa-times\"></i></button></label><div class=\"form-group input-group\"><input type=\"file\" name=\"ufile[]\" id=\"ufile" + c + "\" class=\"form-control ufile\" required></div></div>");
});

function delete_sub(what){
document.getElementById(what).outerHTML = "";
}
//-->
</script>

<?php
}
?>

<script>
<!--
var conf_text = "file";
//-->
</script>

<script src="js/general-form.js"></script>

<?php  if(!isset($_REQUEST["gh"])){?>

</div>
</div>

</div>
<?php require_once("../includes/portal-footer.php"); } ?>