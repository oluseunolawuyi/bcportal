<?php if(!isset($_REQUEST["gh"])){ include_once("../includes/admin-header.php"); 
}else{ 
include_once("../includes/gen-header.php");
} ?>

<?php
error_reporting(0);

check_admin("");

$pn = nr_input("pn");
$view = nr_input("view");
$edit = nr_input("edit");
$allow_check = np_input("allow_check");

$report_conclusion = tp_input("report_conclusion");
$conclusion_data = tp_input("conclusion_data");

$investigation_officer = np_input("investigation_officer");
$prev_investigation_officer = in_table("investigation_officer","bc_sub_reports","WHERE id = '{$edit}'","investigation_officer");
$assigned_agent = np_input("assigned_agent");
$completion_date = tp_input("completion_date");
$date_received_from_school = tp_input("date_received_from_school");
$school_letter_date = tp_input("school_letter_date");
$status = tp_input("status");
$verified_status = tp_input("verified_status");
$remark = tp_input("remark");


//////////======================Generate Report==========================///////////
$extract = np_input("extract");
$extract_client = np_input("extract_client");
$extract_client_name = in_table("name","reg_users","WHERE id = '{$extract_client}'","name");
$extract_client_email = in_table("email","reg_users","WHERE id = '{$extract_client}'","email");
$extract_keyword = tp_input("extract_keyword");
$extract_start_post_date = tp_input("extract_start_post_date");
$extract_end_post_date = tp_input("extract_end_post_date");
$extract_view_mine = tp_input("extract_view_mine");

