<?php if(!isset($_REQUEST["gh"])){ include_once("../includes/admin-header.php"); 
}else{ 
include_once("../includes/gen-header.php"); require_once("../includes/resize-image.php");
} ?>

<?php
admin_role_redirect("admin_analysis");

////////////////////////////////////////////////////******************************//////////////

$keyword = search_option("keyword");
$no_of_rows = search_option("no_of_rows");
$search_start_date = search_option("search_start_date");
$search_start_date = (!empty($search_start_date))?$search_start_date:date("Y-m-01");
$search_end_date = search_option("search_end_date");
$search_end_date = (!empty($search_end_date))?$search_end_date:$date;

$where = "WHERE id > '0' AND admin = '1'";
$where .= (!empty($keyword))?" AND (name LIKE '%{$keyword}%' OR email LIKE '%{$keyword}%')":"";

$where_date_bc = $where_date_cv = "";
$where_date_bc .= (!empty($search_start_date) && !empty($search_end_date))?" AND end_date BETWEEN '{$search_start_date} 00:00:00' AND '{$search_end_date} 23:59:59'":"";
$where_date_bc .= (!empty($search_start_date) && empty($search_end_date))?" AND end_date >= '{$search_start_date} 00:00:00'":"";
$where_date_bc .= (empty($search_start_date) && !empty($search_end_date))?" AND end_date <= '{$search_end_date} 23:59:59'":"";

$where_date_cv .= (!empty($search_start_date) && !empty($search_end_date))?" AND completion_date BETWEEN '{$search_start_date} 00:00:00' AND '{$search_end_date} 23:59:59'":"";
$where_date_cv .= (!empty($search_start_date) && empty($search_end_date))?" AND completion_date >= '{$search_start_date} 00:00:00'":"";
$where_date_cv .= (empty($search_start_date) && !empty($search_end_date))?" AND completion_date <= '{$search_end_date} 23:59:59'":"";

$table = "reg_users";
$result = $db->select("$table", "$where", "*", "ORDER BY id DESC");
$count = count_rows($result);

$per_view = 20;
$per_view = (!empty($no_of_rows))?$no_of_rows:$per_view;
$page_link = "{$admin}admin-analysis?pn=";
$link_suffix = "";
$style_class = "general-link";
page_numbers();

$offset = ($per_view * $pn) - $per_view;

$result = $db->select("$table", "$where", "*", "ORDER BY id DESC", "LIMIT {$offset},{$per_view}");
?>

<div class="page-title">Admin Users Analysis</div>

<form action="<?php echo $admin; ?>admin-analysis" class="general-form" id="form-div" method="post" runat="server" autocomplete="off" enctype="multipart/form-data">
<input type="hidden" name="gh" value="1"> 
<div class="search-dates">

<div class="col-md-9">
<label for="keyword">Keyword</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
<input type="text" name="keyword" id="keyword" class="form-control" placeholder="User&#039;s Name or Email" value="<?php check_inputted("keyword", $keyword); ?>">
</div>
</div>

<div class="col-md-3">
<label for="no_of_rows">No. of Rows</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-list" aria-hidden="true"></i></span>
<input type="number" name="no_of_rows" id="no_of_rows" class="form-control only-no" placeholder="No. of rows" value="<?php check_inputted("no_of_rows", $per_view); ?>">
</div>
</div>

<div class="col-md-5">
<label for="search_start_date">Start Date</label>
<div class="form-group input-group">
<span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
<input type="text" name="search_start_date" id="search_start_date" class="form-control gen-date" placeholder="YYYY-MM-DD" value="<?php check_inputted("search_start_date", $search_start_date); ?>">
</div>
</div>

<div class="col-md-5">
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
if($count > 0){
?>
<div class="overflow">
<table class="table table-striped table-hover">
<thead>
<tr class="gen-title">
<th>#ID</th>
<th>Username</th>
<th>Details</th>
<th>Role</th>
<th>BC Reports</th>
<th>CV Reports</th>
</tr>
</thead>
<tbody>
<?php 
while($row = fetch_data($result)){
$get_id = $row["id"];
$user_id = admin_id($get_id);
$new_username = $row["username"];
$name = $row["name"];
$email = $row["email"];
$user_datails = $name . "<br>" . break_long($email);
$super_admin = $row["super_admin"];
$role_id = $row["role_id"];
$role_assigned = (!empty($role_id))?get_table_data("role_management", $role_id, "role"):"<span style=\"color: #b20; font-weight:bold;\">Not Assigned</span>";
$role_assigned = (!empty($super_admin))?"<span style=\"color:#2387a0; font-weight:bold;\">SUPER ADMIN</span>":$role_assigned;
$bc_report = in_table("COUNT(id) AS Total","bc_sub_reports","WHERE investigation_officer = '{$get_id}' AND status = 'COMPLETED' $where_date_bc","Total");
$bc_report = formatQty($bc_report);
$cv_report = in_table("COUNT(id) AS Total","cv_reports","WHERE investigation_officer = '{$get_id}' AND status = 'COMPLETED' $where_date_cv","Total");
$cv_report = formatQty($cv_report);
?>                           
<tr>
<td><?php echo $user_id; ?></td>
<td><?php echo $new_username; ?></td>
<td><?php echo $user_datails; ?></td>
<th><?php echo $role_assigned; ?></th>
<td><?php echo $bc_report; ?></td>
<td><?php echo $cv_report; ?></td>
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
echo "<div class='not-success'>No users found.</div>";
}
?>

<script src="js/general-form.js"></script>

<?php  if(!isset($_REQUEST["gh"])){?>

</div>
</div>

</div>
<?php require_once("../includes/portal-footer.php"); } ?>