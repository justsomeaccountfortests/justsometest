<?php
$db = new SQLite3('../database/database.db');
$userCheck = $db->prepare("SELECT * FROM users WHERE ip_addr=:ipInput");
$userCheck->bindParam(':ipInput', $_SERVER['REMOTE_ADDR'], SQLITE3_TEXT);
$result = $userCheck->execute();
$resultArray = $result->fetchArray();

if (!$resultArray || empty($resultArray['group_id'])) {
    header('Refresh: 1; url=index.php');
    die();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Battlegrounds - HVA battlegrounds</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <script src="./js/jquery.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <script src="./js/battlegrounds_notifications.js"></script>
    <script type="text/javascript">
        var chat = new Chat();
        $(function() {
            chat.getState();
        });
    </script>
    <style>
        .btn-primary-outline {
            background-color: transparent;
            box-shadow: none;
            color: white;
            text-transform: uppercase;
            border-radius: 0;
        }

        .btn-danger-outline {
            background-color: #ff2a6d;
            box-shadow: none;
            color: white;
            text-transform: uppercase;
            border-radius: 0;
        }

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

<body onload="setInterval('chat.update()', 1000)">
    <div class="container">
        <div class="row">
            <div class="col-sm">
                <h1 style="text-transform: uppercase; font-family: 'Hacked CRT'; font-weight: bold; font-style: normal; color: #05d9e8" class="text-center align-middle">battlegrounds</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4" style="height: 616px; padding: 0px">
                <div style="height: 75%; background-color: rgba(0, 0, 0, 0.8); padding: 20px; border-style: solid; border-radius: 10px; border-color: #ff2a6d;">
                    <?php
                    $teamCheck = $db->prepare("SELECT * FROM groups WHERE id=:idInput");
                    $teamCheck->bindParam(':idInput', $resultArray['group_id'], SQLITE3_TEXT);
                    $resultTeam = $teamCheck->execute();
                    $resultTeamArray = $resultTeam->fetchArray();
                    ?>
                    <h4 style="text-transform: uppercase; font-family: 'Hacked CRT'; font-weight: bold; font-style: normal; color: #05d9e8" class="text-center align-middle">Machines</h4>
                    <hr style="border-color: #ff2a6d">
                    <div style="overflow-y: scroll; height: 85%">
                        <?php
                        $machineCheck = $db->prepare("SELECT * FROM machines WHERE group_id=:idInput");
                        $machineCheck->bindParam(':idInput', $resultArray['group_id'], SQLITE3_TEXT);
                        $resultMachine = $machineCheck->execute();
                        $responseMachine = array();
                        while ($resultMachineArray = $resultMachine->fetchArray(SQLITE3_ASSOC)) {
                            $responseMachine[] = $resultMachineArray;
                        }
                        foreach ($responseMachine as $entry) { ?>
                            <div class="list_entry">
                                <h4 id="ip-address-text" style="text-transform: uppercase; font-family: 'Hacked CRT'; color: white"><?php echo $entry['ip_addr']; ?> </h4>
                                <div>
                                    <button style="width: 50%; border: 2px solid black; border-color: #ff2a6d;" type="button" onclick="copyToClipboard('<?php echo $entry['root_pw'] ?>')" class="btn btn-primary-outline">🔑 root password</button>
                                    <button style="width: 50%; border: 2px solid black; border-color: #ff2a6d; float: right" type="button" class="btn btn-primary-outline" disabled>🏁 fetch flag</button>
                                    <button style="width: 50%; border: 2px solid black; border-color: #ff2a6d;" type="button" class="btn btn-primary-outline" disabled>🔎 force check</button>
                                    <button style="width: 50%; border: 2px solid black; border-color: #ff2a6d; float: right" type="button" onclick="revertMachine('<?php echo $entry['ip_addr'] ?>')" class="btn btn-danger-outline">⏪ revert</button>
                                </div>
                            </div>

                        <?php } ?>
                    </div>
                </div>
                <div style="height: 25%; background-color: rgba(0, 0, 0, 0.8); padding: 20px; border-style: solid; border-radius: 10px; border-color: #ff2a6d;">
                    <?php
                    $teamCheck = $db->prepare("SELECT * FROM groups WHERE id=:idInput");
                    $teamCheck->bindParam(':idInput', $resultArray['group_id'], SQLITE3_TEXT);
                    $resultTeam = $teamCheck->execute();
                    $resultTeamArray = $resultTeam->fetchArray();
                    ?>
                    <h4 style="text-transform: uppercase; font-family: 'Hacked CRT'; font-weight: bold; font-style: normal; color: #05d9e8" class="text-center align-middle">Score</h4>
                    <hr style="border-color: #ff2a6d">
                    <h4 id="scoreText" style="text-transform: uppercase; font-family: 'Hacked CRT'; color: white"><?php echo "0" ?> Points</h4>
                </div>
            </div>
            <div class="col-md-4" style="height: 616px; padding: 0px">
                <div style="height: 75%; position:relative; top:0; background-color: rgba(0, 0, 0, 0.8); padding: 20px; border-style: solid; border-radius: 10px; border-color: #ff2a6d;">
                    <h4 style="text-transform: uppercase; font-family: 'Hacked CRT'; font-weight: bold; font-style: normal; color: #05d9e8" class="text-center align-middle">Notifications</h4>
                    <hr style="border-color: #ff2a6d">
                    <div id="notificationArea" style="overflow-y: scroll; height: 85%">
                    </div>
                </div>
                <div style="height: 25%; position:relative; bottom:0; background-color: rgba(0, 0, 0, 0.8); padding: 20px; border-style: solid; border-radius: 10px; border-color: #ff2a6d;">
                    <h4 style="text-transform: uppercase; font-family: 'Hacked CRT'; font-weight: bold; font-style: normal; color: #05d9e8" class="text-center align-middle">Submit flag</h4>
                    <hr style="border-color: #ff2a6d">
                    <input id="flagInput" style="float: left; width: 65%; border-radius: 0" type="text" class="form-control">
                    <button style="float: right; width: 35%; border: 2px solid black; border-color: #ff2a6d;" onclick="postFlag(document.getElementById('flagInput').value)" type="button" class="btn btn-danger-outline">Submit</button>
                </div>
            </div>
            <div style="max-height: 616px; background-color: rgba(0, 0, 0, 0.8); padding: 20px; border-style: solid; border-radius: 10px; border-color: #ff2a6d" class="col-md-4">
                <h4 style="text-transform: uppercase; font-family: 'Hacked CRT'; font-weight: bold; font-style: normal; color: #05d9e8" class="text-center align-middle">Enemy teams</h4>
                <hr style="border-color: #ff2a6d">
                <div style="max-height: 500px; overflow-y: scroll">
                    <div>
                        <?php
                        $enemyCheck = $db->prepare("SELECT * FROM groups WHERE id != :idInput");
                        $enemyCheck->bindParam(':idInput', $resultArray['group_id'], SQLITE3_TEXT);
                        $resultEnemy = $enemyCheck->execute();
                        $responseEnemies = array();
                        while ($resultEnemyArray = $resultEnemy->fetchArray(SQLITE3_ASSOC)) {
                            $responseEnemies[] = $resultEnemyArray;
                        }
                        foreach ($responseEnemies as $entry) { ?>
                            <h4 class="text-right" style="text-transform: uppercase; font-family: 'Hacked CRT'; color: white"><?php echo $entry['name']; ?> </h4>
                            <?php
                            $enemyMachineCheck = $db->prepare("SELECT * FROM machines WHERE group_id = :idInput");
                            $enemyMachineCheck->bindParam(':idInput', $entry['id'], SQLITE3_TEXT);
                            $resultEnemyMachine = $enemyMachineCheck->execute();
                            $responseEnemiesMachine = array();
                            while ($resultEnemyMachineArray = $resultEnemyMachine->fetchArray(SQLITE3_ASSOC)) {
                                $responseEnemiesMachine[] = $resultEnemyMachineArray;
                            }
                            foreach ($responseEnemiesMachine as $entryMachine) { ?>
                                <h4 id="ip-address-text" style="text-transform: uppercase; color: #05d9e8" class="text-right"><?php echo $entryMachine['ip_addr'] ?></h4>
                            <?php } ?>
                            <br>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function copyToClipboard(val) {
                var dummy = document.createElement("input");
                document.body.appendChild(dummy);

                dummy.setAttribute("id", "dummy_id");
                document.getElementById("dummy_id").value = val;
                dummy.focus();
                dummy.select();
                document.execCommand("copy");
                document.body.removeChild(dummy);
            }

            function postFlag(flag) {
                data = new FormData();
                data.append('flag', flag);
                $.ajax({
                    type: 'POST',
                    url: 'submitflag.php',
                    processData: false,
                    contentType: false,
                    data: data,
                    success: function(data) {},
                    complete: function(data) {}
                });
            }
            function revertMachine(machine) {
                var r = confirm("Are you sure you want to revert " + machine + "?");
                if (r == true) {
                    data = new FormData();
                    data.append('ip', machine);
                    $.ajax({
                        type: 'POST',
                        url: 'revert.php',
                        processData: false,
                        contentType: false,
                        data: data,
                        success: function(data) {
                            alert("Machine is reverted.");
                        },
                        complete: function(data) {}
                    });
                }
            }
        </script>
        <?php if ($resultArray && !empty($resultArray['group_id'])) { ?>
            <script>
                var interval = 1000;

                function doAjax() {
                    $.ajax({
                        type: 'GET',
                        url: 'score.php',
                        data: $(this).serialize(),
                        dataType: 'json',
                        success: function(data) {
                            document.getElementById("scoreText").innerHTML = data[0].score + " points";
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
