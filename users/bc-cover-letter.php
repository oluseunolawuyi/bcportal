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

$activity = "Deleted {$i} BC cover letter(s).";
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

////////////////============ Update BC Cover Letter =============///////////////
$client = np_input("client");
$completion_date = tp_input("completion_date");
$attention = tp_input("attention");

$c = 0;
$batch_category = "";
if(isset($_POST["batch"]) && !empty($_POST["batch"])){
foreach($_POST["batch"] as $value){ 
$batch = test_input($value);
$candidates = test_input($_POST["candidates"][$c]);
$batch_category .= "{$batch}+*+*{$candidates}-/-/";
$c++;
}
}

if($_SERVER["REQUEST_METHOD"] == "POST" && !empty($edit) && !empty($client) && !empty($completion_date) && !empty($batch_category)){

$data_array = array(
"client" => $client,
"completion_date" => $completion_date,
"attention" => $attention,
"batch_category" => $batch_category,
"updated_by" => $id,
"date_updated" => $date_time
);

$act = $db->update($data_array, $table, "id = '$edit'");
if($act){
$error = 0;
echo "<div class='success'>BC cover letter successfully updated.</div>";
}else{
echo "<div class='not-success'>Error. Unable to update BC cover letter.</div>";
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

$where = "WHERE cover_letter_type = '1'";
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
$page_link = "{$admin}bc-cover-letter?pn=";
$link_suffix = "";
$style_class = "general-link";
page_numbers();

if(empty($view) && (empty($edit)||(!empty($edit)&&$error==0)) && (empty($add)||(!empty($add)&&$error==0)) ){
?>

<div class="page-title">BC Cover Letters <a href="<?php echo $admin; ?>bc-cover-letter?add=1" class="btn gen-btn general-link float-right">New BC Cover Letter</a></div>

<form action="<?php echo $admin; ?>bc-cover-letter" name="search_form" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
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
<form action="<?php echo $admin; ?>bc-cover-letter" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data" style="overflow-x:auto;">
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
<td><a href="<?php echo $admin; ?>bc-cover-letter?edit=<?php echo $letter_id; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link" title="Edit BC cover letter #<?php echo $letter_id; ?>"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></td>
<td><a href="<?php echo $admin; ?>bc-cover-letter?view=<?php echo $letter_id; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link" title="View BC cover letter #<?php echo $letter_id; ?>"><i class="fa fa-eye" aria-hidden="true"></i> View</a></td>
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
echo "<div class='not-success'>No Background Checks cover letters found at the moment.</div>";
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
$batch_category = $row["batch_category"];
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
text-align:left !important;
}
-->
</style>
<div class="reply-content-wrapper ">

<div class="back"><a href="<?php echo $admin; ?>bc-cover-letter?pn=<?php echo $pn; ?>" class="btn gen-btn general-link"><i class="fa fa-arrow-left"></i> Back to BC cover letters</a></div>

<div class="view-wrapper ">

<div class="view-header ">
<div class="header-img"><img src="images/post.jpg" ></div>
<div class="header-content">
<div class="view-title">Client:  <b><?php echo $client_details; ?></b></div>
<div class="view-title-details">Generated on <b><?php echo $date_generated; ?></b> by <b><?php echo $generated_by_details; ?></b></div>
</div>
</div>

<div class="view-content">

<div class="col-md-6"><b>Completion Date:</b> <?php echo $completion_date; ?></div>
<div class="col-md-6"><b>Attention:</b> <?php echo $attention; ?></div>

<?php if(!empty($updated_by)){ ?>
<div class="col-md-6"><b>Last Update:</b> <?php echo $date_updated; ?></div>
<div class="col-md-6"><b>Updated By:</b> <?php echo $updated_by_details; ?></div>
<?php } ?>

<?php 
if(!empty($batch_category)){ 
$batch_category_array = explode("-/-/",$batch_category);
?>
<table class="table table-striped table-hover">
<tbody>
<?php
foreach($batch_category_array as $value){
if(!empty($value)){
$value_array = explode("+*+*",$value);
$batch_title = $value_array[0];
$batch_content = $value_array[1];
?>
<tr>
<th style="width:100px;">
BATCH <?php echo $batch_title; ?>
</th>
<td>
<?php 
if(!empty($batch_content)){
$batch_content = $batch_content . ",";
$batch_content_array = explode(",",$batch_content);
foreach($batch_content_array as $val){
if(!empty($val)){
echo "<a class=\"btn gen-btn\">{$val}</a> ";
}
}
}
?>
</td>
</tr>
<?php
}
}
?>
</tbody></table>
<?php
}
?>

<div>
<form action="<?php echo $admin; ?>print-report" method="post" runat="server" autocomplete="off" enctype="multipart/form-data" target="_blank">
<input type="hidden" name="bc_letter" value="1"> 
<input type="hidden" name="reprint" value="1"> 
<input type="hidden" name="client" value="<?php echo $client; ?>"> 
<input type="hidden" name="completion_date" value="<?php echo $row["completion_date"]; ?>"> 
<input type="hidden" name="attention" value="<?php echo $attention; ?>"> 
<?php
if(!empty($batch_category)){ 
$batch_category_array = explode("-/-/",$batch_category);
foreach($batch_category_array as $value){
if(!empty($value)){
$value_array = explode("+*+*",$value);
$batch_title = $value_array[0];
$batch_content = $value_array[1];
?>
<input type="hidden" name="batch[]" value="<?php echo $batch_title; ?>" >
<input type="hidden" name="candidates[]" value="<?php echo $batch_content; ?>" >
<?php
}
}
}
?>
<button type="submit" class="btn gen-btn float-left"><i class="fa fa-print"></i> Reprint</button> 
<a href="<?php echo $admin; ?>bc-cover-letter?edit=<?php echo $view; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link float-right" title="Edit this cover letter"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
</form>
</div>

</div>

</div>
</div>

<?php
}else{
echo "<div class='not-success'>This Background Checks cover letter does not exist.</div>";
}
}

