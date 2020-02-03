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

$activity = "Deleted {$i} foreign CV cover letter(s).";
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
$completion_date = tp_input("completion_date");
$client = np_input("client");
$provided_by = tp_input("provided_by");
$subject = tp_input("subject");
$institution = tp_input("institution");
$award_date = tp_input("award_date");
$qualification = tp_input("qualification");
$grade = tp_input("grade");
$course = tp_input("course");
$status = tp_input("status");

if($_SERVER["REQUEST_METHOD"] == "POST" && !empty($edit) && !empty($client)){

$data_array = array(
"client" => $client,
"completion_date" => $completion_date,
"provided_by" => $provided_by,
"subject" => $subject,
"institution" => $institution,
"award_date" => $award_date,
"qualification" => $qualification,
"grade" => $grade,
"course" => $course,
"status" => $status,
"updated_by" => $id,
"date_updated" => $date_time
);

$act = $db->update($data_array, $table, "id = '$edit'");
if($act){
$error = 0;
echo "<div class='success'>Foreign CV cover letter successfully updated.</div>";
}else{
echo "<div class='not-success'>Error. Unable to update Foreign CV cover letter.</div>";
}
}
///////////////////////////////////////////////////////////////////////////////

$search_client = search_option("search_client");
$search_completion_date = search_option("search_completion_date");
$search_subject = search_option("search_subject");
$no_of_rows = search_option("no_of_rows");
$search_start_date = search_option("search_start_date");
$search_end_date = search_option("search_end_date");
$generated_by_me = search_option("generated_by_me", $allow_check);

$where = "WHERE cover_letter_type = '6'";
$where .= (!empty($search_client))?" AND client = '{$search_client}'":"";
$where .= (!empty($search_completion_date))?" AND completion_date = '{$search_completion_date}'":"";
$where .= (!empty($search_subject))?" AND subject LIKE '%{$search_subject}%'":"";
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
$page_link = "{$admin}foreign-cv-cover-letter?pn=";
$link_suffix = "";
$style_class = "general-link";
page_numbers();

if(empty($view) && (empty($edit)||(!empty($edit)&&$error==0)) && (empty($add)||(!empty($add)&&$error==0)) ){
?>

<div class="page-title">Foreign CV Cover Letters <a href="<?php echo $admin; ?>foreign-cv-cover-letter?add=1" class="btn gen-btn general-link float-right">New Cover Letter</a></div>

<form action="<?php echo $admin; ?>foreign-cv-cover-letter" name="search_form" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
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
<label for="search_subject">Subject</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
<input type="text" name="search_subject" id="search_subject" class="form-control" placeholder="The person&#039;s name" value="<?php check_inputted("search_subject", $search_subject); ?>">
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
<form action="<?php echo $admin; ?>foreign-cv-cover-letter" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data" style="overflow-x:auto;">
<input type="hidden" name="pn" value="<?php echo $pn; ?>"> 
<input type="hidden" name="gh" value="1"> 
<input type="hidden" name="delete" value="1"> 
<table class="table table-striped table-hover">
<thead>
<tr class="gen-title">
<th>Date Generated</th>
<th>Client</th>
<th>Completion Date</th>
<th>Subject</th>
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
$subject = $row["subject"];
$date_generated = ($row["date_generated"] != "0000-00-00 00:00:00")?min_full_date($row["date_generated"]):"";
?>
<tr>
<td><?php echo $date_generated; ?></td>
<td><?php echo $client_details; ?></td>
<td><?php echo $completion_date; ?></td>
<td><?php echo $subject; ?></td>
<td><a href="<?php echo $admin; ?>foreign-cv-cover-letter?edit=<?php echo $letter_id; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link" title="Edit Foreign CV cover letter #<?php echo $letter_id; ?>"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></td>
<td><a href="<?php echo $admin; ?>foreign-cv-cover-letter?view=<?php echo $letter_id; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link" title="View Foreign CV cover letter #<?php echo $letter_id; ?>"><i class="fa fa-eye" aria-hidden="true"></i> View</a></td>
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
echo "<div class='not-success'>No Foreign CV cover letters found at the moment.</div>";
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
$provided_by = $row["provided_by"];
$subject = $row["subject"];
$institution = $row["institution"];
$award_date = $row["award_date"];
$qualification = $row["qualification"];
$grade = $row["grade"];
$course = $row["course"];
$status = $row["status"];
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

<div class="back"><a href="<?php echo $admin; ?>foreign-cv-cover-letter?pn=<?php echo $pn; ?>" class="btn gen-btn general-link"><i class="fa fa-arrow-left"></i> Back to Foreign CV Cover Letters</a></div>

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
<tr><th style="width:150px;">Completion Date:</th><td><?php echo $completion_date; ?></td><th style="width:170px;">Provided By:</th><td><?php echo $provided_by; ?></td></tr>
<tr><th>Subject:</th><td><?php echo $subject; ?></td><th>Award Date:</th><td><?php echo $award_date; ?></td></tr>
<tr><th>Institution:</th><td><?php echo $institution; ?></td><th>Qualification:</th><td><?php echo $qualification; ?></td></tr>
<tr><th>Grade:</th><td><?php echo $grade; ?></td><th>Course:</th><td><?php echo $course; ?></td></tr>
<tr><th>Status:</th><td><?php echo $status; ?></td><th></th><td></td></tr>

<?php if(!empty($updated_by)){ ?>
<tr><th>Last Update:</th><td><?php echo $date_updated; ?></td><th>Updated By:</th><td><?php echo $updated_by_details; ?></td></tr>
<?php } ?>
</tbody>
</table>
<div>
<form action="<?php echo $admin; ?>print-report" method="post" runat="server" autocomplete="off" enctype="multipart/form-data" target="_blank">
<input type="hidden" name="foreign_cv_letter" value="1"> 
<input type="hidden" name="reprint" value="1"> 
<input type="hidden" name="completion_date" value="<?php echo $row["completion_date"]; ?>">
<input type="hidden" name="client" value="<?php echo $client; ?>"> 
<input type="hidden" name="provided_by" value="<?php echo $provided_by; ?>"> 
<input type="hidden" name="subject" value="<?php echo $subject; ?>">
<input type="hidden" name="institution" value="<?php echo $institution; ?>">
<input type="hidden" name="award_date" value="<?php echo $award_date; ?>">
<input type="hidden" name="qualification" value="<?php echo $qualification; ?>">
<input type="hidden" name="grade" value="<?php echo $grade; ?>">
<input type="hidden" name="course" value="<?php echo $course; ?>">
<input type="hidden" name="status" value="<?php echo $status; ?>">

<button type="submit" class="btn gen-btn float-left"><i class="fa fa-print"></i> Reprint</button> 
<a href="<?php echo $admin; ?>foreign-cv-cover-letter?edit=<?php echo $view; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link float-right" title="Edit this cover letter"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
</form>
</div>

</div>

</div>
</div>

<?php
}else{
echo "<div class='not-success'>This Foreign CV cover letter does not exist.</div>";
}
}

