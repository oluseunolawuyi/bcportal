<?php
$file = (isset($_REQUEST["file"]) && !empty($_REQUEST["file"]))?"../reports/" . $_REQUEST["file"]:"";
if (!empty($file) && file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($file).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
}

$document = (isset($_REQUEST["document"]) && !empty($_REQUEST["document"]))?$_REQUEST["document"]:"";
if (!empty($document) && file_exists($document)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($document).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($document));
    readfile($document);
}
?>