if(check_admin("download_cv_reports") == 1 && $_SERVER["REQUEST_METHOD"] == "POST" && !empty($extract)){

$table_title = (!empty($extract_client))?strtoupper("{$extract_client_name} ({$extract_client_email})"):"ALL CLIENTS";

$where = "WHERE id > '0' AND NOT(status = 'COMPLETED')";
$where .= (!empty($extract_client))?" AND client = '$extract_client'":"";
$where .= (!empty($extract_keyword))?" AND names LIKE '%{$extract_keyword}%'":"";
$where .= (!empty($extract_start_post_date))?" AND date_time >= '{$extract_start_post_date} 00:00:00'":"";
$where .= (!empty($extract_end_post_date))?" AND date_time <= '{$extract_end_post_date} 23:59:59'":"";
$where .= (!empty($extract_view_mine))?" AND investigation_officer	 = '$extract_view_mine'":"";

$result = $db->select("cv_reports", $where, "*", "ORDER BY batch ASC");

if(count_rows($result) > 0){

set_include_path( get_include_path().PATH_SEPARATOR."..");
include_once("../includes/xlsxwriter.class.php");
$writer = new XLSXWriter();
$styles1 = array( 'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center');
$styles2 = array( 'font'=>'Arial','font-style'=>'bold','font-size'=>10,'halign'=>'left' );
$styles3 = array( 'font'=>'Arial','font-size'=>10,'halign'=>'left');
$file_name = "{$id}-report.xlsx";
$sheet1 = "Report";
$rows = "";
$rows[] = array("CERTIFICATE VERIFICATION REPORT FOR " . $table_title);
$rows[] = array("BATCH NO.", "DATE RECEIVED", "EXPECTED COMPLETION DATE", "COMPLETION DATE", "CANDIDATE NAMES", "INSTITUTION", "COURSE", "QUALIFICATION", "GRADE", "SESSION", "MATRIC NO.", "STATUS", "VERIFIED STATUS", "STATUS COMMENT");

while($row = fetch_data($result)){
$extract_id = $row["id"];
$date_received = ($row["date_received"] != "0000-00-00")?$row["date_received"]:"";
$tat = ($row["tat"] != "0000-00-00")?$row["tat"]:"";
$completion_date = ($row["completion_date"] != "0000-00-00")?$row["completion_date"]:"";
$names = decode_data($row["names"]);
$institution = decode_data($row["institution"]);
$course = decode_data($row["course"]);
$qualification = decode_data($row["qualification"]);
$grade = decode_data($row["grade"]);
$session = decode_data($row["session"]);
$matric_number = decode_data($row["matric_number"]);
$batch = $row["batch"];
$status = $row["status"];

$datetime1 = new DateTime($row["tat"]);
$datetime2 = new DateTime($date);
$difference = $datetime1->diff($datetime2);
if($difference->days >= 90 && $status != "COMPLETED"){
$status = "CLOSED OUT";
}

$verified_status = $row["verified_status"];
$status_comment = decode_data($row["status_comment"]);
$rows[] = array($batch, $date_received, $tat, $completion_date, $names, $institution, $course, $qualification, $grade, $session, $matric_number, $status, $verified_status, $status_comment);
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
$writer->markMergedCell($sheet1, $start_row=0, $start_col=0, $end_row=0, $end_col=13, $styles1);
$writer->writeToFile("../reports/{$file_name}");

$activity = "Downloaded Certificate Verification report.";
$audit_data_array = array(
"user_id" => "'$id'",
"name" => "'$username'",
"email" => "'$user_email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$db->insert($audit_data_array, "audit_log");

$_SESSION["msg"] = $file_name;

redirect("{$directory}{$admin}cv-pending-tasks");

}else{
echo "<div class='not-success'>No reports available for the search parameters.</div>";
}

}
///////=====================Ends Generate Report======================/////////

////////////// Add or Update Sub Report //////////////////////////////
if($_SERVER["REQUEST_METHOD"] == "POST" && !empty($edit) && !empty($investigation_officer) && !empty($status) && ($prev_investigation_officer == $id || !empty($is_super_admin))){

/////////////////////////***************************************************************************************************************
$prev_investigation_officer = in_table("investigation_officer","cv_reports","WHERE id = '{$edit}'","investigation_officer");
$date_received = in_table("date_received","cv_reports","WHERE id = '{$edit}'","date_received");
$date_received = ($prev_investigation_officer != $investigation_officer)?$date:$date_received;
$prev_assigned_agent = in_table("assigned_agent","cv_reports","WHERE id = '{$edit}'","assigned_agent");
$prev_date_sent_out = in_table("date_sent_out","cv_reports","WHERE id = '{$edit}'","date_sent_out");
$date_sent_out = (!empty($assigned_agent) && ($prev_assigned_agent != $assigned_agent || $prev_date_sent_out == "0000-00-00"))?$date:$prev_date_sent_out;
$date_sent_out = (empty($assigned_agent))?"0000-00-00":$date_sent_out;
////////////////////*********************------------------------------------------------------------------------------------------------

$data_array = array(
"investigation_officer" => $investigation_officer,
"completion_date" => $completion_date,
"assigned_agent" => $assigned_agent,
"date_sent_out" => $date_sent_out,
"date_received_from_school" => $date_received_from_school,
"school_letter_date" => $school_letter_date,
"status" => $status,
"verified_status" => $verified_status,
"remark" => $remark,
"last_update" => $date_time
);

$act = $db->update($data_array, "cv_reports", "id = '$edit'");

///////////===========Insert Report Status==========//////////
$report_id = $edit;
$client = in_table("client","cv_reports","WHERE id = '{$report_id}'","client");
$names = in_table("names","cv_reports","WHERE id = '{$report_id}'","names");
$institution = in_table("institution","cv_reports","WHERE id = '{$report_id}'","institution");
$batch = in_table("batch","cv_reports","WHERE id = '{$report_id}'","batch");
$investigation_officer_name = in_table("name","reg_users","WHERE id = '{$investigation_officer}'","name");
$investigation_officer_email = in_table("email","reg_users","WHERE id = '{$investigation_officer}'","email");
$assigned_agent_name = in_table("name","reg_users","WHERE id = '{$assigned_agent}'","name");
$assigned_agent_email = in_table("email","reg_users","WHERE id = '{$assigned_agent}'","email");

$used_status = "";
$used_status .= (!empty($investigation_officer))?"Assigned to an Investigation Officer - {$investigation_officer_name} ({$investigation_officer_email}) on " . min_sub_date($date_received) . ",":"";
$used_status .= (!empty($assigned_agent) && !empty($date_sent_out))?"Assigned to an Agent - {$assigned_agent_name} ({$assigned_agent_email}) on " . min_sub_date($date_sent_out) . ",":"";
$used_status .= (!empty($date_sent_out))?"Sent to Agent/School on " . min_sub_date($date_sent_out) . ",":"";
$used_status .= (!empty($date_received_from_school))?"Received from Agent on " . min_sub_date($date_received_from_school) . ",":"";
$used_status .= ($status == "COMPLETED" || !empty($completion_date))?"COMPLETED,":"";

$report_data_array = array(
"reference_code" => "'$report_id'",
"client" => "'$client'",
"names" => "'$names'",
"institution" => "'$institution'",
"batch" => "'$batch'",
"status" => "'$used_status'",
"updated_by" => "'$id'",
"date_time" => "'$date_time'"
);
$db->insert($report_data_array, "cv_reports_log");
///////////===========Ends Insert Report Status==========//////////

if($act){

$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$client_email = in_table("email","reg_users","WHERE id = '{$client}'","email");

$activity = "Updated a CV report for {$client_name} ({$client_email}) with the subject: {$names}.";
$audit_data_array = array(
"user_id" => "'$id'",
"name" => "'$username'",
"email" => "'$user_email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$db->insert($audit_data_array, "audit_log");

$error = 0;

echo "<div class='success'>CV report successfully saved.</div>";
}else{
echo "<div class='not-success'>Unable to save report.</div>";
}

}


if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($edit) && (empty($investigation_officer) || empty($status))){
echo "<div class='not-success'>Not submitted! All the * fields are required.</div>";
}


////////////=============Conclusion Pre-Final Report=====================/////
if(check_admin("edit_cv_reports") == 1 && !empty($report_conclusion) && $report_conclusion == "pre-final-update" && !empty($conclusion_data) && isset($_POST["conclusion_id"])){

$act = "";
$i = 0;
if(is_array($_POST["conclusion_id"])){
foreach($_POST["conclusion_id"] as $value){
$value = testQty($value);
$status_comment = test_input($_POST["status_comment"][$i]);
$assigned_agent = testQty($_POST["assigned_agent"][$i]);
$date_received_from_school = test_input($_POST["date_received_from_school"][$i]);
$school_letter_date = test_input($_POST["school_letter_date"][$i]);
$data_array = array(
"status_comment" => $status_comment,
"assigned_agent" => $assigned_agent,
"date_received_from_school" => $date_received_from_school,
"school_letter_date" => $school_letter_date,
"last_update" => $date_time
);
$act = $db->update($data_array, "cv_reports", "id = '$value'");

$client = in_table("client","cv_reports","WHERE id = '{$value}'","client");
$names = in_table("names","cv_reports","WHERE id = '{$value}'","names");
$institution = in_table("institution","cv_reports","WHERE id = '{$value}'","institution");
$batch = in_table("batch","cv_reports","WHERE id = '{$value}'","batch");

$prev_assigned_agent = in_table("assigned_agent","cv_reports","WHERE id = '{$value}'","assigned_agent");
$prev_date_sent_out = in_table("date_sent_out","cv_reports","WHERE id = '{$value}'","date_sent_out");
$date_sent_out = (!empty($assigned_agent) && ($prev_assigned_agent != $assigned_agent || $prev_date_sent_out == "0000-00-00"))?$date:$prev_date_sent_out;
$date_sent_out = (empty($assigned_agent))?"0000-00-00":$date_sent_out;

$assigned_agent_name = $assigned_agent_email = "";
$assigned_agent_name = in_table("name","reg_users","WHERE id = '{$assigned_agent}'","name");
$assigned_agent_email = in_table("email","reg_users","WHERE id = '{$assigned_agent}'","email");

///////////===========Insert Report Status==========//////////
$used_status = "";
$used_status .= (!empty($assigned_agent) && !empty($date_sent_out))?"Assigned to an Agent - {$assigned_agent_name} ({$assigned_agent_email}) on " . min_sub_date($date_sent_out) . ", ":"";
$used_status .= (!empty($date_received_from_school))?"Received from School on " . min_sub_date($date_received_from_school) . ",":"";

$report_data_array = array(
"reference_code" => "'$value'",
"client" => "'$client'",
"names" => "'$names'",
"institution" => "'$institution'",
"batch" => "'$batch'",
"status" => "'$used_status'",
"updated_by" => "'$id'",
"date_time" => "'$date_time'"
);
$db->insert($report_data_array, "cv_reports_log");
///////////===========Ends Insert Report Status==========//////////

$i++;
}
}

if($act && $i > 0){
$error = 0;
$activity = "Pre-final update of {$i} CV report(s).";
$audit_data_array = array(
"user_id" => "'$id'",
"name" => "'$username'",
"email" => "'$user_email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$db->insert($audit_data_array, "audit_log");
echo "<div class='success'>{$i} CV report(s) successfully pre-finally updated.</div>";
}else{
echo "<div class='not-success'>Error. Unable to pre-finally update CV report(s).</div>";
}

}

////////////=============Conclusion Final Report=====================/////
if(check_admin("edit_cv_reports") == 1 && !empty($report_conclusion) && $report_conclusion == "final-update" && !empty($conclusion_data) && isset($_POST["conclusion_id"])){

$act = "";
$i = 0;
if(is_array($_POST["conclusion_id"])){
foreach($_POST["conclusion_id"] as $value){
$value = testQty($value);
$status_comment = test_input($_POST["status_comment"][$i]);
$completion_date = test_input($_POST["completion_date"][$i]);
$status = test_input($_POST["status"][$i]);
$verified_status = test_input($_POST["verified_status"][$i]);
$data_array = array(
"status_comment" => $status_comment,
"completion_date" => $completion_date,
"status" => $status,
"verified_status" => $verified_status,
"last_update" => $date_time
);
$act = $db->update($data_array, "cv_reports", "id = '$value'");

$client = in_table("client","cv_reports","WHERE id = '{$value}'","client");
$names = in_table("names","cv_reports","WHERE id = '{$value}'","names");
$institution = in_table("institution","cv_reports","WHERE id = '{$value}'","institution");
$batch = in_table("batch","cv_reports","WHERE id = '{$value}'","batch");

///////////===========Insert Report Status==========//////////
$used_status = "";
$used_status .= ($status == "COMPLETED" || !empty($completion_date))?"COMPLETED,":"";

$report_data_array = array(
"reference_code" => "'$value'",
"client" => "'$client'",
"names" => "'$names'",
"institution" => "'$institution'",
"batch" => "'$batch'",
"status" => "'$used_status'",
"updated_by" => "'$id'",
"date_time" => "'$date_time'"
);
$db->insert($report_data_array, "cv_reports_log");
///////////===========Ends Insert Report Status==========//////////

$i++;
}
}

if($act && $i > 0){
$error = 0;
$activity = "Final update of {$i} CV report(s).";
$audit_data_array = array(
"user_id" => "'$id'",
"name" => "'$username'",
"email" => "'$user_email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$db->insert($audit_data_array, "audit_log");
echo "<div class='success'>{$i} CV report(s) successfully finally updated.</div>";
}else{
echo "<div class='not-success'>Error. Unable to finally update CV report(s).</div>";
}

}

////////////////////////////////////////////////////******************************//////////////

$search_client = search_option("search_client");
$keyword = search_option("keyword");
$no_of_rows = search_option("no_of_rows");
$view_mine = search_option("view_mine");
$search_start_post_date = search_option("search_start_post_date");
$search_end_post_date = search_option("search_end_post_date");

$where = "WHERE status = 'PENDING'";
$where .= (!empty($search_client))?" AND client = '{$search_client}'":"";
$where .= (!empty($keyword))?" AND names LIKE '%{$keyword}%'":"";
$where .= (!empty($view_mine) && $view_mine == 1)?" AND investigation_officer = '{$id}'":"";
$where .= (!empty($search_start_post_date) && !empty($search_end_post_date))?" AND date_time BETWEEN '{$search_start_post_date} 00:00:00' AND '{$search_end_post_date} 23:59:59'":"";
$where .= (!empty($search_start_post_date) && empty($search_end_post_date))?" AND date_time >= '{$search_start_post_date} 00:00:00'":"";
$where .= (empty($search_start_post_date) && !empty($search_end_post_date))?" AND date_time <= '{$search_end_post_date} 23:59:59'":"";

$result = $db->select("cv_reports", "$where", "*", "ORDER BY tat ASC");

$per_view = 20;
$per_view = (!empty($no_of_rows))?$no_of_rows:$per_view;
$page_link = "{$admin}cv-pending-tasks?pn=";
$link_suffix = "";
$style_class = "general-link";
page_numbers();

if(isset($_SESSION["msg"]) && !empty($_SESSION["msg"]) && empty($extract)){
echo "<iframe src=\"{$admin}download-report?file=" . $_SESSION["msg"] . "\" style=\"width:1px; height:1px;\"></iframe>";
unset($_SESSION["msg"]);
}

if(empty($view) && empty($edit) && (empty($report_conclusion)||(!empty($report_conclusion)&&$error==0)) ){
?>

<div class="page-title">CV Pending Tasks</div>

<form action="<?php echo $admin; ?>cv-pending-tasks" class="general-form" id="form-div" name="search_form" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
<input type="hidden" name="gh" value="1"> 
<input type="hidden" name="allow_check" value="1"> 
<div class="search-dates">

<div class="col-md-4">
<label for="search_client">Client</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
<select name="search_client" id="search_client" title="Select a client" class="form-control js-example-basic-single" style="width:100%">
<option value="">**Select a client**</option>
<?php 
$result2 = $db->select("reg_users", "WHERE client = '1'", "DISTINCT *", "ORDER BY id ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$client_id = $row2["id"];
$client_name = $row2["name"];
$client_email = $row2["email"];
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
<label for="keyword">Keyword</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-file-text" aria-hidden="true"></i></span>
<input type="text" name="keyword" id="keyword" class="form-control" placeholder="Type a name" value="<?php check_inputted("keyword", $keyword); ?>">
</div>
</div>

<div class="col-md-2">
<label for="no_of_rows">No. of Rows</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-list" aria-hidden="true"></i></span>
<input type="text" name="no_of_rows" id="no_of_rows" class="form-control only-no" placeholder="E.g. 10" value="<?php check_inputted("no_of_rows", $per_view); ?>">
</div>
</div>

<div class="col-md-3">
<label for="view_mine">View Selection</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
<select name="view_mine" id="view_mine" title="Select an option" class="form-control js-example-basic-single" style="width:100%">
<option value="">View assigned to all</option>
<option value="1" <?php echo ($view_mine == 1)?"selected":""; ?>>View assigned to me</option>
</select>
</div>
</div>


<div class="col-md-4">
<label for="search_start_post_date">Start Post Date</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
<input type="text" name="search_start_post_date" id="search_start_post_date" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php check_inputted("search_start_post_date", $search_start_post_date); ?>">
</div>
</div>

<div class="col-md-4">
<label for="search_end_post_date">End Post Date</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
<input type="text" name="search_end_post_date" id="search_end_post_date" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php check_inputted("search_end_post_date", $search_end_post_date); ?>">
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

<form action="<?php echo $admin; ?>cv-pending-tasks" name="extract_form" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
<input type="hidden" name="extract" value="1"> 
<input type="hidden" name="extract_client" value=""> 
<input type="hidden" name="extract_keyword" value=""> 
<input type="hidden" name="extract_start_post_date" value=""> 
<input type="hidden" name="extract_end_post_date" value=""> 
<input type="hidden" name="extract_view_mine" value=""> 
</form>

<?php
$d = 0;

$offset = ($per_view * $pn) - $per_view;

$result = $db->select("cv_reports", "$where", "*", "ORDER BY tat ASC", "LIMIT {$offset},{$per_view}");

if(count_rows($result) > 0){
?>
<div style="overflow-x:auto;">
<table class="table table-striped table-hover">
<thead>
<tr class="gen-title">
<th>Date Posted</th>
<th>Ref. Code</th>
<th>Client</th>
<th style="width:50px;">Batch No.</th>
<th>Names</th>
<th>Institution</th>
<th>Period</th>
<th>Days Remaining</th>
<th style="width:70px;">Agent Assigned?</th>
<th style="width:70px;">View</th>
<th style="width:70px;">Action</th>
<th style="width:30px;"><input type="checkbox" name="sel_all" id="delG" class="sel-group" value=""></th>
</tr>
</thead>
<tbody>
<?php
while($row = fetch_data($result)){
$report_id = $row["id"];
$client = $row["client"];
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$client_email = in_table("email","reg_users","WHERE id = '{$client}'","email");
$client_email = break_long($client_email, "", "");
$batch = $row["batch"];
$names = $row["names"];
$institution = $row["institution"];
$date_received = min_sub_date($row["date_received"]);
$tat = min_sub_date($row["tat"]);
$status_comment = $row["status_comment"];
$status = $row["status"];
$days_remaining = days_remaining($row["tat"],"1",$status);
$investigation_officer = $row["investigation_officer"];
$assigned_agent = $row["assigned_agent"];
$assigned_agent = (!empty($assigned_agent))?"<span style=\"color:#2387a0; font-weight:bold;\">Yes</span>":"<span style=\"color:#b20; font-weight:bold;\">No</span>";
$date_posted = min_sub_date($row["date_time"]);
?>
<tr>
<td><?php echo $date_posted; ?></td>
<td><?php echo $report_id; ?></td>
<td><?php echo "{$client_name}<br>({$client_email})"; ?></td>
<td><?php echo formatQty($batch); ?></td>
<td><?php echo $names; ?></td>
<td><?php echo $institution; ?></td>
<td><?php echo $date_received . " - " . $tat; ?></td>
<td><?php echo $days_remaining; ?></td>
<td><?php echo $assigned_agent; ?></td>
<td><a href="<?php echo $admin; ?>cv-pending-tasks?view=<?php echo $report_id; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link" title="<?php echo $status_comment; ?>"><i class="fa fa-eye" aria-hidden="true"></i> View</a></td>
<td><?php if($investigation_officer == $id || !empty($is_super_admin)){ ?><a href="<?php echo $admin; ?>cv-pending-tasks?edit=<?php echo $report_id; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link" title="Edit CV report #<?php echo $report_id; ?>"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a><?php } ?></td>
<td><input type="checkbox" name="del[<?php echo $d; ?>]" id="del<?php echo $d; ?>" class="delG" value="<?php echo $report_id; ?>"></td>
</tr>
<?php 
}
?>

<tr><td colspan="12">
<?php if(check_admin("edit_cv_reports") == 1){ ?>
<button type="button" id="final-update" class="btn gen-btn float-right report-conclusion" style="margin-right:10px;"><i class="fa fa-pencil" aria-hidden="true" style="font-size:14px;"></i> Final edit on CV report(s)</button>
<button type="button" id="pre-final-update" class="btn gen-btn float-right report-conclusion" style="margin-right:10px;"><i class="fa fa-pencil" aria-hidden="true" style="font-size:14px;"></i> Pre-final edit on CV report(s)</button>
<?php } ?>
</td></tr>

</tbody>
</table>
</div>
<?php
echo ($last_page>1)?"<div class=\"page-nos\">" . $center_pages . "</div>":"";
}else{
echo "<div class='not-success'>No CV pending tasks found at the moment.</div>";
}

}

/////============View Report=============////
if(!empty($view)){
$result = $db->select("cv_reports", "WHERE id='$view'", "*");

if(count_rows($result) == 1){
$row = fetch_data($result);
$client = $row["client"];
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$client_email = in_table("email","reg_users","WHERE id = '{$client}'","email");
$batch = $row["batch"];
$names = $row["names"];
$institution = $row["institution"];
$course = $row["course"];
$qualification = $row["qualification"];
$grade = $row["grade"];
$session = $row["session"];
$matric_number = $row["matric_number"];
$status = $row["status"];
$verified_status = $row["verified_status"];
$status_comment = $row["status_comment"];
$transaction_ref = $row["transaction_ref"];
$date_received = min_sub_date($row["date_received"]);
$tat = min_sub_date($row["tat"]);
$days_remaining = days_remaining($row["tat"],"",$status);
$completion_date = (!empty($row["completion_date"]))?min_sub_date($row["completion_date"]):"";
$investigation_officer = $row["investigation_officer"];
$assigned_agent = $row["assigned_agent"];
$assigned_agent_name = (!empty($assigned_agent))?in_table("name","reg_users","WHERE id = '{$assigned_agent}'","name"):"";
$assigned_agent_email = (!empty($assigned_agent))?in_table("email","reg_users","WHERE id = '{$assigned_agent}'","email"):"";
$date_sent_out = ($row["date_sent_out"] != "0000-00-00" && !empty($assigned_agent))?sub_date($row["date_sent_out"]):"";
$date_received_from_school = ($row["date_received_from_school"] != "0000-00-00" && !empty($assigned_agent))?sub_date($row["date_received_from_school"]):"";
$school_letter_date = ($row["school_letter_date"] != "0000-00-00" && !empty($assigned_agent))?sub_date($row["school_letter_date"]):"";
$remark = $row["remark"];
$report_date_time = full_date($row["date_time"]);
?>

<style>
<!--
div table thead tr th, div table tr th, div table tbody tr td, div table tr td{
text-align:left !important;
}
-->
</style>

<div class="reply-content-wrapper ">

<div class="back"><a href="<?php echo $admin; ?>cv-pending-tasks?pn=<?php echo $pn; ?>" class="btn gen-btn general-link"><i class="fa fa-arrow-left"></i> Back to CV Pending Tasks</a></div>

<div class="view-wrapper ">

<div class="view-header ">
<div class="header-img"><img src="images/post.jpg" ></div>
<div class="header-content">
<div class="view-title">Names:  <?php echo $names; ?></div>
<div class="view-title-details">Added on <?php echo $report_date_time; ?></div>
</div>
</div>

<div class="view-content">

<br />
<div class="align-right"><div class="btn btn-default"><b>Turn-Around Time:</b> <div class="btn btn-danger"><?php echo $tat; ?></div></div> &nbsp;&nbsp; <div class="btn btn-default"><b>Remaining Days:</b> <?php echo $days_remaining; ?></div></div>
<br />

<table class="table table-hover table-striped">
<tr><th class="gen-title" style="width:200px;">Client</th><td><?php echo "{$client_name} ({$client_email})"; ?></td></tr>
<tr><th class="gen-title">Batch No.</th><td><?php echo formatQty($batch); ?></td></tr>
<tr><th class="gen-title">Date Received</th><td><?php echo $date_received; ?></td></tr>
<tr><th class="gen-title">Completion Date</th><td><?php echo $completion_date; ?></td></tr>
<tr><th class="gen-title">Institution</th><td><?php echo $institution; ?></td></tr>
<tr><th class="gen-title">Course</th><td><?php echo $course; ?></td></tr>
<tr><th class="gen-title">Qualification</th><td><?php echo $qualification; ?></td></tr>
<tr><th class="gen-title">Grade</th><td><?php echo $grade; ?></td></tr>
<tr><th class="gen-title">Session</th><td><?php echo $session; ?></td></tr>
<tr><th class="gen-title">Matric Number</th><td><?php echo $matric_number; ?></td></tr>
<tr><th class="gen-title">Status</th><td><?php echo $status; ?></td></tr>
<tr><th class="gen-title">Verified Status</th><td><?php echo $verified_status; ?></td></tr>
<tr><th class="gen-title">Status Comment</th><td><?php echo $status_comment; ?></td></tr>
<tr><th class="gen-title">Transaction Ref.</th><td><?php echo $transaction_ref; ?></td></tr>
<tr><th class="gen-title">Assigned Agent</th><td><?php echo (!empty($assigned_agent))?"{$assigned_agent_name} ({$assigned_agent_email})":""; ?></td></tr>
<tr><th class="gen-title">Date Sent Out</th><td><?php echo $date_sent_out; ?></td></tr>
<tr><th class="gen-title">Date Received from School</th><td><?php echo $date_received_from_school; ?></td></tr>
<tr><th class="gen-title">School Letter Date</th><td><?php echo $school_letter_date; ?></td></tr>
<tr><th class="gen-title">Remark</th><td><?php echo $remark; ?></td></tr>
</table>

<div><?php if($investigation_officer == $id || !empty($is_super_admin)){ ?><a href="<?php echo $admin; ?>cv-pending-tasks?edit=<?php echo $view; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link float-right" title="Edit CV report #<?php echo $view; ?>"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a><?php } ?></div>

</div>

</div>
</div>

<?php
}else{
echo "<div class='not-success'>This CV pending task does not exist.</div>";
}
}

////==============Edit Report=============//////
if(!empty($edit) && $error == 1){

$result = $db->select("cv_reports", "WHERE id='$edit'", "*");

if(count_rows($result) == 1){
$row = fetch_data($result);
$investigation_officer = $row["investigation_officer"];
$assigned_agent = $row["assigned_agent"];
$completion_date = ($row["completion_date"] != "0000-00-00")?$row["completion_date"]:"";
$date_received_from_school = ($row["date_received_from_school"] != "0000-00-00")?$row["date_received_from_school"]:"";
$school_letter_date = ($row["school_letter_date"] != "0000-00-00")?$row["school_letter_date"]:"";
$status = $row["status"];
$verified_status = $row["verified_status"];
$remark = $row["remark"];
?>

<div class="back">
<a href="<?php echo $admin; ?>cv-pending-tasks?pn=<?php echo $pn; ?>" class="btn gen-btn general-link"><i class="fa fa-arrow-left"></i> Back to CV Pending Tasks</a>
</div>

<div class="page-title">Edit Report</div>

<form action="<?php echo $admin; ?>cv-pending-tasks" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data" style="border:1px solid #eee;">
<input type="hidden" name="gh" value="1"> 
<input type="hidden" name="edit" value="<?php echo $edit; ?>"> 
<input type="hidden" name="pn" value="<?php echo $pn; ?>"> 
<input type="hidden" name="view" value="<?php echo $edit; ?>"> 

<?php 
$report_id = $edit;
$client = in_table("client","cv_reports","WHERE id = '$report_id'","client");
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$client_email = in_table("email","reg_users","WHERE id = '{$client}'","email");
$report_names = in_table("names","cv_reports","WHERE id = '$report_id'","names");
?>
<div class="sub-report-title col-sm-12 align-center"><b>Names:</b> <?php echo $report_names; ?> &nbsp;&nbsp;&nbsp; <b>Client:</b> <?php echo "{$client_name} ({$client_email})"; ?><br /><br /></div>

<table class="table table-striped table-hover">

<tr>
<th class="gen-title">Investigation Officer*</th>
<th class="gen-title">Completion Date</th>
<th class="gen-title">Assigned Agent</th>
<th class="gen-title">Date Received from School</th>
</tr>
<tr><td>
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
<input type="text" name="completion_date" id="completion_date" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php echo check_inputted("completion_date", $completion_date); ?>">
</td><td>
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
<input type="text" name="date_received_from_school" id="date_received_from_school" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php check_inputted("date_received_from_school", $date_received_from_school); ?>">
</td></tr>

<tr>
<th class="gen-title">School Letter Date</th>
<th class="gen-title">Status*</th>
<th class="gen-title">Verified Status</th>
<th class="gen-title">Remark</th>
</tr>
<tr><td>
<input type="text" name="school_letter_date" id="school_letter_date" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php check_inputted("school_letter_date", $school_letter_date); ?>">
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
</td><td>
<select name="verified_status" id="verified_status" title="Select a verified status" class="form-control">
<option value="">**Select a verified status**</option>
<?php 
$result2 = $db->select("status_types", "", "DISTINCT *", "ORDER BY id ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$sub_type = $row2["type"];
echo "<option value='{$sub_type}'";
check_selected("verified_status", $sub_type, $verified_status); 
echo ">{$sub_type}</option>";
}
}
?>
</select>
</td><td>
<textarea rows="2" name="remark" id="remark" class="form-control" placeholder="Remark"><?php check_inputted("remark", $remark); ?></textarea>
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


////==============Edit Report Conclusion=============//////
if(check_admin("edit_cv_reports") == 1 && !empty($report_conclusion) && $error == 1 && !empty($conclusion_data)){
$conclusion_data_array = explode(",",$conclusion_data);
?>
<div class="back">

<a href="<?php echo $admin; ?>cv-pending-tasks?pn=<?php echo $pn; ?>" class="btn gen-btn general-link"><i class="fa fa-arrow-left"></i> Back to CV Pending Tasks</a>

</div>

<div class="page-title">Edit Report</div>

<form action="<?php echo $admin; ?>cv-pending-tasks" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data" style="border:1px solid #eee;">
<input type="hidden" name="gh" value="1"> 
<input type="hidden" name="report_conclusion" value="<?php echo $report_conclusion; ?>"> 
<input type="hidden" name="conclusion_data" value="<?php echo $conclusion_data; ?>"> 
<input type="hidden" name="pn" value="<?php echo $pn; ?>"> 

<?php 
/////=======Pre-Final Update============///////
if($report_conclusion == "pre-final-update"){ 
?>

<table class="table table-striped table-hover">
<thead>
<tr class="gen-title">
<th>Client</th>
<th>Candidate</th>
<th>Status Comment</th>
<th>Assigned Agent</th>
<th>Date Received from School</th>
<th>School Letter Date</th>
</tr>
</thead>
<tbody>

<?php  
foreach($conclusion_data_array as $value){
if(!empty($value)){
$client = get_table_data("cv_reports", $value, "client");
$client_name = get_table_data("reg_users", $client, "name");
$client_email = get_table_data("reg_users", $client, "email");
$client_email = break_long($client_email, "(", ")");
$client_details = "{$client_name}<br>{$client_email}";
$names = get_table_data("cv_reports", $value, "names");
$institution = get_table_data("cv_reports", $value, "institution");
$candidate_details = "<b>{$names}</b><br>{$institution}";

$status_comment = get_table_data("cv_reports", $value, "status_comment");
$assigned_agent = get_table_data("cv_reports", $value, "assigned_agent");
$date_received_from_school = get_table_data("cv_reports", $value, "date_received_from_school");
$date_received_from_school = ($date_received_from_school != "0000-00-00")?$date_received_from_school:"";
$school_letter_date = get_table_data("cv_reports", $value, "school_letter_date");
$school_letter_date = ($school_letter_date != "0000-00-00")?$school_letter_date:"";
?>
<tr>
<td><?php echo $client_details; ?><input type="hidden" name="conclusion_id[]" value="<?php echo $value; ?>"></td>
<td><?php echo $candidate_details; ?></td>
<td><textarea name="status_comment[]" id="status_comment" class="form-control" rows="1" placeholder="Status Comment"><?php check_inputted("status_comment", $status_comment); ?></textarea></td>
<td><select name="assigned_agent[]" id="assigned_agent" title="Select an agent" class="form-control" style="width:100%">
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
</select></td>
<td><input type="text" name="date_received_from_school[]" id="date_received_from_school<?php echo $value; ?>" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php check_inputted("date_received_from_school", $date_received_from_school); ?>"></td>
<td><input type="text" name="school_letter_date[]" id="school_letter_date<?php echo $value; ?>" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php check_inputted("school_letter_date", $school_letter_date); ?>"></td>
</tr>
<?php
}
}
?>
</tbody>
</table>

<?php }

/////=======Final Update============///////
if($report_conclusion == "final-update"){ 
?>

<table class="table table-striped table-hover">
<thead>
<tr class="gen-title">
<th>Client</th>
<th>Candidate</th>
<th>Status Comment</th>
<th>Completion Date</th>
<th>Status</th>
<th>Verified Status</th>
</tr>
</thead>
<tbody>

<?php  
foreach($conclusion_data_array as $value){
if(!empty($value)){
$client = get_table_data("cv_reports", $value, "client");
$client_name = get_table_data("reg_users", $client, "name");
$client_email = get_table_data("reg_users", $client, "email");
$client_email = break_long($client_email, "(", ")");
$client_details = "{$client_name}<br>{$client_email}";
$names = get_table_data("cv_reports", $value, "names");
$institution = get_table_data("cv_reports", $value, "institution");
$candidate_details = "<b>{$names}</b><br>{$institution}";

$status_comment = get_table_data("cv_reports", $value, "status_comment");
$completion_date = get_table_data("cv_reports", $value, "completion_date");
$completion_date = ($completion_date != "0000-00-00")?$completion_date:"";
$status = get_table_data("cv_reports", $value, "status");
$verified_status = get_table_data("cv_reports", $value, "verified_status");
?>
<tr>
<td><?php echo $client_details; ?><input type="hidden" name="conclusion_id[]" value="<?php echo $value; ?>"></td>
<td><?php echo $candidate_details; ?></td>
<td><textarea name="status_comment[]" id="status_comment" class="form-control" rows="1" placeholder="Status Comment"><?php check_inputted("status_comment", $status_comment); ?></textarea></td>
<td><input type="text" name="completion_date[]" id="completion_date<?php echo $value; ?>" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php check_inputted("completion_date", $completion_date); ?>"></td>
<td><select name="status[]" id="status" title="Select a status" class="form-control" style="width:100%" required>
<option value="">**Select a status**</option>
<?php 
$result2 = $db->select("status_types", "", "DISTINCT *", "ORDER BY id ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$status_type = $row2["type"];
echo "<option value='{$status_type}'";
check_selected("status", $status_type, $status); 
echo ">{$status_type}</option>";
}
}
?>
</select></td>
<td><select name="verified_status[]" id="verified_status" title="Select a verified status" class="form-control" style="width:100%" required>
<option value="">**Select a verified status**</option>
<?php 
$result2 = $db->select("status_types", "", "DISTINCT *", "ORDER BY id ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$status_type = $row2["type"];
echo "<option value='{$status_type}'";
check_selected("verified_status", $status_type, $verified_status); 
echo ">{$status_type}</option>";
}
}
?>
</select></td>
</tr>
<?php
}
}
?>
</tbody>
</table>

<?php } ?>

<div class="submit-div">
<button class="btn gen-btn float-right"><i class="fa fa-upload"></i> Save</button>
</div>

</form>
<?php
}
?>

<script>
<!--
var conf_text = "report";

$(document).ready(function(){

$(".download-report").click(function(){
document.extract_form.extract_client.value = $("#search_client").val();
document.extract_form.extract_keyword.value = document.search_form.keyword.value;
document.extract_form.extract_start_post_date.value = document.search_form.search_start_post_date.value;
document.extract_form.extract_end_post_date.value = document.search_form.search_end_post_date.value;
document.extract_form.extract_view_mine.value = $("#view_mine").val();
document.extract_form.submit();
});

////////////////////////////////////////////////////////
$(".report-conclusion").click(function(){
var this_conclusion_id = $(this).attr("id");
var det_checked_conclusion = "";

$("input:checkbox:not(.sel-group)").each(function(){
var this_val = $(this).val();
if(this_val != "" && $(this).prop("checked") == true){
det_checked_conclusion += this_val + ",";
}
});

if(det_checked_conclusion != ""){
$(this).children("i").attr("class","fa fa-spinner fa-spin fa-3x fa-fw");
$.post("<?php echo $admin; ?>cv-pending-tasks", {gh : 1, pn : <?php echo $pn; ?>, report_conclusion : this_conclusion_id, conclusion_data : det_checked_conclusion}, function(data){
$(".form-div").html(data);
})
.error(function() { 
sweetAlert("Notice", "An error occured!", "error");
$(this).children("i").attr("class","fa fa-pencil");
});
}

});
/////////////////////////////////////////////////

});

//-->
</script>

<script src="js/general-form.js"></script>

<?php if(!isset($_REQUEST["gh"])){ ?>

</div>
</div>

</div>

<?php require_once("../includes/portal-footer.php"); } ?>