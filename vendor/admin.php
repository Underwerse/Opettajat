<?php

require 'userheader.php';

?>

<h2 style="color: #b7625c">Pääkäyttäjän paneeli</h2>
<br>

<div class="container">


<!-- Check teacher's data -->


	<h3>Tietojen tarkastus</h3>
	<div class="row justify-content-center admin_check">
		<div class="col-md-3 col-sm-12">
		<form class="" action="" method="post">
			<label>Aloituspäivä</label>
			<input type="date" name="lesson_date_from" value="<?php echo date('Y-m-d', strtotime("-6 day")); ?>" />
			<label>Päättymispäivä</label>
			<input type="date" name="lesson_date_to" value="<?php echo date('Y-m-d'); ?>" />
			<label>Valitse opettaja</label>
			<?php
	
				require_once "connect.php";
				$sql_teacher_name = "SELECT CONCAT(`name`, ' ', `surname`) AS `name` FROM `users` WHERE NOT login = 'admin' ORDER BY `name`";
				$result_teacher_name = mysqli_query($connect, $sql_teacher_name);
				echo '<input name="teacher_name" list="teacher_name" autocomplete="off">
				<datalist id="teacher_name">';
				while($rows_name = mysqli_fetch_array($result_teacher_name)) {
					?>
					<option value="<?php echo $rows_name['name']; ?>">
					<?php
				}

			?>
			</datalist>

			<!-- <label>Valitse koulutuksen tyyppi</label>
			#<?php
		
			#	require_once "connect.php";
			#	$sql_study_type = "SELECT * FROM `studies`";
			#	$result_study_type = mysqli_query($connect, $sql_study_type);
			#	echo '<input name="study_name" list="study_name">
			#	<datalist id="study_name">';
			#	while($rows=mysqli_fetch_array($result_study_type)) {
			#	?>
			#	<option value="<?php echo $rows['study_name']; ?>">
			#	<?php
			#	}
			#	?>
				</datalist> -->
				<button name="admin_submit" class="btn btn-success" type="submit">Näytä tiedot</button>
			</div>
				
			<div class="col-md-9 col-sm-12 table_statistics">
				<h3>Tiedot valitusta ajanjaksosta</h3>
				
				<?php
	
					require_once 'connect.php';

					$_SESSION['check_form'] = [];
					
					if (isset($_POST['admin_submit'])) {
						$lesson_date_from = $_POST['lesson_date_from'];
						$lesson_date_to = $_POST['lesson_date_to'];
						$teacher_name = $_POST['teacher_name'];

						$_SESSION['check_form'] = [
							"lesson_date_from" => $lesson_date_from,
							"lesson_date_to" => $lesson_date_to,
							"teacher_name" => $teacher_name
						];

					} else {
						$lesson_date_from = date('Y-m-d', strtotime("-6 day"));
						$lesson_date_to = date('Y-m-d');
						$teacher_name = '';
					}
					
					if (isset($_GET['del_id'])) { //check if it's row to delete
						//delete row from DB
						$sql = mysqli_query($connect, "DELETE FROM `basiclessons` WHERE `id` = {$_GET['del_id']}");
						if ($sql) { //show message of successfull deletion
							echo '<script>$(window).load(function() {
								$("#dialog").dialog();
							});
						</script>';
							echo '<div id="dialog" title="Viesti">Rivi poistettu</div>';
						} else {
							echo '<div id="dialog" title="Viesti">Virhe: ' . mysqli_error($connect) . '</div>';
						}
						echo '<script type="text/javascript">
						location.href="/profile.php"</script>';
					}

					// $total = mysqli_fetch_assoc(mysqli_query($connect, "SELECT TIME_FORMAT(SEC_TO_TIME(SUM(time_to_sec(lesson_time))), '%k:%i') AS sum_time,
					// 	round(SUM(TIME_TO_SEC(`lesson_time`) / 2700),2) as sum_qty
					// 	FROM basiclessons
					// 	WHERE (SELECT CONCAT(users.name, ' ', users.surname) FROM users 
					// 	WHERE users.id = basiclessons.id_teacher LIMIT 1) = '$teacher_name' 
					// 	AND lesson_date >= '$lesson_date_from' AND lesson_date <= '$lesson_date_to'"));
					
					if ($_SESSION['check_form'] != []) {
						$lesson_date_from = $_SESSION['check_form']['lesson_date_from'];
						$lesson_date_to = $_SESSION['check_form']['lesson_date_to'];
						$teacher_name = $_SESSION['check_form']['teacher_name'];
					}

					// echo '<pre>';
					// print_r($_SESSION);
					// print_r($_POST);
					// print_r($_GET);
					// echo '</pre>';

						$res_study_types = mysqli_query($connect, "SELECT DISTINCT `study_type` 
						FROM `basiclessons` 
						WHERE `id_teacher` = 
						(SELECT id_teacher FROM basiclessons 
						WHERE id_teacher =
						(SELECT id FROM users WHERE CONCAT(users.name, ' ', users.surname) = '$teacher_name') LIMIT 1) AND `lesson_date` >= '$lesson_date_from' AND `lesson_date` <= '$lesson_date_to'
						ORDER BY `study_type`") or die;
						
						echo '<h5 style="text-align: center">Ajanjakso: '.date("d-m-Y", strtotime($lesson_date_from)).' - '.date("d-m-Y", strtotime($lesson_date_to)).'</h5>';
						echo '<h4 style="text-align: center">'.$teacher_name.'</h4>';

						// echo '<pre>';
						// print_r($teacher_name);
						// echo '</pre>';

						while($res_study_types_rows = mysqli_fetch_assoc($res_study_types)) {

							$res_study_types_row = $res_study_types_rows['study_type'];

							$res = mysqli_query($connect, "SELECT id, `group_name`, `study_type`, TIME_FORMAT	(lesson_time, '%k:%i') as `lesson_time`, ROUND((TIME_TO_SEC(lesson_time) / 2700), 2) as lesson_qty, lesson_date, comment FROM `basiclessons` WHERE (SELECT CONCAT(users.name, ' ', users.surname) FROM users WHERE users.id = basiclessons.id_teacher LIMIT 1) = '$teacher_name' AND `study_type` = '$res_study_types_row' AND `lesson_date` >= '$lesson_date_from' AND `lesson_date` <= '$lesson_date_to'
							ORDER BY `lesson_date`") or die;

							$res_sum_lesson_time = mysqli_query($connect, "SELECT TIME_FORMAT(SEC_TO_TIME(SUM(time_to_sec(lesson_time))), '%k:%i') as `sum_lesson_time` FROM `basiclessons` WHERE (SELECT CONCAT(users.name, ' ', users.surname) FROM users WHERE users.id = basiclessons.id_teacher LIMIT 1) = '$teacher_name' AND `study_type` = '$res_study_types_row' AND `lesson_date` >= '$lesson_date_from' AND `lesson_date` <= '$lesson_date_to'") or die;

							$row_sum_lesson_time = mysqli_fetch_assoc($res_sum_lesson_time);

							// echo '<pre>';
							// print_r($res_study_types_rows);
							// echo '</pre>';
						
							echo '<div class="table_check_data_wrap"><table class="table_added_data">
							<h4>'.$res_study_types_row.':</h4>
							<tr>
							<th>Päivä</th><th>Ryhmä</th><th>Tuntien määrä</th><th>Oppituntien lkm</th><th>Kommentti</th>
							</tr>';

							while($row = mysqli_fetch_assoc($res)) {
								$total = mysqli_fetch_assoc(mysqli_query($connect, "SELECT TIME_FORMAT(SEC_TO_TIME(SUM(time_to_sec(lesson_time))), '%k:%i') AS sum_time,
								round(SUM(TIME_TO_SEC(`lesson_time`) / 2700),2) as sum_qty
								FROM basiclessons
								WHERE (SELECT CONCAT(users.name, ' ', users.surname) FROM users 
								WHERE users.id = basiclessons.id_teacher LIMIT 1) = '$teacher_name' 
								AND lesson_date >= '$lesson_date_from' AND lesson_date <= '$lesson_date_to'"));

								echo '<tr><td>'.$row['lesson_date'].'</td><td>'.$row['group_name'].'</td><td>'.$row['lesson_time'].' t.</td><td>'.$row['lesson_qty'].'</td><td>'.$row['comment'].'</td><td><a href="?del_id='.$row['id'].'">Poistaa</a></td></tr>';
							}
							echo '</table>';
						
							echo '<br>';

							$sum = mysqli_query($connect, "SELECT TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(`lesson_time`))), '%k:%i') AS `total_time`, round(SUM(TIME_TO_SEC(`lesson_time`) / 2700),2) as `total_les_qty` FROM basiclessons WHERE (SELECT CONCAT(users.name, ' ', users.surname) FROM users WHERE users.id = basiclessons.id_teacher LIMIT 1) = '$teacher_name' AND `study_type` = '$res_study_types_row' AND `lesson_date` >= '$lesson_date_from' AND `lesson_date` <= '$lesson_date_to'") or die;
					
							echo '<table class="table_added_data table_summary_row">';
							while($summary = mysqli_fetch_assoc($sum)) {
								echo '<tr><td>'.'Kokonaisaika'.'</td><td></td><td>'.$summary['total_time'].' t.</td><td>'.$summary['total_les_qty'].'</td><td>'.round($summary['total_les_qty'] / $total['sum_qty'] * 100, 1).' %</td><td></td></tr>';
							}
							echo '</table></div>';
							echo '<br>';
						}

						
						$total = mysqli_fetch_assoc(mysqli_query($connect, "SELECT TIME_FORMAT(SEC_TO_TIME(SUM(time_to_sec(lesson_time))), '%k:%i') AS sum_time,
						round(SUM(TIME_TO_SEC(`lesson_time`) / 2700),2) as sum_qty
						FROM basiclessons
						WHERE (SELECT CONCAT(users.name, ' ', users.surname) FROM users 
						WHERE users.id = basiclessons.id_teacher LIMIT 1) = '$teacher_name' 
						AND lesson_date >= '$lesson_date_from' AND lesson_date <= '$lesson_date_to';"));

						$res_groups = mysqli_query($connect, "SELECT DISTINCT `group_name` 
						FROM `basiclessons` 
						WHERE `id_teacher` = 
						(SELECT id_teacher FROM basiclessons 
						WHERE id_teacher =
						(SELECT id FROM users WHERE CONCAT(users.name, ' ', users.surname) = '$teacher_name') LIMIT 1) AND `lesson_date` >= '$lesson_date_from' AND `lesson_date` <= '$lesson_date_to'
						ORDER BY `group_name`") or die;

						echo '<div class="table_check_data_wrap total_summary"><table class="table_added_data table_summary_row total_summary">

						<tr><h3>Yhteenveto</h3></tr>
						<tr>
						<th></th><th></th><th>Tuntimäärä</th><th>Oppituntien lkm</th><th></th>
						</tr>';
						echo '<tr><td>Opetus</td><td></td><td>'.$total['sum_time'].' t.</td><td>'.$total['sum_qty'].'</td><td>100%</td><td></td></tr>';
						echo '</table></div>';
						echo '<br>';








						echo '<div class="table_check_data_wrap total_summary"><table class="table_added_data total_summary">';

						while($res_groups_rows = mysqli_fetch_assoc($res_groups)) {

							$res_groups_row = $res_groups_rows['group_name'];

							$res = mysqli_query($connect, "SELECT id, `group_name`, `study_type`, TIME_FORMAT	(lesson_time, '%k:%i') as `lesson_time`, ROUND((TIME_TO_SEC(lesson_time) / 2700), 2) as lesson_qty, lesson_date, comment FROM `basiclessons` WHERE (SELECT CONCAT(users.name, ' ', users.surname) FROM users WHERE users.id = basiclessons.id_teacher LIMIT 1) = '$teacher_name' AND `group_name` = '$res_groups_row' AND `lesson_date` >= '$lesson_date_from' AND `lesson_date` <= '$lesson_date_to'") or die;

							$res_sum_lesson_time = mysqli_query($connect, "SELECT TIME_FORMAT(SEC_TO_TIME(SUM(time_to_sec(lesson_time))), '%k:%i') as `sum_lesson_time` FROM `basiclessons` WHERE (SELECT CONCAT(users.name, ' ', users.surname) FROM users WHERE users.id = basiclessons.id_teacher LIMIT 1) = '$teacher_name' AND `group_name` = '$res_groups_row' AND `lesson_date` >= '$lesson_date_from' AND `lesson_date` <= '$lesson_date_to'") or die;

							$row_sum_lesson_time = mysqli_fetch_assoc($res_sum_lesson_time);

							// echo '<pre>';
							// print_r($res_study_types_rows);
							// echo '</pre>';
						
							$sum = mysqli_query($connect, "SELECT TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(`lesson_time`))), '%k:%i') AS `total_time`, round(SUM(TIME_TO_SEC(`lesson_time`) / 2700),2) as `total_les_qty` FROM basiclessons WHERE (SELECT CONCAT(users.name, ' ', users.surname) FROM users WHERE users.id = basiclessons.id_teacher LIMIT 1) = '$teacher_name' AND `group_name` = '$res_groups_row' AND `lesson_date` >= '$lesson_date_from' AND `lesson_date` <= '$lesson_date_to'") or die;
					
							

							while($summary = mysqli_fetch_assoc($sum)) {
								echo '<tr><td>'.$res_groups_row.'</td><td></td><td>'.$summary['total_time'].' t.</td><td>'.$summary['total_les_qty'].'</td><td>'.round($summary['total_les_qty'] / $total['sum_qty'] * 100, 1).' %</td><td></td></tr>';
							}
						}
						echo '</table></div>';












							
				
				?>
			</div>

			<div class="col-sm-12 col-md-3">
			</div>
			<div class="col-md-9 col-sm-12 table_statistics">
			<h3>Mut työt valitusta ajanjaksosta</h3>

<!-- Muut työt check block statistics -->
			
			<?php

				if (isset($_GET['del_id'])) { //check if it's row to delete
					//delete row from DB
					$sql = mysqli_query($connect, "DELETE FROM `otherjobs` WHERE `id` = {$_GET['del_id']}");
					if ($sql) { //show message of successfull deletion
						echo '<script>$(window).load(function() {
							$("#dialog").dialog();
						});
					</script>';
						echo '<div id="dialog" title="Viesti">Rivi poistettu</div>';
					} else {
						echo '<div id="dialog" title="Viesti">Virhe: ' . mysqli_error($connect) . '</div>';
					}
					echo '<script type="text/javascript">
					location.href="/profile.php"</script>';
				}


				// Summary other_jobs time in seconds:

				$other_jobs_total = mysqli_fetch_assoc(mysqli_query($connect, "SELECT TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(`lesson_time`))), '%k:%i') AS `other_jobs_total_time`, SUM(time_to_sec(lesson_time)) AS other_jobs_total_sec
				FROM otherjobs
				WHERE (SELECT CONCAT(users.name, ' ', users.surname) FROM users 
				WHERE users.id = otherjobs.id_teacher LIMIT 1) = '$teacher_name'	
				AND lesson_date >= '$lesson_date_from' AND lesson_date <= '$lesson_date_to'"));

				// echo '<pre>';
				// print_r($other_jobs_total);
				// echo '</pre>';

				echo '<h5 style="text-align: center">Ajanjakso: '.date("d-m-Y", strtotime($lesson_date_from)).' - '.date("d-m-Y", strtotime($lesson_date_to)).'</h5>';

				// all the other_jobs rows on selected dates

				$other_jobs = mysqli_query($connect, "SELECT DISTINCT `job_name` 
				FROM `otherjobs` 
				WHERE (SELECT CONCAT(users.name, ' ', users.surname) FROM users 
				WHERE users.id = otherjobs.id_teacher LIMIT 1) = '$teacher_name'
				AND `lesson_date` >= '$lesson_date_from' AND `lesson_date` <= '$lesson_date_to'
				ORDER BY `job_name`") or die;

				while($other_jobs_rows = mysqli_fetch_assoc($other_jobs)) {
					
				$other_jobs_row = $other_jobs_rows['job_name'];
				
				$res = mysqli_query($connect, "SELECT id, `group_name`, `job_name`, TIME_FORMAT(lesson_time, '%k:%i') as `lesson_time`, lesson_date, TIME_TO_SEC(`lesson_time`) as `job_sec`, comment FROM `otherjobs` WHERE (SELECT CONCAT(users.name, ' ', users.surname) FROM users
				WHERE users.id = otherjobs.id_teacher LIMIT 1) = '$teacher_name' AND `job_name` = '$other_jobs_row' AND `lesson_date` >= '$lesson_date_from' AND `lesson_date` <= '$lesson_date_to'") or die;

				echo '<div class="table_check_data_wrap"><table class="table_added_data">
				<h4>'.$other_jobs_row.':</h4>
				<tr>
				<th>Päivä</th><th>Ryhmä</th><th>Tuntimäärä</th><th></th><th>Kommentti</th>
				</tr>';
				while($row = mysqli_fetch_assoc($res)) {
					echo '<tr><td>'.$row['lesson_date'].'</td><td>'.$row['group_name'].'</td><td>'.$row['lesson_time'].' t.</td><td></td><td>'.$row['comment'].'</td><td><a href="?del_id='.$row['id'].'">Poistaa</a></td></tr>';
				}
				echo '</table>';
		
				echo '<br>';

				$res_groups = mysqli_query($connect, "SELECT DISTINCT `group_name` 
				FROM `otherjobs` 
				WHERE `id_teacher` = 
				(SELECT id_teacher FROM otherjobs 
				WHERE id_teacher =
				(SELECT id FROM users WHERE CONCAT(users.name, ' ', users.surname) = '$teacher_name') LIMIT 1) AND `lesson_date` >= '$lesson_date_from' AND `lesson_date` <= '$lesson_date_to'
				ORDER BY `group_name`") or die;

				$other_jobs_sum = mysqli_query($connect, "SELECT TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(`lesson_time`))), '%k:%i') AS `total_time`, SUM(TIME_TO_SEC(`lesson_time`)) as `total_jobs_sec` FROM otherjobs WHERE (SELECT CONCAT(users.name, ' ', users.surname) FROM users 
				WHERE users.id = otherjobs.id_teacher LIMIT 1) = '$teacher_name' AND `job_name` = '$other_jobs_row' AND `lesson_date` >= '$lesson_date_from' AND `lesson_date` <= '$lesson_date_to'") or die;
		
				echo '<table class="table_added_data table_summary_row">';

				while($other_jobs_summary = mysqli_fetch_assoc($other_jobs_sum)) {
					echo '<tr><td>'.'Kokonaismäärä'.'</td><td></td><td>'.$other_jobs_summary['total_time'].' t.</td><td></td><td>'.round($other_jobs_summary['total_jobs_sec'] / $other_jobs_total['other_jobs_total_sec'] * 100, 1).' %</td><td></td></tr>';
				}
				echo '</table></div>';
				echo '<br>';
			}

			echo '<div class="table_check_data_wrap total_summary"><table class="table_added_data table_summary_row total_summary">
				
				
				<tr><h3>Yhteenveto</h3></tr>
				<tr>
				<th></th><th></th><th>Tuntimäärä</th><th></th><th></th>
				</tr>';
				echo '<tr><td>Muut työt</td><td></td><td>'.$other_jobs_total['other_jobs_total_time'].' t.</td><td></td><td>100%</td><td></td></tr>';
				echo '</table></div><br>';
				












				echo '<div class="table_check_data_wrap total_summary"><table class="table_added_data total_summary">';

				while($res_groups_rows = mysqli_fetch_assoc($res_groups)) {

					$res_groups_row = $res_groups_rows['group_name'];

					$res = mysqli_query($connect, "SELECT id, `group_name`, `job_name`, TIME_FORMAT	(lesson_time, '%k:%i') as `lesson_time`, ROUND((TIME_TO_SEC(lesson_time) / 2700), 2) as lesson_qty, lesson_date, comment FROM `otherjobs` WHERE (SELECT CONCAT(users.name, ' ', users.surname) FROM users WHERE users.id = otherjobs.id_teacher LIMIT 1) = '$teacher_name' AND `group_name` = '$res_groups_row' AND `lesson_date` >= '$lesson_date_from' AND `lesson_date` <= '$lesson_date_to'") or die;

					$res_sum_lesson_time = mysqli_query($connect, "SELECT TIME_FORMAT(SEC_TO_TIME(SUM(time_to_sec(lesson_time))), '%k:%i') as `sum_lesson_time` FROM `otherjobs` WHERE (SELECT CONCAT(users.name, ' ', users.surname) FROM users WHERE users.id = otherjobs.id_teacher LIMIT 1) = '$teacher_name' AND `group_name` = '$res_groups_row' AND `lesson_date` >= '$lesson_date_from' AND `lesson_date` <= '$lesson_date_to'") or die;

					$row_sum_lesson_time = mysqli_fetch_assoc($res_sum_lesson_time);

					// echo '<pre>';
					// print_r($res_study_types_rows);
					// echo '</pre>';
				
					$sum = mysqli_query($connect, "SELECT TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(`lesson_time`))), '%k:%i') AS `total_time`, SUM(TIME_TO_SEC(`lesson_time`)) as `total_time_sec` FROM otherjobs WHERE (SELECT CONCAT(users.name, ' ', users.surname) FROM users WHERE users.id = otherjobs.id_teacher LIMIT 1) = '$teacher_name' AND `group_name` = '$res_groups_row' AND `lesson_date` >= '$lesson_date_from' AND `lesson_date` <= '$lesson_date_to'") or die;
			
					

					while($summary = mysqli_fetch_assoc($sum)) {
						echo '<tr><td>'.$res_groups_row.'</td><td></td><td>'.$summary['total_time'].' t.</td><td></td><td>'.round($summary['total_time_sec'] / $other_jobs_total['other_jobs_total_sec'] * 100, 1).' %</td><td></td></tr>';
					}
				}
				echo '</table></div>';


















			?>
		</div>


		</form>
	</div>


	<!-- Add data to database -->


	<h3>Lisää tiedot tietokantaan</h3>

	<div class="row justify-content-center admin_data_add">
		<div class="col-md-6 col-lg-4">
			<form action="vendor/lessontype.php" method="post">
			
				<h4>Lisää uuden oppitunnin</h4>
				<input type="text" name="lesson_type" autocomplete="off" placeholder="Syötä uuden">
					<button name="admin_add" class="btn btn-success" type="submit">Lisää</button>
				<br>
	
			</form>
		</div>
		
		<div class="col-md-6 col-lg-4">
			<form action="vendor/study.php" method="post">
			
				<h4>Lisää koulutuksen tyyppi</h4>
				<input type="text" name="study_name" autocomplete="off" placeholder="Syötä uuden">
					<button name="admin_add" class="btn btn-success" type="submit">Lisää</button>
				<br>
			
			</form>
		</div>
		
		<div class="col-md-6 col-lg-4">
			<form action="vendor/lessongroup.php" method="post">
			
				<h4>Lisää uuden opetusryhmän</h4>
				<input type="text" name="lesson_group" autocomplete="off" placeholder="Syötä uuden">
					<button name="admin_add" class="btn btn-success" type="submit">Lisää</button>
				<br>
			
			</form>
		</div>
		
		<div class="col-md-6 col-lg-4">
			<form action="vendor/otherjobsadd.php" method="post">
			
				<h4>Lisää muu työ</h4>
				<input type="text" name="job_name" autocomplete="off" placeholder="Syötä uuden">
					<button name="admin_add" class="btn btn-success" type="submit">Lisää</button>
				<br>
			
			</form>
		</div>
		
	</div>

	<h3>Poistaa tiedot tietokannasta</h3>

	<div class="row justify-content-center admin_data_add">
		<div class="col-md-6 col-lg-4">
			<form action="" method="post">
			
				<h4>Poistaa oppitunnin</h4>

				<?php
	
					require_once "connect.php";
					$sql_lessons = "SELECT * FROM `lessons` ORDER BY `les_type`";
					$result_lessons = mysqli_query($connect, $sql_lessons);
					echo '<input name="lessons" list="lessons" autocomplete="off">
					<datalist id="lessons">';
					while($rows=mysqli_fetch_array($result_lessons)){
					?>
					<option value="<?php echo $rows['les_type']; ?>">
					<?php
					}
					?>
					</datalist>

					<button name="admin_lesson_remove" class="btn btn-warning" type="submit">Poista</button>
				<br>
	
				<?php
					if (isset($_POST['admin_lesson_remove'])) {
						$lesson_type = $_POST['lessons'];
						if ($lesson_type != '') {
							require_once 'connect.php';
	
							mysqli_query($connect, "DELETE FROM `lessons` WHERE les_type = '$lesson_type'");
							echo '<script type="text/javascript">
							location.href="/profile.php"</script>';
						}
					}
				?>
			
			</form>
		</div>
		
		<div class="col-md-6 col-lg-4">
			<form action="" method="post">
			
				<h4>Poistaa koulutuksen tyyppi</h4>

				<?php
	
					require_once "connect.php";
					$sql_studies = "SELECT * FROM `studies` ORDER BY `study_name`";
					$result_studies = mysqli_query($connect, $sql_studies);
					echo '<input name="studies" list="studies" autocomplete="off">
					<datalist id="studies">';
					while($rows=mysqli_fetch_array($result_studies)){
					?>
					<option value="<?php echo $rows['study_name']; ?>">
					<?php
					}
					?>
					</datalist>

					<button name="admin_study_remove" class="btn btn-warning" type="submit">Poista</button>
				<br>
	
				<?php
					if (isset($_POST['admin_study_remove'])) {
						$studies = $_POST['studies'];
						if ($studies != '') {
							require_once 'connect.php';
	
							mysqli_query($connect, "DELETE FROM `studies` WHERE study_name = '$studies'");
							echo '<script type="text/javascript">
							location.href="/profile.php"</script>';
						}
					}
				?>
			
			</form>
		</div>
		
		<div class="col-md-6 col-lg-4">
			<form action="" method="post">
			
				<h4>Poistaa opetusryhmän</h4>
				
				<?php
	
					require_once "connect.php";
					$sql_lesson_groups = "SELECT * FROM `lessongroups` ORDER BY `lesson_group`";
					$result_lesson_groups = mysqli_query($connect, $sql_lesson_groups);
					echo '<input name="lesson_groups" list="lesson_groups" autocomplete="off">
					<datalist id="lesson_groups">';
					while($rows=mysqli_fetch_array($result_lesson_groups)){
					?>
					<option value="<?php echo $rows['lesson_group']; ?>">
					<?php
					}
					?>
					</datalist>

					<button name="admin_lesson_group_remove" class="btn btn-warning" type="submit">Poista</button>
				<br>
	
				<?php
					if (isset($_POST['admin_lesson_group_remove'])) {
						$lesson_group = $_POST['lesson_groups'];
						if ($lesson_group != '') {
							require_once 'connect.php';
	
							mysqli_query($connect, "DELETE FROM `lessongroups` WHERE lesson_group = '$lesson_group'");
							echo '<script type="text/javascript">
							location.href="/profile.php"</script>';
						}
					}
				?>
			
			</form>
		</div>
		
		<div class="col-md-6 col-lg-4">
			<form action="" method="post">
			
				<h4>Poistaa muu työ</h4>
				
				<?php
	
					require_once "connect.php";
					$sql_other_jobs = "SELECT * FROM `jobs` ORDER BY `job_name`";
					$result_other_jobs = mysqli_query($connect, $sql_other_jobs);
					echo '<input name="other_jobs" list="other_jobs" autocomplete="off">
					<datalist id="other_jobs">';
					while($rows=mysqli_fetch_array($result_other_jobs)){
					?>
					<option value="<?php echo $rows['job_name']; ?>">
					<?php
					}
					?>
					</datalist>

					<button name="admin_other_jobs_remove" class="btn btn-warning" type="submit">Poista</button>
				<br>
	
				<?php
					if (isset($_POST['admin_other_jobs_remove'])) {
						$other_job = $_POST['other_jobs'];
						if ($other_job != '') {
							require_once 'connect.php';
	
							mysqli_query($connect, "DELETE FROM `jobs` WHERE job_name = '$other_job'");
							echo '<script type="text/javascript">
							location.href="/profile.php"</script>';
						}
					}
				?>
			
			</form>
		</div>
		
	</div>
</div>
