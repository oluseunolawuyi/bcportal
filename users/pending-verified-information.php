<?php if(!isset($_REQUEST["gh"])){ include_once("../includes/admin-header.php"); 
}else{ 
include_once("../includes/gen-header.php");
} ?>

<?php
check_admin("");

$pn = nr_input("pn");
$view = nr_input("view");
$edit = nr_input("edit");

$allow_check = np_input("allow_check");

$investigation_officer = np_input("investigation_officer");
$prev_investigation_officer = in_table("investigation_officer","bc_sub_reports","WHERE id = '{$edit}'","investigation_officer");
$assigned_agent = np_input("assigned_agent");
$verification_type = tp_input("verification_type");
$education = tp_input("education");
$source = tp_input("source");
$comment = tp_input("comment");
$end_date = tp_input("end_date");
$date_received_from_agent = tp_input("date_received_from_agent");
$status = tp_input("status");

//////////======================Generate Report==========================///////////
$extract = np_input("extract");
$extract_verification_type = tp_input("extract_verification_type");
$extract_start_post_date = tp_input("extract_start_post_date");
$extract_end_post_date = tp_input("extract_end_post_date");
$extract_view_mine = tp_input("extract_view_mine");

if(check_admin("download_bc_reports") == 1 && $_SERVER["REQUEST_METHOD"] == "POST" && !empty($extract)){

$table_title = "ALL CLIENTS";

$where = "WHERE id > '0' AND NOT(status = 'COMPLETED')";
$where .= (!empty($extract_verification_type))?" AND verification_type = '$extract_verification_type'":"";
$where .= (!empty($extract_start_post_date))?" AND date_time >= '{$extract_start_post_date} 00:00:00'":"";
$where .= (!empty($extract_end_post_date))?" AND date_time <= '{$extract_end_post_date} 23:59:59'":"";
$where .= (!empty($extract_view_mine))?" AND investigation_officer = '$extract_view_mine'":"";

///======================== Prepare Report ============//////////

set_include_path( get_include_path().PATH_SEPARATOR."..");
include_once("../includes/xlsxwriter.class.php");
$writer = new XLSXWriter();
$styles1 = array( 'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center');
$styles2 = array( 'font'=>'Arial','font-style'=>'bold','font-size'=>10,'halign'=>'left' );
$styles3 = array( 'font'=>'Arial','font-size'=>10,'halign'=>'left');
$file_name = "{$id}-report.xlsx";
$sheet1 = "Report";
$rows = "";
$rows[] = array("BACKGROUND CHECKS REPORT FOR " . $table_title);
$rows[] = array("BATCH NO.", "CANDIDATE NAMES", "VERIFICATION TYPE", "EDUCATION", "SOURCE", "COMMENT", "DATE RECEIVED", "EXPECTED COMPLETION DATE", "COMPLETION DATE", "STATUS");

$result = $db->select("bc_sub_reports", $where, "*", "ORDER BY verification_order_id ASC");
if(count_rows($result) > 0){
while($row = fetch_data($result)){

$sub_bc_report_id = decode_data($row["bc_report_id"]);
$sub_batch = in_table("batch","bc_reports","WHERE id = '{$sub_bc_report_id}'","batch");
$sub_subject = in_table("subject","bc_reports","WHERE id = '{$sub_bc_report_id}'","subject");

$sub_verification_type = decode_data($row["verification_type"]);
$sub_education = decode_data($row["education"]);
$sub_source = decode_data($row["source"]);
$sub_comment = decode_data($row["comment"]);
$sub_start_date = ($row["start_date"] != "0000-00-00")?decode_data($row["start_date"]):"";
$sub_tat = ($row["tat"] != "0000-00-00")?decode_data($row["tat"]):"";
$sub_end_date = ($row["end_date"] != "0000-00-00")?decode_data($row["end_date"]):"";
$sub_status	 = $row["status"];

$datetime1 = new DateTime($row["tat"]);
$datetime2 = new DateTime($date);
$difference = $datetime1->diff($datetime2);
if($difference->days >= 90 && $sub_status != "COMPLETED"){
$sub_status = "CLOSED OUT";
}

$rows[] = array($sub_batch, $sub_subject, $sub_verification_type, $sub_education, $sub_source, $sub_comment, $sub_start_date, $sub_tat, $sub_end_date, $sub_status);
}
}

$c = 0;
foreach($rows as $row){
if($c == 0){
$writer->writeSheetRow($sheet1, $row, $styles1);
}else if($c == 1){
$writer->writeSheetRow($sheet1, $row, $styles2);
}else{
$writer->writeSheetRow($sheet1, $row, $styles3);
}
$c++;
}
$writer->markMergedCell($sheet1, $start_row=0, $start_col=0, $end_row=0, $end_col=9, $styles1);
$writer->writeToFile("../reports/{$file_name}");

$activity = "Downloaded Background Checks report.";
$audit_data_array = array(
"user_id" => "'$id'",
"name" => "'$username'",
"email" => "'$user_email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$db->insert($audit_data_array, "audit_log");

$_SESSION["msg"] = $file_name;
redirect("{$directory}{$admin}pending-verified-information");
////====================================================////////////

}
///////=====================Ends Generate Report======================/////////

////////////// Add or Update Sub Report //////////////////////////////
if($_SERVER["REQUEST_METHOD"] == "POST" && !empty($edit) && !empty($investigation_officer) && !empty($verification_type) && !empty($status) && ($prev_investigation_officer == $id || !empty($is_super_admin))){

/////////////////////////***************************************************************************************************************
$prev_investigation_officer = in_table("investigation_officer","bc_sub_reports","WHERE id = '{$edit}'","investigation_officer");
$execution_start_date = in_table("start_date","bc_sub_reports","WHERE id = '{$edit}'","start_date");
$execution_start_date = ($prev_investigation_officer != $investigation_officer)?$date:$execution_start_date;
$prev_assigned_agent = in_table("assigned_agent","bc_sub_reports","WHERE id = '{$edit}'","assigned_agent");
$prev_agent_start_date = in_table("date_sent_to_agent","bc_sub_reports","WHERE id = '{$edit}'","date_sent_to_agent");
$agent_start_date = (!empty($assigned_agent) && ($prev_assigned_agent != $assigned_agent || $prev_agent_start_date == "0000-00-00"))?$date:$prev_agent_start_date;
$agent_start_date = (empty($assigned_agent))?"0000-00-00":$agent_start_date;
////////////////////*********************------------------------------------------------------------------------------------------------

$data_array = array(
"verification_type" => $verification_type,
"education" => $education,
"source" => $source,
"comment" => $comment,
"investigation_officer" => $investigation_officer,
"end_date" => $end_date,
"assigned_agent" => $assigned_agent,
"date_sent_to_agent" => $agent_start_date,
"date_received_from_agent" => $date_received_from_agent,
"status" => $status,
"last_update" => $date_time
);

$act = $db->update($data_array, "bc_sub_reports", "id = '$edit'");

///////////===========Insert Report Status==========//////////
$report_id = in_table("bc_report_id","bc_sub_reports","WHERE id = '{$edit}'","bc_report_id");
$client = in_table("client","bc_reports","WHERE id = '{$report_id}'","client");
$subject = in_table("subject","bc_reports","WHERE id = '{$report_id}'","subject");
$batch = in_table("batch","bc_reports","WHERE id = '{$report_id}'","batch");
$investigation_officer_name = in_table("name","reg_users","WHERE id = '{$investigation_officer}'","name");
$investigation_officer_email = in_table("email","reg_users","WHERE id = '{$investigation_officer}'","email");
$assigned_agent_name = in_table("name","reg_users","WHERE id = '{$assigned_agent}'","name");
$assigned_agent_email = in_table("email","reg_users","WHERE id = '{$assigned_agent}'","email");

$used_status = "";
$used_status .= (!empty($investigation_officer))?"Assigned to an Investigation Officer - {$investigation_officer_name} ({$investigation_officer_email}) on " . min_sub_date($execution_start_date) . ",":"";
$used_status .= (!empty($assigned_agent) && !empty($agent_start_date))?"Assigned to an Agent - {$assigned_agent_name} ({$assigned_agent_email}) on " . min_sub_date($agent_start_date) . ",":"";
$used_status .= (!empty($agent_start_date) && !empty($assigned_agent))?"Sent to Agent on " . min_sub_date($agent_start_date) . ",":"";
$used_status .= (!empty($date_received_from_agent) && !empty($assigned_agent))?"Received from Agent on " . min_sub_date($date_received_from_agent) . ",":"";
$used_status .= ($status == "COMPLETED")?"COMPLETED,":"";

$report_data_array = array(
"reference_code" => "'$report_id'",
"client" => "'$client'",
"subject" => "'$subject'",
"verification_type" => "'$verification_type'",
"batch" => "'$batch'",
"status" => "'$used_status'",
"updated_by" => "'$id'",
"date_time" => "'$date_time'"
);
$db->insert($report_data_array, "bc_reports_log");
///////////===========Ends Insert Report Status==========//////////

if($act){

///////========Update Main Table=========/////////////////
$det_start_date = in_table("start_date","bc_sub_reports","WHERE bc_report_id = '{$report_id}' ORDER BY start_date ASC","start_date");
$det_end_date = in_table("end_date","bc_sub_reports","WHERE bc_report_id = '{$report_id}' ORDER BY end_date DESC","end_date");
$det_tat= in_table("tat","bc_sub_reports","WHERE bc_report_id = '{$report_id}' ORDER BY tat ASC","tat");
$det_status = in_table("COUNT(id) AS Total","bc_sub_reports","WHERE bc_report_id = '{$report_id}' AND status = 'PENDING'","Total");
$used_status = ($det_status > 0)?"PENDING":"COMPLETED";

$data_array = array(
"start_date" => $det_start_date,
"end_date" => $det_end_date,
"tat" => $det_tat,
"status" => $used_status,
"last_update" => $date_time
);
$db->update($data_array, "bc_reports", "id = '$report_id'");
//////=====Ends Update Main Table=========//////

$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$client_email = in_table("email","reg_users","WHERE id = '{$client}'","email");
$report_subject = in_table("subject","bc_reports","WHERE id = '$report_id'","subject");

$activity = "Updated a BC verified information for {$client_name} ({$client_email}) with the subject: {$subject}.";
$audit_data_array = array(
"user_id" => "'$id'",
"name" => "'$username'",
"email" => "'$user_email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$db->insert($audit_data_array, "audit_log");

$error = 0;

echo "<div class='success'>BC verification information successfully saved.</div>";
}else{
echo "<div class='not-success'>Unable to save verification information.</div>";
}

}


if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($edit) && (empty($investigation_officer) || empty($verification_type) || empty($status))){
echo "<div class='not-success'>Not submitted! All the * fields are required.</div>";
}


////////////////////////////////////////////////////******************************//////////////
 
$search_verification_type = search_option("search_verification_type");
$no_of_rows = search_option("no_of_rows");
$view_mine = search_option("view_mine", $allow_check);
$search_start_post_date = search_option("search_start_post_date");
$search_end_post_date = search_option("search_end_post_date");

$where = "WHERE status = 'PENDING'";
$where .= (!empty($search_verification_type))?" AND verification_type = '{$search_verification_type}'":"";
$where .= (!empty($view_mine) && $view_mine == 1)?" AND investigation_officer = '{$id}'":"";
$where .= (!empty($search_start_post_date) && !empty($search_end_post_date))?" AND date_time BETWEEN '{$search_start_post_date} 00:00:00' AND '{$search_end_post_date} 23:59:59'":"";
$where .= (!empty($search_start_post_date) && empty($search_end_post_date))?" AND date_time >= '{$search_start_post_date} 00:00:00'":"";
$where .= (empty($search_start_post_date) && !empty($search_end_post_date))?" AND date_time <= '{$search_end_post_date} 23:59:59'":"";

$result = $db->select("bc_sub_reports", "$where", "*", "ORDER BY tat ASC");

$per_view = 20;
$per_view = (!empty($no_of_rows))?$no_of_rows:$per_view;
$page_link = "{$admin}pending-verified-information?pn=";
$link_suffix = "";
$style_class = "general-link";
page_numbers();

if(isset($_SESSION["msg"]) && !empty($_SESSION["msg"]) && empty($extract)){
echo "<iframe src=\"{$admin}download-report?file=" . $_SESSION["msg"] . "\" style=\"width:1px; height:1px;\"></iframe>";
unset($_SESSION["msg"]);
}

if(empty($view) && empty($edit)){
?>

<div class="page-title">Pending Verified Information</div>

<form action="<?php echo $admin; ?>pending-verified-information" class="general-form" name="search_form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
<input type="hidden" name="gh" value="1"> 
<input type="hidden" name="allow_check" value="1"> 
<div class="search-dates">

<div class="col-md-4">
<label for="search_verification_type">Verification Type</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-file-text" aria-hidden="true"></i></span>
<select name="search_verification_type" id="search_verification_type" title="Select a verification type" class="form-control js-example-basic-single" style="width:100%">
<option value="">**Verification type**</option>
<?php 
$result2 = $db->select("bc_sub_reports", "", "DISTINCT verification_type", "ORDER BY verification_type ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$verification_type = $row2["verification_type"];
echo "<option value='{$verification_type}'";
check_selected("search_verification_type", $verification_type, $search_verification_type);
echo ">{$verification_type}</option>";
}
}
?>
</select>
</div>
</div>

<div class="col-md-3">
<label for="no_of_rows">No. of Rows</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-list" aria-hidden="true"></i></span>
<input type="text" name="no_of_rows" id="no_of_rows" class="form-control only-no" placeholder="E.g. 10" value="<?php check_inputted("no_of_rows", $per_view); ?>">
</div>
</div>

<div class="col-md-5">
<label for="search_start_post_date">Start Post Date</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
<input type="text" name="search_start_post_date" id="search_start_post_date" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php check_inputted("search_start_post_date", $search_start_post_date); ?>">
</div>
</div>

<div class="col-md-5">
<label for="search_end_post_date">End Post Date</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
<input type="text" name="search_end_post_date" id="search_end_post_date" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php check_inputted("search_end_post_date", $search_end_post_date); ?>">
</div>
</div>

<div class="col-md-3">
<br />
<div class="form-group input-group">
<span class="input-group-addon"><input type="checkbox" name="view_mine" id="view_mine" value="1" <?php echo (!empty($view_mine))?"checked":""; ?>></span>
<label for="view_mine"> &nbsp;&nbsp; View assigned to me</label>
</div>
</div>

<div class="col-md-2 col-xs-6">
<br />
<button type="submit" class="btn gen-btn"><i class="fa fa-search"></i> Search</button>
</div>

<?php if(check_admin("download_bc_reports") == 1){ ?>
<div class="col-md-2 col-xs-6">
<br />
<a class="btn gen-btn download-report"><i class="fa fa-download"></i> Download</a>
</div>
<?php } ?>

</div>
</form>

<form action="<?php echo $admin; ?>pending-verified-information" name="extract_form" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
<input type="hidden" name="extract" value="1"> 
<input type="hidden" name="extract_verification_type" value=""> 
<input type="hidden" name="extract_start_post_date" value=""> 
<input type="hidden" name="extract_end_post_date" value=""> 
<input type="hidden" name="extract_view_mine" value=""> 
</form>

<?php
$d = 0;

$offset = ($per_view * $pn) - $per_view;

$result = $db->select("bc_sub_reports", "$where", "*", "ORDER BY tat ASC", "LIMIT {$offset},{$per_view}");

if(count_rows($result) > 0){
?>
<div style="overflow-x:auto;">
<table class="table table-striped table-hover">
<thead>
<tr class="gen-title">
<th>Date Posted</th>
<th style="width:30px;">#ID</th>
<th style="width:30px;">Ref. Code</th>
<th>Client</th>
<th style="width:50px;">Batch No.</th>
<th>Subject</th>
<th>Period</th>
<th>Verified Info.</th>
<th>Days Remaining</th>
<th style="width:70px;">Agent Assigned?</th>
<th style="width:70px;">View</th>
<th style="width:70px;">Action</th>
</tr>
</thead>
<tbody>
<?php
while($row = fetch_data($result)){
$report_id = $row["id"];
$bc_report_id = $row["bc_report_id"];
$verification_type = $row["verification_type"];
$investigation_officer = $row["investigation_officer"];
$client = in_table("client","bc_reports","WHERE id = '{$bc_report_id}'","client");
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$client_email = in_table("email","reg_users","WHERE id = '{$client}'","email");
$client_email = break_long($client_email, "", "");
$batch = in_table("batch","bc_reports","WHERE id = '{$bc_report_id}'","batch");
$subject = in_table("subject","bc_reports","WHERE id = '{$bc_report_id}'","subject");
$start_date = min_sub_date($row["start_date"]);
$tat = min_sub_date($row["tat"]);
$status = $row["status"];
$days_remaining = days_remaining($row["tat"],"1",$status);
$assigned_agent = $row["assigned_agent"];
$assigned_agent = (!empty($assigned_agent))?"<span style=\"color:#2387a0; font-weight:bold;\">Yes</span>":"<span style=\"color:#b20; font-weight:bold;\">No</span>";
$date_posted = min_sub_date($row["date_time"]);
?>
<tr>
<td><?php echo $date_posted; ?></td>
<td><?php echo $report_id; ?></td>
<td><?php echo $bc_report_id; ?></td>
<td><?php echo "{$client_name}<br>({$client_email})"; ?></td>
<td><?php echo formatQty($batch); ?></td>
<td><?php echo $subject; ?></td>
<td><?php echo $start_date . " - " . $tat; ?></td>
<td><?php echo $verification_type; ?></td>
<td><?php echo $days_remaining; ?></td>
<td><?php echo $assigned_agent; ?></td>
<td><a href="<?php echo $admin; ?>pending-verified-information?view=<?php echo $report_id; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link" title="View BC verified information report #<?php echo $report_id; ?>"><i class="fa fa-eye" aria-hidden="true"></i> View</a></td>
<td><?php if($investigation_officer == $id || !empty($is_super_admin)){ ?><a href="<?php echo $admin; ?>pending-verified-information?edit=<?php echo $report_id; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link" title="Edit BC verified information report #<?php echo $report_id; ?>"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a><?php } ?></td>
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
echo "<div class='not-success'>No pending BC verified information found at the moment.</div>";
}

}


/////============View Report=============////
if(!empty($view)){
$result = $db->select("bc_sub_reports", "WHERE id='$view'", "*");

if(count_rows($result) == 1){
$row = fetch_data($result);
$bc_report_id = $row["bc_report_id"];
$client = in_table("client","bc_reports","WHERE id = '{$bc_report_id}'","client");
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$client_email = in_table("email","reg_users","WHERE id = '{$client}'","email");
$batch = in_table("batch","bc_reports","WHERE id = '{$bc_report_id}'","batch");
$subject = in_table("subject","bc_reports","WHERE id = '{$bc_report_id}'","subject");
$verification_type = $row["verification_type"];
$education = $row["education"];
$source = $row["source"];
$comment = $row["comment"];
$start_date = ($row["start_date"] != "0000-00-00")?min_sub_date($row["start_date"]):"";
$tat = ($row["tat"] != "0000-00-00")?min_sub_date($row["tat"]):"";
$investigation_officer = $row["investigation_officer"];
$investigation_officer_name = (!empty($investigation_officer))?in_table("name","reg_users","WHERE id = '{$investigation_officer}'","name"):"";
$investigation_officer_email = (!empty($investigation_officer))?in_table("email","reg_users","WHERE id = '{$investigation_officer}'","email"):"";
$investigation_officer_data = (!empty($investigation_officer))?"{$investigation_officer_name} ({$investigation_officer_email})":"";
$assigned_agent = $row["assigned_agent"];
$assigned_agent_name = (!empty($assigned_agent))?in_table("name","reg_users","WHERE id = '{$assigned_agent}'","name"):"";
$assigned_agent_email = (!empty($assigned_agent))?in_table("email","reg_users","WHERE id = '{$assigned_agent}'","email"):"";
$assigned_agent = (!empty($assigned_agent))?"{$assigned_agent_name} ({$assigned_agent_email})":"";
$date_sent_to_agent = ($row["date_sent_to_agent"] != "0000-00-00")?min_sub_date($row["date_sent_to_agent"]):"";
$date_received_from_agent = ($row["date_received_from_agent"] != "0000-00-00")?min_sub_date($row["date_received_from_agent"]):"";
$status = $row["status"];
$status_div = ($status == "COMPLETED")?"primary":"danger";
$days_remaining = days_remaining($row["tat"],"",$status);
$report_date_time = full_date($row["date_time"]);
?>

<div class="reply-content-wrapper ">

<div class="back"><a href="<?php echo $admin; ?>pending-verified-information?pn=<?php echo $pn; ?>" class="btn gen-btn general-link"><i class="fa fa-arrow-left"></i> Back to pending verified information</a></div>

<div class="view-wrapper ">

<div class="view-header ">
<div class="header-img"><img src="images/post.jpg" ></div>
<div class="header-content">
<div class="view-title">Subject:  <?php echo $subject; ?></div>
<div class="view-title-details">Added on <?php echo $report_date_time; ?></div>
</div>
</div>

<div class="view-content">
<div class="align-right"><button class="btn btn-default"><b>Turn-Around Time:</b> <?php echo $tat; ?></button> &nbsp;&nbsp; <button class="btn btn-default"><b>Days Remaining:</b> <?php echo $days_remaining; ?></button></div>
<b>Client:</b> <?php echo "{$client_name} ({$client_email})"; ?> <br /> 
<b>Batch:</b> <?php echo $batch; ?> <br /> <br /> 
<b>Status:</b> <?php echo (!empty($status))?"<div class=\"btn btn-{$status_div}\">{$status}</div>":"<div class=\"btn btn-danger\">{$status}</div>"; ?>

<div class="body-header"><i class="fa fa-tag" aria-hidden="true"></i> Verified Information</div>

<table class="table table-striped table-hover">

<thead><tr class="gen-title"><th>Verification Type</th><th>Education</th><th>Source</th><th>Comment</th></tr></thead>
<tbody><tr><th><?php echo $verification_type; ?></th><td><?php echo $education; ?></td><td><?php echo $source; ?></td><td><?php echo $comment; ?></td></tr></tbody>

<thead><tr class="gen-title"><th>Investigation Officer</th><th>Execution Period</th><th>Assigned Agent</th><th>Agent Execution Period</th></tr></thead>
<tbody><tr><td><?php echo $investigation_officer_data; ?></td><td><?php echo "{$start_date} - {$tat}"; ?></td><td><?php echo $assigned_agent; ?></td><td><?php echo "{$date_sent_to_agent} - {$date_received_from_agent}"; ?></td></tr></tbody>

</table>

<div><?php if($investigation_officer == $id || !empty($is_super_admin)){ ?><a href="<?php echo $admin; ?>pending-verified-information?edit=<?php echo $view; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link float-right" title="Edit CV report #<?php echo $view; ?>"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a><?php } ?></div>

</div>

</div>
</div>

<?php
}else{
echo "<div class='not-success'>This BC report does not exist.</div>";
}
}



////==============Edit Report=============//////
if(!empty($edit) && $error == 1){

$result = $db->select("bc_sub_reports", "WHERE id='$edit'", "*");

if(count_rows($result) == 1){
$row = fetch_data($result);
$investigation_officer = $row["investigation_officer"];
$assigned_agent = $row["assigned_agent"];
$verification_type = $row["verification_type"];
$education = $row["education"];
$source = $row["source"];
$comment = $row["comment"];
$end_date = ($row["end_date"] != "0000-00-00")?$row["end_date"]:"";
$date_received_from_agent = ($row["date_received_from_agent"] != "0000-00-00")?$row["date_received_from_agent"]:"";
$status = $row["status"];
?>

<div class="back">
<a href="<?php echo $admin; ?>pending-verified-information?pn=<?php echo $pn; ?>" class="btn gen-btn general-link"><i class="fa fa-arrow-left"></i> Back to Pending Verified Information</a>
</div>

<div class="page-title">Edit Information</div>

<form action="<?php echo $admin; ?>pending-verified-information" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data" style="border:1px solid #eee;">
<input type="hidden" name="gh" value="1"> 
<input type="hidden" name="edit" value="<?php echo $edit; ?>"> 
<input type="hidden" name="pn" value="<?php echo $pn; ?>"> 
<input type="hidden" name="view" value="<?php echo $edit; ?>"> 


<?php 
$report_id = in_table("bc_report_id","bc_sub_reports","WHERE id = '$edit'","bc_report_id");
$client = in_table("client","bc_reports","WHERE id = '$report_id'","client");
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$client_email = in_table("email","reg_users","WHERE id = '{$client}'","email");
$report_subject = in_table("subject","bc_reports","WHERE id = '$report_id'","subject");
?>
<div class="sub-report-title col-sm-12 align-center"><b>Subject:</b> <?php echo $report_subject; ?> &nbsp;&nbsp;&nbsp; <b>Client:</b> <?php echo "{$client_name} ({$client_email})"; ?><br /><br /></div>

<table class="table table-striped table-hover sub-cat-table">

<tr>
<th class="gen-title">Verification Type*</th>
<th class="gen-title">Education</th>
<th class="gen-title">Source</th>
<th class="gen-title">Comment</th>
</tr>

<tr><td>
<select name="verification_type" id="verification_type" title="Select a verification type" class="form-control" required>
<option value="">**Verification type**</option>
<?php 
$result2 = $db->select("bc_verification_types", "", "DISTINCT *", "ORDER BY type ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$verification_type2 = $row2["type"];
echo "<option value='{$verification_type2}'";
check_selected("verification_type", $verification_type2, $verification_type); 
echo ">{$verification_type2}</option>";
}
}
?>
</select>
</td><td>
<select name="education" id="education" title="Select an education type" class="form-control">
<option value="">**Education**</option>
<?php 
$result2 = $db->select("bc_education_types", "", "DISTINCT *", "ORDER BY type ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$education_type = $row2["type"];
echo "<option value='{$education_type}'";
check_selected("education", $education_type, $education); 
echo ">{$education_type}</option>";
}
}
?>
</select>
</td><td>
<textarea rows="3" name="source" id="source" class="form-control" placeholder="Source"><?php check_inputted("source", $source); ?></textarea>
</td><td>
<textarea rows="3" name="comment" id="comment" class="form-control" placeholder="Comment"><?php check_inputted("comment", $comment); ?></textarea>
</td></tr>

<tr>
<th colspan="3" class="gen-title">Investigation Officer*</th>
<th class="gen-title">Execution End Date</th>
</tr>
<tr><td colspan="3">
<select name="investigation_officer" id="investigation_officer" title="Select an investigation officer" class="form-control" required>
<option value="">**Select an investigation officer**</option>
<?php 
$result2 = $db->select("reg_users", "WHERE admin = '1'", "DISTINCT *", "ORDER BY id ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$admin_id = $row2["id"];
$admin_name = $row2["name"];
$admin_email = $row2["email"];
echo "<option value='{$admin_id}'";
check_selected("investigation_officer", $admin_id, $investigation_officer); 
echo ">{$admin_name} ({$admin_email})</option>";
}
}
?>
</select>
</td><td>
<input type="text" name="end_date" id="end_date" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php echo check_inputted("end_date", $end_date); ?>">
</td></tr>

