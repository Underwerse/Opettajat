<?php
session_start();

if (isset($_SESSION['user'])) {
    header('Location: profile.php');
}

?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="assets/modules/bootstrap.min.css">
	<script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
	<!-- <script src="assets/modules/jquery-latest.min.js" type="text/javascript"></script> -->
	<script src="assets/modules/bootstrap.min.js" type="text/javascript"></script>
	<link rel="stylesheet" href="assets/css/main.css">
	<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
  <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
    <title>Kirjaudu sisään</title>
</head>
<body>

<!-- Форма авторизации -->

<div class="sign">
	<img src="images/keko.png">
	<h5>Opettajien tuntienseuranna palvelu</h5>
	<form action="vendor/signin.php" method="post">
		<label>Käyttäjän nimi</label>
		<input type="text" name="login" placeholder="Kirjoita käyttäjän nimisi">
		<label>Salasana</label>
		<input type="password" name="password" placeholder="Kirjoita salasanasi">
		<button class="btn btn-success" type="submit">Kirjaudu sisään</button>
		<br>
		<p>
		Eikö sinulla ole tiliä? - <a href="/register.php">rekisteröi</a>!
		</p>
		<?php
			if (isset($_SESSION['message'])) {
				echo '<p class="msg"> ' . $_SESSION['message'] . ' </p>';
			}
			unset($_SESSION['message']);
			if (isset($_SESSION['check_form'])) {
				$_SESSION['check_form'] = [];
			}
		?>
	</form>
</div>

</body>
</html>