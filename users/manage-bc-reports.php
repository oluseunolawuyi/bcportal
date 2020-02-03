<?php if(!isset($_REQUEST["gh"])){ include_once("../includes/admin-header.php"); 
}else{ 
include_once("../includes/gen-header.php");
} ?>

<?php
admin_role_redirect("manage_bc_reports");

$main_edit = nr_input("main_edit");
$sub_edit = nr_input("sub_edit");
$sub_add = nr_input("sub_add");
$sub_delete = nr_input("sub_delete");

$edit = nr_input("edit");
$add = nr_input("add");
$pn = nr_input("pn");
$view = nr_input("view");
$main_report = np_input("main_report");
$sub_report = np_input("sub_report");

$client = np_input("client");
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$client_email = in_table("email","reg_users","WHERE id = '{$client}'","email");
$batch = np_input("batch");
$subject = tp_input("subject");
$recommendation = tp_input("recommendation");

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

if(check_admin("download_bc_reports") == 1 && $_SERVER["REQUEST_METHOD"] == "POST" && !empty($extract)){

$table_title = (!empty($extract_client))?strtoupper("{$extract_client_name} ({$extract_client_email})"):"ALL CLIENTS";

$where = "WHERE id > '0'";
$where .= (!empty($extract_client))?" AND client = '$extract_client'":"";
$where .= (!empty($extract_keyword))?" AND subject LIKE '%{$extract_keyword}%'":"";
$where .= (!empty($extract_reference_code))?" AND id = '$extract_reference_code'":"";
$where .= (!empty($extract_batch))?" AND batch = '$extract_batch'":"";
$where .= (!empty($extract_start_date))?" AND start_date >= '{$extract_start_date} 00:00:00'":"";
$where .= (!empty($extract_end_date))?" AND end_date <= '{$extract_end_date} 23:59:59'":"";
$sub_where = (!empty($extract_status))?" AND status = '$extract_status'":"";

$extract_array = array();

$result = $db->select("bc_reports", "$where", "DISTINCT *", "ORDER BY batch ASC");
if(count_rows($result) > 0){
while($row = fetch_data($result)){
$extract_id = $row["id"];
$extract_array[] = $extract_id;
}
}

///======================== Prepare Report ============//////////
if(count($extract_array) > 0){

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

foreach($extract_array as $value){

$sub_batch = $sub_subject = "";

$sub_batch = in_table("batch","bc_reports","WHERE id = '{$value}'","batch");
$sub_subject = in_table("subject","bc_reports","WHERE id = '{$value}'","subject");

$result = $db->select("bc_sub_reports", "WHERE bc_report_id = '$value' {$sub_where}", "*", "ORDER BY verification_order_id ASC");
if(count_rows($result) > 0){
while($row = fetch_data($result)){
$sub_verification_type = $sub_education = $sub_source = $sub_comment = $sub_start_date = $sub_end_date = $sub_status = "";
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
redirect("{$directory}{$admin}manage-bc-reports");
}
////====================================================////////////

}
///////=====================Ends Generate Report======================/////////


$i = 0;
$act = "";

$report_id = np_input("report_id");
$informed_officer[] = "";

////////////// Add or Update Main Report //////////////////////////////
if($_SERVER["REQUEST_METHOD"] == "POST" && ((!empty($add) && !empty($sub_report)) || !empty($edit)) && !empty($main_report) && !empty($client) && !empty($batch) && !empty($subject)){

$data_array = array(
"client" => $client,
"batch" => $batch,
"subject" => $subject,
"recommendation" => $recommendation
);

if(check_admin("add_bc_reports") == 1 && !empty($add)){
$data_array += array("date_time" => $date_time, "last_update" => $date_time);
$act = $db->insert2($data_array, "bc_reports");
$report_id = in_table("id","bc_reports","WHERE subject = '$subject' AND date_time = '$date_time'","id");
}else if(check_admin("edit_bc_reports") == 1 && !empty($edit)){ 
$data_array += array("last_update" => $date_time);
$act = $db->update($data_array, "bc_reports", "id = '$edit'");
$report_id = $edit;
}

if($act){

$activity = (!empty($add))?"Added a new":"Updated a";
$activity .= " BC report for {$client_name} ({$client_email}) with subject: {$subject}, in batch {$batch}.";
$audit_data_array = array(
"user_id" => "'$id'",
"name" => "'$username'",
"email" => "'$user_email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$db->insert($audit_data_array, "audit_log");

$error = 0;

echo "<div class='success'>BC report successfully saved.</div>";
}else{
echo "<div class='not-success'>Error occured.</div>";
}

}


////////////// Add or Update Sub Report //////////////////////////////
if($_SERVER["REQUEST_METHOD"] == "POST" && (((!empty($add) || !empty($sub_add)) && !empty($_POST["execution_start_date"])) || (!empty($edit) && !empty($_POST["sub_report_id"]))) && !empty($sub_report) && !empty($report_id) && !empty($_POST["verification_type"]) && !empty($_POST["execution_end_date"]) && !empty($_POST["sub_status"])){

if(check_admin("add_bc_reports") == 1 && !empty($add)){

foreach($_POST["verification_type"] as $val){
$verification_type = test_input($val);
$verification_order_id = in_table("order_id","bc_verification_types","WHERE type = '{$verification_type}'","order_id");
$tat_days = in_table("tat","bc_verification_types","WHERE type = '{$verification_type}'","tat");
$education = test_input($_POST["education"][$i]);
$source = test_input($_POST["source"][$i]);
$comment = test_input($_POST["comment"][$i]);
$execution_start_date = test_input($_POST["execution_start_date"][$i]);
$execution_end_date = test_input($_POST["execution_end_date"][$i]);
$investigation_officer = test_input($_POST["investigation_officer"][$i]);
$assigned_agent = test_input($_POST["assigned_agent"][$i]);
$agent_start_date = test_input($_POST["agent_start_date"][$i]);
$agent_end_date = test_input($_POST["agent_end_date"][$i]);
$sub_status = test_input($_POST["sub_status"][$i]);

if(!empty($execution_start_date)){
$dat = date_create($execution_start_date);
date_add($dat, date_interval_create_from_date_string("{$tat_days} days"));
$tat = date_format($dat, "Y-m-d");
}

if(!empty($verification_type) && !empty($execution_start_date) && !empty($tat) && !empty($investigation_officer) && !empty($sub_status)){

$informed_officer[] = $investigation_officer;

$data_array = array(
"bc_report_id" => $report_id,
"verification_type" => $verification_type,
"verification_order_id" => $verification_order_id,
"education" => $education,
"source" => $source,
"comment" => $comment,
"investigation_officer" => $investigation_officer,
"start_date" => $execution_start_date,
"end_date" => $execution_end_date,
"tat" => $tat,
"assigned_agent" => $assigned_agent,
"date_sent_to_agent" => $agent_start_date,
"date_received_from_agent" => $agent_end_date,
"status" => $sub_status,
"date_time" => $date_time,
"last_update" => $date_time
); 
$act = $db->insert2($data_array, "bc_sub_reports");

///////////===========Insert Report Status==========//////////
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
$used_status .= (!empty($agent_end_date) && !empty($assigned_agent))?"Received from Agent on " . min_sub_date($agent_end_date) . ",":"";
$used_status .= ($sub_status == "COMPLETED" || !empty($execution_end_date))?"COMPLETED,":"";

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

}
$i++;
}

if(!empty($informed_officer)){

$informed_officer = array_unique($informed_officer);

///////////===========Send mail to each officer==========//////////
foreach($informed_officer as $info_value){
if(!empty($info_value)){
$informed_officer_name = $informed_officer_email = $message = "";
$informed_officer_name = in_table("name","reg_users","WHERE id = '{$info_value}'","name");
$informed_officer_email = in_table("email","reg_users","WHERE id = '{$info_value}'","email");

$subject = "New BC Task(s)";
$message = "<p>Dear {$informed_officer_name},</p><p>This is to notify you of new background checks task(s) assigned to you. Kindly log in to your account for details.</p>";
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
$act = $db->insert($admin_data_array, "admin_messages");

}
}
///////////===========Ends Send mail to each officer==========//////////

}

}else if(check_admin("edit_bc_reports") == 1 && !empty($edit)){

foreach($_POST["verification_type"] as $val){
$verification_type = test_input($val);
$verification_order_id = in_table("order_id","bc_verification_types","WHERE type = '{$verification_type}'","order_id");
$tat_days = in_table("tat","bc_verification_types","WHERE type = '{$verification_type}'","tat");
$education = test_input($_POST["education"][$i]);
$source = test_input($_POST["source"][$i]);
$comment = test_input($_POST["comment"][$i]);
$execution_end_date = test_input($_POST["execution_end_date"][$i]);
$investigation_officer = test_input($_POST["investigation_officer"][$i]);
$assigned_agent = test_input($_POST["assigned_agent"][$i]);
$agent_end_date = test_input($_POST["agent_end_date"][$i]);
$sub_status = test_input($_POST["sub_status"][$i]);
$sub_report_id = testTotal($_POST["sub_report_id"][$i]);


if(!empty($sub_report_id) && !empty($verification_type) && !empty($investigation_officer) && !empty($sub_status)){

/////////////////////////***************************************************************************************************************
$prev_investigation_officer = in_table("investigation_officer","bc_sub_reports","WHERE id = '{$sub_report_id}'","investigation_officer");
$execution_start_date = in_table("start_date","bc_sub_reports","WHERE id = '{$sub_report_id}'","start_date");
$execution_start_date = ($prev_investigation_officer != $investigation_officer)?$date:$execution_start_date;
$prev_assigned_agent = in_table("assigned_agent","bc_sub_reports","WHERE id = '{$sub_report_id}'","assigned_agent");
$prev_agent_start_date = in_table("date_sent_to_agent","bc_sub_reports","WHERE id = '{$sub_report_id}'","date_sent_to_agent");
$agent_start_date = (!empty($assigned_agent) && ($prev_assigned_agent != $assigned_agent || $prev_agent_start_date == "0000-00-00"))?$date:$prev_agent_start_date;
$agent_start_date = (empty($assigned_agent))?"0000-00-00":$agent_start_date;
////////////////////*********************------------------------------------------------------------------------------------------------

$data_array = array(
"verification_type" => $verification_type,
"verification_order_id" => $verification_order_id,
"education" => $education,
"source" => $source,
"comment" => $comment,
"investigation_officer" => $investigation_officer,
"end_date" => $execution_end_date,
"assigned_agent" => $assigned_agent,
"date_sent_to_agent" => $agent_start_date,
"date_received_from_agent" => $agent_end_date,
"status" => $sub_status,
"last_update" => $date_time
);

$act = $db->update($data_array, "bc_sub_reports", "id = '$sub_report_id' AND bc_report_id = '$report_id'");

///////////===========Insert Report Status==========//////////
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
$used_status .= (!empty($agent_end_date) && !empty($assigned_agent))?"Received from Agent on " . min_sub_date($agent_end_date) . ",":"";
$used_status .= ($sub_status == "COMPLETED")?"COMPLETED,":"";

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

}
$i++;
}

}

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

$client = in_table("client","bc_reports","WHERE id = '$report_id'","client");
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$client_email = in_table("email","reg_users","WHERE id = '{$client}'","email");
$report_subject = in_table("subject","bc_reports","WHERE id = '$report_id'","subject");

$activity = (!empty($add))?"Added new":"Updated";
$activity .= " {$i} BC verified information for {$client_name} ({$client_email}) with the subject: {$report_subject}.";
$audit_data_array = array(
"user_id" => "'$id'",
"name" => "'$username'",
"email" => "'$user_email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$db->insert($audit_data_array, "audit_log");

$error = 0;

echo (empty($main_report))?"<div class='success'>BC report successfully saved.</div>":"";
}else{
echo "<div class='not-success'>Unable to save all verification information.</div>";
}

}


if($_SERVER['REQUEST_METHOD'] == "POST" && ((!empty($add) && !empty($sub_report)) || !empty($edit)) && !empty($main_report) && (empty($client) || empty($batch) || empty($subject))){
echo "<div class='not-success'>Not submitted! All the * fields are required.</div>";
}

if($_SERVER['REQUEST_METHOD'] == "POST" && ( ((!empty($add) || !empty($sub_add)) && empty($_POST["execution_start_date"]))   || ((!empty($add) || !empty($sub_add)) || (!empty($edit) && !empty($_POST["sub_report_id"]))) && !empty($sub_report) && !empty($report_id) && (empty($_POST["verification_type"]) || empty($_POST["investigation_officer"]) || empty($_POST["sub_status"])))){
echo "<div class='not-success'>Not submitted! All the * fields are required.</div>";
}

//////////==================== Delete Main Report =====================/////////////
if(check_admin("delete_bc_reports") == 1 && isset($_POST["delete"]) && isset($_POST["del"]) && !empty($main_report)){
$i = $act = 0;
if(is_array($_POST["del"])){
foreach ($_POST["del"] as $k => $c) {
if($c != ""){ 
$c = testQty($c);
$db->delete("bc_sub_reports", "bc_report_id = '$c'");		
$act = $db->delete("bc_reports", "id = '$c'");
$i++;		
}else{
continue;
}
}

if($act && $i > 0){

$activity = "Deleted {$i} BC report(s).";
$audit_data_array = array(
"user_id" => "'$id'",
"name" => "'$username'",
"email" => "'$user_email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$db->insert($audit_data_array, "audit_log");

echo "<div class='success'>{$i} BC report(s) successfully deleted.</div>";
}else{
echo "<div class='not-success'>Error. Unable to delete BC report(s).</div>";
}

}else{
echo "<div class='not-success'>Atleast one report must be selected.</div>";
}
}

//////////==================== Delete Sub Report =====================/////////////
if(check_admin("delete_bc_reports") == 1 && !empty($sub_delete)){

$act = $db->delete("bc_sub_reports", "id = '$sub_delete'");		

if($act){

$activity = "Deleted BC verified information #{$sub_delete}.";
$audit_data_array = array(
"user_id" => "'$id'",
"name" => "'$username'",
"email" => "'$user_email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$db->insert($audit_data_array, "audit_log");

echo "<div class='success'>BC verified information #{$sub_delete} successfully deleted.</div>";
}else{
echo "<div class='not-success'>Error. Unable to delete BC verified information #{$sub_delete}.</div>";
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
$where .= (!empty($keyword))?" AND subject LIKE '%{$keyword}%'":"";
$where .= (!empty($reference_code))?" AND id = '{$reference_code}'":"";
$where .= (!empty($search_batch))?" AND batch = '{$search_batch}'":"";
$where .= (!empty($search_start_date))?" AND start_date >= '{$search_start_date} 00:00:00'":"";
$where .= (!empty($search_end_date))?" AND end_date <= '{$search_end_date} 23:59:59'":"";
$where .= (!empty($search_status))?" AND status = '{$search_status}'":"";

$result = $db->select("bc_reports", "$where", "*", "ORDER BY id DESC");

$per_view = 20;
$per_view = (!empty($no_of_rows))?$no_of_rows:$per_view;
$page_link = "{$admin}manage-bc-reports?pn=";
$link_suffix = "";
$style_class = "general-link";
page_numbers();

if(isset($_SESSION["msg"]) && !empty($_SESSION["msg"]) && empty($extract)){
echo "<iframe src=\"{$admin}download-report?file=" . $_SESSION["msg"] . "\" style=\"width:1px; height:1px;\"></iframe>";
unset($_SESSION["msg"]);
}

if(empty($view) && (empty($edit)||(!empty($edit)&&$error==0)) && (empty($add)||(!empty($add)&&$error==0)) ){
?>

<div class="page-title">Manage Background Checks Reports <a href="<?php echo $admin; ?>manage-bc-reports?add=1" class="btn gen-btn general-link float-right">New BC Report</a></div>

<form action="<?php echo $admin; ?>manage-bc-reports" name="search_form" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
<input type="hidden" name="gh" value="1"> 
<div class="search-dates">

<div class="col-md-3">
<label for="search_client">Client</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
<select name="search_client" id="search_client" title="Select a client" class="form-control js-example-basic-single" style="width:100%">
<option value="">**Select a client**</option>
<?php 
$result2 = $db->select("bc_reports", "", "DISTINCT client", "ORDER BY client ASC");
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
$result2 = $db->select("bc_sub_reports", "", "DISTINCT status", "ORDER BY status ASC");
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

<?php if(check_admin("download_bc_reports") == 1){ ?>
<div class="col-md-2 col-xs-6">
<br />
<a class="btn gen-btn download-report"><i class="fa fa-download"></i> Download</a>
</div>
<?php } ?>

</div>
</form>

<form action="<?php echo $admin; ?>manage-bc-reports" name="extract_form" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
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

$result = $db->select("bc_reports", "$where", "*", "ORDER BY id DESC", "LIMIT {$offset},{$per_view}");

if(count_rows($result) > 0){
?>
<form action="<?php echo $admin; ?>manage-bc-reports" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data" style="overflow-x:auto;">
<input type="hidden" name="pn" value="<?php echo $pn; ?>"> 
<input type="hidden" name="gh" value="1"> 
<input type="hidden" name="delete" value="1"> 
<input type="hidden" name="main_report" value="1"> 
<table class="table table-striped table-hover">
<thead>
<tr class="gen-title">
<th>Ref. Code</th>
<th>Client</th>
<th style="width:50px;">Batch</th>
<th>Subject</th>
<th>Execution Period</th>
<th>Date Completed</th>
<th>Verified Info.</th>
<th>Officer Assignment</th>
<th>Days Remaining</th>
<?php if(check_admin("edit_bc_reports") == 1){ ?>
<th>Action</th>
<?php } ?>
<th>Details</th>
<?php if(check_admin("print_bc_reports") == 1){ ?>
<th>Print</th>
<?php } 
if(check_admin("delete_bc_reports") == 1){ ?>
<th style="width:30px;"><input type="checkbox" name="sel_all" id="delG" class="sel-group" value=""></th>
<?php } ?>
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
$subject = $row["subject"];
$start_date = ($row["start_date"] != "0000-00-00")?min_sub_date($row["start_date"]):"";
$end_date = ($row["end_date"] != "0000-00-00")?min_sub_date($row["end_date"]):"";
$tat = ($row["tat"] != "0000-00-00")?min_sub_date($row["tat"]):"";
$investigation_officer = (!empty($row["investigation_officer"]))?"Assigned":"Not Assigned";
$status = $row["status"];
$days_remaining = days_remaining($row["tat"],"1",$status);
$sub_reports = in_table("COUNT(id) AS Total","bc_sub_reports","WHERE bc_report_id = '{$report_id}'","Total");
$cols = 0;
?>
<tr>
<td style="width:30px;"><?php echo $report_id; ?></td>
<td><?php echo "{$client_name}<br>({$client_email})"; ?></td>
<td><?php echo formatQty($batch); ?></td>
<td><?php echo $subject; ?></td>
<td><?php echo $start_date . " - " . $tat; ?></td>
<td><?php echo $end_date; ?></td>
<td><?php echo formatQty($sub_reports); ?></td>
<td><?php echo $investigation_officer; ?></td>
<td><?php echo $days_remaining; ?></td>
<?php if(check_admin("edit_bc_reports") == 1){ $cols++; ?>
<td style="width:70px;"><a href="<?php echo $admin; ?>manage-bc-reports?edit=<?php echo $report_id; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link" title="Edit BC report #<?php echo $report_id; ?>"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></td>
<?php } ?>
<td style="width:70px;"><a href="<?php echo $admin; ?>manage-bc-reports?view=<?php echo $report_id; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link" title="View BC report #<?php echo $report_id; ?>"><i class="fa fa-eye" aria-hidden="true"></i> View</a></td>
<?php if(check_admin("print_bc_reports") == 1){ $cols++; ?>
<td style="width:70px;"><a href="<?php echo $admin; ?>print-report?view=<?php echo $report_id; ?>" class="btn gen-btn" target="_blank" title="Print BC report #<?php echo $report_id; ?>"><i class="fa fa-print" aria-hidden="true"></i> Print</a></td>
<?php } 
if(check_admin("delete_bc_reports") == 1){ $cols++; ?>
<td style="width:30px;"><input type="checkbox" name="del[<?php echo $d; ?>]" id="del<?php echo $d; ?>" class="delG" value="<?php echo $report_id; ?>"></td>
<?php } ?>
</tr>
<?php 
$d++;
}
?>
<?php if(check_admin("delete_bc_reports") == 1){ ?>
<tr><td colspan="<?php echo 10 + $cols; ?>"><input class="sub-del" type="submit" value=" "><button type="button" class="btn del-btn gen-btn float-right"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete selected BC report(s)</button></td></tr>
<?php } ?>
</tbody>
</table>
</form>
<?php
echo ($last_page>1)?"<div class=\"page-nos\">" . $center_pages . "</div>":"";
}else{
echo "<div class='not-success'>No Background Checks reports found at the moment.</div>";
}

}

/////============View Report=============////
if(!empty($view) && (empty($edit) || (!empty($edit) && $error == 0))){
$result = $db->select("bc_reports", "WHERE id='$view'", "*");

if(count_rows($result) == 1){
$row = fetch_data($result);
$client = $row["client"];
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$client_email = in_table("email","reg_users","WHERE id = '{$client}'","email");
$batch = $row["batch"];
$subject = $row["subject"];
$recommendation = $row["recommendation"];
$start_date = ($row["start_date"] != "0000-00-00")?min_sub_date($row["start_date"]):"";
$end_date = ($row["end_date"] != "0000-00-00")?min_sub_date($row["end_date"]):"";
$tat = ($row["tat"] != "0000-00-00")?min_sub_date($row["tat"]):"";
$status = $row["status"];
$days_remaining = days_remaining($row["tat"],"",$status);
$status_text = ($status == "COMPLETED")?"Fully Completed":"Not Fully Completed";
$status_div = ($status == "COMPLETED")?"primary":"danger";
$sub_reports = in_table("COUNT(id) AS Total","bc_sub_reports","WHERE bc_report_id = '{$view}'","Total");
$report_date_time = full_date($row["date_time"]);
?>

<div class="reply-content-wrapper ">

<div class="back"><a href="<?php echo $admin; ?>manage-bc-reports?pn=<?php echo $pn; ?>" class="btn gen-btn general-link"><i class="fa fa-arrow-left"></i> Back to BC reports</a></div>

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
<?php if(!empty($recommendation)){ ?>
<b>Recommendation:</b> <?php echo $recommendation; ?> <br /> <br /> 
<?php } ?>
<b>Start Date:</b> <?php echo $start_date; ?> <br /> <br /> 
<b>End Date:</b> <?php echo $end_date; ?> <br /> <br /> 
<b>Status:</b> <?php echo (!empty($status))?"<div class=\"btn btn-{$status_div}\">{$status_text}</div>":"<div class=\"btn btn-danger\">{$status_text}</div>"; ?> <br />

<?php if(check_admin("edit_bc_reports") == 1){ ?>
<div><a href="<?php echo $admin; ?>manage-bc-reports?edit=<?php echo $view; ?>&pn=<?php echo $pn; ?>&main_edit=1" class="btn gen-btn general-link float-right" title="Edit this part"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></div>
<?php } ?>

<?php 
$result = $db->select("bc_sub_reports", "WHERE bc_report_id='{$view}'", "*", "ORDER BY verification_order_id ASC");
if(count_rows($result) > 0){ 
?>

<div class="body-header"><i class="fa fa-tag" aria-hidden="true"></i> BC Verified Information (<?php echo formatQty($sub_reports); ?>)</div>

<table class="table table-striped table-hover">
<thead>
<tr>
<th style="width:30px;">#ID</th>
<th>Details</th>
<th>Days Remaining</th>
<?php if(check_admin("edit_bc_reports") == 1){ ?>
<th>Option</th>
<?php } if(check_admin("delete_bc_reports") == 1){ ?>
<th>Delete</th>
<?php } ?>
</tr>
</thead>
<tbody>
<?php
while($row = fetch_data($result)){
$sub_report_id = $row["id"];
$verification_type = $row["verification_type"];
$education = $row["education"];
$source = $row["source"];
$comment = $row["comment"];
$start_date = ($row["start_date"] != "0000-00-00")?min_sub_date($row["start_date"]):"";
$end_date = ($row["end_date"] != "0000-00-00")?min_sub_date($row["end_date"]):"";
$tat = ($row["tat"] != "0000-00-00")?min_sub_date($row["tat"]):"";
$investigation_officer = $row["investigation_officer"];
$investigation_officer_name = (!empty($investigation_officer))?in_table("name","reg_users","WHERE id = '{$investigation_officer}'","name"):"";
$investigation_officer_email = (!empty($investigation_officer))?in_table("email","reg_users","WHERE id = '{$investigation_officer}'","email"):"";
$investigation_officer = (!empty($investigation_officer))?"{$investigation_officer_name} ({$investigation_officer_email})":"";
$assigned_agent = $row["assigned_agent"];
$assigned_agent_name = (!empty($assigned_agent))?in_table("name","reg_users","WHERE id = '{$assigned_agent}'","name"):"";
$assigned_agent_email = (!empty($assigned_agent))?in_table("email","reg_users","WHERE id = '{$assigned_agent}'","email"):"";
$assigned_agent = (!empty($assigned_agent))?"{$assigned_agent_name} ({$assigned_agent_email})":"";
$date_sent_to_agent = ($row["date_sent_to_agent"] != "0000-00-00")?min_sub_date($row["date_sent_to_agent"]):"";
$date_received_from_agent = ($row["date_received_from_agent"] != "0000-00-00")?min_sub_date($row["date_received_from_agent"]):"";
$sub_status = $row["status"];
$sub_days_remaining = days_remaining($row["tat"],"1",$sub_status);
?>

<tr>
<th><?php echo $sub_report_id; ?></th>
<td>

<table class="table">
<tr class="gen-title"><th>Completion Date</th><th>Verification Type</th><th>Source</th><th>Comment</th></tr>
<tr><td><?php echo $end_date; ?></td><th><?php echo "<u>" . strtoupper($verification_type) . "</u> " . $education; ?></th><td><?php echo $source; ?></td><td><?php echo $comment; ?></td></tr>
<tr class="gen-title"><th>Investigation Officer</th><th>Execution Period</th><th>Assigned Agent</th><th>Agent Execution Period</th></tr>
<tr><td><?php echo $investigation_officer; ?></td><td><?php echo "{$start_date} - {$tat}"; ?></td><td><?php echo $assigned_agent; ?></td><td><?php echo "{$date_sent_to_agent} - {$date_received_from_agent}"; ?></td></tr>
</table>

</td>

<td><?php echo $sub_days_remaining; ?></td>
<?php if(check_admin("edit_bc_reports") == 1){ ?>
<td><a href="<?php echo $admin; ?>manage-bc-reports?edit=<?php echo $view; ?>&pn=<?php echo $pn; ?>&sub_edit=<?php echo $sub_report_id; ?>" class="btn gen-btn general-link" title="Edit this verified information #<?php echo $sub_report_id; ?>"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></td>
<?php } if(check_admin("delete_bc_reports") == 1){ ?>
<td><a href="<?php echo $admin; ?>manage-bc-reports?view=<?php echo $view; ?>&pn=<?php echo $pn; ?>&sub_delete=<?php echo $sub_report_id; ?>" class="btn gen-btn general-link" title="Delete verified information #<?php echo $sub_report_id; ?>"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a></td>
<?php } ?>
</tr>
<?php
}
?>
</tbody>
</table>
<?php
}
?>

<?php if(check_admin("add_bc_reports") == 1){ ?>
<div><a href="<?php echo $admin; ?>manage-bc-reports?add=1&sub_add=<?php echo $view; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link float-left" title="Add new verification information to report #<?php echo $view; ?>"><i class="fa fa-upload" aria-hidden="true"></i> New info. to report</a></div>
<?php } ?>

</div>

</div>
</div>

<?php
}else{
echo "<div class='not-success'>This Background Checks report does not exist.</div>";
}
}

////==============Edit Report=============//////
if(((check_admin("add_bc_reports") == 1 && !empty($add)) || (check_admin("edit_bc_reports") == 1 && !empty($edit))) && $error == 1){

$client = $batch = $subject = "";
if(!empty($edit)){
$result = $db->select("bc_reports", "WHERE id='$edit'", "*", "");
if(count_rows($result) == 1){
$row = fetch_data($result);
$client = $row["client"];
$batch = $row["batch"];
$subject = $row["subject"];
$recommendation = $row["recommendation"];
}
}

$c = 0;

$field_name = (!empty($edit))?"edit":"add";
$field_value = (!empty($edit))?$edit:1;
$action_title = (!empty($edit))?"Edit":"Add New";
?>

<div class="back">

<?php if(!empty($add)){ ?>
<a href="<?php echo $admin; ?>manage-bc-reports?pn=<?php echo $pn; ?>" class="btn gen-btn general-link"><i class="fa fa-arrow-left"></i> Back to BC reports</a>
<?php }else if(empty($sub_edit) || empty($sub_add)){ 
$used_report_id_back = (!empty($edit))?$edit:$sub_add;
?>
<a href="<?php echo $admin; ?>manage-bc-reports?view=<?php echo $used_report_id_back; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link" title="View BC report #<?php echo $used_report_id_back; ?>"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to BC reports view</a>
<?php }else{ ?>
<a href="<?php echo $admin; ?>manage-bc-reports?pn=<?php echo $pn; ?>" class="btn gen-btn general-link"><i class="fa fa-arrow-left"></i> Back to BC reports</a>
<?php } ?>

</div>

<div class="page-title"><?php echo $action_title; ?> Background Checks report</div>

<form action="<?php echo $admin; ?>manage-bc-reports" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data" style="border:1px solid #eee;">
<input type="hidden" name="gh" value="1"> 
<input type="hidden" name="<?php echo $field_name; ?>" value="<?php echo $field_value; ?>"> 
<?php if(!empty($edit)){ ?>
<input type="hidden" name="pn" value="<?php echo $pn; ?>"> 
<?php
}

if(!empty($edit) || !empty($sub_add)){ 
$used_edit = (!empty($edit))?$edit:$sub_add;
?>
<input type="hidden" name="view" value="<?php echo $used_edit; ?>"> 
<?php
}

if(empty($sub_edit) && empty($sub_add)){ ?>

<input type="hidden" name="main_report" value="1"> 

<?php if(!empty($main_edit)){ ?>
<input type="hidden" name="main_edit" value="1"> 
<?php } ?>

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

<div class="col-sm-12">
<label for="subject">Subject*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-file-text"></i></span>
<input type="text" name="subject" id="subject" class="form-control" placeholder="Subject of the report" value="<?php check_inputted("subject", $subject); ?>" required>
</div>
</div>

<div class="col-sm-12">
<label for="recommendation">Recommendation</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-file-text"></i></span>
<select name="recommendation" id="recommendation" title="Select an option" class="form-control">
<option value="">**Select an option**</option>
<?php 
$result2 = $db->select("recommendation_types", "", "*", "ORDER BY id ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$recommendation_type = $row2["type"];
echo "<option value='{$recommendation_type}'";
check_selected("recommendation", $recommendation_type, $recommendation);
echo ">{$recommendation_type}</option>";
}
}
?>
</select>
</div>
</div>

<?php } ?>

<input type="hidden" name="sub_report" value="1"> 

<?php if(empty($main_edit)){ ?>

<?php if(!empty($sub_edit)){ ?>
<input type="hidden" name="sub_edit" value="1"> 
<input type="hidden" name="report_id" value="<?php echo $edit; ?>"> 
<?php } ?>

<?php if(!empty($sub_add)){ ?>
<input type="hidden" name="sub_add" value="1"> 
<input type="hidden" name="report_id" value="<?php echo $sub_add; ?>"> 
<?php } ?>

<div class="body-header col-sm-12"><i class="fa fa-tag" aria-hidden="true"></i> <?php echo $action_title; ?> Verification Grouping</div>

<?php if(!empty($sub_add) || !empty($edit)){ 
$used_report_id = (!empty($edit))?$edit:$sub_add;

$client = in_table("client","bc_reports","WHERE id = '$used_report_id'","client");
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$client_email = in_table("email","reg_users","WHERE id = '{$client}'","email");
$report_subject = in_table("subject","bc_reports","WHERE id = '$used_report_id'","subject");
?>
<div class="sub-report-title col-sm-12 align-center"><b>Subject:</b> <?php echo $report_subject; ?> &nbsp;&nbsp;&nbsp; <b>Client:</b> <?php echo "{$client_name} ({$client_email})"; ?><br /><br /></div>
<?php } ?>

<table class="table table-striped table-hover sub-cat-table">

<tr><th style="width:30px">S/N</th><th>Details</th><th style="width:30px"><i class="fa fa-minus"></i></th></tr>

<?php 
//////======= Add Sub Plan =======/////
if(!empty($add)){ 
if(isset($_POST["verification_type"])){ 
foreach($_POST["verification_type"] as $val){
?>

<tr class="sub-row" id="row<?php echo $c + 1; ?>"><th class="sub-cell" id="td<?php echo $c + 1; ?>"><?php echo $c + 1; ?></th><td>

<table class="table">
<tr class="gen-title">
<th>Verification Type*</th>
<th>Education</th>
<th>Source</th>
<th>Comment</th>
</tr>
<tr><td>
<select name="verification_type[]" id="verification_type" title="Select a verification type" class="form-control" required>
<option value="">**Verification type**</option>
<?php 
$result2 = $db->select("bc_verification_types", "", "DISTINCT *", "ORDER BY type ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$verification_type = $row2["type"];
echo "<option value='{$verification_type}'";
check_selected2($val, $verification_type); 
echo ">{$verification_type}</option>";
}
}
?>
</select>
</td><td>
<select name="education[]" id="education" title="Select an education type" class="form-control">
<option value="">**Education**</option>
<?php 
$result2 = $db->select("bc_education_types", "", "DISTINCT *", "ORDER BY type ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$education_type = $row2["type"];
echo "<option value='{$education_type}'";
check_selected2($_POST["education"][$c], $education_type); 
echo ">{$education_type}</option>";
}
}
?>
</select>
</td><td>
<textarea rows="3" name="source[]" id="source" class="form-control" placeholder="Source"><?php echo $_POST["source"][$c]; ?></textarea>
</td><td>
<textarea rows="3" name="comment[]" id="comment" class="form-control" placeholder="Comment"><?php echo $_POST["comment"][$c]; ?></textarea>
</td></tr>

<tr class="gen-title">
<th colspan="2">Investigation Officer*</th>
<th>Execution Start Date*</th>
<th>Execution End Date</th>
</tr>
<tr><td colspan="2">
<select name="investigation_officer[]" id="investigation_officer" title="Select an investigation officer" class="form-control" required>
<option value="">**Select an investigation officer**</option>
<?php 
$result2 = $db->select("reg_users", "WHERE admin = '1'", "DISTINCT *", "ORDER BY id ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$admin_id = $row2["id"];
$admin_name = $row2["name"];
$admin_email = $row2["email"];
echo "<option value='{$admin_id}'";
check_selected2($_POST["investigation_officer"][$c], $admin_id); 
echo ">{$admin_name} ({$admin_email})</option>";
}
}
?>
</select>
</td><td>
<input type="text" name="execution_start_date[]" id="execution_start_date" onfocus="javascript: $(this).blur();" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php echo $_POST["execution_start_date"][$c]; ?>" required>
</td><td>
<input type="text" name="execution_end_date[]" id="execution_end_date" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php echo $_POST["execution_end_date"][$c]; ?>">
</td></tr>

<tr class="gen-title">
<th>Assigned Agent</th>
<th>Date Sent to Agent</th>
<th>Date Received from Agent</th>
<th>Status*</th>
</tr>
<tr><td>
<select name="assigned_agent[]" id="assigned_agent" title="Select an agent" class="form-control">
<option value="">**Select an agent**</option>
<?php 
$result2 = $db->select("reg_users", "WHERE agent = '1'", "DISTINCT *", "ORDER BY id ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$agent_id = $row2["id"];
$agent_name = $row2["name"];
$agent_email = $row2["email"];
echo "<option value='{$agent_id}'";
check_selected2($_POST["assigned_agent"][$c], $agent_id); 
echo ">{$agent_name} ({$agent_email})</option>";
}
}
?>
</select>
</td><td>
<input type="text" name="agent_start_date[]" id="agent_start_date" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php echo $_POST["agent_start_date"][$c]; ?>">
</td><td>
<input type="text" name="agent_end_date[]" id="agent_end_date" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php echo $_POST["agent_end_date"][$c]; ?>">
</td><td>
<select name="sub_status[]" id="sub_status" title="Select a status" class="form-control" required>
<option value="">**Select a status**</option>
<?php 
$result2 = $db->select("status_types", "", "DISTINCT *", "ORDER BY id ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$sub_type = $row2["type"];
echo "<option value='{$sub_type}'";
check_selected2($_POST["sub_status"][$c], $sub_type); 
echo ">{$sub_type}</option>";
}
}
?>
</select>
</td></tr>
</table>

</td><td><?php if($c > 0){ ?><button type="button" class="btn gen-btn del-sub-cat" lang="row<?php echo $c + 1; ?>" onclick="javascript: delete_sub(this.lang);"><i class="fa fa-minus"></i></button><?php } ?></td></tr>

<?php 
$c++;
}
}else{
?>

<tr class="sub-row" id="row1"><th class="sub-cell" id="td1">1</th><td>

<table class="table">
<tr class="gen-title">
<th>Verification Type*</th>
<th>Education</th>
<th>Source</th>
<th>Comment</th>
</tr>
<tr><td>
<select name="verification_type[]" id="verification_type" title="Select a verification type" class="form-control" required>
<option value="">**Verification type**</option>
<?php 
$result2 = $db->select("bc_verification_types", "", "DISTINCT *", "ORDER BY type ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$verification_type = $row2["type"];
echo "<option value='{$verification_type}'>{$verification_type}</option>";
}
}
?>
</select>
</td><td>
<select name="education[]" id="education" title="Select an education type" class="form-control">
<option value="">**Education**</option>
<?php 
$result2 = $db->select("bc_education_types", "", "DISTINCT *", "ORDER BY type ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$education_type = $row2["type"];
echo "<option value='{$education_type}'>{$education_type}</option>";
}
}
?>
</select>
</td><td>
<textarea rows="3" name="source[]" id="source" class="form-control" placeholder="Source"></textarea>
</td><td>
<textarea rows="3" name="comment[]" id="comment" class="form-control" placeholder="Comment"></textarea>
</td></tr>

<tr class="gen-title">
<th colspan="2">Investigation Officer*</th>
<th>Execution Start Date*</th>
<th>Execution End Date</th>
</tr>
<tr><td colspan="2">
<select name="investigation_officer[]" id="investigation_officer" title="Select an investigation officer" class="form-control" required>
<option value="">**Select an investigation officer**</option>
<?php 
$result2 = $db->select("reg_users", "WHERE admin = '1'", "DISTINCT *", "ORDER BY id ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$admin_id = $row2["id"];
$admin_name = $row2["name"];
$admin_email = $row2["email"];
echo "<option value='{$admin_id}'>{$admin_name} ({$admin_email})</option>";
}
}
?>
</select>
</td><td>
<input type="text" name="execution_start_date[]" id="execution_start_date" onfocus="javascript: $(this).blur();" class="form-control gen-date" placeholder="YYYY-MM-DD" value="" required>
</td><td>
<input type="text" name="execution_end_date[]" id="execution_end_date" class="form-control gen-date" placeholder="YYYY-MM-DD" value="">
</td></tr>

<tr class="gen-title">
<th>Assigned Agent</th>
<th>Date Sent to Agent</th>
<th>Date Received from Agent</th>
<th>Status*</th>
</tr>
<tr><td>
<select name="assigned_agent[]" id="assigned_agent" title="Select an agent" class="form-control">
<option value="">**Select an agent**</option>
<?php 
$result2 = $db->select("reg_users", "WHERE agent = '1'", "DISTINCT *", "ORDER BY id ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$agent_id = $row2["id"];
$agent_name = $row2["name"];
$agent_email = $row2["email"];
echo "<option value='{$agent_id}'>{$agent_name} ({$agent_email})</option>";
}
}
?>
</select>
</td><td>
<input type="text" name="agent_start_date[]" id="agent_start_date" class="form-control gen-date" placeholder="YYYY-MM-DD" value="">
</td><td>
<input type="text" name="agent_end_date[]" id="agent_end_date" class="form-control gen-date" placeholder="YYYY-MM-DD" value="">
</td><td>
<select name="sub_status[]" id="sub_status" title="Select a status" class="form-control" required>
<option value="">**Select a status**</option>
<?php 
$result2 = $db->select("status_types", "", "DISTINCT *", "ORDER BY id ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$sub_type = $row2["type"];
echo "<option value='{$sub_type}'>{$sub_type}</option>";
}
}
?>
</select>
</td></tr>
</table>

