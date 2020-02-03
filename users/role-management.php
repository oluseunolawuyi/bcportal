<?php if(!isset($_REQUEST["gh"])){ include_once("../includes/admin-header.php"); 
}else{ 
include_once("../includes/gen-header.php");
} ?>

<?php
admin_role_redirect("role_management");

$pn = nr_input("pn");

$add = nr_input("add");
$edit = nr_input("edit");
$delete = np_input("delete");
$role = tp_input("role");

//////////==================== Delete Role(s) =====================/////////////
if(check_admin("role_management") == 1 && isset($_POST["delete"]) && isset($_POST["del"])){
$i = $act = 0;
$role_text = "";
if(is_array($_POST["del"])){

foreach ($_POST["del"] as $k => $c) {
if($c != ""){ 
$c = testQty($c);
$role_text .= get_table_data("role_management", $c, "role") . ", ";
$db->query("UPDATE reg_users SET role_id = '0' WHERE role_id = '{$c}'");
$act = $db->delete("role_management", "id = '$c'");
$i++;		
}else{
continue;
}
}

if($act && $i > 0){

$role_text = substr($role_text,0,-2);
$activity = "Deleted {$i} role from database: {$role_text}.";
$audit_data_array = array(
"user_id" => "'$id'",
"name" => "'$username'",
"email" => "'$user_email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$db->insert($audit_data_array, "audit_log");

echo "<div class='success'>{$i} role(s) successfully deleted.</div>";
}else{
echo "<div class='not-success'>Error. Unable to delete role(s).</div>";
}

}else{
echo "<div class='not-success'>Atleast one role must be selected.</div>";
}
}

//////////////////////////////////=====Create Row============//////////////////////////////////////////
if(check_admin("role_management") == 1 && $_SERVER['REQUEST_METHOD'] == "POST" && !empty($role) && (!empty($add) || !empty($edit))){

$check_row_exists = in_table("role","role_management","WHERE role='{$role}'","role");

$data_array = array();
$roles_combined = "";
foreach($_POST as $key => $val){
if($key != "add" && $key != "edit" && $key != "gh" && $key != "pn" && $key != "sel-all"){
$val = (!empty($val))?$val:0;
$data_array += array($key => $val);
$roles_combined .= ($key != "role" && !empty($val))? in_table("role_text","roles","WHERE role_title='{$key}'","role_text") . ", " : "";
}
}

$act = "";

if(!empty($add) && empty($check_row_exists)){
$data_array += array("date_created" => $date_time, "created_by" => $id);
$act = $db->insert2($data_array, "role_management");
}else if(!empty($edit)){
$data_array += array("date_updated" => $date_time, "updated_by" => $id);
$act = $db->update($data_array, "role_management", "id = '$edit'");
}

if($act){
$error = 0;
$roles_combined = substr($roles_combined,0,-2);
$activity = "Allowed the following privileges to a role ({$role}): {$roles_combined}.";
$audit_data_array = array(
"user_id" => "'$id'",
"name" => "'$username'",
"email" => "'$user_email'",
"activity" => "'$activity'",
"date_time" => "'$date_time'"
);
$db->insert($audit_data_array, "audit_log");

echo (!empty($add))?"<div class='success'>Role successfully added.</div>":"<div class='success'>Role successfully updated.</div>";
}else{
if(!empty($add) && !empty($check_row_exists)){
echo "<div class='not-success'>Not successful. This role was previously created.</div>";
}else{
echo (!empty($add))?"<div class='not-success'>Error. Unable to add role.</div>":"<div class='not-success'>Error. Unable to update role.</div>";
}
}

}

////////////////////////////////////////////////////******************************//////////////

$result = $db->select("role_management", "", "*", "ORDER BY id DESC");

$per_view = 20;
$page_link = "{$admin}role-management?pn=";
$link_suffix = "";
$style_class = "general-link";
page_numbers();
?>

<?php
if((empty($edit)||(!empty($edit)&&$error==0)) && (empty($add)||(!empty($add)&&$error==0)) ){
?>

<div class="page-title">Role Management <a href="<?php echo $admin; ?>role-management?add=1" class="btn gen-btn general-link float-right">New Role</a></div>

<?php
$d = 0;

$offset = ($per_view * $pn) - $per_view;

$result = $db->select("role_management", "", "*", "ORDER BY id DESC", "LIMIT {$offset},{$per_view}");

if(count_rows($result) > 0){
?> 
<form action="<?php echo $admin; ?>role-management" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data" style="overflow-x:auto;">
<input type="hidden" name="pn" value="<?php echo $pn; ?>"> 
<input type="hidden" name="gh" value="1"> 
<input type="hidden" name="delete" value="1"> 
<table class="table table-striped table-hover">
<thead>
<tr class="gen-title">
<th style="width: 40px;">#ID</th>
<th>Role Name</th>
<th>Date Created</th>
<th>Created By</th>
<th>Last Update</th>
<th>Updated By</th>
<th style="width: 100px;">Option</th>
<th style="width:30px;"><input type="checkbox" name="sel_all" id="delG" class="sel-group" value=""></th>
</tr>
</thead>
<tbody>
<?php
while($row = fetch_data($result)){
$role_id = $row["id"];
$role = $row["role"];
$date_created = ($row["date_created"] != "0000-00-00 00:00:00")?min_full_date($row["date_created"]):"";
$created_by_name = (!empty($row["created_by"]))?get_table_data("reg_users", $row["created_by"], "name"):"";
$created_by_email = (!empty($row["created_by"]))?get_table_data("reg_users", $row["created_by"], "email"):"";
$date_updated = ($row["date_updated"] != "0000-00-00 00:00:00")?min_full_date($row["date_updated"]):"";
$updated_by_name = (!empty($row["updated_by"]))?get_table_data("reg_users", $row["updated_by"], "name"):"";
$updated_by_email = (!empty($row["updated_by"]))?get_table_data("reg_users", $row["updated_by"], "email"):"";
?>
<tr>
<td><?php echo $role_id; ?></td>
<td><?php echo $role; ?></td>
<td><?php echo $date_created; ?></td>
<td><?php echo "{$created_by_name} <br> ({$created_by_email})"; ?></td>
<td><?php echo $date_updated; ?></td>
<td><?php echo (!empty($row["updated_by"]))?"{$updated_by_name} <br> ({$updated_by_email})":""; ?></td>
<td><a href="<?php echo $admin; ?>role-management?edit=<?php echo $role_id; ?>&pn=<?php echo $pn; ?>" class="btn gen-btn general-link" title="Edit recommendation type #<?php echo $role_id; ?>"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></td>
<td><?php if($role_id > 1){ ?><input type="checkbox" name="del[<?php echo $d; ?>]" id="del<?php echo $d; ?>" class="delG" value="<?php echo $role_id; ?>"><?php } ?></td>
</tr>
<?php 
$d++;
}
?>
<tr><td colspan="9"><input class="sub-del" type="submit" value=" "><button type="button" class="btn del-btn gen-btn float-right"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete selected role(s)</button></td></tr>
</tbody>
</table>
</form>
<?php
echo ($last_page>1)?"<div class=\"page-nos\">" . $center_pages . "</div>":"";
}else{
echo "<div class='not-success'>No roles found at the moment.</div>";
}

}

if(check_admin("role_management") == 1 && (!empty($add) || !empty($edit)) && $error == 1){

$result = $db->select("roles", "", "*", "");

$role = "";
if(!empty($edit)){
$role = get_table_data("role_management", $edit, "role");
}

$field_name = (!empty($edit))?"edit":"add";
$field_value = (!empty($edit))?$edit:1;
$action_title = (!empty($edit))?"Edit":"Add New";
?>

<style>
<!--
div table thead tr th, div table tr th, div table tbody tr td, div table tr td{
text-align:left !important;
}

.title_adjust{
font-weight:bold; 
vertical-align:middle !important;
}
.title_adjust *{
font-weight:bold;
}
-->
</style>

<div class="back"><a href="<?php echo $admin; ?>role-management?pn=<?php echo $pn; ?>" class="btn gen-btn general-link"><i class="fa fa-arrow-left"></i> Back to role management</a></div>

<div class="page-title"><?php echo $action_title; ?> Role</div>

<form action="<?php echo $admin; ?>role-management" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data" style="border:1px solid #eee;">
<input type="hidden" name="gh" value="1"> 
<input type="hidden" name="<?php echo $field_name; ?>" value="<?php echo $field_value; ?>"> 
<?php if(!empty($edit)){ ?>
<input type="hidden" name="pn" value="<?php echo $pn; ?>"> 
<?php
}
?>

<div class="overflow">
<table class="table table-striped table-hover">
<tr><td></td><td class="title_adjust" style="text-align:right!important;"><label for="role">Role Name:</label></td><td colspan="2"><input type="text" name="role" id="role" class="form-control" placeholder="Type the role name" required value="<?php echo check_inputted("role", $role); ?>">
</td><td></td><td></td><td class="title_adjust"><input type="checkbox" name="sel-all" id="delG" class="sel-group" value=""></td><td class="title_adjust"><label for="delG">Select all</label></td></tr>
<tr>
<?php
$c = 0;
while($row = fetch_data($result)){
$c++;
$id = $row["id"];
$role_title = $row["role_title"];
$role_text = $row["role_text"];
?>
<td class="field_td"><input type="hidden" name="<?php echo $role_title; ?>" id="<?php echo $role_title; ?>1" value="0"><input type="checkbox" name="<?php echo $role_title; ?>" id="<?php echo $role_title; ?>" class="delG" value="1"<?php role_exists($edit, $role_title); ?>></td>
<td><label for="<?php echo $role_title; ?>"><?php echo $role_text; ?></label></td>
<?php
if($c == 4){
?>
</tr><tr>
<?php
$c = 0;
}
}

if($c > 0 && $c < 4){
?>
<td colspan="<?php echo (4 - $c) * 2; ?>"></td>
<?php
}
?>
</tr>
</table>
</div>

<div class="submit-div col-sm-12">
<button class="btn gen-btn float-right"><i class="fa fa-upload"></i> <?php echo (!empty($add))?"Add role":"Update role"; ?></button>
</div>
</form>
              
<?php
}
?>

<script>
<!--
var conf_text = "role";
//-->
</script>

<script src="js/general-form.js"></script>

<?php  if(!isset($_REQUEST["gh"])){?>

</div>
</div>

</div>

<?php require_once("../includes/portal-footer.php"); } ?>