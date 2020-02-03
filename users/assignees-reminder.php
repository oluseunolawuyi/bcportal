<?php
// Date in the past
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

ini_set('session.gc_maxlifetime', 3600);
session_start();
ini_set('allow_url_include', '1');

error_reporting(E_ALL); ini_set('display_errors', 1);

    class DB {  
      
        protected $db_name = "FAjayi_bcportal";  
        protected $db_user = "bcportal";  
        protected $db_pass = "bcportal1()[]{}!";  
        protected $db_host = "localhost";   
        public $connection = '';
         
        public function connect() {  
            $this->connection = mysqli_connect($this->db_host, $this->db_user, $this->db_pass, $this->db_name);  
            mysqli_select_db($this->connection, $this->db_name);  
      
            return true;  
        }
        
        public function disconnect() {  
            mysqli_close($this->connection);
        }  
      
      public function query($query="") {
            $result = null;
            if(trim($query) != ''){            
                $result = mysqli_query($this->connection, $query);            
                if(!$result){
                   $result = null;
                }
            }            
            return $result;            
        }
        
         
        public function select($table, $where, $columns ='*', $order='', $limit='') {
            
            $sql = "SELECT $columns FROM $table $where $order $limit";
            
            $result = mysqli_query($this->connection, $sql);
            
            if($result){
                return $result;
            }else{
                return null;
            }
        }
        
        public function select_count($table, $where, $as='count') {
            
            $sql = "SELECT count(*) as $as FROM $table $where";
            
            $result = mysqli_query($this->connection, $sql);
            
            if($result){
                return $result;
            }else{
                return null;
            }
        }
        
        
        public function select_single($table, $where, $columns='*', $order='') {
           
            $fetch_columns = "";
            $sql = "";
            
            if($columns == '*'){
                $sql = "SELECT * FROM $table $where $order";
            }else{
                foreach ($columns as $column) {  
                   $fetch_columns .= ($columns == "") ? "" : ", ";  
                   $fetch_columns .= $column;    
                }
            
                $sql = "SELECT $fetch_columns FROM $table $where $order"; 
            }
            
            $result = mysqli_query($this->connection, $sql);
            
            if($result){
                return $result;
            }else{
                return null;
            }
        }  
      
          
        public function update($data, $table, $where) {
            $sqlString = "UPDATE $table SET ";
            foreach ($data as $key => $value) {  
                $sqlString .= "$key = '$value',";  
            }
            
            $sqlString = substr($sqlString, 0, -1);
            $sqlString .= " WHERE $where";
          
            if(mysqli_query($this->connection, $sqlString)){
                return true;  
            }else{
                return false;
            }
            
        }  
      
        
        public function insert($data, $table) {  
      
            $columns = "";  
            $values = "";  
      
            foreach ($data as $column => $value) {  
                $columns .= ($columns == "") ? "" : ", ";  
                $columns .= $column;  
                $values .= ($values == "") ? "" : ", ";  
                $values .= $value;  
            }  
      
            $sql = "insert into $table ($columns) values ($values)";  
            
            mysqli_query($this->connection, $sql) or die(mysqli_error($this->connection));  
      
            return mysqli_insert_id($this->connection);  
        }  
		
        public function insert2($data, $table) {  
      
            $columns = "";  
            $values = "";  
      
            foreach ($data as $column => $value) {  
				$columns .= ($columns == "") ? "" : ", ";  
                $columns .= $column;  
                $values .= ($values == "") ? "" : ", ";  
                $values .= "'".$value."'"; 
            } 
      
            $sql = "insert into $table ($columns) values ($values)";  
            
            mysqli_query($this->connection, $sql) or die(mysqli_error($this->connection));  
      
            return mysqli_insert_id($this->connection);  
        } 
      
      
        public function delete($table, $where) {       
            
            $sql = "DELETE FROM $table WHERE $where";  
             
           if(mysqli_query($this->connection, $sql)){            
                return true;        
           }else{
                die(mysqli_error($this->connection));  
           }
        }
        
    } 

date_default_timezone_set("Africa/Lagos");

$directory = "https://riskcontrolnigeria.com/bcportal/";
function directory() {
global $directory;
echo $directory;
}
$parent_domain = "riskcontrolnigeria.com";
$domain = "riskcontrolnigeria.com";
$gen_name = "BC Portal";
$full_gen_name = "BC Portal";
$gen_email = "info@riskcontrolnigeria.com";
$gen_phone = "+234 1 295 4283";
$date = date("Y-m-d");
$date_time = date("Y-m-d H:i:s");
$ticket_id = date("YmdHis");
$rand_no = rand(1000,9999);
$foot_note = "<b>Note:</b> This email is autogenerated. Please do not reply.";
$regards = "<p>&nbsp;</p><p>Regards,<br>{$gen_name} Team.</p>";
$admin = "users/";
$error = 1;
$rand = rand(1001,9999);

$db = new DB();
$db->connect();

function count_rows($data) {
return mysqli_num_rows($data);
}
function fetch_data($data) {
return mysqli_fetch_array($data, MYSQLI_BOTH);
}
function in_table($col,$table,$where_col,$return) {
    $result = 0;
    $db = new DB();
    $db->connect();

    $vend = $db->query("SELECT {$col} FROM {$table} {$where_col}");
    if (count_rows($vend) > 0) {
        $Row = fetch_data($vend);
        $result = $Row[$return];
    }
    return $result;
}


