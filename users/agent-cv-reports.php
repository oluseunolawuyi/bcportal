<?php if(!isset($_REQUEST["gh"])){ include_once("../includes/admin-header.php"); 
}else{ 
include_once("../includes/gen-header.php");
} ?>

<?php
if(empty($is_agent)){ 
redirect($directory . $admin);
}

$pn = nr_input("pn");
$view = nr_input("view");

////////////////////////////////////////////////////******************************//////////////

$keyword = search_option("keyword");
$no_of_rows = search_option("no_of_rows");

$where = "WHERE assigned_agent = '$id'";
$where .= (!empty($keyword))?" AND names LIKE '%{$keyword}%'":"";

$result = $db->select("cv_reports", "$where", "*", "ORDER BY tat ASC");

$per_view = 20;
$per_view = (!empty($no_of_rows))?$no_of_rows:$per_view;
$page_link = "{$admin}agent-cv-reports?pn=";
$link_suffix = "";
$style_class = "general-link";
page_numbers();

if(empty($view)){
?>

<div class="page-title">CV Reports</div>

<form action="<?php echo $admin; ?>agent-cv-reports" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
<input type="hidden" name="gh" value="1"> 
<div class="search-dates">

<div class="col-md-6">
<label for="keyword">Keyword</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-file-text" aria-hidden="true"></i></span>
<input type="text" name="keyword" id="keyword" class="form-control" placeholder="Type a name" value="<?php check_inputted("keyword", $keyword); ?>">
</div>
</div>

<div class="col-md-4">
<label for="no_of_rows">No. of Rows</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-list" aria-hidden="true"></i></span>
<input type="text" name="no_of_rows" id="no_of_rows" class="form-control only-no" placeholder="E.g. 10" value="<?php check_inputted("no_of_rows", $per_view); ?>">
</div>
</div>

<div class="col-md-2">
<br />
<button type="submit" class="btn gen-btn"><i class="fa fa-search"></i> Search</button>
</div>

</div>
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
<th>Ref. Code</th>
<th>Client</th>
<th style="width:50px;">Batch No.</th>
<th>Names</th>
<th>Institution</th>
<th>TAT</th>
<th>Status</th>
<th>Days Remaining</th>
<th style="width:70px;">View</th>
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
$date_sent_out = min_sub_date($row["date_sent_out"]);
$tat = min_sub_date($row["tat"]);
$date_received_from_school = $row["date_received_from_school"];
$status = ($date_received_from_school == "0000-00-00")?"PENDING":"COMPLETED";
$status_div = ($status == "COMPLETED")?"primary":"danger";
$days_remaining = days_remaining($row["tat"],"1",$status);
?>
<tr>
<td style="width:30px;"><?php echo $report_id; ?></td>
<td><?php echo "{$client_name}<br>({$client_email})"; ?></td>
<td><?php echo formatQty($batch); ?></td>
<td><?php echo $names; ?></td>
<td><?php echo $institution; ?></td>
<td><?php echo $tat; ?></td>
<td><?php echo (!empty($status))?"<div class=\"btn btn-{$status_div}\">{$status}</div>":"<div class=\"btn btn-danger\">{$status}</div>"; ?></td>
<td><?php echo $days_remaining; ?></td>
<td><a href="<?php echo $admin; ?>agent-cv-reports?view=<?php echo $report_id; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link" title="View CV report #<?php echo $report_id; ?>"><i class="fa fa-eye" aria-hidden="true"></i> View</a></td>
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
echo "<div class='not-success'>No CV reports found at the moment.</div>";
}

}

/////============View Report=============////
if(!empty($view)){
$result = $db->select("cv_reports", "WHERE id='$view' AND assigned_agent = '$id'", "*");

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
$verified_status = $row["verified_status"];
$status_comment = $row["status_comment"];
$transaction_ref = $row["transaction_ref"];
$tat = min_sub_date($row["tat"]);
$date_sent_out = ($row["date_sent_out"] != "0000-00-00")?sub_date($row["date_sent_out"]):"";
$date_received_from_school = ($row["date_received_from_school"] != "0000-00-00")?sub_date($row["date_received_from_school"]):"";
$school_letter_date = ($row["school_letter_date"] != "0000-00-00")?sub_date($row["school_letter_date"]):"";
$remark = $row["remark"];
$status = ($row["date_received_from_school"] == "0000-00-00")?"PENDING":"COMPLETED";
$status_div = ($status == "COMPLETED")?"primary":"danger";
$days_remaining = days_remaining($row["tat"],"",$status);
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

<div class="back"><a href="<?php echo $admin; ?>agent-cv-reports?pn=<?php echo $pn; ?>" class="btn gen-btn general-link"><i class="fa fa-arrow-left"></i> Back to CV reports</a></div>

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
<div class="align-right"><div class="btn btn-default"><b>Turn-Around Time:</b> <?php echo $tat; ?></div> &nbsp;&nbsp; <div class="btn btn-default"><b>Remaining Days:</b> <?php echo $days_remaining; ?></div></div>
<br />

<table class="table table-hover table-striped">
<tr><th class="gen-title" style="width:200px;">Client</th><td><?php echo "{$client_name} ({$client_email})"; ?></td></tr>
<tr><th class="gen-title">Batch No.</th><td><?php echo formatQty($batch); ?></td></tr>
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
<tr><th class="gen-title">Date Received</th><td><?php echo $date_sent_out; ?></td></tr>
<tr><th class="gen-title">Completion Date</th><td><?php echo $date_received_from_school; ?></td></tr>
<tr><th class="gen-title">School Letter Date</th><td><?php echo $school_letter_date; ?></td></tr>
<tr><th class="gen-title">Remark</th><td><?php echo $remark; ?></td></tr>
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
//-->
</script>

<script src="js/general-form.js"></script>

<?php if(!isset($_REQUEST["gh"])){ ?>

</div>
</div>

</div>

<?php require_once("../includes/portal-footer.php"); } ?>