////==============Add or Edit Cover Letter=============//////
if((!empty($add) || !empty($edit)) && $error == 1){

$client = $completion_date = $attention = $batch_category = "";
if(!empty($edit)){
$result = $db->select("cover_letters", "WHERE id='$edit'", "*", "");
if(count_rows($result) == 1){
$row = fetch_data($result);
$client = $row["client"];
$completion_date = ($row["completion_date"] != "0000-00-00")?$row["completion_date"]:"";
$attention = $row["attention"];
$batch_category = $row["batch_category"];
}
}

$field_name = (!empty($edit))?"edit":"add";
$field_value = (!empty($edit))?$edit:1;
$action_title = (!empty($edit))?"Edit":"Add New";
?>

<div class="back">
<a href="<?php echo $admin; ?>bc-cover-letter?view=<?php echo $edit; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link"><i class="fa fa-arrow-left"></i> Back to view letter</a>
</div>

<div class="page-title"><?php echo $action_title; ?> Background Checks Cover Letter</div>

<?php if(!empty($edit)){ ?>

<form action="<?php echo $admin; ?>bc-cover-letter" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data" style="border:1px solid #eee;">
<input type="hidden" name="gh" value="1"> 
<input type="hidden" name="edit" value="<?php echo $edit; ?>"> 
<input type="hidden" name="pn" value="<?php echo $pn; ?>"> 
<input type="hidden" name="view" value="<?php echo $edit; ?>"> 

<?php }else{ ?>

<form action="<?php echo $admin; ?>print-report" method="post" runat="server" autocomplete="off" enctype="multipart/form-data" target="_blank">
<input type="hidden" name="bc_letter" value="1"> 
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
<label for="completion_date">Completion Date*</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
<input type="text" name="completion_date" id="completion_date" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php check_inputted("completion_date", $completion_date); ?>">
</div>
</div>

<div class="col-sm-12">
<label for="attention">Attention</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-bullhorn"></i></span>
<input type="text" name="attention" id="attention" class="form-control" placeholder="The person&#039;s name" value="<?php check_inputted("attention", $attention); ?>">
</div>
</div>

<table class="table table-striped table-hover batches">

<tr>
<th class="gen-title" style="width:30px">S/N</th>
<th class="gen-title" style="width:200px;">Batch</th>
<th class="gen-title">Name(s) of Subject</th>
<th class="gen-title" style="width:30px"><i class="fa fa-minus"></i></th>
</tr>

<?php 
$c = 0;
if(!empty($batch_category)){ 
$batch_category_array = explode("-/-/",$batch_category);

foreach($batch_category_array as $value){
if(!empty($value)){
$c++;
$value_array = explode("+*+*",$value);
$batch_title = $value_array[0];
$batch_content = $value_array[1];
?>
<tr class="sub-row" id="row<?php echo $c; ?>">
<td class="sub-cell" id="td<?php echo $c; ?>"><?php echo $c; ?></td>
<td><input type="text" name="batch[]" id="batch" class="form-control only-no" placeholder="Batch no." value="<?php echo $batch_title; ?>" required></td>
<td><textarea rows="2" name="candidates[]" id="candidates" class="form-control" placeholder="Candidates names separated by comma, e.g. Olawale Ishola, John Isaac" required><?php echo $batch_content; ?></textarea></td>
<td>
<?php 
if($c > 1){
?>
<button type="button" class="btn gen-btn del-sub-cat" lang="row<?php echo $c; ?>" onclick="javascript: delete_sub(this.lang);"><i class="fa fa-minus"></i></button>
<?php
}
?>
</td>
</tr>
<?php
}
}

}else{ 
$c++;
?>
<tr class="sub-row" id="row<?php echo $c; ?>">
<td class="sub-cell" id="td<?php echo $c; ?>"><?php echo $c; ?></td>
<td><input type="text" name="batch[]" id="batch" class="form-control only-no" placeholder="Batch no." value="" required></td>
<td><textarea rows="2" name="candidates[]" id="candidates" class="form-control" placeholder="Candidates names separated by comma, e.g. Olawale Ishola, John Isaac" required></textarea></td>
<td></td>
</tr>
<?php } ?>

</table>
                     
<div class="submit-div col-sm-12">
<button type="button" class="btn add-new-batch gen-btn float-left"><i class="fa fa-plus"></i> Add</button>
<button class="btn gen-btn float-right">
<?php if(!empty($add)){ ?>
<i class="fa fa-file-text"></i> Generate Letter
<?php }else{ ?>
<i class="fa fa-upload"></i> Save Letter
<?php } ?>
</button>
</div>

</form>

<script>
<!--

var c = <?php echo $c; ?>;

$(".add-new-batch").click(function(){

c++;

$(".batches").append("<tr class=\"sub-row\" id=\"row" + c + "\"><td class=\"sub-cell\" id=\"td" + c + "\">" + c + "</td><td><input type=\"text\" name=\"batch[]\" id=\"batch\" class=\"form-control\" placeholder=\"E.g. 4\" value=\"\" onkeyup=\"javascript: only_no(this);\" onchange=\"javascript: only_no(this);\"></td><td><textarea rows=\"2\" name=\"candidates[]\" id=\"candidates\" class=\"form-control\" placeholder=\"Candidates names separated by comma, e.g. Olawale Ishola, John Isaac\"></textarea></td><td><button type=\"button\" class=\"btn gen-btn del-sub-cat\" lang=\"row" + c + "\" onclick=\"javascript: delete_sub(this.lang);\"><i class=\"fa fa-minus\"></i></button></td></tr>");

});

function add_sub(){
c++;
var tb_htm = document.getElementById("batches").innerHTML;
document.getElementById("batches").innerHTML = tb_htm + "";
}

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

}
//-->
</script>

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