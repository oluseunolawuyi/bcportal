<?php if(!isset($_REQUEST["gh"])){ include_once("../includes/admin-header.php"); 
}else{ 
include_once("../includes/gen-header.php");
} ?>

<?php
if(empty($is_client)){ 
redirect($directory . $admin);
}

$pn = nr_input("pn");
$view = nr_input("view");

//////////======================Generate Report==========================///////////
$extract = np_input("extract");
$extract_client = $id;
$extract_client_name = in_table("name","reg_users","WHERE id = '{$extract_client}'","name");
$extract_client_email = in_table("email","reg_users","WHERE id = '{$extract_client}'","email");
$extract_keyword = tp_input("extract_keyword");
$extract_batch = np_input("extract_batch");
$extract_start_date = tp_input("extract_start_date");
$extract_end_date = tp_input("extract_end_date");
$extract_status = tp_input("extract_status");

if($_SERVER["REQUEST_METHOD"] == "POST" && !empty($extract)){

$table_title = strtoupper("{$extract_client_name} ({$extract_client_email})");

$where = "WHERE client = '$id'";
$where .= (!empty($extract_keyword))?" AND subject LIKE '%{$extract_keyword}%'":"";
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
$rows[] = array("BATCH NO.", "CANDIDATE NAMES", "VERIFICATION TYPE", "EDUCATION", "SOURCE", "COMMENT", "START DATE", "EXPECTED COMPLETION DATE", "END DATE", "STATUS");

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
$sub_start_date = decode_data($row["start_date"]);
$sub_tat = decode_data($row["tat"]);
$sub_end_date = decode_data($row["end_date"]);
$sub_status	 = decode_data($row["status"]);

$datetime1 = new DateTime($row["tat"]);
$datetime2 = new DateTime($date);
$difference = $datetime1->diff($datetime2);
if($difference->days >= 90 && $sub_status == "PENDING"){
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

$activity = "Downloaded Own Background Checks report.";
$audit_data_array = array(
"user_id" => "'$id'",
"name" => "'$username'",
"email" => "'$user_email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$db->insert($audit_data_array, "audit_log");

$_SESSION["msg"] = $file_name;
redirect("{$directory}{$admin}client-bc-reports");
}
////====================================================////////////

}
///////=====================Ends Generate Report======================/////////


////////////////////////////////////////////////////******************************//////////////

$keyword = search_option("keyword");
$no_of_rows = search_option("no_of_rows");
$search_batch = search_option("search_batch");
$search_start_date = search_option("search_start_date");
$search_end_date = search_option("search_end_date");
$search_status = search_option("search_status");

$where = "WHERE client = '{$id}'";
$where .= (!empty($keyword))?" AND subject LIKE '%{$keyword}%'":"";
$where .= (!empty($search_batch))?" AND batch = '{$search_batch}'":"";
$where .= (!empty($search_start_date))?" AND start_date >= '{$search_start_date} 00:00:00'":"";
$where .= (!empty($search_end_date))?" AND end_date <= '{$search_end_date} 23:59:59'":"";
$where .= (!empty($search_status))?" AND status = '{$search_status}'":"";

$result = $db->select("bc_reports", "$where", "*", "ORDER BY id DESC");

$per_view = 20;
$per_view = (!empty($no_of_rows))?$no_of_rows:$per_view;
$page_link = "{$admin}client-bc-reports?pn=";
$link_suffix = "";
$style_class = "general-link";
page_numbers();

if(isset($_SESSION["msg"]) && !empty($_SESSION["msg"]) && empty($extract)){
echo "<iframe src=\"{$admin}download-report?file=" . $_SESSION["msg"] . "\" style=\"width:1px; height:1px;\"></iframe>";
unset($_SESSION["msg"]);
}

if(empty($view)){
?>

<div class="page-title">Background Checks Reports</div>

<form action="<?php echo $admin; ?>client-bc-reports" name="search_form" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
<input type="hidden" name="gh" value="1"> 
<div class="search-dates">

<div class="col-md-5">
<label for="keyword">Keyword</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-file-text" aria-hidden="true"></i></span>
<input type="text" name="keyword" id="keyword" class="form-control" placeholder="Type a keyword" value="<?php check_inputted("keyword", $keyword); ?>">
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
<label for="search_status">Status</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
<select name="search_status" id="search_status" title="Select a status" class="form-control js-example-basic-single" style="width:100%">
<option value="">**Select**</option>
<?php 
$result2 = $db->select("bc_sub_reports", "", "DISTINCT status", "ORDER BY id ASC");
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




<div class="col-md-2 col-xs-6">
<br />
<button type="submit" class="btn gen-btn"><i class="fa fa-search"></i> Search</button>
</div>

<div class="col-md-2 col-xs-6">
<br />
<a class="btn gen-btn download-report"><i class="fa fa-download"></i> Download</a>
</div>

</div>
</form>

<form action="<?php echo $admin; ?>client-bc-reports" name="extract_form" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
<input type="hidden" name="extract" value="1"> 
<input type="hidden" name="extract_keyword" value=""> 
<input type="hidden" name="extract_batch" value=""> 
<input type="hidden" name="extract_start_date" value=""> 
<input type="hidden" name="extract_end_date" value=""> 
<input type="hidden" name="extract_status" value=""> 
</form>

<?php
$d = 0;

$offset = ($per_view * $pn) - $per_view;

$result = $db->select("bc_reports", "$where", "*", "ORDER BY tat ASC", "LIMIT {$offset},{$per_view}");

if(count_rows($result) > 0){
?>
<form action="<?php echo $admin; ?>client-bc-reports" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data" style="overflow-x:auto;">
<table class="table table-striped table-hover">
<thead>
<tr class="gen-title">
<th>Ref. Code</th>
<th style="width:50px;">Batch</th>
<th>Subject</th>
<th>Start Date</th>
<th>Expected End Date</th>
<th>End Date</th>
<th>Verified Info.</th>
<th>Status</th>
<th>Days Remaining</th>
<th style="width:70px;">Details</th>
<th style="width:70px;">Report</th>
</tr>
</thead>
<tbody>
<?php
while($row = fetch_data($result)){
$report_id = $row["id"];
$batch = $row["batch"];
$subject = $row["subject"];
$start_date = ($row["start_date"] != "0000-00-00")?min_sub_date($row["start_date"]):"";
$end_date = ($row["end_date"] != "0000-00-00")?min_sub_date($row["end_date"]):"";
$tat = ($row["tat"] != "0000-00-00")?min_sub_date($row["tat"]):"";
$status = $row["status"];
$days_remaining = days_remaining($row["tat"],"1",$status);
$status_div = ($status == "COMPLETED")?"primary":"danger";
$sub_reports = in_table("COUNT(id) AS Total","bc_sub_reports","WHERE bc_report_id = '{$report_id}'","Total");
?>
<tr>
<td style="width:30px;"><?php echo $report_id; ?></td>
<td><?php echo formatQty($batch); ?></td>
<td><?php echo $subject; ?></td>
<td><?php echo $start_date; ?></td>
<td><?php echo $tat; ?></td>
<td><?php echo $end_date; ?></td>
<td><?php echo formatQty($sub_reports); ?></td>
<td><?php echo (!empty($status))?"<div class=\"btn btn-{$status_div}\">{$status}</div>":"<div class=\"btn btn-danger\">{$status}</div>"; ?></td>
<td><?php echo $days_remaining; ?></td>
<td><a href="<?php echo $admin; ?>client-bc-reports?view=<?php echo $report_id; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link" title="View BC report #<?php echo $report_id; ?>"><i class="fa fa-eye" aria-hidden="true"></i> View</a></td>
<td><a href="<?php echo $admin; ?>client-print-report?view=<?php echo $report_id; ?>" class="btn gen-btn" target="_blank" title="Print BC report #<?php echo $report_id; ?>"><i class="fa fa-print" aria-hidden="true"></i> Print</a></td>
</tr>
<?php 
$d++;
}
?>
</tbody>
</table>
</form>
<?php
echo ($last_page>1)?"<div class=\"page-nos\">" . $center_pages . "</div>":"";
}else{
echo "<div class='not-success'>No BC reports found at the moment.</div>";
}

}

/////============View Report=============////
if(!empty($view)){
$result = $db->select("bc_reports", "WHERE id='$view' AND client = '$id'", "*");

if(count_rows($result) == 1){
$row = fetch_data($result);
$batch = $row["batch"];
$subject = $row["subject"];
$start_date = ($row["start_date"] != "0000-00-00")?min_sub_date($row["start_date"]):"";
$end_date = ($row["end_date"] != "0000-00-00")?min_sub_date($row["end_date"]):"";
$tat = ($row["tat"] != "0000-00-00")?min_sub_date($row["tat"]):"";
$status = $row["status"];
$status_text = ($status == "COMPLETED")?"Fully Completed":"Not Fully Completed";
$status_div = ($status == "COMPLETED")?"primary":"danger";
$sub_reports = in_table("COUNT(id) AS Total","bc_sub_reports","WHERE bc_report_id = '{$view}'","Total");
$report_date_time = full_date($row["date_time"]);
$days_remaining = days_remaining($row["tat"],"",$status);
?>

<div class="reply-content-wrapper ">

<div class="back"><a href="<?php echo $admin; ?>client-bc-reports?pn=<?php echo $pn; ?>" class="btn gen-btn general-link"><i class="fa fa-arrow-left"></i> Back to Background Checks Reports</a></div>

<div class="view-wrapper ">

<div class="view-header ">
<div class="header-img"><img src="images/post.jpg" ></div>
<div class="header-content">
<div class="view-title">Subject:  <?php echo $subject; ?></div>
<div class="view-title-details">Added on <?php echo $report_date_time; ?></div>
</div>
</div>

<div class="view-content">
<div class="align-right"><button class="btn btn-default"><b>Expected Completion Date:</b> <?php echo $tat; ?></button> &nbsp;&nbsp; <button class="btn btn-default"><b>Days Remaining:</b> <?php echo $days_remaining; ?></button></div>
<b>Batch:</b> <?php echo $batch; ?> <br /> <br /> 
<b>Start Date:</b> <?php echo $start_date; ?> <br /> <br /> 
<b>End Date:</b> <?php echo $end_date; ?> <br /> <br /> 
<b>Status:</b> <?php echo (!empty($status))?"<div class=\"btn btn-{$status_div}\">{$status_text}</div>":"<div class=\"btn btn-danger\">{$status_text}</div>"; ?> <br />

<?php 
$result = $db->select("bc_sub_reports", "WHERE bc_report_id='{$view}'", "*", "ORDER BY verification_order_id ASC");
if(count_rows($result) > 0){ 
?>

<div class="body-header"><i class="fa fa-tag" aria-hidden="true"></i> Background Checks Verified Information (<?php echo formatQty($sub_reports); ?>)</div>

<table class="table table-striped table-hover">
<thead>
<tr>
<th style="width:30px;">#ID</th>
<th>Details</th>
<th>Status</th>
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
$sub_status = $row["status"];
$sub_status_div = ($sub_status == "COMPLETED")?"primary":"danger";
?>

<tr>
<th><?php echo $sub_report_id; ?></th>
<td>

<table class="table">
<tr class="gen-title"><th>Expected Completion Date</th><th>Verification Type</th><th colspan="2">Source</th></tr>
<tr><td><?php echo $tat; ?></td><th><?php echo "<u>" . strtoupper($verification_type) . "</u> " . $education; ?></th><td colspan="2"><?php echo $source; ?></td></tr>
<tr class="gen-title"><th colspan="2">Comment</th><th>Start Date</th><th>End Date</th></tr>
<tr><td colspan="2"><?php echo $comment; ?></td><td><?php echo $start_date; ?></td><td><?php echo $end_date; ?></td></tr>
</table>

</td>

<td><?php echo (!empty($sub_status))?"<div class=\"btn btn-{$sub_status_div}\">{$sub_status}</div>":"<div class=\"btn btn-danger\">{$sub_status}</div>"; ?></td>
</tr>
<?php
}
?>
</tbody>
</table>
<?php
}
?>

</div>

</div>
</div>

<?php
}else{
echo "<div class='not-success'>This BC report does not exist.</div>";
}
}
?>

<script>
<!--

$(document).ready(function(){

$(".download-report").click(function(){
document.extract_form.extract_keyword.value = document.search_form.keyword.value;
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