<?php
$db = new SQLite3('../database/database.db');
$userCheck = $db->prepare("SELECT * FROM users WHERE ip_addr=:ipInput");
$userCheck->bindParam(':ipInput', $_SERVER['REMOTE_ADDR'], SQLITE3_TEXT);
$result = $userCheck->execute();
$resultArray = $result->fetchArray();

if (!$resultArray) {
    header('Refresh: 1; url=index.php');
    die();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lobby - HVA battlegrounds</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <script src="./js/jquery.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>

    <style>
        /* width */
        ::-webkit-scrollbar {
            width: 5px;
        }

        /* Track */
        ::-webkit-scrollbar-track {
            border-radius: 10px;
        }

        /* Handle */
        ::-webkit-scrollbar-thumb {
            background: #ff2a6d;
            border-radius: 10px;
        }

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
            background: url("./img/2ff68556b9d25c1aed0d365af26a8042.gif") no-repeat center center fixed;
            -webkit-background-size: cover;
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
                <h1 style="text-transform: uppercase; font-family: 'Hacked CRT'; font-weight: bold; font-style: normal; color: #05d9e8" class="text-center align-middle">Lobby</h1>
            </div>
        </div>
        <div class="row">
            <div style="background-color: rgba(0, 0, 0, 0.8); padding: 20px; border-style: solid; border-radius: 10px; border-color: #ff2a6d;" class="col-md-4">
                <?php
                $teamCheck = $db->prepare("SELECT * FROM groups WHERE id=:idInput");
                $teamCheck->bindParam(':idInput', $resultArray['group_id'], SQLITE3_TEXT);
                $resultTeam = $teamCheck->execute();
                $resultTeamArray = $resultTeam->fetchArray();
                ?>
                <h4 style="text-transform: uppercase; font-family: 'Hacked CRT'; font-weight: bold; font-style: normal; color: #05d9e8" class="text-center align-middle"><?php echo $resultTeamArray['name'] ?></h4>
                <hr style="border-color: #ff2a6d">
                <div style="max-height: 500px; overflow-y: auto">
                    <?php
                    $memberCheck = $db->prepare("SELECT username,ip_addr FROM users WHERE group_id=:idInput");
                    $memberCheck->bindParam(':idInput', $resultArray['group_id'], SQLITE3_TEXT);
                    $resultMember = $memberCheck->execute();
                    $response = array();
                    while ($resultMemberArray = $resultMember->fetchArray(SQLITE3_ASSOC)) {
                        $response[] = $resultMemberArray;
                    }
                    foreach ($response as $entry) { ?>
                        <div class="list_entry">
                            <h4 style="text-transform: uppercase; font-family: 'Hacked CRT'; color: white"><?php echo $entry['username']; ?> </h4>
                            <p><?php echo $entry['ip_addr']; ?> </p>
                        </div>

                    <?php } ?>
                </div>
            </div>
            <div style="background-color: rgba(0, 0, 0, 0.8); padding: 20px; border-style: solid; border-radius: 10px; border-color: #ff2a6d;" class="col-md-4">
                <h4 style="text-transform: uppercase; font-family: 'Hacked CRT'; font-weight: bold; font-style: normal; color: #05d9e8" class="text-center align-middle">Rules</h4>
                <hr style="border-color: #ff2a6d">
                <div style="color: white; max-height: 500px; overflow-y: auto">
			<ul>
				<li>You can not change your root password. This will prevent the machinechecks and flag-planting from working.</li>
				<li>Reverse shells may not always work on this network, so don't give up if something does not work.</li>
				<li>If your server does not want to revert, please contact Tigo for a manual revert.</li>
				<li>Try to think ahead: plant backdoors, create new users, do what it takes to keep your access to other machines. Once a vulnerability has been patched, it is patched.</li>
				<li>Do not get frustrated if you get hacked. It's part of the fun, and we are all here to learn. Instead, try to research the vulnerabilities of yourself. Teamwork is crucial.</li>
				<li>At the end of the 24 hours, the winner will be announced via microsoft teams</li>
				<li>Please keep in mind that this infrastructure is currently set up in a home network, and sits on a desk attached to a cheap network switch. Things may not always work as expected.</li>
			</ul>

			<h2>Rules for passing the machine tests</h2>
			<ul>
				<li>Machines must remain online and powered on</li>
				<li>Users present by default on your machines, must ALWAYS be there. You cannot remove users</li>
				<li>You cannot alter network configurations.</li>
				<li>Functionality of webapplications cannot change. They must serve their purpose</li>
				<li>All common services active by default must remain active. You are allowed to restart these services for maintenance services, but if the service is not active during an check you will lose points.</li>
			</ul>
			<p>Failing these tests will result in a loss of 10 points. This process is automated.</p>
			<p>Automated checking (runs every 2 minutes) is working, but not perfect. We will be actively checking the servers. If we manually detect something, 30 points will be subtracted (we can change this value according to how long the machine has been failing these guidelines)</p>
			<p>If you suspect someone is breaking the rules, notify Tigo, Jasper, Elianne, Safae, Martijn and/or Dani privately over microsoft teams. We will be the deciding factor if someone is breaking the rules.</p>
		</div>
            </div>
            <div style="background-color: rgba(0, 0, 0, 0.8); padding: 20px; border-style: solid; border-radius: 10px; border-color: #ff2a6d" class="col-md-4">
                <h4 style="text-transform: uppercase; font-family: 'Hacked CRT'; font-weight: bold; font-style: normal; color: #05d9e8" class="text-center align-middle">Enemy teams</h4>
                <hr style="border-color: #ff2a6d">
                <div style="max-height: 500px; overflow-y: auto">
                    <?php
                    $enemyCheck = $db->prepare("SELECT * FROM groups WHERE id != :idInput");
                    $enemyCheck->bindParam(':idInput', $resultArray['group_id'], SQLITE3_TEXT);
                    $resultEnemy = $enemyCheck->execute();
                    $responseEnemies = array();
                    while ($resultEnemyArray = $resultEnemy->fetchArray(SQLITE3_ASSOC)) {
                        $responseEnemies[] = $resultEnemyArray;
                    }
                    foreach ($responseEnemies as $entry) { ?>
                        <div class="list_entry">
                            <h4 class="text-right" style="text-transform: uppercase; font-family: 'Hacked CRT'; color: white"><?php echo $entry['name']; ?> </h4>
                        </div>

                    <?php } ?>
                </div>
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
                            if (data[0].enabled == "no") {
                                window.location.replace("./index.php");
                            }
			    if (data[1].enabled == "yes") {
                                window.location.replace("./battlegrounds.php");
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