</td><td></td></tr>

<?php 
$c++;
}

} 

//////======= Edit Sub Plan =======/////
if(!empty($edit)){ 
if(isset($_POST["verification_type"])){ 
foreach($_POST["verification_type"] as $val){
?>

<tr><th><?php echo $c + 1; ?></th><td>

<table class="table">
<tr class="gen-title">
<th>Verification Type*</th>
<th>Education</th>
<th>Source</th>
<th>Comment</th>
</tr>

<tr><td>
<input type="hidden" name="sub_report_id[]" value="<?php echo $_POST["sub_report_id"][$c]; ?>" />
<select name="verification_type[]" id="verification_type" title="Select a verification type" class="form-control" required>
<option value="">**Verification type**</option>
<?php 
$result2 = $db->select("bc_verification_types", "", "DISTINCT *", "ORDER BY type ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$verification_type = $row2["type"];
echo "<option value='{$verification_type}'";
check_selected2($val, $verification_type); 
echo ">{$verification_type}</option>";
}
}
?>
</select>
</td><td>
<select name="education[]" id="education" title="Select an education type" class="form-control">
<option value="">**Education**</option>
<?php 
$result2 = $db->select("bc_education_types", "", "DISTINCT *", "ORDER BY type ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$education_type = $row2["type"];
echo "<option value='{$education_type}'";
check_selected2($_POST["education"][$c], $education_type); 
echo ">{$education_type}</option>";
}
}
?>
</select>
</td><td>
<textarea rows="3" name="source[]" id="source" class="form-control" placeholder="Source"><?php echo $_POST["source"][$c]; ?></textarea>
</td><td>
<textarea rows="3" name="comment[]" id="comment" class="form-control" placeholder="Comment"><?php echo $_POST["comment"][$c]; ?></textarea>
</td></tr>

<tr class="gen-title">
<th colspan="2">Investigation Officer*</th>
<th>Execution End Date</th>
</tr>
<tr><td colspan="2">
<select name="investigation_officer[]" id="investigation_officer" title="Select an investigation officer" class="form-control" required>
<option value="">**Select an investigation officer**</option>
<?php 
$result2 = $db->select("reg_users", "WHERE admin = '1'", "DISTINCT *", "ORDER BY id ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$admin_id = $row2["id"];
$admin_name = $row2["name"];
$admin_email = $row2["email"];
echo "<option value='{$admin_id}'";
check_selected2($_POST["investigation_officer"][$c], $admin_id); 
echo ">{$admin_name} ({$admin_email})</option>";
}
}
?>
</select>
</td><td>
<input type="text" name="execution_end_date[]" id="execution_end_date" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php echo $_POST["execution_end_date"][$c]; ?>">
</td></tr>

