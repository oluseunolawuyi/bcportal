<?php if(!isset($_REQUEST["gh"])){ include_once("../includes/admin-header.php"); 
}else{ 
include_once("../includes/gen-header.php");
} ?>

<?php
error_reporting(0);

admin_role_redirect("manage_cv_reports");

$edit = nr_input("edit");
$add = nr_input("add");
$pn = nr_input("pn");
$view = nr_input("view");

$report_conclusion = tp_input("report_conclusion");
$conclusion_data = tp_input("conclusion_data");

$client = np_input("client");
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$client_email = in_table("email","reg_users","WHERE id = '{$client}'","email");
$date_received = tp_input("date_received");
$completion_date = tp_input("completion_date");
$names = tp_input("names");
$institution = tp_input("institution");
$course = tp_input("course");
$qualification = tp_input("qualification");
$grade = tp_input("grade");
$session = tp_input("session");
$matric_number = tp_input("matric_number");
$batch = np_input("batch");
$status = tp_input("status");
$verified_status = tp_input("verified_status");
$status_comment = tp_input("status_comment");
$transaction_ref = tp_input("transaction_ref");
$investigation_officer = np_input("investigation_officer");
$assigned_agent = np_input("assigned_agent");
$date_sent_out = tp_input("date_sent_out");
$date_received_from_school = tp_input("date_received_from_school");
$school_letter_date = tp_input("school_letter_date");
$remark = tp_input("remark");

$tat = "";

if(!empty($date_received)){
$dat = date_create($date_received);
date_add($dat, date_interval_create_from_date_string("78 days"));
$tat = date_format($dat, "Y-m-d");
}

//////////======================Generate Report==========================///////////
$extract = np_input("extract");
$extract_client = np_input("extract_client");
$extract_client_name = in_table("name","reg_users","WHERE id = '{$extract_client}'","name");
$extract_client_email = in_table("email","reg_users","WHERE id = '{$extract_client}'","email");
$extract_keyword = tp_input("extract_keyword");
$extract_reference_code = tp_input("extract_reference_code");
$extract_batch = np_input("extract_batch");
$extract_start_date = tp_input("extract_start_date");
$extract_end_date = tp_input("extract_end_date");
$extract_status = tp_input("extract_status");

if(check_admin("download_cv_reports") == 1 && $_SERVER["REQUEST_METHOD"] == "POST" && !empty($extract)){

$table_title = (!empty($extract_client))?strtoupper("{$extract_client_name} ({$extract_client_email})"):"ALL CLIENTS";

$where = "WHERE id > '0'";
$where .= (!empty($extract_client))?" AND client = '$extract_client'":"";
$where .= (!empty($extract_keyword))?" AND names LIKE '%{$extract_keyword}%'":"";
$where .= (!empty($extract_reference_code))?" AND id = '$extract_reference_code'":"";
$where .= (!empty($extract_batch))?" AND batch = '$extract_batch'":"";
$where .= (!empty($extract_start_date))?" AND start_date >= '{$extract_start_date} 00:00:00'":"";
$where .= (!empty($extract_end_date))?" AND end_date <= '{$extract_end_date} 23:59:59'":"";
$where .= (!empty($extract_status))?" AND status = '$extract_status'":"";

$result = $db->select("cv_reports", "$where", "DISTINCT *", "ORDER BY batch ASC");
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

redirect("{$directory}{$admin}manage-cv-reports");

}

}
///////=====================Ends Generate Report======================/////////



$i = 0;
$act = "";

$report_id = np_input("report_id");

