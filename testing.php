<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

ini_set('session.gc_maxlifetime', 3600);
session_start();

require_once("classes/db-class.php");
require_once("includes/functions.php");

$subject = "Testing New CV Task(s)";
$message = "<p>Dear Admin</p><p>This is to notify you of new certificate verification task(s) assigned to you. Kindly log in to your account for details.</p>";
$message2 = $message;
$message = message_template();
$headers = "{$gen_name} <no-reply@{$domain}>";
$to = "wasiuonline@gmail.com, oseghale.charles1186@gmail.com,  coseghale@riskcontrolnigeria.com, conwuka@riskcontrolnigeria.com, investigation2@riskcontrolnigeria.com, verification@riskcontrolnigeria.com, investigation@riskcontrolnigeria.com, verify@riskcontrolnigeria.com, investigation3@riskcontrolnigeria.com, wowasanoye@riskcontrolnigeria.com";

if(send_mail()){
echo "Mail Sent";
}else{
echo "Mail NOT Sent";
}
?>