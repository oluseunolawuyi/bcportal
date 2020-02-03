<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");
ini_set('session.gc_maxlifetime', 86400);
session_start();

require_once("../classes/db-class.php");
require_once("../includes/functions.php");
require_once("../includes/resize-image.php");

$cover_letter_type = np_input("cover_letter_type");
$client = np_input("client");
$completion_date = tp_input("completion_date");
$attention = tp_input("attention");
$batch_category = tp_input("batch_category");

$reference_no = tp_input("reference_no");
$client_designation = tp_input("client_designation");
$re = tp_input("re");
$invoice_attachment = tp_input("invoice_attachment");
$signatory = tp_input("signatory");
$details_category = tp_input("details_category");
$list_category = tp_input("list_category");

$names = tp_input("names");
$school = tp_input("school");
$year = tp_input("year");
$qualification = tp_input("qualification");
$grade = tp_input("grade");
$course = tp_input("course");
$transaction_ref = tp_input("transaction_ref");
$comment = tp_input("comment");
$report_source = tp_input("report_source");

$confirmation_type = tp_input("confirmation_type");
$provided_by = tp_input("provided_by");
$subject = tp_input("subject");
$institution = tp_input("institution");
$centre = tp_input("centre");
$candidate_number = tp_input("candidate_number");
$status = tp_input("status");
$course_category = tp_input("course_category");

$award_date = tp_input("award_date");

///////////////=== Save Report ===///////////////
if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($cover_letter_type)){

$data_array = array(
"cover_letter_type" => "'$cover_letter_type'",
"client" => "'$client'",
"completion_date" => "'$completion_date'",
"attention" => "'$attention'",
"reference_no" => "'$reference_no'",
"client_designation" => "'$client_designation'",
"re" => "'$re'",
"invoice_attachment" => "'$invoice_attachment'",
"signatory" => "'$signatory'",
"names" => "'$names'",
"school" => "'$school'",
"year" => "'$year'",
"qualification" => "'$qualification'",
"grade" => "'$grade'",
"course" => "'$course'",
"transaction_ref" => "'$transaction_ref'",
"comment" => "'$comment'",
"report_source" => "'$report_source'",
"confirmation_type" => "'$confirmation_type'",
"provided_by" => "'$provided_by'",
"subject" => "'$subject'",
"institution" => "'$institution'",
"centre" => "'$centre'",
"candidate_number" => "'$candidate_number'",
"status" => "'$status'",
"award_date" => "'$award_date'",
"batch_category" => "'$batch_category'",
"details_category" => "'$details_category'",
"list_category" => "'$list_category'",
"course_category" => "'$course_category'",
"generated_by" => "'$id'",
"date_generated" => "'$date_time'"
);
$db->insert($data_array, "cover_letters");

}
///////////////////////////////////////////

?>