////////////// Add or Update Main Report //////////////////////////////
if($_SERVER["REQUEST_METHOD"] == "POST" && ((!empty($add) && !empty($date_received)) || !empty($edit)) && !empty($client) && !empty($names) && !empty($institution) && !empty($course) && !empty($qualification) && !empty($grade) && !empty($session) && !empty($batch) && !empty($investigation_officer) && !empty($status) && !empty($verified_status)){

$assignment_date = $date_received;
if(check_admin("edit_cv_reports") == 1 && !empty($edit)){ 
/////////////////////////***************************************************************************************************************
$prev_investigation_officer = in_table("investigation_officer","cv_reports","WHERE id = '{$edit}'","investigation_officer");
$assignment_date = in_table("date_received","cv_reports","WHERE id = '{$edit}'","date_received");
$assignment_date = ($prev_investigation_officer != $investigation_officer)?$date:$assignment_date;
$prev_assigned_agent = in_table("assigned_agent","cv_reports","WHERE id = '{$edit}'","assigned_agent");
$prev_date_sent_out = in_table("date_sent_out","cv_reports","WHERE id = '{$edit}'","date_sent_out");
$date_sent_out = (!empty($assigned_agent) && ($prev_assigned_agent != $assigned_agent || $prev_date_sent_out == "0000-00-00"))?$date:$prev_date_sent_out;
$date_sent_out = (empty($assigned_agent))?"0000-00-00":$date_sent_out;
////////////////////*********************------------------------------------------------------------------------------------------------
}

$data_array = array(
"client" => $client,
"completion_date" => $completion_date,
"names" => $names,
"institution" => $institution,
"course" => $course,
"qualification" => $qualification,
"grade" => $grade,
"session" => $session,
"matric_number" => $matric_number,
"batch" => $batch,
"status" => $status,
"verified_status" => $verified_status,
"status_comment" => $status_comment,
"transaction_ref" => $transaction_ref,
"investigation_officer" => $investigation_officer,
"assigned_agent" => $assigned_agent,
"date_sent_out" => $date_sent_out,
"date_received_from_school" => $date_received_from_school,
"school_letter_date" => $school_letter_date,
"remark" => $remark
);

if(check_admin("add_cv_reports") == 1 && !empty($add)){
$data_array += array("date_received" => $date_received, "tat" => $tat, "date_time" => $date_time, "last_update" => $date_time);
$act = $db->insert2($data_array, "cv_reports");
$report_id = in_table("id","cv_reports","WHERE date_time = '{$date_time}' AND client = '{$client}'","id");;
}else if(check_admin("edit_cv_reports") == 1 && !empty($edit)){ 
$data_array += array("last_update" => $date_time);
$act = $db->update($data_array, "cv_reports", "id = '$edit'");
$report_id = $edit;
}

if($act){

///////////===========Send mail to officer==========//////////
$informed_officer_name = $informed_officer_email = $assigned_agent_name = $assigned_agent_email = $message = "";
$informed_officer_name = in_table("name","reg_users","WHERE id = '{$investigation_officer}'","name");
$informed_officer_email = in_table("email","reg_users","WHERE id = '{$investigation_officer}'","email");
$assigned_agent_name = in_table("name","reg_users","WHERE id = '{$assigned_agent}'","name");
$assigned_agent_email = in_table("email","reg_users","WHERE id = '{$assigned_agent}'","email");

$subject = "New CV Task(s)";
$message = "<p>Dear {$informed_officer_name},</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>";
$message2 = $message;
$message = message_template();
$headers2 = "{$gen_name} <no-reply@{$domain}>";

$to = $informed_officer_email;
$headers = $headers2;
$act = send_mail();
$admin_data_array = array(
"ticket_id" => "'$ticket_id'",
"sender_name" => "'$gen_name'",
"sender_email" => "'$gen_email'",
"recipient_name" => "'$informed_officer_name'",
"recipient_email" => "'$informed_officer_email'",
"subject" => "'$subject'",
"message" => "'$message2'",
"inbox" => "'1'",
"date_time" => "'$date_time'"
);
$db->insert($admin_data_array, "admin_messages");
///////////===========Ends Send mail to each officer==========//////////

///////////===========Insert Report Status==========//////////
$used_status = "";
$used_status .= (!empty($investigation_officer))?"Assigned to an Investigation Officer - {$informed_officer_name} ({$informed_officer_email}) on " . min_sub_date($assignment_date) . ",":"";
$used_status .= (!empty($assigned_agent) && !empty($date_sent_out))?"Assigned to an Agent - {$assigned_agent_name} ({$assigned_agent_email}) on " . min_sub_date($date_sent_out) . ",":"";
$used_status .= (!empty($date_sent_out))?"Sent to Agent/School on " . min_sub_date($date_sent_out) . ",":"";
$used_status .= (!empty($date_received_from_school))?"Received from School on " . min_sub_date($date_received_from_school) . ",":"";
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

$activity = (!empty($add))?"Added a new":"Updated a";
$activity .= " CV report for {$client_name} ({$client_email}) with names: {$names}, in batch {$batch}.";
$audit_data_array = array(
"user_id" => "'$id'",
"name" => "'$username'",
"email" => "'$user_email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$db->insert($audit_data_array, "audit_log");

$error = 0;

echo "<div class='success'>CV Report successfully saved.</div>";
}else{
echo "<div class='not-success'>Error occured.</div>";
}

}


if($_SERVER['REQUEST_METHOD'] == "POST" && ((!empty($add) && empty($date_received)) || (!empty($add) || !empty($edit)) && (empty($client) || empty($names) || empty($institution) || empty($course) || empty($qualification) || empty($grade) || empty($session) || empty($batch) || empty($investigation_officer) || empty($status) || empty($verified_status)))){
echo "<div class='not-success'>Not submitted! All the * fields are required.</div>";
}

//////////==================== Delete CV Report =====================/////////////
if(check_admin("delete_cv_reports") == 1 && isset($_POST["delete"]) && isset($_POST["del"])){
$i = $act = 0;
if(is_array($_POST["del"])){
foreach ($_POST["del"] as $k => $c) {
if($c != ""){ 
$c = testQty($c);
$act = $db->delete("cv_reports", "id = '$c'");
$i++;		
}else{
continue;
}
}

if($act && $i > 0){

$activity = "Deleted {$i} CV report(s).";
$audit_data_array = array(
"user_id" => "'$id'",
"name" => "'$username'",
"email" => "'$user_email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$db->insert($audit_data_array, "audit_log");

echo "<div class='success'>{$i} CV report(s) successfully deleted.</div>";
}else{
echo "<div class='not-success'>Error. Unable to delete CV report(s).</div>";
}

}else{
echo "<div class='not-success'>Atleast one CV report must be selected.</div>";
}
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
$used_status .= (!empty($assigned_agent) && !empty($date_sent_out))?"Assigned to an Agent - {$assigned_agent_name} ({$assigned_agent_email}) on " . min_sub_date($date_sent_out) . ",":"";
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
$reference_code = search_option("reference_code");
$no_of_rows = search_option("no_of_rows");
$search_batch = search_option("search_batch");
$search_start_date = search_option("search_start_date");
$search_end_date = search_option("search_end_date");
$search_status = search_option("search_status");

$where = "WHERE id > '0'";
$where .= ($search_client >= 0 && $search_client != "")?" AND client = '{$search_client}'":"";
$where .= (!empty($keyword))?" AND names LIKE '%{$keyword}%'":"";
$where .= (!empty($reference_code))?" AND id = '{$reference_code}'":"";
$where .= (!empty($search_batch))?" AND batch = '{$search_batch}'":"";
$where .= (!empty($search_start_date))?" AND date_received >= '{$search_start_date} 00:00:00'":"";
$where .= (!empty($search_end_date))?" AND completion_date <= '{$search_end_date} 23:59:59'":"";
$where .= (!empty($search_status))?" AND status = '{$search_status}'":"";

$result = $db->select("cv_reports", "$where", "*", "ORDER BY id DESC");

$per_view = 20;
$per_view = (!empty($no_of_rows))?$no_of_rows:$per_view;
$page_link = "{$admin}manage-cv-reports?pn=";
$link_suffix = "";
$style_class = "general-link";
page_numbers();

if(isset($_SESSION["msg"]) && !empty($_SESSION["msg"]) && empty($extract)){
echo "<iframe src=\"{$admin}download-report?file=" . $_SESSION["msg"] . "\" style=\"width:1px; height:1px;\"></iframe>";
unset($_SESSION["msg"]);
}

if(empty($view) && (empty($edit)||(!empty($edit)&&$error==0)) && (empty($add)||(!empty($add)&&$error==0)) && (empty($report_conclusion)||(!empty($report_conclusion)&&$error==0)) ){
?>

<div class="page-title">Manage Certificate Verification Reports <a href="<?php echo $admin; ?>manage-cv-reports?add=1" class="btn gen-btn general-link float-right">New CV Report</a></div>

<form action="<?php echo $admin; ?>manage-cv-reports" name="search_form" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
<input type="hidden" name="gh" value="1"> 
<div class="search-dates">

<div class="col-md-3">
<label for="search_client">Client</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
<select name="search_client" id="search_client" title="Select a client" class="form-control js-example-basic-single" style="width:100%">
<option value="">**Select a client**</option>
<?php 
$result2 = $db->select("cv_reports", "", "DISTINCT client", "ORDER BY client ASC");
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
<label for="keyword">Keyword</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-file-text" aria-hidden="true"></i></span>
<input type="text" name="keyword" id="keyword" class="form-control" placeholder="Type a keyword" value="<?php check_inputted("keyword", $keyword); ?>">
</div>
</div>

<div class="col-md-2">
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
<input type="text" name="no_of_rows" id="no_of_rows" class="form-control only-no" placeholder="E.g. 10" value="<?php check_inputted("no_of_rows", $per_view); ?>">
</div>
</div>

<div class="col-md-2">
<label for="search_batch">Batch No.</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-code" aria-hidden="true"></i></span>
<input type="text" name="search_batch" id="search_batch" class="form-control only-no" placeholder="E.g. 2" value="<?php check_inputted("search_batch", $search_batch); ?>">
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

<div class="col-md-2">
<label for="search_status">Status</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
<select name="search_status" id="search_status" title="Select a status" class="form-control js-example-basic-single" style="width:100%">
<option value="">**Select**</option>
<?php 
$result2 = $db->select("cv_reports", "", "DISTINCT status", "ORDER BY id ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$status_type = $row2["status"];
echo "<option value='{$status_type}'";
check_selected("search_status", $status_type, $search_status); 
echo ">{$status_type}</option>";
}
}
?>
</select>
</div>
</div>

<div class="col-md-2 col-xs-6">
<br />
<button type="submit" class="btn gen-btn"><i class="fa fa-search"></i> Search</button>
</div>

<?php if(check_admin("download_cv_reports") == 1){  ?>
<div class="col-md-2 col-xs-6">
<br />
<a class="btn gen-btn download-report"><i class="fa fa-download"></i> Download</a>
</div>
<?php } ?>

</div>
</form>

<form action="<?php echo $admin; ?>manage-cv-reports" name="extract_form" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
<input type="hidden" name="extract" value="1"> 
<input type="hidden" name="extract_client" value=""> 
<input type="hidden" name="extract_keyword" value=""> 
<input type="hidden" name="extract_reference_code" value=""> 
<input type="hidden" name="extract_batch" value=""> 
<input type="hidden" name="extract_start_date" value=""> 
<input type="hidden" name="extract_end_date" value=""> 
<input type="hidden" name="extract_status" value=""> 
</form>

<?php
$d = 0;

$offset = ($per_view * $pn) - $per_view;

$result = $db->select("cv_reports", "$where", "*", "ORDER BY id DESC", "LIMIT {$offset},{$per_view}");

if(count_rows($result) > 0){
?>
<form action="<?php echo $admin; ?>manage-cv-reports" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data" style="overflow-x:auto;">
<input type="hidden" name="pn" value="<?php echo $pn; ?>"> 
<input type="hidden" name="gh" value="1"> 
<input type="hidden" name="delete" value="1"> 
<table class="table table-striped table-hover">
<thead>
<tr class="gen-title">
<th>Ref. Code</th>
<th>Client</th>
<th style="width:50px;">Batch</th>
<th>Names</th>
<th>Institution</th>
<th>Execution Period</th>
<th>Completion Date</th>
<th>Status</th>
<th>Task Assignment</th>
<?php if(check_admin("edit_cv_reports") == 1){  ?><th>Action</th><?php } ?>
<th>Details</th>
<?php if(check_admin("delete_cv_reports") == 1){  ?>
<th style="width:30px;"><input type="checkbox" name="sel_all" id="delG" class="sel-group" value=""></th>
<?php }  ?>
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
$date_received = ($row["date_received"] != "0000-00-00")?min_sub_date($row["date_received"]):"";
$tat = ($row["tat"] != "0000-00-00")?min_sub_date($row["tat"]):"";
$completion_date = ($row["completion_date"] != "0000-00-00")?min_sub_date($row["completion_date"]):"";
$assigned_agent = $row["assigned_agent"];
$status_comment = $row["status_comment"];
$status = $row["status"];
$days_remaining = days_remaining($row["tat"],"1",$status);
$cols = 0;
?>
<tr>
<td style="width:30px;"><?php echo $report_id; ?></td>
<td><?php echo "{$client_name}<br>({$client_email})"; ?></td>
<td><?php echo formatQty($batch); ?></td>
<td><?php echo $names; ?></td>
<td><?php echo $institution; ?></td>
<td><?php echo $date_received . " - " . $tat; ?></td>
<td><?php echo $completion_date; ?></td>
<td><?php echo $days_remaining; ?></td>
<td><?php echo (!empty($assigned_agent))?"Assigned":"Not Assigned"; ?></td>
<?php if(check_admin("edit_cv_reports") == 1){ $cols++; ?>
<td style="width:70px;"><a href="<?php echo $admin; ?>manage-cv-reports?edit=<?php echo $report_id; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link" title="Edit CV report #<?php echo $report_id; ?>"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></td>
<?php } ?>
<td style="width:70px;"><a href="<?php echo $admin; ?>manage-cv-reports?view=<?php echo $report_id; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link" title="<?php echo $status_comment; ?>"><i class="fa fa-eye" aria-hidden="true"></i> View</a></td>
<?php if(check_admin("delete_cv_reports") == 1){ $cols++; ?>
<td style="width:30px;"><input type="checkbox" name="del[<?php echo $d; ?>]" id="del<?php echo $d; ?>" class="delG" value="<?php echo $report_id; ?>"></td>
<?php } ?>
</tr>
<?php 
$d++;
}
?>

<tr><td colspan="<?php echo 10 + $cols; ?>">
<?php if(check_admin("delete_cv_reports") == 1){  ?>
<input class="sub-del" type="submit" value=" "><button type="button" class="btn del-btn gen-btn float-right"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete selected CV report(s)</button>
<?php } if(check_admin("edit_cv_reports") == 1){ ?>
<button type="button" id="final-update" class="btn gen-btn float-right report-conclusion" style="margin-right:10px;"><i class="fa fa-pencil" aria-hidden="true" style="font-size:14px;"></i> Final edit on CV report(s)</button>
<button type="button" id="pre-final-update" class="btn gen-btn float-right report-conclusion" style="margin-right:10px;"><i class="fa fa-pencil" aria-hidden="true" style="font-size:14px;"></i> Pre-final edit on CV report(s)</button>

<?php } ?>
</td></tr>

</tbody>
</table>
</form>
<?php
echo ($last_page>1)?"<div class=\"page-nos\">" . $center_pages . "</div>":"";
}else{
echo "<div class='not-success'>No CV reports found at the moment.</div>";
}

}

/////============View Report=============////
if(!empty($view) && (empty($edit) || (!empty($edit) && $error == 0))){
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
$date_received = ($row["date_received"] != "0000-00-00")?min_sub_date($row["date_received"]):"";
$tat = ($row["tat"] != "0000-00-00")?min_sub_date($row["tat"]):"";
$days_remaining = days_remaining($row["tat"],"",$status);
$completion_date = ($row["completion_date"] != "0000-00-00")?min_sub_date($row["completion_date"]):"";
$investigation_officer = $row["investigation_officer"];
$investigation_officer_name = (!empty($investigation_officer))?in_table("name","reg_users","WHERE id = '{$investigation_officer}'","name"):"";
$investigation_officer_email = (!empty($investigation_officer))?in_table("email","reg_users","WHERE id = '{$investigation_officer}'","email"):"";
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

<div class="back"><a href="<?php echo $admin; ?>manage-cv-reports?pn=<?php echo $pn; ?>" class="btn gen-btn general-link"><i class="fa fa-arrow-left"></i> Back to CV reports</a></div>

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
<tr><th class="gen-title">Investigation Officer</th><td><?php echo (!empty($investigation_officer))?"{$investigation_officer_name} ({$investigation_officer_email})":""; ?></td></tr>
<tr><th class="gen-title">Assigned Agent</th><td><?php echo (!empty($assigned_agent))?"{$assigned_agent_name} ({$assigned_agent_email})":""; ?></td></tr>
<tr><th class="gen-title">Date Sent Out</th><td><?php echo $date_sent_out; ?></td></tr>
<tr><th class="gen-title">Date Received from School</th><td><?php echo $date_received_from_school; ?></td></tr>
<tr><th class="gen-title">School Letter Date</th><td><?php echo $school_letter_date; ?></td></tr>
<tr><th class="gen-title">Remark</th><td><?php echo $remark; ?></td></tr>
</table>

<?php if(check_admin("edit_cv_reports") == 1){ ?>
<div><a href="<?php echo $admin; ?>manage-cv-reports?edit=<?php echo $view; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link float-right" title="Edit report"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></div>
<?php } ?>

</div>

</div>
</div>

<?php
}else{
echo "<div class='not-success'>This CV report does not exist.</div>";
}
}

