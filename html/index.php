<?php
$db = new SQLite3('../database/database.db');
$userCheck = $db->prepare("SELECT * FROM users WHERE ip_addr=:ipInput");
$userCheck->bindParam(':ipInput', $_SERVER['REMOTE_ADDR'], SQLITE3_TEXT);
$result = $userCheck->execute();
$resultArray = $result->fetchArray();
$emptyString = "";
if (!$resultArray){
	$JSONInfo = json_decode(file_get_contents("../users.json"), true);
	foreach ($JSONInfo as $arrayKey) {
		if ($arrayKey['ip_addr'] == $_SERVER['REMOTE_ADDR']) {
			$userAdd = $db->prepare("INSERT INTO users (username, id, group_id, ip_addr) VALUES (:userInput, :idInput, :groupInput, :ipInput)");
			$userAdd->bindParam(':userInput', $arrayKey['user'], SQLITE3_TEXT);
			$userAdd->bindParam(':idInput', bin2hex(random_bytes(5)), SQLITE3_TEXT);
			$userAdd->bindParam(':groupInput', $emptyString, SQLITE3_TEXT);
			$userAdd->bindParam(':ipInput', $arrayKey['ip_addr'], SQLITE3_TEXT);
			$resultUserAdd = $userAdd->execute();
			header('Refresh: 1; url=index.php');
			die();

		} else {
			continue;
		}
	}
}
if (isset($_POST['team_id'])) {
	if ($resultArray) {
		$teamAdd = $db->prepare("UPDATE users SET group_id=:idInput WHERE username=:userInput");
		$teamAdd->bindParam(':idInput', strtolower($_POST['team_id']), SQLITE3_TEXT);
		$teamAdd->bindParam(':userInput', $resultArray['username'], SQLITE3_TEXT);
		$resultAdd = $teamAdd->execute();
		$resultAddArray = $resultAdd->fetchArray();
		header('Refresh: 1; url=index.php');
		die();
	}
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>HVA battlegrounds</title>
	<link rel="stylesheet" href="./css/bootstrap.min.css">
	<script src="./js/jquery.min.js"></script>
	<script src="./js/bootstrap.min.js"></script>

	<style>
		.rainbow-text {
			background-image: linear-gradient(to left, violet, indigo, blue, green, yellow, orange, red);
			-webkit-background-clip: text;
			color: transparent;
		}

		@font-face {
			font-family: 'Hacked CRT';
			src: url('./fonts/Hacked-CRT.woff2') format('woff2'),
				url('./fonts/Hacked-CRT.woff') format('woff');
			font-weight: normal;
			font-style: normal;
			font-display: swap;
		}

		body {
			<?php if (!$resultArray) { ?>background: url("./img/4b4d097cc9344e51b49dbd3065fa98ca.gif") no-repeat center center fixed;
			<?php } else { ?>background: url("./img/2ff68556b9d25c1aed0d365af26a8042.gif") no-repeat center center fixed;
			<?php } ?>-webkit-background-size: cover;
			-moz-background-size: cover;
			-o-background-size: cover;
			background-size: cover;
		}
	</style>
</head>

<body>
	<div class="container">
		<div class="row">
			<div class="col-sm">
				<img src="./img/logo.png" class="img-responsive mx-auto center-block">
			</div>
			<div style="background-color: rgba(0, 0, 0, 0.8); padding: 20px; border-style: solid; border-radius: 10px; border-color: #ff2a6d;" class="col-sm">
				<?php
				if (!$resultArray) { ?>
					<h1 style="text-transform: uppercase; font-family: 'Hacked CRT'; font-weight: bold; font-style: normal; color: red" class="text-center align-middle">Not connected through VPN</h1>
					<p>Please note: it can take up to 10 seconds for the server to register your connection. Try again in a couple seconds</p>
				<?php
				} else { ?>
					<h1 style="text-transform: uppercase; font-family: 'Hacked CRT'; font-weight: bold; font-style: normal; color: #05d9e8" class="text-center align-middle">Welcome, <?php echo $resultArray['username']; ?></h1>
				<?php } ?>
			</div>
			<br>
			<?php if ($resultArray) { ?>
				<div style="background-color: rgba(0, 0, 0, 0.8); padding: 20px; border-style: solid; border-radius: 10px; border-color: #ff2a6d" class="col-sm">
					<?php
					if (empty($resultArray['group_id'])) {
					?>
						<form action="index.php" method="POST">
							<div class="form-group mb-2">
								<label style="text-transform: uppercase; font-family: 'Hacked CRT'; color: #05d9e8" for="team_id">Join team</label>
								<input name="team_id" type="text" class="form-control" id="team_id" aria-describedby="emailHelp" placeholder="Teamcode" required>
								<br>
								<button type="submit" class="btn btn-primary mb-2">Join</button>
							</div>
						</form>
					<?php
						exit;
					} else {
						$teamCheck = $db->prepare("SELECT * FROM groups WHERE id=:idInput");
						$teamCheck->bindParam(':idInput', $resultArray['group_id'], SQLITE3_TEXT);
						$resultTeam = $teamCheck->execute();
						$resultTeamArray = $resultTeam->fetchArray();
					?>
						<p style="text-transform: uppercase; font-family: 'Hacked CRT'; color: #05d9e8">You are part of team <span class="rainbow-text"><?php echo $resultTeamArray['name'] ?></span></p>
						<p style="text-transform: uppercase; font-family: 'Hacked CRT'; color: #05d9e8">The game has not yet started</p>
					<?php } ?>
				</div><?php } ?>
		</div>
	</div>
	<?php if ($resultArray && !empty($resultArray['group_id'])) { ?>
		<script>
			var interval = 1000;
			function doAjax() {
				$.ajax({
					type: 'GET',
					url: 'gamestate.php',
					data: $(this).serialize(),
					dataType: 'json',
					success: function(data) {
						if (data[0].enabled == "yes") {
							window.location.replace("./lobby.php");
						}     
					},
					complete: function(data) {
						setTimeout(doAjax, interval);
					}
				});
			}
			setTimeout(doAjax, interval);
		</script>
	<?php } ?>
</body>

</html>
