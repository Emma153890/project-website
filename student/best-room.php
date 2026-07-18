<?php
    include('../includes/dbconn.php');

    $query = "SELECT room_no, beds, occupied_beds, (beds - occupied_beds) AS free_seats FROM rooms WHERE occupied_beds < beds ORDER BY free_seats ASC, room_no ASC LIMIT 1";

    $result = $mysqli->query($query);

    if($row = $result->fetch_assoc()){
        echo json_encode([
            "room_no" => $row['room_no'],
            "free_seats" => $row['free_seats']
        ]);
    }else{
        echo json_encode([
            "room_no" => null
        ]);
    }
?>