////==============Edit Plan=============//////
if(((check_admin("add_cv_reports") == 1 && !empty($add)) || (check_admin("edit_cv_reports") == 1 && !empty($edit))) && $error == 1){

$date_received = $completion_date = $client = $names = $institution = $course = $qualification = $grade = $session = $matric_number = $batch = $status = $verified_status = $status_comment = $transaction_ref = $investigation_officer = $assigned_agent = $date_sent_out = $date_received_from_school = $school_letter_date = $remark = "";
if(!empty($edit)){
$result = $db->select("cv_reports", "WHERE id='$edit'", "*", "");
if(count_rows($result) == 1){
$row = fetch_data($result);
$date_received = ($row["date_received"] != "0000-00-00")?$row["date_received"]:"";
$completion_date = ($row["completion_date"] != "0000-00-00")?$row["completion_date"]:"";
$client = $row["client"];
$names = $row["names"];
$institution = $row["institution"];
$course = $row["course"];
$qualification = $row["qualification"];
$grade = $row["grade"];
$session = $row["session"];
$matric_number = $row["matric_number"];
$batch = $row["batch"];
$status = $row["status"];
$verified_status = $row["verified_status"];
$status_comment = $row["status_comment"];
$transaction_ref = $row["transaction_ref"];
$investigation_officer = $row["investigation_officer"];
$assigned_agent = $row["assigned_agent"];
$date_sent_out = ($row["date_sent_out"] != "0000-00-00")?$row["date_sent_out"]:"";
$date_received_from_school = ($row["date_received_from_school"] != "0000-00-00")?$row["date_received_from_school"]:"";
$school_letter_date = ($row["school_letter_date"] != "0000-00-00")?$row["school_letter_date"]:"";
$remark = $row["remark"];
}
}

$c = 0;

$field_name = (!empty($edit))?"edit":"add";
$field_value = (!empty($edit))?$edit:1;
$action_title = (!empty($edit))?"Edit":"Add New";
?>

<div class="back">

<a href="<?php echo $admin; ?>manage-cv-reports?pn=<?php echo $pn; ?>" class="btn gen-btn general-link"><i class="fa fa-arrow-left"></i> Back to CV reports</a>

</div>

<div class="page-title"><?php echo $action_title; ?> CV Report</div>

<form action="<?php echo $admin; ?>manage-cv-reports" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data" style="border:1px solid #eee;">
<input type="hidden" name="gh" value="1"> 
<input type="hidden" name="<?php echo $field_name; ?>" value="<?php echo $field_value; ?>"> 
<?php if(!empty($edit)){ ?>
<input type="hidden" name="pn" value="<?php echo $pn; ?>"> 
<input type="hidden" name="view" value="<?php echo $edit; ?>"> 
<?php
}
?>

<div class="col-sm-12">
<label for="client">Client*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user"></i></span>
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

<div class="col-sm-12">
<label for="batch">Batch Number*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-code"></i></span>
<input type="text" name="batch" id="batch" class="form-control only-no" placeholder="Batch no." value="<?php check_inputted("batch", $batch); ?>" required>
</div>
</div>

<?php if(!empty($add)){ ?>
<div class="col-sm-12">
<label for="date_received">Date Received*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
<input type="text" name="date_received" id="date_received" onfocus="javascript: $(this).blur();" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php check_inputted("date_received", $date_received); ?>" required>
</div>
</div>
<?php } ?>

<div class="col-sm-12">
<label for="completion_date">Completion Date</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
<input type="text" name="completion_date" id="completion_date" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php check_inputted("completion_date", $completion_date); ?>">
</div>
</div>

<div class="col-sm-12">
<label for="names">Names*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user"></i></span>
<input type="text" name="names" id="names" class="form-control" placeholder="Full Name" value="<?php check_inputted("names", $names); ?>" required>
</div>
</div>

<div class="col-sm-12">
<label for="institution">Institution*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-university"></i></span>
<input type="text" name="institution" id="institution" class="form-control" placeholder="Institution" value="<?php check_inputted("institution", $institution); ?>" required>
</div>
</div>

<div class="col-sm-12">
<label for="course">Course*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-university"></i></span>
<input type="text" name="course" id="course" class="form-control" placeholder="Course" value="<?php check_inputted("course", $course); ?>" required>
</div>
</div>

<div class="col-sm-12">
<label for="qualification">Qualification*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-university"></i></span>
<input type="text" name="qualification" id="qualification" class="form-control" placeholder="Qualification" value="<?php check_inputted("qualification", $qualification); ?>" required>
</div>
</div>

<div class="col-sm-12">
<label for="grade">Grade*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-university"></i></span>
<input type="text" name="grade" id="grade" class="form-control" placeholder="Grade" value="<?php check_inputted("grade", $grade); ?>" required>
</div>
</div>

<div class="col-sm-12">
<label for="session">Session*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-university"></i></span>
<input type="text" name="session" id="session" class="form-control" placeholder="Session" value="<?php check_inputted("session", $session); ?>" required>
</div>
</div>

<div class="col-sm-12">
<label for="matric_number">Matric Number</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-university"></i></span>
<input type="text" name="matric_number" id="matric_number" class="form-control" placeholder="Matric Number" value="<?php check_inputted("matric_number", $matric_number); ?>">
</div>
</div>

<div class="col-sm-12">
<label for="status">Status*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user"></i></span>
<select name="status" id="status" title="Select a status" class="form-control" style="width:100%" required>
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
</select>
</div>
</div>

<div class="col-sm-12">
<label for="verified_status">Verified Status*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user"></i></span>
<select name="verified_status" id="verified_status" title="Select a verified status" class="form-control" style="width:100%" required>
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
</select>
</div>
</div>

<div class="col-sm-12">
<label for="status_comment">Status Comment</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-file-text"></i></span>
<textarea name="status_comment" id="status_comment" class="form-control" rows="2" placeholder="Status Comment"><?php check_inputted("status_comment", $status_comment); ?></textarea>
</div>
</div>

<div class="col-sm-12">
<label for="transaction_ref">Transaction Ref.</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-code"></i></span>
<input type="text" name="transaction_ref" id="transaction_ref" class="form-control" placeholder="Transaction Ref." value="<?php check_inputted("transaction_ref", $transaction_ref); ?>">
</div>
</div>

<div class="col-sm-12">
<label for="investigation_officer">Investigation Officer*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user"></i></span>
<select name="investigation_officer" id="investigation_officer" title="Select a officer" class="form-control" style="width:100%" required>
<option value="">**Select a officer**</option>
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
</div>
</div>

<div class="col-sm-12">
<label for="assigned_agent">Assigned Agent</label> 
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user"></i></span>
<select name="assigned_agent" id="assigned_agent" title="Select an agent" class="form-control" style="width:100%">
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
</div>
</div>

<?php if(!empty($add)){ ?>
<div class="col-sm-12">
<label for="date_sent_out">Date Sent out to Agent/School</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
<input type="text" name="date_sent_out" id="date_sent_out" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php check_inputted("date_sent_out", $date_sent_out); ?>">
</div>
</div>
<?php } ?>

<div class="col-sm-12">
<label for="date_received_from_school">Date Received from School</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
<input type="text" name="date_received_from_school" id="date_received_from_school" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php check_inputted("date_received_from_school", $date_received_from_school); ?>">
</div>
</div>

<div class="col-sm-12">
<label for="school_letter_date">School Letter Date</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
<input type="text" name="school_letter_date" id="school_letter_date" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php check_inputted("school_letter_date", $school_letter_date); ?>">
</div>
</div>

<div class="col-sm-12">
<label for="remark">Remark</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
<textarea name="remark" id="remark" class="form-control" rows="2" placeholder="Remark"><?php check_inputted("remark", $remark); ?></textarea>
</div>
</div>
          
<div class="submit-div col-sm-12">
<button class="btn gen-btn float-right" name="update"><i class="fa fa-upload"></i> Save</button>
</div>

</form>

<?php
}

