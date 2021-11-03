<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"> -->
	<!-- <script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script> -->
	<script src="assets/modules/jquery-latest.min.js" type="text/javascript"></script>
  <!-- <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script> -->
	<script src="assets/modules/bootstrap.min.js" type="text/javascript"></script>
  <script src="assets/modules/jquery-ui.min.js"></script>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link href="assets/modules/jquery-ui.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" href="assets/modules/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/main.css">
	<title>Henkilö</title>
</head>
<body>
	

	<?php

	session_start();

	#if (!$_SESSION['user']) {
	#  header('Location: /');

	if ($_SESSION['user']['login'] != 'admin') {
		require 'vendor/user.php';
	}

	else {
		require 'vendor/admin.php';
	}
	
	?>

</body>
</html>