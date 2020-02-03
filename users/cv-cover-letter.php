<?php if(!isset($_REQUEST["gh"])){ include_once("../includes/admin-header.php"); 
}else{ 
include_once("../includes/gen-header.php");
}

admin_role_redirect("manage_cover_letters");

$table = "cover_letters";

/////////////////////////////////////////////////////////////////////////////////////////
if(isset($_POST["delete"]) && isset($_POST["del"])){
$i = 0;
if(is_array($_POST["del"])){
foreach ($_POST["del"] as $k => $c) {
if($c != ""){ 
$c = testQty($c);
$act = $db->delete($table, " id='{$c}'");	
$i++;			
}else{
continue;
}
}

if($act){

$activity = "Deleted {$i} CV cover letter(s) 1.";
$audit_data_array = array(
"user_id" => "'$id'",
"name" => "'$username'",
"email" => "'$user_email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$db->insert($audit_data_array, "audit_log");

echo "<div class='success'>{$i} cover letter(s) successfully deleted.</div>";
}else{
echo "<div class='not-success'>Error. Unable to delete cover letter(s).</div>";
}

}else{
echo "<div class='not-success'>Atleast one field must be selected.</div>";
}
}
///////////////////////////////////////////////////////////////////////

$edit = nr_input("edit");
$add = nr_input("add");
$pn = nr_input("pn");
$view = nr_input("view");
$allow_check = np_input("allow_check");

////////////////============ Update CV Cover Letter =============///////////////
$reference_no = tp_input("reference_no");
$completion_date = tp_input("completion_date");
$client = np_input("client");
$client_designation = tp_input("client_designation");
$attention = tp_input("attention");
$re = tp_input("re");
$invoice_attachment = tp_input("invoice_attachment");
$signatory = tp_input("signatory");
$names = tp_input("names");
$school = tp_input("school");
$year = tp_input("year");
$qualification = tp_input("qualification");
$grade = tp_input("grade");
$course = tp_input("course");
$transaction_ref = tp_input("transaction_ref");
$comment = tp_input("comment");
$report_source = tp_input("report_source");

if($_SERVER["REQUEST_METHOD"] == "POST" && !empty($edit) && !empty($client) && !empty($completion_date)){

$data_array = array(
"client" => $client,
"completion_date" => $completion_date,
"attention" => $attention,
"reference_no" => $reference_no,
"client_designation" => $client_designation,
"re" => $re,
"invoice_attachment" => $invoice_attachment,
"signatory" => $signatory,
"names" => $names,
"school" => $school,
"year" => $year,
"qualification" => $qualification,
"grade" => $grade,
"course" => $course,
"transaction_ref" => $transaction_ref,
"comment" => $comment,
"report_source" => $report_source,
"updated_by" => $id,
"date_updated" => $date_time
);

$act = $db->update($data_array, $table, "id = '$edit'");
if($act){
$error = 0;
echo "<div class='success'>CV cover letter 1 successfully updated.</div>";
}else{
echo "<div class='not-success'>Error. Unable to update CV cover letter 1.</div>";
}
}
///////////////////////////////////////////////////////////////////////////////

$search_client = search_option("search_client");
$search_completion_date = search_option("search_completion_date");
$search_attention = search_option("search_attention");
$no_of_rows = search_option("no_of_rows");
$search_start_date = search_option("search_start_date");
$search_end_date = search_option("search_end_date");
$generated_by_me = search_option("generated_by_me", $allow_check);

$where = "WHERE cover_letter_type = '2'";
$where .= (!empty($search_client))?" AND client = '{$search_client}'":"";
$where .= (!empty($search_completion_date))?" AND completion_date = '{$search_completion_date}'":"";
$where .= (!empty($search_attention))?" AND attention LIKE '%{$search_attention}%'":"";
if(!empty($search_start_date) && !empty($search_end_date)){	
$where .= " AND date_generated BETWEEN '{$search_start_date} 00:00:00' AND '{$search_end_date} 23:59:59'";
}else if(!empty($search_start_date)){
$where .= " AND date_generated >= '{$search_start_date} 00:00:00'";
}else if(!empty($search_end_date)){
$where .= " AND date_generated <= '{$search_end_date} 23:59:59'";
}
$where .= (!empty($generated_by_me))?" AND generated_by = '{$id}'":"";

$result = $db->select($table, $where, "*", "ORDER BY id DESC");

$per_view = 20;
$per_view = (!empty($no_of_rows))?$no_of_rows:$per_view;
$page_link = "{$admin}cv-cover-letter?pn=";
$link_suffix = "";
$style_class = "general-link";
page_numbers();

if(empty($view) && (empty($edit)||(!empty($edit)&&$error==0)) && (empty($add)||(!empty($add)&&$error==0)) ){
?>

<div class="page-title">CV Cover Letter 1 <a href="<?php echo $admin; ?>cv-cover-letter?add=1" class="btn gen-btn general-link float-right">New Cover Letter</a></div>

<form action="<?php echo $admin; ?>cv-cover-letter" name="search_form" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
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
$result2 = $db->select($table, $where, "DISTINCT client", "ORDER BY client ASC");
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
<label for="search_completion_date">Completion Date</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
<input type="text" name="search_completion_date" id="search_completion_date" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php check_inputted("search_completion_date", $search_completion_date); ?>">
</div>
</div>

<div class="col-md-3">
<label for="search_attention">Attention</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-bullhorn" aria-hidden="true"></i></span>
<input type="text" name="search_attention" id="search_attention" class="form-control" placeholder="The person&#039;s name" value="<?php check_inputted("search_attention", $search_attention); ?>">
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
<label for="search_start_date">Gen. Start Date</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
<input type="text" name="search_start_date" id="search_start_date" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php check_inputted("search_start_date", $search_start_date); ?>">
</div>
</div>

<div class="col-md-3">
<label for="search_end_date">Gen. End Date</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
<input type="text" name="search_end_date" id="search_end_date" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php check_inputted("search_end_date", $search_end_date); ?>">
</div>
</div>


<div class="col-md-4">
<br />
<div class="form-group input-group">
<span class="input-group-addon"><input type="checkbox" name="generated_by_me" id="generated_by_me" value="<?php echo $id; ?>" <?php echo (!empty($generated_by_me))?"checked":""; ?>></span>
<label for="generated_by_me"> &nbsp;&nbsp; Generated by me</label>
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

$result = $db->select($table, $where, "*", "ORDER BY id DESC", "LIMIT {$offset},{$per_view}");

if(count_rows($result) > 0){
?>
<form action="<?php echo $admin; ?>cv-cover-letter" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data" style="overflow-x:auto;">
<input type="hidden" name="pn" value="<?php echo $pn; ?>"> 
<input type="hidden" name="gh" value="1"> 
<input type="hidden" name="delete" value="1"> 
<table class="table table-striped table-hover">
<thead>
<tr class="gen-title">
<th>Date Generated</th>
<th>Client</th>
<th>Completion Date</th>
<th>Attention</th>
<th style="width:70px;">Option</th>
<th style="width:70px;">Details</th>
<th style="width:30px;"><input type="checkbox" name="sel_all" id="delG" class="sel-group" value=""></th>
</tr>
</thead>
<tbody>
<?php
while($row = fetch_data($result)){
$letter_id = $row["id"];
$client = $row["client"];
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$client_email = in_table("email","reg_users","WHERE id = '{$client}'","email");
$client_details = $client_name . "<br>" . break_long($client_email, "(", ")");
$completion_date = ($row["completion_date"] != "0000-00-00")?min_sub_date($row["completion_date"]):"";
$attention = $row["attention"];
$batch_category = $row["batch_category"];
$date_generated = ($row["date_generated"] != "0000-00-00 00:00:00")?min_full_date($row["date_generated"]):"";
?>
<tr>
<td><?php echo $date_generated; ?></td>
<td><?php echo $client_details; ?></td>
<td><?php echo $completion_date; ?></td>
<td><?php echo $attention; ?></td>
<td><a href="<?php echo $admin; ?>cv-cover-letter?edit=<?php echo $letter_id; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link" title="Edit CV cover letter #<?php echo $letter_id; ?>"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></td>
<td><a href="<?php echo $admin; ?>cv-cover-letter?view=<?php echo $letter_id; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link" title="View CV cover letter #<?php echo $letter_id; ?>"><i class="fa fa-eye" aria-hidden="true"></i> View</a></td>
<td><input type="checkbox" name="del[<?php echo $d; ?>]" id="del<?php echo $d; ?>" class="delG" value="<?php echo $letter_id; ?>"></td>
</tr>
<?php 
$d++;
}
?>
<tr><td colspan="7"><input class="sub-del" type="submit" value=" "><button type="button" class="btn del-btn gen-btn float-right"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete selected letter(s)</button></td></tr>
</tbody>
</table>
</form>
<?php
echo ($last_page>1)?"<div class=\"page-nos\">" . $center_pages . "</div>":"";
}else{
echo "<div class='not-success'>No CV cover letter 1 found at the moment.</div>";
}

}

/////============View Report=============////
if(!empty($view) && (empty($edit) || (!empty($edit) && $error == 0))){
$result = $db->select($table, "WHERE id='$view'", "*");

if(count_rows($result) == 1){
$row = fetch_data($result);
$client = $row["client"];
$client_name = in_table("name","reg_users","WHERE id = '{$client}'","name");
$client_email = in_table("email","reg_users","WHERE id = '{$client}'","email");
$client_details = $client_name . " (" . $client_email . ")";
$completion_date = ($row["completion_date"] != "0000-00-00")?sub_date($row["completion_date"]):"";
$attention = $row["attention"];
$reference_no = $row["reference_no"];
$client_designation = $row["client_designation"];
$re = $row["re"];
$invoice_attachment = $row["invoice_attachment"];
$signatory = $row["signatory"];
$signatory_name = in_table("name","reg_users","WHERE id = '{$signatory}'","name");
$signatory_name = (!empty($signatory_name))?$signatory_name:"";
$signatory_email = in_table("email","reg_users","WHERE id = '{$signatory}'","email");
$signatory_email = (!empty($signatory_email))?$signatory_email:"";

$names = $row["names"];
$school = $row["school"];
$year = $row["year"];
$qualification = $row["qualification"];
$grade = $row["grade"];
$course = $row["course"];
$transaction_ref = $row["transaction_ref"];
$comment = $row["comment"];
$report_source = $row["report_source"];

$generated_by = $row["generated_by"];
$generated_by_name = in_table("name","reg_users","WHERE id = '{$generated_by}'","name");
$generated_by_email = in_table("email","reg_users","WHERE id = '{$generated_by}'","email");
$generated_by_details = $generated_by_name . " (" . $generated_by_email . ")";
$date_generated = ($row["date_generated"] != "0000-00-00 00:00:00")?full_date($row["date_generated"]):"";
$updated_by = $row["updated_by"];
$updated_by_name = in_table("name","reg_users","WHERE id = '{$updated_by}'","name");
$updated_by_email = in_table("email","reg_users","WHERE id = '{$updated_by}'","email");
$updated_by_details = (!empty($updated_by))?$updated_by_name . " (" . $updated_by_email . ")":"";
$date_updated = ($row["date_updated"] != "0000-00-00 00:00:00")?full_date($row["date_updated"]):"";
?>
<style>
<!--
div table thead tr th, div table tr th, div table tbody tr td, div table tr td{
dtext-align:left !important;
}
.details-table *{
text-align:left !important;
}
-->
</style>
<div class="reply-content-wrapper ">

<div class="back"><a href="<?php echo $admin; ?>cv-cover-letter?pn=<?php echo $pn; ?>" class="btn gen-btn general-link"><i class="fa fa-arrow-left"></i> Back to CV cover letters</a></div>

<div class="view-wrapper ">

<div class="view-header ">
<div class="header-img"><img src="images/post.jpg" ></div>
<div class="header-content">
<div class="view-title">Client:  <b><?php echo $client_details; ?></b></div>
<div class="view-title-details">Generated on <b><?php echo $date_generated; ?></b> by <b><?php echo $generated_by_details; ?></b></div>
</div>
</div>

<div class="view-content">

<table class="table table-striped table-hover details-table"><tbody>
<tr><th style="width:150px;">Completion Date:</th><td><?php echo $completion_date; ?></td><th style="width:170px;">Attention:</th><td><?php echo $attention; ?></td></tr>
<tr><th>Ref. No.:</th><td><?php echo $reference_no; ?></td><th>Client Designation:</th><td><?php echo $client_designation; ?></td></tr>
<tr><th>RE:</th><td><?php echo $re; ?></td><th>Invoice Attachment:</th><td><?php echo $invoice_attachment; ?></td></tr>

<tr><th>Names:</th><td><?php echo $names; ?></td><th>School:</th><td><?php echo $school; ?></td></tr>
<tr><th>Year:</th><td><?php echo $year; ?></td><th>Qualification:</th><td><?php echo $qualification; ?></td></tr>
<tr><th>Grade:</th><td><?php echo $grade; ?></td><th>Course:</th><td><?php echo $course; ?></td></tr>
<tr><th>Trans. Ref.:</th><td><?php echo $transaction_ref; ?></td><th>Report Source:</th><td><?php echo $report_source; ?></td></tr>
<tr><th>Comment:</th><td colspan="3"><?php echo $comment; ?></td></tr>


<?php if(!empty($signatory)){ ?>
<tr><th>Signatory Name:</th><td><?php echo $signatory_name; ?></td><th>Signatory Email:</th><td><?php echo $signatory_email; ?></td></tr>
<?php } ?>

<?php if(!empty($updated_by)){ ?>
<tr><th>Last Update:</th><td><?php echo $date_updated; ?></td><th>Updated By:</th><td><?php echo $updated_by_details; ?></td></tr>
<?php } ?>
</tbody>
</table>

<div>
<form action="<?php echo $admin; ?>print-report" method="post" runat="server" autocomplete="off" enctype="multipart/form-data" target="_blank">
<input type="hidden" name="cv_letter" value="1"> 
<input type="hidden" name="reprint" value="1"> 
<input type="hidden" name="reference_no" value="<?php echo $reference_no; ?>">
<input type="hidden" name="completion_date" value="<?php echo $row["completion_date"]; ?>">
<input type="hidden" name="client" value="<?php echo $client; ?>"> 
<input type="hidden" name="client_designation" value="<?php echo $client_designation; ?>">
<input type="hidden" name="attention" value="<?php echo $attention; ?>"> 
<input type="hidden" name="re" value="<?php echo $re; ?>">
<input type="hidden" name="invoice_attachment" value="<?php echo $invoice_attachment; ?>">
<input type="hidden" name="signatory" value="<?php echo $signatory; ?>">
<input type="hidden" name="names" value="<?php echo $names; ?>">
<input type="hidden" name="school" value="<?php echo $school; ?>">
<input type="hidden" name="year" value="<?php echo $year; ?>">
<input type="hidden" name="qualification" value="<?php echo $qualification; ?>">
<input type="hidden" name="grade" value="<?php echo $grade; ?>">
<input type="hidden" name="course" value="<?php echo $course; ?>">
<input type="hidden" name="transaction_ref" value="<?php echo $transaction_ref; ?>">
<input type="hidden" name="comment" value="<?php echo $comment; ?>">
<input type="hidden" name="report_source" value="<?php echo $report_source; ?>">

<button type="submit" class="btn gen-btn float-left"><i class="fa fa-print"></i> Reprint</button> 
<a href="<?php echo $admin; ?>cv-cover-letter?edit=<?php echo $view; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link float-right" title="Edit this cover letter"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
</form>
</div>


</div>

</div>
</div>

<?php
}else{
echo "<div class='not-success'>This CV cover letter 1 does not exist.</div>";
}
}

////==============Add or Edit Cover Letter=============//////
if((!empty($add) || !empty($edit)) && $error == 1){

$client = $completion_date = $attention = $reference_no = $client_designation = $re = $invoice_attachment = $signatory = $names = $school = $year = $qualification = $grade = $course = $transaction_ref = $comment = $report_source = "";
if(!empty($edit)){
$result = $db->select("cover_letters", "WHERE id='$edit'", "*", "");
if(count_rows($result) == 1){
$row = fetch_data($result);
$client = $row["client"];
$completion_date = ($row["completion_date"] != "0000-00-00")?$row["completion_date"]:"";
$attention = $row["attention"];
$reference_no = $row["reference_no"];
$client_designation = $row["client_designation"];
$re = $row["re"];
$invoice_attachment = $row["invoice_attachment"];
$signatory = $row["signatory"];
$names = $row["names"];
$school = $row["school"];
$year = $row["year"];
$qualification = $row["qualification"];
$grade = $row["grade"];
$course = $row["course"];
$transaction_ref = $row["transaction_ref"];
$comment = $row["comment"];
$report_source = $row["report_source"];
}
}

$field_name = (!empty($edit))?"edit":"add";
$field_value = (!empty($edit))?$edit:1;
$action_title = (!empty($edit))?"Edit":"Add New";
?>

<div class="back">
<a href="<?php echo $admin; ?>cv-cover-letter?view=<?php echo $edit; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link"><i class="fa fa-arrow-left"></i> Back to view letter</a>
</div>

<div class="page-title"><?php echo $action_title; ?> CV Cover Letter 1</div>

<?php if(!empty($edit)){ ?>

<form action="<?php echo $admin; ?>cv-cover-letter" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data" style="border:1px solid #eee;">
<input type="hidden" name="gh" value="1"> 
<input type="hidden" name="edit" value="<?php echo $edit; ?>"> 
<input type="hidden" name="pn" value="<?php echo $pn; ?>"> 
<input type="hidden" name="view" value="<?php echo $edit; ?>"> 

<?php }else{ ?>

<form action="<?php echo $admin; ?>print-report" method="post" runat="server" autocomplete="off" enctype="multipart/form-data" target="_blank">
<input type="hidden" name="cv_letter" value="1"> 
<?php } ?>

<div class="col-sm-12">
<label for="reference_no">Reference</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-code"></i></span>
<input type="text" name="reference_no" id="reference_no" class="form-control" placeholder="Our ref." value="<?php check_inputted("reference_no", $reference_no); ?>">
</div>
</div>

<div class="col-sm-12">
<label for="completion_date">Date*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
<input type="text" name="completion_date" id="completion_date" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php check_inputted("completion_date", $completion_date); ?>" required>
</div>
</div>

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
<label for="client_designation">Client&#039;s Designation</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user"></i></span>
<input type="text" name="client_designation" id="client_designation" class="form-control" placeholder="Client&#039;s designation" value="<?php check_inputted("client_designation", $client_designation); ?>">
</div>
</div>

<div class="col-sm-12">
<label for="attention">Attention</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-bullhorn"></i></span>
<input type="text" name="attention" id="attention" class="form-control" placeholder="Staff member&#039;s name" value="<?php check_inputted("attention", $attention); ?>">
</div>
</div>

<div class="col-sm-12">
<label for="re">RE*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-globe"></i></span>
<input type="text" name="re" id="re" class="form-control" placeholder="RE" value="<?php check_inputted("re", $re); ?>" required>
</div>
</div>

<div class="col-sm-12">
<label for="names">Names*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user"></i></span>
<input type="text" name="names" id="names" class="form-control" placeholder="Candidate names" value="<?php check_inputted("names", $names); ?>" required>
</div>
</div>

<div class="col-sm-12">
<label for="school">School*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-university"></i></span>
<input type="text" name="school" id="school" class="form-control" placeholder="E.g. University of Lagos" value="<?php check_inputted("school", $school); ?>">
</div>
</div>

<div class="col-sm-12">
<label for="year">Year*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
<input type="text" name="year" id="year" class="form-control" placeholder="E.g. 2018" value="<?php check_inputted("year", $year); ?>">
</div>
</div>

<div class="col-sm-12">
<label for="qualification">Qualification*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-university"></i></span>
<input type="text" name="qualification" id="qualification" class="form-control" placeholder="E.g. B.Sc." value="<?php check_inputted("qualification", $qualification); ?>">
</div>
</div>

<div class="col-sm-12">
<label for="grade">Grade</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-university"></i></span>
<input type="text" name="grade" id="grade" class="form-control" placeholder="E.g. Ist Class" value="<?php check_inputted("grade", $grade); ?>">
</div>
</div>

<div class="col-sm-12">
<label for="course">Course</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-university"></i></span>
<input type="text" name="course" id="course" class="form-control" placeholder="E.g. Accounting" value="<?php check_inputted("course", $course); ?>">
</div>
</div>

<div class="col-sm-12">
<label for="transaction_ref">Transaction Ref.</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-globe"></i></span>
<input type="text" name="transaction_ref" id="transaction_ref" class="form-control" placeholder="E.g. 23323462646623525" value="<?php check_inputted("transaction_ref", $transaction_ref); ?>">
</div>
</div>

<div class="col-sm-12">
<label for="comment">Comment</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-file-text"></i></span>
<input type="text" name="comment" id="comment" class="form-control" placeholder="E.g. AUTHENTIC" value="<?php check_inputted("comment", $comment); ?>">
</div>
</div>

<div class="col-sm-12">
<label for="report_source">Report Source</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-file-text"></i></span>
<input type="text" name="report_source" id="report_source" class="form-control" placeholder="Source of the report" value="<?php check_inputted("report_source", $report_source); ?>">
</div>
</div>

<div class="col-sm-12">
<label for="invoice_attachment">Invoice Attachment</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-file-text"></i></span>
<input type="text" name="invoice_attachment" id="invoice_attachment" class="form-control" placeholder="Statement on invoice attachment" value="<?php check_inputted("invoice_attachment", $invoice_attachment); ?>">
</div>
</div>

<div class="col-sm-12">
<label for="signatory">Signatory</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user"></i></span>
<select name="signatory" id="signatory" title="Select a signatory" class="form-control">
<option value="">**Select a signatory**</option>
<?php 
$result2 = $db->select("reg_users", "WHERE admin = '1' AND signature = '1'", "*", "ORDER BY name ASC");
if(count_rows($result2) > 0){
while($row2 = fetch_data($result2)){
$signatory_id = $row2["id"];
$signatory_name = $row2["name"];
echo "<option value='{$signatory_id}'";
check_selected("signatory", $signatory_id, $signatory); 
echo ">{$signatory_name}</option>";
}
}
?>
</select>
</div>
</div>
                     
<div class="submit-div col-sm-12">
<button class="btn gen-btn float-right">
<?php if(!empty($add)){ ?>
<i class="fa fa-file-text"></i> Generate Letter
<?php }else{ ?>
<i class="fa fa-upload"></i> Save Letter
<?php } ?>
</button>
</div>

</form>

<?php } ?>

<script>
<!--
var conf_text = "cover letter";
//-->
</script>

<script src="js/general-form.js"></script>

<?php if(!isset($_REQUEST["gh"])){ ?>

</div>
</div>

</div>

<?php require_once("../includes/portal-footer.php"); } ?>