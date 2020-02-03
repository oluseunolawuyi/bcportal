<?php if(!isset($_REQUEST["gh"])){ include_once("../includes/admin-header.php"); 
}else{ 
include_once("../includes/gen-header.php");
} ?>

<?php
admin_role_redirect("manage_clients_reports");

//////////=============Add New User===================///////////////////////////////////////

$client = np_input("client");
$add = nr_input("add");
$download = nr_input("download");
$type = nr_input("type");

$client_name = (!empty($client))?get_table_data("reg_users", $client, "name"):"";
$client_email = (!empty($client))?get_table_data("reg_users", $client, "email"):"";

////////////// Report Upload //////////////////////////////
if(check_admin("manage_clients_reports") == 1 && $_SERVER['REQUEST_METHOD'] == "POST" && !empty($add) && !empty($client) && !empty($_FILES["ufile"]["tmp_name"])){ 

$file_name = $_FILES["ufile"]["name"]; 
$file_temp_name = $_FILES["ufile"]["tmp_name"];
$info   = getimagesize($file_temp_name);
$file_size = $_FILES["ufile"]["size"];
$file_error_message = $_FILES["ufile"]["error"];
$file_name_2_array = explode(".", $file_name);
$file_extension = end($file_name_2_array);

if (!$file_temp_name) {
    echo "<div class=\"not-success\">ERROR: Please browse for a file before clicking the upload button.</div><button class=\"btn gen-btn\"  onclick=\"javascript:location.reload();\"><i class=\"fa fa-refresh\"></i> Try Again</button>";
    exit();
} 
else if (!preg_match("/.(pdf|PDF)$/i", $file_name) ) {
     echo "<div class=\"not-success\">ERROR: Your image was not .pdf</div><button class=\"btn gen-btn\"  onclick=\"javascript:location.reload();\"><i class=\"fa fa-refresh\"></i> Try Again</button>";
     unlink($file_temp_name);
     exit();
}
else if ($file_error_message == 1) {
    echo "<div class=\"not-success\">ERROR: An error occured while processing the file. Try again.</div><button class=\"btn gen-btn\"  onclick=\"javascript:location.reload();\"><i class=\"fa fa-refresh\"></i> Try Again</button>";
    exit();
}

$file_name = "1-{$client}-{$ticket_id}-{$rand_no}.pdf";
$move_file = move_uploaded_file($file_temp_name, "../clients-reports/{$file_name}");
if ($move_file != true) {
    echo "<div class=\"not-success\">ERROR: File not uploaded. Try again.</div><button class=\"btn gen-btn\"  onclick=\"javascript:location.reload();\"><i class=\"fa fa-refresh\"></i> Try Again</button>";
    unlink($file_temp_name);
    exit();
}

$data_array = array(
"client" => "'$client'",
"date_time" => "'$date_time'"
);
$db->insert($data_array, "clients_reports");

$report_id = in_table("id", "clients_reports", "WHERE client = '$client' AND date_time = '$date_time'", "id");

if(file_exists("../clients-reports/{$file_name}")){
if(copy("../clients-reports/{$file_name}", "../clients-reports/{$report_id}report{$rand_no}.pdf")){
unlink("../clients-reports/{$file_name}");
}else{
$db->delete("clients_reports", "id = '$report_id'");
unlink("../clients-reports/{$file_name}");
exit();
}
}

$activity = "Uploaded a new PDF report for $client_name ({$client_email}).";
$audit_data_array = array(
"user_id" => "'$id'",
"name" => "'$username'",
"email" => "'$user_email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$act = $db->insert($audit_data_array, "audit_log");

$to = $client_email;
$subject = "New PDF Report Upload";
$message = "<p>Dear {$client_name},</p>
<p>We are pleased to inform you that a new PDF report has been uploaded for you.</p>
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
////////////// Ends Report Upload //////////////////////////////

if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($add) && (empty($client) || empty($_FILES["ufile"]["tmp_name"]))){
echo "<div class='not-success'>Not submitted! All * the fields are required.</div>";
}

//////////====================Download Report==========//////
if(check_admin("manage_clients_reports") == 1 && !empty($download) && !empty($type)){
$ext = ($type == 2)?".xls*":".pdf";
$file_name = glob("../clients-reports/{$download}report*{$ext}");
$file_name = (file_exists($file_name[0]))?$file_name[0]:"";
if(!empty($file_name)){
echo "<iframe src=\"{$admin}download-report?document={$file_name}\" style=\"width:1px; height:1px;\"></iframe>";
}
}

////////////////////////////////////////////////////******************************//////////////

$search_type = search_option("search_type");
$search_client = search_option("search_client");
$reference_code = search_option("reference_code");
$search_start_date = search_option("search_start_date");
$search_end_date = search_option("search_end_date");
$no_of_rows = search_option("no_of_rows");

$where = "WHERE id > '0'";
$where .= (!empty($search_client))?" AND client = '$search_client'":"";
$where .= (!empty($search_type))?" AND direction = '{$search_type}'":"";
$where .= (!empty($reference_code))?" AND id = '{$reference_code}'":"";
$where .= (!empty($search_start_date) && !empty($search_end_date))?" AND date_time BETWEEN '{$search_start_date} 00:00:00' AND '{$search_end_date} 23:59:59'":"";
$where .= (!empty($search_start_date) && empty($search_end_date))?" AND date_time >= '{$search_start_date} 00:00:00'":"";
$where .= (empty($search_start_date) && !empty($search_end_date))?" AND date_time <= '{$search_end_date} 23:59:59'":"";

$result = $db->select("clients_reports", $where, "*", "ORDER BY id DESC");
$count = count_rows($result);

$per_view = 20;
$per_view = (!empty($no_of_rows))?$no_of_rows:$per_view;
$page_link = "{$admin}manage-clients-reports?pn=";
$link_suffix = "";
$style_class = "general-link";
page_numbers();

$offset = ($per_view * $pn) - $per_view;

$result = $db->select("clients_reports", "$where", "*", "ORDER BY id DESC", "LIMIT {$offset},{$per_view}");

if(empty($add)||(!empty($add)&&$error==0)){
?>

<div class="page-title">Manage Clients Reports/Requests <a href="<?php echo $admin; ?>manage-clients-reports?add=1&pn=<?php echo $pn; ?>" class="btn gen-btn general-link float-right"><i class="fa fa-upload" aria-hidden="true"></i> New Report</a></div>

<form action="<?php echo $admin; ?>manage-clients-reports" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
<input type="hidden" name="gh" value="1"> 
<div class="search-dates">

<div class="col-md-5">
<label for="search_client">Client</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
<select name="search_client" id="search_client" title="Select a client" class="form-control js-example-basic-single" style="width:100%">
<option value="">**Select a client**</option>
<?php 
$result2 = $db->select("clients_reports", "", "DISTINCT client", "ORDER BY client ASC");
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

<div class="col-md-4">
<label for="search_type">Type</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-file-text" aria-hidden="true"></i></span>
<select name="search_type" id="search_type" title="Select an option" class="form-control js-example-basic-single" style="width:100%">
<option value="">**Select an option**</option>
<option value="1" <?php check_selected("search_type", 1, $search_type); ?>>Incoming Report</option>
<option value="2" <?php check_selected("search_type", 2, $search_type); ?>>Sent Requests</option>
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

<div class="col-md-2">
<label for="no_of_rows">No. of Rows</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-list" aria-hidden="true"></i></span>
<input type="number" name="no_of_rows" id="no_of_rows" class="form-control only-no" placeholder="No. of rows" value="<?php check_inputted("no_of_rows", $per_view); ?>">
</div>
</div>

<div class="col-md-4">
<label for="search_start_date">Start Date</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
<input type="text" name="search_start_date" id="search_start_date" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php check_inputted("search_start_date", $search_start_date); ?>">
</div>
</div>

<div class="col-md-4">
<label for="search_end_date">End Date</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
<input type="text" name="search_end_date" id="search_end_date" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php check_inputted("search_end_date", $search_end_date); ?>">
</div>
</div>

<div class="col-md-2">
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
<th>Ref. Code</th>
<th>Client Name</th>
<th>Client Email</th>
<th>Date Uploaded</th>
<th>Type</th>
<th>PDF</th>
<th>Excel</th>
</tr>
</thead>
<tbody>
<?php
while($row = fetch_data($result)){
$get_id = $row["id"];
$client_id = $row["client"];
$client_name = in_table("name","reg_users","WHERE id = '{$client_id}'","name");
$client_email = in_table("email","reg_users","WHERE id = '{$client_id}'","email");
$direction_type = $row["direction"];
$direction = ($direction_type == 2)?"<div class=\"btn btn-primary\"><i class=\"fa fa-arrow-down\" aria-hidden=\"true\"></i> Request</div>":"<div class=\"btn btn-success\"><i class=\"fa fa-arrow-up\" aria-hidden=\"true\"></i> Report</div>";
$date_time = ($row["date_time"] != "0000-00-00 00:00:00")?min_full_date($row["date_time"]):"";
?>
<tr>
<td><?php echo $get_id; ?></td>
<td><?php echo $client_name; ?></td>
<td><?php echo $client_email; ?></td>
<td><?php echo $date_time; ?></td>
<td><?php echo $direction; ?></td>
<td><a href="<?php echo $admin; ?>manage-clients-reports?download=<?php echo $get_id; ?>&type=1&pn=<?php echo $pn; ?>" class="btn gen-btn general-link" title="Download report <?php echo $get_id; ?>"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Download</a></td>
<td><?php if($direction_type == 2){ ?><a href="<?php echo $admin; ?>manage-clients-reports?download=<?php echo $get_id; ?>&type=2&pn=<?php echo $pn; ?>" class="btn gen-btn general-link" title="Download report <?php echo $get_id; ?>"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Download</a><?php } ?></td>
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
echo "<div class='not-success'>No reports found.</div>";
}

}

//=======================Add New Report==============================//
if(check_admin("manage_clients_reports") == 1 && !empty($add) && $error == 1){ ?>

<style>
<!--
.check-code{
color:#f00;
}
-->
</style>

<div><a href="<?php echo $admin; ?>manage-clients-reports?pn=<?php echo $pn; ?>" class="btn gen-btn general-link float-left"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to clients reports</a></div>

<form action="<?php echo $admin; ?>manage-clients-reports" method="post" class="general-form" id="form-div" runat="server" autocomplete="off" enctype="multipart/form-data">
<div class="body-header">Add a <span>New PDF Report</span></div>    

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
check_selected("client", $client_id, $client); 
echo ">{$client_name} ({$client_email})</option>";
}
}
?>
</select>
</div>
</div>

<div class="col-sm-6">
<label for="ufile"><b>Format:</b> .pdf*</label>
<div class="form-group input-group">
<input type="file" name="ufile" class="form-control" required>
</div>
</div>
                     
<div class="col-sm-12 submit-div">
<button class="btn gen-btn float-right"><i class="fa fa-upload"></i> Save</button>
</div>
</form>

<?php
}
?>

<script src="js/general-form.js"></script>

<?php  if(!isset($_REQUEST["gh"])){?>

</div>
</div>

</div>
<?php require_once("../includes/portal-footer.php"); } ?>