<tr class="gen-title">
<th>Assigned Agent</th>
<th>Date Received from Agent</th>
<th>Status*</th>
</tr>
<tr><td>
<select name="assigned_agent[]" id="assigned_agent" title="Select an agent" class="form-control">
<option value="">**Select an agent**</option>
<?php 
$result2 = $db->select("reg_users", "WHERE agent = '1'", "DISTINCT *", "ORDER BY id ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$agent_id = $row2["id"];
$agent_name = $row2["name"];
$agent_email = $row2["email"];
echo "<option value='{$agent_id}'";
check_selected2($_POST["assigned_agent"][$c], $agent_id); 
echo ">{$agent_name} ({$agent_email})</option>";
}
}
?>
</select>
</td><td>
<input type="text" name="agent_end_date[]" id="agent_end_date" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php echo $_POST["agent_end_date"][$c]; ?>">
</td><td>
<select name="sub_status[]" id="sub_status" title="Select a status" class="form-control" required>
<option value="">**Select a status**</option>
<?php 
$result2 = $db->select("status_types", "", "DISTINCT *", "ORDER BY id ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$sub_type = $row2["type"];
echo "<option value='{$sub_type}'";
check_selected2($_POST["sub_status"][$c], $sub_type); 
echo ">{$sub_type}</option>";
}
}
?>
</select>
</td></tr>
</table>

