<?php 
error_reporting(E_ALL); ini_set('display_errors', 1);
require_once("../includes/admin-header.php"); require_once("../includes/simplexlsx.class.php"); ?>

<?php

admin_role_redirect("manage_bulk_bc_reports");

$upload = tp_input("upload");

$saved_id = $error_message = "";
$error_code = 1;

////////////// Upload image //////////////////////////////
if(check_admin("manage_bulk_bc_reports") == 1 && $_SERVER['REQUEST_METHOD'] == "POST" && !empty($upload) && !empty($_FILES["ufile"]["tmp_name"])){ 

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
$file_name = "bc-reports" . $ticket_id . $rand_no . ".xlsx";

foreach (glob("bc-reports*.xlsx") as $filename) {
unlink($filename);
}

if(move_uploaded_file($file_temp_name, $file_name)){

////////===================== Read File ====================///////
if(file_exists($file_name)){

$informed_officer[] = "";

if ($xlsx = SimpleXLSX::parse($file_name)) {
$file_rows_array = $xlsx->rows();

///////////============Starts Each Row============/////////	
for($i = 0; $i < count($file_rows_array); $i++){
  
$client = test_input($file_rows_array[$i][0]);
$batch = test_input($file_rows_array[$i][1]);
$subject = test_input($file_rows_array[$i][2]);
$start_date = $file_rows_array[$i][3];
$start_date = (!empty($start_date))?date("Y-m-d",strtotime($start_date)):"";
$end_date = $file_rows_array[$i][4];
$end_date = (!empty($end_date))?date("Y-m-d",strtotime($end_date)):"";
$investigation_officer = test_input($file_rows_array[$i][5]);
$verification_type = test_input($file_rows_array[$i][6]);
$verification_order_id = in_table("order_id","bc_verification_types","WHERE type = '{$verification_type}'","order_id");
$tat_days = in_table("tat","bc_verification_types","WHERE type = '{$verification_type}'","tat");
$education = test_input($file_rows_array[$i][7]);
$source = test_input($file_rows_array[$i][8]);
$comment = test_input($file_rows_array[$i][9]);	
$status = test_input($file_rows_array[$i][10]);	

$tat = "";
if(!empty($start_date) && !empty($tat_days)){
$dat = date_create($start_date);
date_add($dat, date_interval_create_from_date_string("{$tat_days} days"));
$tat = date_format($dat, "Y-m-d");
}

if($i > 0){
 
if(!empty($client) && !empty($batch) && !empty($subject)){	 
$error_code = 0;
$data_array = array(
"client" => $client,
"batch" => $batch,
"subject" => $subject,
"date_time" => $date_time,
"last_update" => $date_time
);
$act = $db->insert2($data_array, "bc_reports");
$saved_id = in_table("id","bc_reports","WHERE subject = '$subject' AND date_time = '$date_time'","id");

}else if(!empty($saved_id) && !empty($verification_type) && !empty($start_date) && !empty($tat) && !empty($investigation_officer) && !empty($status) && $error_code == 0){

$informed_officer[] = $investigation_officer;

$data_array = array(
"bc_report_id" => $saved_id,
"investigation_officer" => $investigation_officer,
"verification_type" => $verification_type,
"verification_order_id" => $verification_order_id,
"education" => $education,
"source" => $source,
"comment" => $comment,
"start_date" => $start_date,
"end_date" => $end_date,
"tat" => $tat,
"status" => $status,
"date_time" => $date_time,
"last_update" => $date_time
);
$act = $db->insert2($data_array, "bc_sub_reports");

///////////===========Insert Report Status==========//////////
$client = in_table("client","bc_reports","WHERE id = '{$saved_id}'","client");
$subject = in_table("subject","bc_reports","WHERE id = '{$saved_id}'","subject");
$batch = in_table("batch","bc_reports","WHERE id = '{$saved_id}'","batch");
$investigation_officer_name = in_table("name","reg_users","WHERE id = '{$investigation_officer}'","name");
$investigation_officer_email = in_table("email","reg_users","WHERE id = '{$investigation_officer}'","email");

$used_status = "";
$used_status .= (!empty($investigation_officer))?"Assigned to an Investigation Officer - {$investigation_officer_name} ({$investigation_officer_email}) on " . min_sub_date($start_date) . ",":"";
$used_status .= ($status == "COMPLETED" || !empty($end_date))?"COMPLETED,":"";

$report_data_array = array(
"reference_code" => "'$saved_id'",
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

///////////========Update Main Table=========/////////////////
$det_start_date = in_table("start_date","bc_sub_reports","WHERE bc_report_id = '{$saved_id}' ORDER BY start_date ASC","start_date");
$det_end_date = in_table("end_date","bc_sub_reports","WHERE bc_report_id = '{$saved_id}' ORDER BY end_date DESC","end_date");
$det_tat= in_table("tat","bc_sub_reports","WHERE bc_report_id = '{$saved_id}' ORDER BY tat ASC","tat");
$det_status = in_table("COUNT(id) AS Total","bc_sub_reports","WHERE bc_report_id = '{$saved_id}' AND status = 'PENDING'","Total");
$used_status = ($det_status > 0)?"PENDING":"COMPLETED";

$data_array = array(
"start_date" => $det_start_date,
"end_date" => $det_end_date,
"tat" => $det_tat,
"status" => $used_status,
"last_update" => $date_time
);
$db->update($data_array, "bc_reports", "id = '$saved_id'");
//////////=======Ends Update Main Table=========///////////////

}else if(!empty($client) && (empty($batch) || empty($subject)) ){	
$error_code = 1;
$i_row = $i + 1;
$error_message .= "<div class='not-success'>Bulk BC report on row <b>{$i_row}</b> of the excel file with the sub verifiable information not saved.</div>";
}else if(!empty($saved_id) && !empty($verification_type) && $error_code == 0 && empty($tat)){	
$i_row = $i + 1;
$error_message .= "<div class='not-success'>Bulk BC sub report on row <b>{$i_row}</b> verifiable information not saved due to unavailable TAT for <b>{$verification_type}</b> (BC verification type).</div>";
}

}


}

//////////===============Send Mail to Investigation Officer==========//////

if(!empty($informed_officer)){

$informed_officer = array_unique($informed_officer);

///////////===========Send mail to each officer==========//////////
foreach($informed_officer as $info_value){
if(!empty($info_value)){
$informed_officer_name = $informed_officer_email = $message = $subject = $headers = $to = "";
$informed_officer_name = in_table("name","reg_users","WHERE id = '{$info_value}'","name");
$informed_officer_email = in_table("email","reg_users","WHERE id = '{$info_value}'","email");

$subject = "New BC Task(s)";
$message = "<p>Dear {$informed_officer_name},</p><p>This is to notify you of new background checks task(s) assigned to you. Kindly log in to your account for details.</p>";
$message2 = $message;
$message = message_template();
$headers = "{$gen_name} <no-reply@{$domain}>";
$to = $informed_officer_email;

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

$activity = "Uploaded new bulk BC report.";
$audit_data_array = array(
"user_id" => "'$id'",
"name" => "'$username'",
"email" => "'$user_email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$db->insert($audit_data_array, "audit_log");

$_SESSION["msg"] = $error_message;
$_SESSION["msg"] .= "<div class='success'>Bulk BC report processed on " . full_date($date_time) . ".</div>";
redirect("{$directory}{$admin}bulk-bc-reports");
/////////////============Ends Mail to Investigation Officer==========/////

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

<div class="page-title">Upload New Background Checks Bulk Report <a href="format/background-checks-reports.xlsx" class="btn gen-btn float-right">Download BC Report Format</a></div>

<form action="<?php echo $admin; ?>bulk-bc-reports" class="img-form" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">  
<input type="hidden" name="gh" value="1">
<input type="hidden" name="upload" value="1">                      
<p><b>Format:</b> .xlsx<br /><br /></p>
<input type="file" name="ufile" id="ufile" required>
<label for="ufile" id="pic-label" class="btn gen-btn" ><i class="fa fa-upload" aria-hidden="true"></i> Upload report</label>
</form>

<script src="js/general-form.js"></script>

</div>
</div>

</div>

<?php require_once("../includes/portal-footer.php"); ?>