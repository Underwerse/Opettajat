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
	<!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"> -->
	<link rel="stylesheet" href="assets/modules/bootstrap.min.css">
	<!-- <script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script> -->
	<script src="assets/modules/jquery-latest.min.js" type="text/javascript"></script>
	<script src="assets/modules/bootstrap.min.js" type="text/javascript"></script>
	<link rel="stylesheet" href="assets/css/main.css">
	<script src="assets/js/script.js" type="text/javascript"></script>
    <title>Rekisteroi</title>
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>

	<div class="register">
		<form action="vendor/signup.php" method="post" enctype="multipart/form-data">
			<label>Nimi</label>
			<input type="text" name="name" placeholder="Kirjoita nimisi">
			<label>Sukunimi</label>
			<input type="text" name="surname" placeholder="Kirjoita sukunimisi">
			<label>Login</label>
			<input type="text" name="login" placeholder="Kirjoita login">
			<label>Sähköpostiosoite</label>
			<input type="email" name="email" placeholder="Kirjoita sähköpostiosoitesi">
			<label>Kuva</label>
			<input type="file" name="avatar">
			<label>Salasana</label>
			<input type="password" name="password" placeholder="Kirjoita salasana">
			<label>Salasanan vahvistus</label>
			<input type="password" name="password_confirm" placeholder="Vahvista salasanasi">
			<button class="btn btn-success" type="submit">Rekisteroi</button>
			<p>
				Onko sinulla jo tili? - <a href="/">kirjaudu sisään</a>!
		</p>
			<?php
				if (isset($_SESSION['message'])) {
						echo '<p class="msg"> ' . $_SESSION['message'] . ' </p>';
				}
				unset($_SESSION['message']);
			?>
		</form>
	</div>

</body>
</html>