////==============Add or Edit Cover Letter=============//////
if((!empty($add) || !empty($edit)) && $error == 1){

$client = $completion_date = $provided_by = $subject = $institution = $award_date = $qualification = $grade = $course = $status = "";
if(!empty($edit)){
$result = $db->select("cover_letters", "WHERE id='$edit'", "*", "");
if(count_rows($result) == 1){
$row = fetch_data($result);
$client = $row["client"];
$completion_date = ($row["completion_date"] != "0000-00-00")?$row["completion_date"]:"";
$provided_by = $row["provided_by"];
$subject = $row["subject"];
$institution = $row["institution"];
$award_date = $row["award_date"];
$qualification = $row["qualification"];
$grade = $row["grade"];
$course = $row["course"];
$status = $row["status"];
}
}

$field_name = (!empty($edit))?"edit":"add";
$field_value = (!empty($edit))?$edit:1;
$action_title = (!empty($edit))?"Edit":"Add New";
?>

<div class="back">
<a href="<?php echo $admin; ?>foreign-cv-cover-letter?view=<?php echo $edit; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link"><i class="fa fa-arrow-left"></i> Back to view letter</a>
</div>

<div class="page-title"><?php echo $action_title; ?> Foreign CV Cover Letter</div>

<?php if(!empty($edit)){ ?>

<form action="<?php echo $admin; ?>foreign-cv-cover-letter" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data" style="border:1px solid #eee;">
<input type="hidden" name="gh" value="1"> 
<input type="hidden" name="edit" value="<?php echo $edit; ?>"> 
<input type="hidden" name="pn" value="<?php echo $pn; ?>"> 
<input type="hidden" name="view" value="<?php echo $edit; ?>"> 

<?php }else{ ?>

<form action="<?php echo $admin; ?>print-report" method="post" runat="server" autocomplete="off" enctype="multipart/form-data" target="_blank">
<input type="hidden" name="foreign_cv_letter" value="1"> 
<?php } ?> 

<div class="col-sm-12">
<label for="completion_date">Date*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
<input type="text" name="completion_date" id="completion_date" class="form-control gen-date" onfocus="javascript: $(this).blur();" placeholder="YYYY-MM-DD" value="<?php check_inputted("completion_date", $completion_date); ?>" required>
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
<label for="provided_by">Data Provided by</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user"></i></span>
<input type="text" name="provided_by" id="provided_by" class="form-control" placeholder="Provider&#039;s name" value="<?php check_inputted("provided_by", $provided_by); ?>">
</div>
</div>

<div class="col-sm-12">
<label for="subject">Subject*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user"></i></span>
<input type="text" name="subject" id="subject" class="form-control" placeholder="Candidate names" value="<?php check_inputted("subject", $subject); ?>" required>
</div>
</div>

<div class="col-sm-12">
<label for="institution">Institution*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-university"></i></span>
<input type="text" name="institution" id="institution" class="form-control" placeholder="E.g. King&#039;s College London" value="<?php check_inputted("institution", $institution); ?>" required>
</div>
</div>

<div class="col-sm-12">
<label for="award_date">Award Date*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
<input type="text" name="award_date" id="award_date" class="form-control" placeholder="E.g. 01/07/2014" value="<?php check_inputted("award_date", $award_date); ?>" required>
</div>
</div>

<div class="col-sm-12">
<label for="qualification">Qualification*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-university"></i></span>
<input type="text" name="qualification" id="qualification" class="form-control" placeholder="E.g. Bachelor of Science" value="<?php check_inputted("qualification", $qualification); ?>">
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
<label for="course">Course (Major)</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-university"></i></span>
<input type="text" name="course" id="course" class="form-control" placeholder="E.g. Accounting" value="<?php check_inputted("course", $course); ?>">
</div>
</div>

<div class="col-sm-12">
<label for="status">Status</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-file-text"></i></span>
<input type="text" name="status" id="status" class="form-control" placeholder="E.g. AUTHENTIC" value="<?php check_inputted("status", $status); ?>">
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