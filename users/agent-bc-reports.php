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

$search_verification_type = search_option("search_verification_type");
$no_of_rows = search_option("no_of_rows");

$where = "WHERE assigned_agent = '$id'";
$where .= (!empty($search_verification_type))?" AND verification_type = '{$search_verification_type}'":"";

$result = $db->select("bc_sub_reports", "$where", "*", "ORDER BY tat ASC");

$per_view = 20;
$per_view = (!empty($no_of_rows))?$no_of_rows:$per_view;
$page_link = "{$admin}agent-bc-reports?pn=";
$link_suffix = "";
$style_class = "general-link";
page_numbers();

if(empty($view)){
?>

<div class="page-title">BC Reports</div>

<form action="<?php echo $admin; ?>agent-bc-reports" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
<input type="hidden" name="gh" value="1"> 
<div class="search-dates">

<div class="col-sm-7">
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

<div class="col-sm-3">
<label for="no_of_rows">No. of Rows</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-list" aria-hidden="true"></i></span>
<input type="text" name="no_of_rows" id="no_of_rows" class="form-control only-no" placeholder="E.g. 10" value="<?php check_inputted("no_of_rows", $per_view); ?>">
</div>
</div>

<div class="col-sm-2">
<br />
<button type="submit" class="btn gen-btn"><i class="fa fa-search"></i> Search</button>
</div>

</div>
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
<th>Ref. Code</th>
<th>Sub-Ref.</th>
<th>Client</th>
<th style="width:50px;">Batch No.</th>
<th>Subject</th>
<th>Date Received</th>
<th>TAT</th>
<th>Verified Info.</th>
<th>Status</th>
<th>Days Remaining</th>
<th style="width:70px;">View</th>
</tr>
</thead>
<tbody>
<?php
while($row = fetch_data($result)){
$report_id = $row["id"];
$bc_report_id = $row["bc_report_id"];
$verification_type = $row["verification_type"];
$client = in_table("client","bc_reports","WHERE id = '{$bc_report_id}'","client");
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$client_email = in_table("email","reg_users","WHERE id = '{$client}'","email");
$batch = in_table("batch","bc_reports","WHERE id = '{$bc_report_id}'","batch");
$subject = in_table("subject","bc_reports","WHERE id = '{$bc_report_id}'","subject");
$date_sent_to_agent = ($row["date_sent_to_agent"] != "0000-00-00")?min_sub_date($row["date_sent_to_agent"]):"";
$tat = ($row["tat"] != "0000-00-00")?min_sub_date($row["tat"]):"";
$date_received_from_agent = $row["date_received_from_agent"];
$status = ($date_received_from_agent == "0000-00-00")?"PENDING":"COMPLETED";
$status_div = ($status == "COMPLETED")?"primary":"danger";
$days_remaining = days_remaining($row["tat"],"1",$status);
?>
<tr>
<td style="width:30px;"><?php echo $bc_report_id; ?></td>
<td style="width:30px;"><?php echo $report_id; ?></td>
<td><?php echo "{$client_name}<br>({$client_email})"; ?></td>
<td><?php echo formatQty($batch); ?></td>
<td><?php echo $subject; ?></td>
<td><?php echo $date_sent_to_agent; ?></td>
<td><?php echo $tat; ?></td>
<td><?php echo $verification_type; ?></td>
<td><?php echo (!empty($status))?"<div class=\"btn btn-{$status_div}\">{$status}</div>":"<div class=\"btn btn-danger\">{$status}</div>"; ?></td>
<td><?php echo $days_remaining; ?></td>
<td><a href="<?php echo $admin; ?>agent-bc-reports?view=<?php echo $report_id; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link" title="View BC report #<?php echo $report_id; ?>"><i class="fa fa-eye" aria-hidden="true"></i> View</a></td>
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
echo "<div class='not-success'>No BC reports found at the moment.</div>";
}

}


/////============View Report=============////
if(!empty($view)){
$result = $db->select("bc_sub_reports", "WHERE id='$view' AND assigned_agent = '$id'", "*");

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
$tat = ($row["tat"] != "0000-00-00")?min_sub_date($row["tat"]):"";
$investigation_officer = $row["investigation_officer"];
$investigation_officer_name = (!empty($investigation_officer))?in_table("name","reg_users","WHERE id = '{$investigation_officer}'","name"):"";
$investigation_officer_email = (!empty($investigation_officer))?in_table("email","reg_users","WHERE id = '{$investigation_officer}'","email"):"";
$investigation_officer = (!empty($investigation_officer))?"{$investigation_officer_name} ({$investigation_officer_email})":"";
$date_sent_to_agent = ($row["date_sent_to_agent"] != "0000-00-00")?min_sub_date($row["date_sent_to_agent"]):"";
$date_received_from_agent = ($row["date_received_from_agent"] != "0000-00-00")?min_sub_date($row["date_received_from_agent"]):"";
$status = ($row["date_received_from_agent"] == "0000-00-00")?"PENDING":"COMPLETED";
$status_div = ($status == "COMPLETED")?"primary":"danger";
$days_remaining = days_remaining($row["tat"],"",$status);
$report_date_time = full_date($row["date_time"]);
?>

<div class="reply-content-wrapper ">

<div class="back"><a href="<?php echo $admin; ?>agent-bc-reports?pn=<?php echo $pn; ?>" class="btn gen-btn general-link"><i class="fa fa-arrow-left"></i> Back to BC reports</a></div>

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
<b>Ref. Code:</b> <?php echo $bc_report_id; ?> <br /> 
<b>Sub-Ref.:</b> <?php echo $view; ?> <br /> <br />
<b>Client:</b> <?php echo "{$client_name} ({$client_email})"; ?> <br /> <br />
<b>Batch:</b> <?php echo $batch; ?> <br /> <br /> 
<b>Status:</b> <?php echo (!empty($status))?"<div class=\"btn btn-{$status_div}\">{$status}</div>":"<div class=\"btn btn-danger\">{$status}</div>"; ?>

<div class="body-header"><i class="fa fa-tag" aria-hidden="true"></i> Verified Information</div>

<table class="table table-striped table-hover">

<thead><tr class="gen-title"><th>Verification Type</th><th>Source</th><th>Comment</th></tr></thead>
<tbody><tr><th><?php echo "<u>" . strtoupper($verification_type) . "</u> " . $education; ?></th><td><?php echo $source; ?></td><td><?php echo $comment; ?></td></tr></tbody>

<thead><tr class="gen-title"><th>Investigation Officer</th><th>Date Received</th><th>Date Submitted</th></tr></thead>
<tbody><tr><td><?php echo $investigation_officer; ?></td><td><?php echo $date_sent_to_agent; ?></td><td><?php echo $date_received_from_agent; ?></td></tr></tbody>

</table>

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
var conf_text = "report";
//-->
</script>

<script src="js/general-form.js"></script>

<?php if(!isset($_REQUEST["gh"])){ ?>

</div>
</div>

</div>

<?php require_once("../includes/portal-footer.php"); } ?>