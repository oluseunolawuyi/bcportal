<?php if(!isset($_REQUEST["gh"])){ include_once("../includes/admin-header.php"); 
}else{ 
include_once("../includes/gen-header.php");
} ?>

<?php
check_admin("");

$pn = nr_input("pn");

////////////////////////////////////////////////////******************************//////////////

$search_client = search_option("search_client");
$keyword = search_option("keyword");
$search_reference_code = search_option("search_reference_code");
$no_of_rows = search_option("no_of_rows");
$search_batch = search_option("search_batch");
$search_start_date = search_option("search_start_date");
$search_end_date = search_option("search_end_date");

$where = "WHERE id > '0'";
$where .= (!empty($search_client))?" AND client = '{$search_client}'":"";
$where .= (!empty($keyword))?" AND names LIKE '%{$keyword}%'":"";
$where .= (!empty($search_reference_code))?" AND reference_code = '{$search_reference_code}'":"";
$where .= (!empty($search_batch))?" AND batch = '{$search_batch}'":"";
$where .= (!empty($search_start_date) && !empty($search_end_date))?" AND date_time BETWEEN '{$search_start_date} 00:00:00' AND '{$search_end_date} 23:59:59'":"";
$where .= (!empty($search_start_date) && empty($search_end_date))?" AND date_time >= '{$search_start_date} 00:00:00'":"";
$where .= (empty($search_start_date) && !empty($search_end_date))?" AND date_time <= '{$search_end_date} 23:59:59'":"";

$result = $db->select("cv_reports_log", "$where", "*", "ORDER BY id DESC");

$per_view = 20;
$per_view = (!empty($no_of_rows))?$no_of_rows:$per_view;
$page_link = "{$admin}cv-report-log?pn=";
$link_suffix = "";
$style_class = "general-link";
page_numbers();
?>

<div class="page-title">Certificate Verification Report Log</div>

<form action="<?php echo $admin; ?>cv-report-log" name="search_form" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
<input type="hidden" name="gh" value="1"> 
<div class="search-dates">

<div class="col-md-5">
<label for="search_client">Client</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
<select name="search_client" id="search_client" title="Select a client" class="form-control js-example-basic-single" style="width:100%">
<option value="">**Select a client**</option>
<?php 
$result2 = $db->select("cv_reports_log", "", "DISTINCT client", "ORDER BY client ASC");
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

<div class="col-md-5">
<label for="keyword">Keyword</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-file-text" aria-hidden="true"></i></span>
<input type="text" name="keyword" id="keyword" class="form-control" placeholder="Type a candidate&#039;s name" value="<?php check_inputted("keyword", $keyword); ?>">
</div>
</div>

<div class="col-md-2">
<label for="search_reference_code">Ref. Code</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-code" aria-hidden="true"></i></span>
<input type="text" name="search_reference_code" id="search_reference_code" class="form-control only-no" placeholder="E.g. 15" value="<?php check_inputted("search_reference_code", $search_reference_code); ?>">
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
<br />
<button type="submit" class="btn gen-btn"><i class="fa fa-search"></i> Search</button>
</div>

</div>
</form>

<?php
$offset = ($per_view * $pn) - $per_view;

$result = $db->select("cv_reports_log", "$where", "*", "ORDER BY id DESC", "LIMIT {$offset},{$per_view}");

if(count_rows($result) > 0){
?>
<form style="overflow-x:auto;">
<table class="table table-striped table-hover">
<thead>
<tr class="gen-title">
<th>Date</th>
<th>Ref. Code</th>
<th>Client</th>
<th style="width:50px;">Batch</th>
<th>Names</th>
<th>Institution</th>
<th>Status</th>
<th>Updated By</th>
</tr>
</thead>
<tbody>
<?php
while($row = fetch_data($result)){
$report_id = $row["id"];
$reference_code = $row["reference_code"];
$client = $row["client"];
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$client_email = in_table("email","reg_users","WHERE id = '{$client}'","email");
$names = $row["names"];
$institution = $row["institution"];
$batch = $row["batch"];
$status = $row["status"];
$updated_by = $row["updated_by"];
$updated_by_name = in_table("name","reg_users","WHERE id = '{$updated_by}'","name");
$updated_by_email = in_table("email","reg_users","WHERE id = '{$updated_by}'","email");
$updated_by = (!empty($updated_by))?"{$updated_by_name}<br>({$updated_by_email})":"";
$update_date = min_full_date($row["date_time"]);
?>
<tr>
<td><?php echo $update_date; ?></td>
<td style="width:30px;"><?php echo formatQty($reference_code); ?></td>
<td><?php echo "{$client_name}<br>({$client_email})"; ?></td>
<td><?php echo formatQty($batch); ?></td>
<td><?php echo $names; ?></td>
<td><?php echo $institution; ?></td>
<td><?php 
$status = substr($status,0,-1);
$status = str_replace(","," <i class=\"fa fa-chevron-circle-right\" style=\"font-size:15px\"></i> ",$status);
echo $status; 
?></td>
<td><?php echo $updated_by; ?></td>
</tr>
<?php 
}
?>
</tbody>
</table>
</form>
<?php
echo ($last_page>1)?"<div class=\"page-nos\">" . $center_pages . "</div>":"";
}else{
echo "<div class='not-success'>No Background Checks report log found at the moment.</div>";
}
?>

<script src="js/general-form.js"></script>

<?php if(!isset($_REQUEST["gh"])){ ?>

</div>
</div>

</div>

<?php require_once("../includes/portal-footer.php"); } ?>