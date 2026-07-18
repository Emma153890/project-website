<?php
    require_once("../includes/dbconn.php");
    if(!empty($_POST["emailid"])) {
        $email= $_POST["emailid"];
        if (filter_var($email, FILTER_VALIDATE_EMAIL)===false) {

            echo "error : You did not enter a valid email.";
        } else {
            $result ="SELECT count(*) FROM userRegistration WHERE email=?";
            $stmt = $mysqli->prepare($result);
            $stmt->bind_param('s',$email);
            $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    if($count>0){
    echo "<span style='color:red'> Email already exist! Try using new one.</span>";
        } else {
            echo "<span style='color:green'> Email available for registration!!</span>";
        }
     }
    }

    if(!empty($_POST["oldpassword"])) {
    $pass=$_POST["oldpassword"];
    $pass=md5($pass);
    $result ="SELECT password FROM userregistration WHERE password=?";
    $stmt = $mysqli->prepare($result);
    $stmt->bind_param('s',$pass);
    $stmt->execute();
    $stmt -> bind_result($result);
    $stmt -> fetch();
    $opass=$result;
    if($opass==$pass) 
    echo "<span style='color:green'> Password  matched.</span>";
    else echo "<span style='color:red'>Password doesnot match!</span>";
    }


    if(!empty($_POST["roomno"])) {
        $roomno = $_POST["roomno"];

        $query = "SELECT beds, occupied_beds FROM rooms WHERE room_no=?";

        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('i', $roomno);
        $stmt->execute();

        $result = $stmt->get_result();

        if($row = $result->fetch_assoc()){
            $capacity = $row['beds'];
            $occupied = $row['occupied_beds'];
            $available = $capacity - $occupied;

            if($available <= 0){
                echo "<span style='color:red'>Room is FULL</span>";
            }else{
                echo "<span style='color:green'>$available bed(s) available out of $capacity</span>";
            }
        }
    }
?>