</td><td></td></tr>

<?php 
$c++;
}
}else{
$where = (!empty($sub_edit))?" AND id = '$sub_edit'":"";
$result = $db->select("bc_sub_reports", "WHERE bc_report_id = '$edit' $where", "*", "ORDER BY id ASC");
if(count_rows($result) > 0){
while($row = fetch_data($result)){
$sub_report_id = $row["id"];
$verification_type = $row["verification_type"];
$education = $row["education"];
$source = $row["source"];
$comment = $row["comment"];
$execution_end_date = ($row["end_date"] != "0000-00-00")?$row["end_date"]:"";
$investigation_officer = $row["investigation_officer"];
$assigned_agent = $row["assigned_agent"];
$agent_end_date = ($row["date_received_from_agent"] != "0000-00-00")?$row["date_received_from_agent"]:"";
$sub_status = $row["status"];
?>

<tr><th><?php echo $c + 1; ?></th><td>

<table class="table">
<tr class="gen-title">
<th>Verification Type*</th>
<th>Education</th>
<th>Source</th>
<th>Comment</th>
</tr>

<tr><td>
<input type="hidden" name="sub_report_id[]" value="<?php echo $sub_report_id; ?>" />
<select name="verification_type[]" id="verification_type" title="Select a verification type" class="form-control" required>
<option value="">**Verification type**</option>
<?php 
$result2 = $db->select("bc_verification_types", "", "DISTINCT *", "ORDER BY type ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$verification_type2 = $row2["type"];
echo "<option value='{$verification_type2}'";
check_selected2($verification_type, $verification_type2); 
echo ">{$verification_type2}</option>";
}
}
?>
</select>
</td><td>
<select name="education[]" id="education" title="Select an education type" class="form-control">
<option value="">**Education**</option>
<?php 
$result2 = $db->select("bc_education_types", "", "DISTINCT *", "ORDER BY type ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$education_type = $row2["type"];
echo "<option value='{$education_type}'";
check_selected2($education, $education_type); 
echo ">{$education_type}</option>";
}
}
?>
</select>
</td><td>
<textarea rows="3" name="source[]" id="source" class="form-control" placeholder="Source"><?php echo $source; ?></textarea>
</td><td>
<textarea rows="3" name="comment[]" id="comment" class="form-control" placeholder="Comment"><?php echo $comment; ?></textarea>
</td></tr>

<tr class="gen-title">
<th colspan="3">Investigation Officer*</th>
<th>Execution End Date</th>
</tr>
<tr><td colspan="3">
<select name="investigation_officer[]" id="investigation_officer" title="Select an investigation officer" class="form-control" required>
<option value="">**Select an investigation officer**</option>
<?php 
$result2 = $db->select("reg_users", "WHERE admin = '1'", "DISTINCT *", "ORDER BY id ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$admin_id = $row2["id"];
$admin_name = $row2["name"];
$admin_email = $row2["email"];
echo "<option value='{$admin_id}'";
check_selected2($investigation_officer, $admin_id); 
echo ">{$admin_name} ({$admin_email})</option>";
}
}
?>
</select>
</td><td>
<input type="text" name="execution_end_date[]" id="execution_end_date<?php echo $sub_report_id; ?>" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php echo $execution_end_date; ?>">
</td></tr>

