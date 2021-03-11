<?php
    $db = new SQLite3('../database/database.db');
    $stateCheck = $db->prepare("SELECT * FROM status");
    $executed = $stateCheck->execute();
    $response = array();
    while ($responsePost = $executed->fetchArray(SQLITE3_ASSOC)) {
        $response[] = $responsePost;
    }
    http_response_code(200);
    header('Content-type: application/json');
    echo json_encode($response);
    die();
