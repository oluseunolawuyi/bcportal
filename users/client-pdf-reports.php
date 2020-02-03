<?php if(!isset($_REQUEST["gh"])){ include_once("../includes/admin-header.php"); 
}else{ 
include_once("../includes/gen-header.php");
} ?>

<?php
if(empty($is_client)){ 
redirect($directory . $admin);
}

$add = nr_input("add");
$download = nr_input("download");
$type = nr_input("type");

$file_name_1 = $file_name_2 = "";

////////////// Request Upload //////////////////////////////
if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($add) && !empty($_FILES["ufile"]["tmp_name"])){ 

$check_file_exists = glob("../clients-reports/1-{$id}-*");
if($check_file_exists){ 
foreach($check_file_exists as $val){ 
unlink($val);
}
}

/////============== PDF File Treatment =============///////
if(!empty($_FILES["ufile"]["tmp_name"][0])){

$file_name = $_FILES["ufile"]["name"][0]; 
$file_temp_name = $_FILES["ufile"]["tmp_name"][0];
$file_size = $_FILES["ufile"]["size"][0];
$file_error_message = $_FILES["ufile"]["error"][0];
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

$file_name_1 = "1-{$id}-{$ticket_id}-{$rand_no}.pdf";
$move_file = move_uploaded_file($file_temp_name, "../clients-reports/{$file_name_1}");
if ($move_file != true) {
    echo "<div class=\"not-success\">ERROR: File not uploaded. Try again.</div><button class=\"btn gen-btn\"  onclick=\"javascript:location.reload();\"><i class=\"fa fa-refresh\"></i> Try Again</button>";
    unlink($file_temp_name);
    exit();
}

}

/////============== Excel File Treatment =============///////

$file_name = $_FILES["ufile"]["name"][1]; 
$file_temp_name = $_FILES["ufile"]["tmp_name"][1];
$file_size = $_FILES["ufile"]["size"][1];
$file_error_message = $_FILES["ufile"]["error"][1];
$file_name_2_array = explode(".", $file_name);
$file_extension = end($file_name_2_array);

if (!$file_temp_name) {
    echo "<div class=\"not-success\">ERROR: Please browse for a file before clicking the upload button.</div><button class=\"btn gen-btn\"  onclick=\"javascript:location.reload();\"><i class=\"fa fa-refresh\"></i> Try Again</button>";
    exit();
} 
else if (!preg_match("/.(xls|XLS|xlsx|XLSX)$/i", $file_name) ) {
     echo "<div class=\"not-success\">ERROR: Your file was not .xls or .xlsx</div><button class=\"btn gen-btn\"  onclick=\"javascript:location.reload();\"><i class=\"fa fa-refresh\"></i> Try Again</button>";
     unlink($file_temp_name);
     exit();
}
else if ($file_error_message == 1) {
    echo "<div class=\"not-success\">ERROR: An error occured while processing the file. Try again.</div><button class=\"btn gen-btn\"  onclick=\"javascript:location.reload();\"><i class=\"fa fa-refresh\"></i> Try Again</button>";
    exit();
}

$file_name_2 = "1-{$id}-{$ticket_id}-{$rand_no}.{$file_extension}";
$move_file = move_uploaded_file($file_temp_name, "../clients-reports/{$file_name_2}");
if ($move_file != true) {
    echo "<div class=\"not-success\">ERROR: File not uploaded. Try again.</div><button class=\"btn gen-btn\"  onclick=\"javascript:location.reload();\"><i class=\"fa fa-refresh\"></i> Try Again</button>";
    unlink($file_temp_name);
    exit();
}

$data_array = array(
"client" => "'$id'",
"direction" => "'2'",
"date_time" => "'$date_time'"
);
$db->insert($data_array, "clients_reports");

$report_id = in_table("id", "clients_reports", "WHERE client = '$id' AND date_time = '$date_time'", "id");

if(file_exists("../clients-reports/{$file_name_1}") || file_exists("../clients-reports/{$file_name_2}")){
if(copy("../clients-reports/{$file_name_2}", "../clients-reports/{$report_id}report{$rand_no}.{$file_extension}")){
unlink("../clients-reports/{$file_name_2}");

if(!empty($_FILES["ufile"]["tmp_name"][0])){
copy("../clients-reports/{$file_name_1}", "../clients-reports/{$report_id}report{$rand_no}.pdf");
unlink("../clients-reports/{$file_name_1}");
}

}else{
$db->delete("clients_reports", "id = '$report_id'");
unlink("../clients-reports/{$file_name_1}");
unlink("../clients-reports/{$file_name_2}");
exit();
}
}

$activity = "Uploaded a new request in PDF and Excel formats.";
$audit_data_array = array(
"user_id" => "'$id'",
"name" => "'$username'",
"email" => "'$user_email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$act = $db->insert($audit_data_array, "audit_log");

$to = $gen_email;
$subject = "New PDF and Excel Requests Upload";
$message = "<p>Dear {$gen_name},</p>
<p>This is to notify you that a new request has been uploaded in PDF and Excel formats.</p>
<p>Kindly check the Manage Clients Reports/Requests page for processing.</p>";

$admin_data_array = array(
"ticket_id" => "'$ticket_id'",
"sender_name" => "'$user_name'",
"sender_email" => "'$user_email'",
"recipient_name" => "'$gen_name'",
"recipient_email" => "'$gen_email'",
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

echo "<div class='success'>Request successfully uploaded.</div>";

}
////////////// Ends Report Upload //////////////////////////////

if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($add) && empty($_FILES["ufile"]["tmp_name"])){
echo "<div class='not-success'>Not submitted! All * the fields are required.</div>";
}

//////////====================Download Report==========//////
if(!empty($download) && !empty($type)){
$ext = ($type == 2)?".xls*":".pdf";
$file_name = glob("../clients-reports/{$download}report*{$ext}");
$file_name = (file_exists($file_name[0]))?$file_name[0]:"";
if(!empty($file_name)){
echo "<iframe src=\"{$admin}download-report?document={$file_name}\" style=\"width:1px; height:1px;\"></iframe>";
}
}

////////////////////////////////////////////////////******************************//////////////

$search_type = search_option("search_type");
$reference_code = search_option("reference_code");
$search_start_date = search_option("search_start_date");
$search_end_date = search_option("search_end_date");
$no_of_rows = search_option("no_of_rows");

$where = "WHERE client = '$id'";
$where .= (!empty($search_type))?" AND direction = '{$search_type}'":"";
$where .= (!empty($reference_code))?" AND id = '{$reference_code}'":"";
$where .= (!empty($search_start_date) && !empty($search_end_date))?" AND date_time BETWEEN '{$search_start_date} 00:00:00' AND '{$search_end_date} 23:59:59'":"";
$where .= (!empty($search_start_date) && empty($search_end_date))?" AND date_time >= '{$search_start_date} 00:00:00'":"";
$where .= (empty($search_start_date) && !empty($search_end_date))?" AND date_time <= '{$search_end_date} 23:59:59'":"";

$result = $db->select("clients_reports", "$where", "*", "ORDER BY id DESC");
$count = count_rows($result);

$per_view = 20;
$per_view = (!empty($no_of_rows))?$no_of_rows:$per_view;
$page_link = "{$admin}client-pdf-reports?pn=";
$link_suffix = "";
$style_class = "general-link";
page_numbers();

$offset = ($per_view * $pn) - $per_view;

$result = $db->select("clients_reports", "$where", "*", "ORDER BY id DESC", "LIMIT {$offset},{$per_view}");

if(empty($add)||(!empty($add)&&$error==0)){
?>

<div class="page-title">Manage Reports/Requests <a href="<?php echo $admin; ?>client-pdf-reports?add=1&pn=<?php echo $pn; ?>" class="btn gen-btn general-link float-right"><i class="fa fa-upload" aria-hidden="true"></i> New Request</a><a href="format/background-checks-request-template.xlsx" class="btn gen-btn float-right" style="margin-right:5px;"><i class="fa fa-download" aria-hidden="true"></i> Download Format</a></div>

<form action="<?php echo $admin; ?>client-pdf-reports" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
<input type="hidden" name="gh" value="1"> 
<div class="search-dates">

<div class="col-md-5">
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

<div class="col-md-4">
<label for="reference_code">Ref. Code</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-code" aria-hidden="true"></i></span>
<input type="text" name="reference_code" id="reference_code" class="form-control only-no" placeholder="E.g. 15" value="<?php check_inputted("reference_code", $reference_code); ?>">
</div>
</div>

<div class="col-md-3">
<label for="no_of_rows">No. of Rows</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-list" aria-hidden="true"></i></span>
<input type="number" name="no_of_rows" id="no_of_rows" class="form-control only-no" placeholder="No. of rows" value="<?php check_inputted("no_of_rows", $per_view); ?>">
</div>
</div>

<div class="col-md-5">
<label for="search_start_date">Start Date</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
<input type="text" name="search_start_date" id="search_start_date" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php check_inputted("search_start_date", $search_start_date); ?>">
</div>
</div>

<div class="col-md-5">
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
$direction_type = $row["direction"];
$direction = ($direction_type == 2)?"<div class=\"btn btn-success\"><i class=\"fa fa-arrow-up\" aria-hidden=\"true\"></i> Request</div>":"<div class=\"btn btn-primary\"><i class=\"fa fa-arrow-down\" aria-hidden=\"true\"></i> Report</div>";
$date_time = ($row["date_time"] != "0000-00-00 00:00:00")?min_full_date($row["date_time"]):"";
?>
<tr>
<td><?php echo $get_id; ?></td>
<td><?php echo $date_time; ?></td>
<td><?php echo $direction; ?></td>
<td><?php if(glob("../clients-reports/{$get_id}report*.pdf")){ ?><a href="<?php echo $admin; ?>client-pdf-reports?download=<?php echo $get_id; ?>&type=1&pn=<?php echo $pn; ?>" class="btn gen-btn general-link" title="Download report <?php echo $get_id; ?>"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Download</a><?php } ?></td>
<td><?php if($direction_type == 2){ ?><a href="<?php echo $admin; ?>client-pdf-reports?download=<?php echo $get_id; ?>&type=2&pn=<?php echo $pn; ?>" class="btn gen-btn general-link" title="Download report <?php echo $get_id; ?>"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Download</a><?php } ?></td>
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
if(!empty($add) && $error == 1){ ?>

<style>
<!--
.check-code{
color:#f00;
}
-->
</style>

<div><a href="<?php echo $admin; ?>client-pdf-reports?pn=<?php echo $pn; ?>" class="btn gen-btn general-link float-left"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to reports/requests</a></div>

<form action="<?php echo $admin; ?>client-pdf-reports" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
<div class="body-header">Add a <span>New Request</span></div>    

<div class="required">All * fields are required.</div>

<input type="hidden" name="gh" value="1">
<input type="hidden" name="add" value="1">
<input type="hidden" name="pn" value="<?php echo $pn; ?>">

<div class="col-sm-6">
<label for="ufile"><b>Format:</b> .pdf</label>
<div class="form-group input-group">
<input type="file" name="ufile[]" class="form-control">
</div>
</div>

<div class="col-sm-6">
<label for="ufile"><b>Format:</b> .xls or .xlsx*</label>
<div class="form-group input-group">
<input type="file" name="ufile[]" class="form-control" required>
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