<tr class="gen-title">
<th colspan="2">Assigned Agent</th>
<th>Date Received from Agent</th>
<th>Status*</th>
</tr>
<tr><td colspan="2">
<select name="assigned_agent[]" id="assigned_agent" title="Select an agent" class="form-control">
<option value="">**Select an agent**</option>
<?php 
$result2 = $db->select("reg_users", "WHERE agent = '1'", "DISTINCT *", "ORDER BY id ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$agent_id = $row2["id"];
$agent_name = $row2["name"];
$agent_email = $row2["email"];
echo "<option value='{$agent_id}'";
check_selected2($assigned_agent, $agent_id); 
echo ">{$agent_name} ({$agent_email})</option>";
}
}
?>
</select>
</td><td>
<input type="text" name="agent_end_date[]" id="agent_end_date<?php echo $sub_report_id; ?>" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php echo $agent_end_date; ?>">
</td><td>
<select name="sub_status[]" id="sub_status" title="Select a status" class="form-control" required>
<option value="">**Select a status**</option>
<?php 
$result2 = $db->select("status_types", "", "DISTINCT *", "ORDER BY id ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$sub_type = $row2["type"];
echo "<option value='{$sub_type}'";
check_selected2($sub_status, $sub_type); 
echo ">{$sub_type}</option>";
}
}
?>
</select>
</td></tr>
</table>