///=========================Mail Functions===========//////
function message_template(){
global $subject, $message, $foot_note, $regards, $directory, $domain, $gen_email, $gen_phone;
$result = "
<html>
<head>
<title>{$subject}</title>
</head>
<body>

<div style=\"background:#f9f9f9 !important; padding:10px !important; font-family:Arial, Helvetica, sans-serif; font-size:14px !important;\">
<div style=\"margin:auto !important; width:100% !important; max-width:800px !important;\">

<div style=\"padding:10px !important; padding-top:30px !important;\">
<img src=\"{$directory}images/risk-control-logo.jpg\">
</div>

<div style=\"min-height:300px !important; padding:10px !important; background:#fff !important;\">
{$message}
{$regards}
</div>

<p style=\"font-size:14px !important;\">
<span style=\"font-weight:bold !important;\">Email:</span> {$gen_email},<br>
<span style=\"font-weight:bold !important;\">Phone:</span> {$gen_phone},<br>
<span style=\"font-weight:bold !important;\">Website:</span> <a href=\"{$directory}\" style=\"color:#f33 !important; text-decoration:none !important;\">{$domain}</a>.
</p>";
$result .= (!empty($foot_note))?"<p style=\"background:#ddd !important;font-size:12px !important; padding:10px !important; overflow:hidden !important;\">{$foot_note}</p>":"";
$result .= "</div>
</div>

</body>
</html>";
return $result;
}

function send_mail($no=""){
global $to, $subject, $message, $headers;

$message = wordwrap($message,70);
// Always set content-type when sending HTML email
$headers2 = $headers;
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: {$headers2}" . "\r\n";
$headers .= ($no == 1)?"BCC: crm@riskcontrolnigeria.com, webmaster@riskcontrolnigeria.com" . "\r\n":"";

$url = "http://riskcontrolnigeria.biz/get-remote/";
$data = array("to" => "$to", "subject" => "$subject", "message" => "$message", "headers" => "$headers");
$risk_content = http_build_query($data);

// use key 'http' even if you send the request to https://...
$options = array("http" => array(
  	"header" => "Content-Type: application/x-www-form-urlencoded\r\n".
                    "Content-Length: " . strlen($risk_content) . "\r\n".
                    "User-Agent:MyAgent/1.0\r\n",
    "method"  => "POST",
    "content" => $risk_content
));

$context = stream_context_create($options);

$result = file_get_contents($url, false, $context);
return $result;

}

$dat = date_create("now");
date_add ($dat, date_interval_create_from_date_string("7 days"));
$to_tat = date_format($dat, "Y-m-d");

////////////======CV Expiry Reminder========/////
$cv_reports_assignees; 

$result = $db->select("cv_reports", "WHERE NOT(status='COMPLETED') AND tat='$to_tat'", "DISTINCT investigation_officer", "ORDER BY investigation_officer ASC");
if(count_rows($result) > 0){
while($row = fetch_data($result)){
$investigation_officer_id = $row["investigation_officer"];
$cv_reports_assignees[] = $investigation_officer_id;
}
}

if(!empty($cv_reports_assignees)){
foreach($cv_reports_assignees as $value){
$assignees_name = in_table("name","reg_users","WHERE id = '{$value}'","name");
$assignees_email = in_table("email","reg_users","WHERE id = '{$value}'","email");
$result = $db->select("cv_reports", "WHERE NOT(status='COMPLETED') AND tat='$to_tat' AND investigation_officer='$value'", "id", "ORDER BY id ASC");
if(count_rows($result) > 0){
$cv_reports_ref_code = "<ol type='1'>";
while($row = fetch_data($result)){
$cv_reports_ref_code .= "<li>" . $row["id"] . "</li>";
}
$cv_reports_ref_code .= "</ol>";
$subject = "CV Pending Task(s) Expiring in Seven (7) Days";
$message = "<p>Dear {$assignees_name},</p>
<p>This is to notify you that the following Certificate Verification pending tasks will expire in seven (7) days, analysed with reference code(s):</p>
<p>{$cv_reports_ref_code}</p>
<p>Thank you.</p>";
$message = message_template();
$headers2 = "{$gen_name} <no-reply@{$domain}>";
$to = $assignees_email;
$headers = $headers2;
send_mail();
}
}
}

////////////======BC Expiry Reminder========/////
$bc_reports_assignees; 

$result = $db->select("bc_sub_reports", "WHERE NOT(status='COMPLETED') AND tat='$to_tat'", "DISTINCT investigation_officer", "ORDER BY investigation_officer ASC");
if(count_rows($result) > 0){
while($row = fetch_data($result)){
$investigation_officer_id = $row["investigation_officer"];
$bc_reports_assignees[] = $investigation_officer_id;
}
}

if(!empty($bc_reports_assignees)){
foreach($bc_reports_assignees as $value){
$assignees_name = in_table("name","reg_users","WHERE id = '{$value}'","name");
$assignees_email = in_table("email","reg_users","WHERE id = '{$value}'","email");
$result = $db->select("bc_sub_reports", "WHERE NOT(status='COMPLETED') AND tat='$to_tat' AND investigation_officer='$value'", "id, bc_report_id", "ORDER BY id ASC");
if(count_rows($result) > 0){
$bc_reports_ref_code = "<ol type='1'>";
while($row = fetch_data($result)){
$bc_reports_ref_code .= "<li>"  . $row["bc_report_id"] . " => " . $row["id"] . "</li>";
}
$bc_reports_ref_code .= "</ol>";
$subject = "BC Pending Task(s) Expiring in Seven (7) Days";
$message = "<p>Dear {$assignees_name},</p>
<p>This is to notify you that the following Background Checks pending tasks will expire in seven (7) days, analysed with 'Main Reference Code(s)' => 'Sub-Reference Code(s)':</p>
<p>{$bc_reports_ref_code}</p>
<p>Thank you.</p>";
$message = message_template();
$headers2 = "{$gen_name} <no-reply@{$domain}>";
$to = $assignees_email;
$headers = $headers2;
send_mail();
}
}
}

?>