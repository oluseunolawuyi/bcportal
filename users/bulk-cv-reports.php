<?php require_once("../includes/admin-header.php"); require_once("../includes/simplexlsx.class.php"); ?>

<?php

admin_role_redirect("manage_bulk_cv_reports");

$upload = tp_input("upload");

////////////// Upload image //////////////////////////////
if(check_admin("manage_bulk_cv_reports") == 1 && $_SERVER['REQUEST_METHOD'] == "POST" && !empty($upload) && !empty($_FILES["ufile"]["tmp_name"])){ 

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
$file_name = "cv-reports" . $ticket_id . $rand_no . ".xlsx";

foreach (glob("cv-reports*.xlsx") as $filename) {
unlink($filename);
}

if(move_uploaded_file($file_temp_name, $file_name)){

////////===================== Read File ====================///////
if(file_exists($file_name)){

$informed_officer[] = "";

if ($xlsx = SimpleXLSX::parse($file_name)) {
$file_rows_array = $xlsx->rows();
	
for($i = 0; $i < count($file_rows_array); $i++){
  
$date_received = $file_rows_array[$i][0];
$date_received = (!empty($date_received))?date("Y-m-d",strtotime($date_received)):"";
$tat = "";
if(!empty($date_received)){
$dat = date_create($date_received);
date_add($dat, date_interval_create_from_date_string("78 days"));
$tat = date_format($dat, "Y-m-d");
}
$completion_date = $file_rows_array[$i][1];
$completion_date = (!empty($completion_date))?date("Y-m-d",strtotime($completion_date)):"";
$client = test_input($file_rows_array[$i][2]);
$names = test_input($file_rows_array[$i][3]);
$institution = test_input($file_rows_array[$i][4]);
$course = test_input($file_rows_array[$i][5]);
$qualification = test_input($file_rows_array[$i][6]);
$grade = test_input($file_rows_array[$i][7]);
$session = test_input($file_rows_array[$i][8]);
$matric_number = test_input($file_rows_array[$i][9]);	
$batch = test_input($file_rows_array[$i][10]);	
$status = test_input($file_rows_array[$i][11]);	
$verified_status = test_input($file_rows_array[$i][12]);	
$status_comment = test_input($file_rows_array[$i][13]);	
$transaction_ref = test_input($file_rows_array[$i][14]);	
$investigation_officer = test_input($file_rows_array[$i][15]);	

if($i > 0){
if(!empty($date_received) && !empty($client) && !empty($names) && !empty($institution) && !empty($course) && !empty($qualification) && !empty($session) && !empty($batch) && !empty($status) && !empty($verified_status) && !empty($investigation_officer)){	
 
$informed_officer[] = $investigation_officer;

$data_array = array(
"date_received" => $date_received,
"completion_date" => $completion_date,
"tat" => $tat,
"client" => $client,
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
"date_time" => $date_time,
"last_update" => $date_time
);
$act = $db->insert2($data_array, "cv_reports");

$report_id = in_table("id","cv_reports","WHERE date_time = '{$date_time}' AND client = '{$client}'","id");;

///////////===========Insert Report Status==========//////////
$investigation_officer_name = in_table("name","reg_users","WHERE id = '{$investigation_officer}'","name");
$investigation_officer_email = in_table("email","reg_users","WHERE id = '{$investigation_officer}'","email");

$used_status = "";
$used_status .= (!empty($investigation_officer))?"Assigned to an Investigation Officer - {$investigation_officer_name} ({$investigation_officer_email}) on " . min_sub_date($date_received) . ",":"";
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

}
}


}

//////////===============Send Mail to Investigation Officer==========//////

if(!empty($informed_officer)){

$informed_officer = array_unique($informed_officer);

///////////===========Send mail to each officer==========//////////
foreach($informed_officer as $info_value){
if(!empty($info_value)){
$informed_officer_name = $informed_officer_email = $message =  $subject = $headers = $to = "";
$informed_officer_name = in_table("name","reg_users","WHERE id = '{$info_value}'","name");
$informed_officer_email = in_table("email","reg_users","WHERE id = '{$info_value}'","email");

$subject = "New CV Task(s)";
$message = "<p>Dear {$informed_officer_name},</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>";
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

$activity = "Uploaded new bulk CV report.";
$audit_data_array = array(
"user_id" => "'$id'",
"name" => "'$username'",
"email" => "'$user_email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$db->insert($audit_data_array, "audit_log");

$_SESSION["msg"] = "<div class='success'>Bulk CV report successfully uploaded at " . full_date($date_time) . ".</div>";
redirect("{$directory}{$admin}bulk-cv-reports");
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

<div class="page-title">Upload New Certificate Verification Bulk Report <a href="format/certificate-verification-reports.xlsx" class="btn gen-btn float-right">Download CV Report Format</a></div>

<form action="<?php echo $admin; ?>bulk-cv-reports" class="img-form" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">  
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