</td><td></td></tr>

<?php 
$c++;
}
}
}

}
?>

</table>

<?php } ?>
                     
<div class="submit-div col-sm-12">
<?php if(!empty($add)){ ?>
<button type="button" class="btn add-new-sub gen-btn float-left"><i class="fa fa-plus"></i> Add</button>
<?php } ?>

<button class="btn gen-btn float-right" name="update"><i class="fa fa-upload"></i> Save</button>
</div>

</form>

<div class="embed-script"></div>

<script>
<!--
var c = <?php echo $c; ?>;
var date_field = c;

$(".add-new-sub").click(function(){

c++;
date_field++;

$(".sub-cat-table").append("<tr class=\"sub-row\" id=\"row" + c + "\"><th class=\"sub-cell\" id=\"td" + c + "\">" + c + "</th><td><table class=\"table\"><tr class=\"gen-title\"><th>Verification Type*</th><th>Education</th><th>Source</th><th>Comment</th></tr><tr><td><select name=\"verification_type[]\" id=\"verification_type\" title=\"Select a verification type\" class=\"form-control\" required><option value=\"\">**Verification type**</option><?php 
$result2 = $db->select("bc_verification_types", "", "DISTINCT *", "ORDER BY type ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$verification_type = $row2["type"];
echo "<option value='{$verification_type}'>{$verification_type}</option>";
}
}
?></select></td><td><select name=\"education[]\" id=\"education\" title=\"Select an education type\" class=\"form-control\"><option value=\"\">**Education**</option><?php 
$result2 = $db->select("bc_education_types", "", "DISTINCT *", "ORDER BY type ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$education_type = $row2["type"];
echo "<option value='{$education_type}'>{$education_type}</option>";
}
}
?></select></td><td><textarea rows=\"3\" name=\"source[]\" id=\"source\" class=\"form-control\" placeholder=\"Source\"></textarea></td><td><textarea rows=\"3\" name=\"comment[]\" id=\"comment\" class=\"form-control\" placeholder=\"Comment\"></textarea></td></tr><tr class=\"gen-title\"><th colspan=\"2\">Investigation Officer*</th><th>Execution Start Date*</th><th>Execution End Date</th></tr><tr><td colspan=\"2\"><select name=\"investigation_officer[]\" id=\"investigation_officer\" title=\"Select an investigation officer\" class=\"form-control\" required><option value=\"\">**Select an investigation officer**</option><?php 
$result2 = $db->select("reg_users", "WHERE admin = '1'", "DISTINCT *", "ORDER BY id ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$admin_id = $row2["id"];
$admin_name = $row2["name"];
$admin_email = $row2["email"];
echo "<option value='{$admin_id}'>{$admin_name} ({$admin_email})</option>";
}
}
?></select></td><td><input type=\"text\" name=\"execution_start_date[]\" id=\"execution-start-date" + date_field + "\" onfocus=\"javascript: $(this).blur();\" maxlength=\"10\" class=\"form-control gen-date execution-start-date\" placeholder=\"YYYY-MM-DD\" value=\"\" required></td><td><input type=\"text\" name=\"execution_end_date[]\" id=\"execution-end-date" + date_field + "\" maxlength=\"10\" class=\"form-control gen-date execution-end-date\" placeholder=\"YYYY-MM-DD\" value=\"\"></td></tr><tr class=\"gen-title\"><th>Assigned Agent</th><th>Date Sent to Agent</th><th>Date Received from Agent</th><th>Status*</th></tr><tr><td><select name=\"assigned_agent[]\" id=\"assigned_agent\" title=\"Select an agent\" class=\"form-control\"><option value=\"\">**Select an agent**</option><?php 
$result2 = $db->select("reg_users", "WHERE agent = '1'", "DISTINCT *", "ORDER BY id ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$agent_id = $row2["id"];
$agent_name = $row2["name"];
$agent_email = $row2["email"];
echo "<option value='{$agent_id}'>{$agent_name} ({$agent_email})</option>";
}
}
?></select></td><td><input type=\"text\" name=\"agent_start_date[]\" id=\"agent-start-date" + date_field + "\" maxlength=\"10\" class=\"form-control gen-date agent-start-date\" placeholder=\"YYYY-MM-DD\" value=\"\"></td><td><input type=\"text\" name=\"agent_end_date[]\" id=\"agent-end-date" + date_field + "\" maxlength=\"10\" class=\"form-control gen-date agent-end-date\" placeholder=\"YYYY-MM-DD\" value=\"\"></td><td><select name=\"sub_status[]\" id=\"sub_status\" title=\"Select a status\" class=\"form-control\" required><option value=\"\">**Select a status**</option><?php 
$result2 = $db->select("status_types", "", "DISTINCT *", "ORDER BY id ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$sub_type = $row2["type"];
echo "<option value='{$sub_type}'>{$sub_type}</option>";
}
}
?></select></td></tr></table></td><td><button type=\"button\" class=\"btn gen-btn del-sub-cat\" lang=\"row" + c + "\" onclick=\"javascript: delete_sub(this.lang);\"><i class=\"fa fa-minus\"></i></button></td></tr>");

$(".embed-script").html("<script> $('.gen-date').datepicker({ dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true, yearRange: '1901:2100'}); </script>");

});

function delete_sub(what){
document.getElementById(what).outerHTML = "";
var sub_row = document.getElementsByClassName("sub-row");
var sub_cell = document.getElementsByClassName("sub-cell");
var del_sub_cat = document.getElementsByClassName("del-sub-cat");
var i;
for(i = 0; i < sub_row.length; i++){
c = i+1;
d = i-1;
sub_row[i].id = "row" + c;
sub_cell[i].id = "td" + c;
sub_cell[i].innerHTML = c;

if(i > 0){
del_sub_cat[d].lang = "row" + c;
}

}

$(".embed-script").html("<script> $('.gen-date').datepicker({ dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true, yearRange: '1901:2100'}); </script>");

}
//-->
</script>

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

});
//-->
</script>

<script src="js/general-form.js"></script>

<?php if(!isset($_REQUEST["gh"])){ ?>

</div>
</div>

</div>

<?php require_once("../includes/portal-footer.php"); } ?>