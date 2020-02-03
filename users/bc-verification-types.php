<?php if(!isset($_REQUEST["gh"])){ include_once("../includes/admin-header.php"); 
}else{ 
include_once("../includes/gen-header.php");
} ?>

<?php
admin_role_redirect("manage_bc_verification_types");

$edit = nr_input("edit");
$add = nr_input("add");
$pn = nr_input("pn");

$type = tp_input("type");
$order_id = np_input("order_id");
$tat = np_input("tat");
$act = "";

////////////// Add or Update Plan //////////////////////////////
if(check_admin("manage_bc_verification_types") == 1 && $_SERVER['REQUEST_METHOD'] == "POST" && (!empty($add) || !empty($edit)) && !empty($type) && !empty($order_id) && !empty($tat)){

$used_type = in_table("type","bc_verification_types","WHERE type = '{$type}'","type");
$det_type = in_table("type","bc_verification_types","WHERE id = '{$edit}'","type");

$data_array = array(
"type" => $type,
"order_id" => $order_id,
"tat" => $tat
);

if(!empty($add)){
if(empty($used_type)){
$act = $db->insert2($data_array, "bc_verification_types");
}else{
echo "<div class='not-success'>BC verification type already exists.</div>";
}
}else if(!empty($edit)){
$act = $db->update($data_array, "bc_verification_types", "id = '$edit'");

$data_array = array(
"verification_type" => $type,
"verification_order_id" => $order_id
);
$db->update($data_array, "bc_sub_reports", "verification_type = '$det_type'");

$data_array = array(
"verification_type" => $type
);
$db->update($data_array, "bc_reports_log", "verification_type = '$det_type'");
}

if($act){

$activity = (!empty($add))?"Added a new":"Updated a";
$activity .= " BC verification type ({$type}).";
$audit_data_array = array(
"user_id" => "'$id'",
"name" => "'$username'",
"email" => "'$user_email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$db->insert($audit_data_array, "audit_log");

$error = 0;

echo "<div class='success'>BC verification type successfully saved.</div>";
}else if(!$act && empty($used_type)){
echo "<div class='not-success'>Error occured.</div>";
}

}

if($_SERVER['REQUEST_METHOD'] == "POST" && (!empty($add) || !empty($edit)) && (empty($type) || empty($order_id) || empty($tat))){
echo "<div class='not-success'>Not submitted! All the fields are required.</div>";
}

/////////////////////////////////////////////////////////////////////////////////////////
if(check_admin("manage_bc_verification_types") == 1 && isset($_POST["delete"]) && isset($_POST["del"])){
$i = $act = 0;
if(is_array($_POST["del"])){
foreach ($_POST["del"] as $k => $c) {
if($c != ""){ 
$c = testQty($c);
$act = $db->delete("bc_verification_types", "id = '$c'");
$i++;		
}else{
continue;
}
}

if($act && $i > 0){

$activity = "Deleted {$i} BC verification type(s).";
$audit_data_array = array(
"user_id" => "'$id'",
"name" => "'$username'",
"email" => "'$user_email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$db->insert($audit_data_array, "audit_log");

echo "<div class='success'>{$i} BC verification type(s) successfully deleted.</div>";
}else{
echo "<div class='not-success'>Error. Unable to delete BC verification type(s).</div>";
}

}else{
echo "<div class='not-success'>Atleast one BC verification type must be selected.</div>";
}
}

////////////////////////////////////////////////////******************************//////////////

$result = $db->select("bc_verification_types", "", "*", "ORDER BY id DESC");

$per_view = 20;
$page_link = "{$admin}bc-verification-types?pn=";
$link_suffix = "";
$style_class = "general-link";
page_numbers();
?>

<?php
if((empty($edit)||(!empty($edit)&&$error==0)) && (empty($add)||(!empty($add)&&$error==0)) ){
?>

<div class="page-title">BC Verification Types <a href="<?php echo $admin; ?>bc-verification-types?add=1" class="btn gen-btn general-link float-right">New Verification Type</a></div>

<?php
$d = 0;

$offset = ($per_view * $pn) - $per_view;

$result = $db->select("bc_verification_types", "", "*", "ORDER BY id DESC", "LIMIT {$offset},{$per_view}");

if(count_rows($result) > 0){
?> 
<form action="<?php echo $admin; ?>bc-verification-types" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data" style="overflow-x:auto;">
<input type="hidden" name="pn" value="<?php echo $pn; ?>"> 
<input type="hidden" name="gh" value="1"> 
<input type="hidden" name="delete" value="1"> 
<table class="table table-striped table-hover">
<thead>
<tr class="gen-title">
<th style="width: 40px;">#ID</th>
<th>Title</th>
<th>Order ID</th>
<th>TAT (Days)</th>
<th style="width: 100px;">Action</th>
<th style="width:30px;"><input type="checkbox" name="sel_all" id="delG" class="sel-group" value=""></th>
</tr>
</thead>
<tbody>
<?php
while($row = fetch_data($result)){
$verification_type_id = $row["id"];
$type = $row["type"];
$order_id = $row["order_id"];
$tat = $row["tat"];
?>
<tr>
<td><?php echo $verification_type_id; ?></td>
<td><?php echo $type; ?></td>
<td><?php echo $order_id; ?></td>
<td><?php echo $tat; ?></td>
<td><a href="<?php echo $admin; ?>bc-verification-types?edit=<?php echo $verification_type_id; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link" title="Edit BC Verification Type #<?php echo $verification_type_id; ?>"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></td>
<td><input type="checkbox" name="del[<?php echo $d; ?>]" id="del<?php echo $d; ?>" class="delG" value="<?php echo $verification_type_id; ?>"></td>
</tr>
<?php 
$d++;
}
?>
<tr><td colspan="9"><input class="sub-del" type="submit" value=" "><button type="button" class="btn del-btn gen-btn float-right"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete selected verification type(s)</button></td></tr>
</tbody>
</table>
</form>
<?php
echo ($last_page>1)?"<div class=\"page-nos\">" . $center_pages . "</div>":"";
}else{
echo "<div class='not-success'>No BC verification types found at the moment.</div>";
}

}

if(check_admin("manage_bc_verification_types") == 1 && (!empty($add) || !empty($edit)) && $error == 1){

$type = $order_id = $tat = "";
if(!empty($edit)){
$result = $db->select("bc_verification_types", "WHERE id='$edit'", "*", "");
if(count_rows($result) == 1){
$row = fetch_data($result);
$type = $row["type"];
$order_id = $row["order_id"];
$tat = $row["tat"];
}
}

$field_name = (!empty($edit))?"edit":"add";
$field_value = (!empty($edit))?$edit:1;
$action_title = (!empty($edit))?"Edit":"Add New";
?>

<div class="back"><a href="<?php echo $admin; ?>bc-verification-types?pn=<?php echo $pn; ?>" class="btn gen-btn general-link"><i class="fa fa-arrow-left"></i> Back to BC verification types</a></div>

<div class="page-title"><?php echo $action_title; ?> BC Verification Type</div>

<form action="<?php echo $admin; ?>bc-verification-types" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data" style="border:1px solid #eee;">
<input type="hidden" name="gh" value="1"> 
<input type="hidden" name="<?php echo $field_name; ?>" value="<?php echo $field_value; ?>"> 
<?php if(!empty($edit)){ ?>
<input type="hidden" name="pn" value="<?php echo $pn; ?>"> 
<?php
}
?>

<div class="col-sm-12">
<label for="type">Title</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-file-text"></i></span>
<input type="text" name="type" id="type" class="form-control" placeholder="Title of the BC verification type" value="<?php check_inputted("type", $type); ?>" required>
</div>
</div>

<div class="col-sm-12">
<label for="order_id">Order ID</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-sort"></i></span>
<input type="text" name="order_id" id="order_id" class="form-control only-no" placeholder="E.g. 2" value="<?php check_inputted("order_id", $order_id); ?>" required>
</div>
</div>

<div class="col-sm-12">
<label for="tat">TAT (Days)</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
<input type="text" name="tat" id="tat" class="form-control only-no" placeholder="E.g. 30" value="<?php check_inputted("tat", $tat); ?>" required>
</div>
</div>
                     
<div class="submit-div col-sm-12">
<button class="btn gen-btn float-right" name="update"><i class="fa fa-upload"></i> Save</button>
</div>

</form>
<?php
}
?>

<script>
<!--
var conf_text = "BC verification type";
//-->
</script>

<script src="js/general-form.js"></script>

<?php  if(!isset($_REQUEST["gh"])){?>

</div>
</div>

</div>

<?php require_once("../includes/portal-footer.php"); } ?>