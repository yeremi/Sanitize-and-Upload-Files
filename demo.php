<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Sanitize and Upload Files</title>
<style>
body {
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
	color:#333;
}
</style>
</head>
<body>
<h2>Sanitize and Upload Files</h2>
<p>Using html5 input file</p>
<?php
if($_SERVER['REQUEST_METHOD'] == 'POST'){
	include 'classes/SanitizeUploadFile.php';
	$obj = new SanitizeUploadFile();
	$obj->upload_dir = 'uploads/';
	$instance->renameFile = true;
	$instance->prefixSuggest = 'y_';
	//$instance->allowedExt = 'jpg|jpeg|png'; //Optional
	$obj->init( $_FILES['uploads'] );
}
?>
<form action="" method="post" enctype="multipart/form-data" name="form1" id="form1">
<input name='uploads[]' type="file" multiple="multiple" />
<input name="submit" type="submit" id="submit" value="Send" />
</form>
</body>
</html>