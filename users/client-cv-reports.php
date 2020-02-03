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

$table_title = (!empty($extract_client))?strtoupper("{$extract_client_name} ({$extract_client_email})"):"ALL CLIENTS";

$where = "WHERE client = '$id'";
$where .= (!empty($extract_keyword))?" AND names LIKE '%{$extract_keyword}%'":"";
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
$rows[] = array("BATCH NO.", "DATE RECEIVED", "EXPECTED END DATE", "COMPLETION DATE", "CANDIDATE NAMES", "INSTITUTION", "COURSE", "QUALIFICATION", "GRADE", "SESSION", "MATRIC NO.", "STATUS", "VERIFIED STATUS", "STATUS COMMENT");

while($row = fetch_data($result)){
$extract_id = $row["id"];
$date_received = decode_data($row["date_received"]);
$tat = decode_data($row["tat"]);
$completion_date = decode_data($row["completion_date"]);
$names = decode_data($row["names"]);
$institution = decode_data($row["institution"]);
$course = decode_data($row["course"]);
$qualification = decode_data($row["qualification"]);
$grade = decode_data($row["grade"]);
$session = decode_data($row["session"]);
$matric_number = decode_data($row["matric_number"]);
$batch = decode_data($row["batch"]);
$status = decode_data($row["status"]);

$datetime1 = new DateTime($row["tat"]);
$datetime2 = new DateTime($date);
$difference = $datetime1->diff($datetime2);
if($difference->days >= 90 && $status == "PENDING"){
$status = "CLOSED OUT";
}

$verified_status = decode_data($row["verified_status"]);
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

echo "<iframe src=\"{$admin}download-report?file={$file_name}\" style=\"width:1px; height:1px;\"></iframe>";

}

}
///////=====================Ends Generate Report======================/////////

$keyword = search_option("keyword");
$no_of_rows = search_option("no_of_rows");
$search_batch = search_option("search_batch");
$search_start_date = search_option("search_start_date");
$search_end_date = search_option("search_end_date");
$search_status = search_option("search_status");

$where = "WHERE client = '$id'";
$where .= (!empty($keyword))?" AND (institution LIKE '%{$keyword}%' OR names LIKE '%{$keyword}%')":"";
$where .= (!empty($search_batch))?" AND batch = '{$search_batch}'":"";
$where .= (!empty($search_start_date))?" AND date_received >= '{$search_start_date} 00:00:00'":"";
$where .= (!empty($search_end_date))?" AND completion_date <= '{$search_end_date} 23:59:59'":"";
$where .= (!empty($search_status))?" AND status = '{$search_status}'":"";

$result = $db->select("cv_reports", "$where", "*", "ORDER BY id DESC");

$per_view = 20;
$per_view = (!empty($no_of_rows))?$no_of_rows:$per_view;
$page_link = "{$admin}client-cv-reports?pn=";
$link_suffix = "";
$style_class = "general-link";
page_numbers();

if(empty($view) && (empty($edit)||(!empty($edit)&&$error==0)) && (empty($add)||(!empty($add)&&$error==0)) ){
?>

<div class="page-title">Certificate Verification Reports</div>

<form action="<?php echo $admin; ?>client-cv-reports" name="search_form" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
<input type="hidden" name="gh" value="1"> 
<div class="search-dates">

<div class="col-md-5">
<label for="keyword">Keyword</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-file-text" aria-hidden="true"></i></span>
<input type="text" name="keyword" id="keyword" class="form-control" placeholder="Institution or Client Name" value="<?php check_inputted("keyword", $keyword); ?>">
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

<form action="<?php echo $admin; ?>client-cv-reports" name="extract_form" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
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

$result = $db->select("cv_reports", "$where", "*", "ORDER BY tat ASC", "LIMIT {$offset},{$per_view}");

if(count_rows($result) > 0){
?>
<form action="<?php echo $admin; ?>client-cv-reports" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data" style="overflow-x:auto;">
<input type="hidden" name="pn" value="<?php echo $pn; ?>"> 
<input type="hidden" name="gh" value="1"> 
<input type="hidden" name="delete" value="1"> 
<table class="table table-striped table-hover">
<thead>
<tr class="gen-title">
<th>Report ID</th>
<th style="width:50px;">Batch</th>
<th>Names</th>
<th>Institution</th>
<th>Date Received</th>
<th>TAT</th>
<th>Status</th>
<th>Days Remaining</th>
<th>Details</th>
</tr>
</thead>
<tbody>
<?php
while($row = fetch_data($result)){
$report_id = $row["id"];
$client = $row["client"];
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$client_email = in_table("email","reg_users","WHERE id = '{$client}'","email");
$batch = $row["batch"];
$names = $row["names"];
$institution = $row["institution"];
$date_received = min_sub_date($row["date_received"]);
$tat = min_sub_date($row["tat"]);
$status = $row["status"];
$days_remaining = days_remaining($row["tat"],"1",$status);
$status_div = ($status == "COMPLETED")?"primary":"danger";
$status_comment = $row["status_comment"];
?>
<tr>
<td style="width:30px;"><?php echo $report_id; ?></td>
<td><?php echo formatQty($batch); ?></td>
<td><?php echo $names; ?></td>
<td><?php echo $institution; ?></td>
<td><?php echo $date_received; ?></td>
<td><?php echo $tat; ?></td>
<td><?php echo (!empty($status))?"<div class=\"btn btn-{$status_div}\">{$status}</div>":"<div class=\"btn btn-danger\">{$status}</div>"; ?></td>
<td><?php echo $days_remaining; ?></td>
<td style="width:70px;"><a href="<?php echo $admin; ?>client-cv-reports?view=<?php echo $report_id; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link" title="<?php echo $status_comment; ?>"><i class="fa fa-eye" aria-hidden="true"></i> View</a></td>
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
echo "<div class='not-success'>No CV reports found at the moment.</div>";
}

}

/////============View Report=============////
if(!empty($view)){
$result = $db->select("cv_reports", "WHERE id='$view' AND client = '$id'", "*");

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
?>

<style>
<!--
div table thead tr th, div table tr th, div table tbody tr td, div table tr td{
text-align:left !important;
}
-->
</style>

<div class="reply-content-wrapper ">

<div class="back"><a href="<?php echo $admin; ?>client-cv-reports?pn=<?php echo $pn; ?>" class="btn gen-btn general-link"><i class="fa fa-arrow-left"></i> Back to CV reports</a></div>

<div class="view-wrapper ">

<div class="view-header ">
<div class="header-img"><img src="images/post.jpg" ></div>
<div class="header-content">
<div class="view-title">Names:  <?php echo $names; ?></div>
</div>
</div>

<div class="view-content">

<br />
<div class="align-right"><div class="btn btn-default"><b>Turn-Around Time:</b> <div class="btn btn-danger"><?php echo $tat; ?></div></div> &nbsp;&nbsp; <div class="btn btn-default"><b>Remaining Days:</b> <?php echo $days_remaining; ?></div></div>
<br />

<table class="table table-hover table-striped">
<tr><th class="gen-title" style="width:150px;">Batch No.</th><td><?php echo formatQty($batch); ?></td></tr>
<tr><th class="gen-title">Date Received</th><td><?php echo $date_received; ?></td></tr>
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
</table>

</div>

</div>
</div>

<?php
}else{
echo "<div class='not-success'>This CV report does not exist.</div>";
}
}
?>

<script>
<!--
var conf_text = "report";

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