////==============Edit Report Conclusion=============//////
if(check_admin("edit_cv_reports") == 1 && !empty($report_conclusion) && $error == 1 && !empty($conclusion_data)){
$conclusion_data_array = explode(",",$conclusion_data);
?>
<div class="back">

<a href="<?php echo $admin; ?>manage-cv-reports?pn=<?php echo $pn; ?>" class="btn gen-btn general-link"><i class="fa fa-arrow-left"></i> Back to CV reports</a>

</div>

<div class="page-title">Edit CV Report</div>

<form action="<?php echo $admin; ?>manage-cv-reports" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data" style="border:1px solid #eee;">
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
document.extract_form.extract_client.value = document.search_form.search_client.value;
document.extract_form.extract_keyword.value = document.search_form.keyword.value;
document.extract_form.extract_reference_code.value = document.search_form.reference_code.value;
document.extract_form.extract_batch.value = document.search_form.search_batch.value;
document.extract_form.extract_start_date.value = document.search_form.search_start_date.value;
document.extract_form.extract_end_date.value = document.search_form.search_end_date.value;
document.extract_form.extract_status.value = document.search_form.search_status.value;
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
$.post("<?php echo $admin; ?>manage-cv-reports", {gh : 1, pn : <?php echo $pn; ?>, report_conclusion : this_conclusion_id, conclusion_data : det_checked_conclusion}, function(data){
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