<tr>
<th colspan="2" class="gen-title">Assigned Agent</th>
<th class="gen-title">Date Received from Agent</th>
<th class="gen-title">Status*</th>
</tr>
<tr><td colspan="2">
<select name="assigned_agent" id="assigned_agent" title="Select an agent" class="form-control">
<option value="">**Select an agent**</option>
<?php 
$result2 = $db->select("reg_users", "WHERE agent = '1'", "DISTINCT *", "ORDER BY id ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$agent_id = $row2["id"];
$agent_name = $row2["name"];
$agent_email = $row2["email"];
echo "<option value='{$agent_id}'";
check_selected("assigned_agent", $agent_id, $assigned_agent); 
echo ">{$agent_name} ({$agent_email})</option>";
}
}
?>
</select>
</td><td>
<input type="text" name="date_received_from_agent" id="date_received_from_agent" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php check_inputted("date_received_from_agent", $date_received_from_agent); ?>">
</td><td>
<select name="status" id="status" title="Select a status" class="form-control" required>
<option value="">**Select a status**</option>
<?php 
$result2 = $db->select("status_types", "", "DISTINCT *", "ORDER BY id ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$sub_type = $row2["type"];
echo "<option value='{$sub_type}'";
check_selected("status", $sub_type, $status); 
echo ">{$sub_type}</option>";
}
}
?>
</select>
</td></tr>
</table>

</table>
                     
<div class="submit-div col-sm-12">
<button class="btn gen-btn float-right" name="update"><i class="fa fa-upload"></i> Save</button>
</div>

</form>

<?php
}

}
?>


<script>
<!--
var conf_text = "report";

$(document).ready(function(){

$(".download-report").click(function(){
document.extract_form.extract_verification_type.value = $("#search_verification_type").val();
document.extract_form.extract_start_post_date.value = document.search_form.search_start_post_date.value;
document.extract_form.extract_end_post_date.value = document.search_form.search_end_post_date.value;
if($("input:checkbox#view_mine").prop("checked")){
document.extract_form.extract_view_mine.value = document.search_form.view_mine.value;
}
document.extract_form.submit();
});

});
//-->
</script>

<script src="js/general-form.js"></script>

<?php if(!isset($_REQUEST["gh"])){ ?>

</div>
</div>

</div>

<?php require_once("../includes/portal-footer.php"); } ?>