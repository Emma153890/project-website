<?php
session_start();

include('../includes/dbconn.php');
include('../includes/check-login.php');

check_login();


/* APPROVE PAYMENT */

if(isset($_GET['approve'])){

    $id = intval($_GET['approve']);


    // Get payment details
    $getPayment = $mysqli->prepare("SELECT * FROM payments WHERE id=?");
    $getPayment->bind_param("i",$id);
    $getPayment->execute();

    $paymentResult = $getPayment->get_result();


    if($paymentResult->num_rows > 0){


        $payment = $paymentResult->fetch_assoc();


        // Approve payment
        $approve = $mysqli->prepare(
            "UPDATE payments SET paymentStatus='Approved' WHERE id=?"
        );

        $approve->bind_param("i",$id);
        $approve->execute();



        // Insert into registration table

        $insert = $mysqli->prepare(
        "INSERT INTO registration
        (roomno,beds,feespm,stayfrom,duration,regNo,
        surName,firstName,lastName,gender,contactno,emailid,
        guardianName,guardianRelation,
        guardianContactno,bookingStatus)

        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
        );


        $status="Booked";


        $insert->bind_param(
            "iiisisssssssssss",

            $payment['roomNo'],
            $payment['beds'],
            $payment['feespm'],
            $payment['stayfrom'],
            $payment['duration'],
            $payment['regNo'],
            $payment['surName'],
            $payment['firstName'],
            $payment['lastName'],
            $payment['gender'],
            $payment['contactNo'],
            $payment['emailId'],
            $payment['guardianName'],
            $payment['guardianRelation'],
            $payment['guardianContact'],
            $status
        );


        $insert->execute();



        // Increase occupied seats

        $updateRoom = $mysqli->prepare(
            "UPDATE rooms 
             SET occupied_beds=occupied_beds+1 
             WHERE room_no=?"
        );


        $updateRoom->bind_param(
            "i",
            $payment['roomNo']
        );


        $updateRoom->execute();



        $_SESSION['success']="Payment Approved Successfully";


        header("Location: verify-payment.php");

        exit();

    }

}

?>



<!DOCTYPE html>

<html dir="ltr" lang="en">


<head>

<meta charset="utf-8">

<meta name="viewport" content="width=device-width, initial-scale=1">


<title>
Payment Verification
</title>


<link rel="icon" type="image/png" sizes="16x16" 
href="../assets/images/favicon.png">


<link href="../assets/libs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">


<link href="../assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">


<link href="../dist/css/style.min.css" rel="stylesheet">


</head>



<body>



<div class="preloader">

<div class="lds-ripple">

<div class="lds-pos"></div>

<div class="lds-pos"></div>

</div>

</div>





<div id="main-wrapper" 
data-theme="light"
data-layout="vertical"
data-navbarbg="skin6"
data-sidebartype="full"
data-sidebar-position="fixed"
data-header-position="fixed">





<!-- Top Navigation -->

<header class="topbar" data-navbarbg="skin6">

<?php include 'includes/navigation.php'; ?>

</header>






<!-- Sidebar -->

<aside class="left-sidebar" data-sidebarbg="skin6">

<div class="scroll-sidebar">

<?php include 'includes/sidebar.php'; ?>

</div>

</aside>







<div class="page-wrapper">



<div class="page-breadcrumb">

<div class="row">

<div class="col-7 align-self-center">


<h4 class="page-title text-truncate text-dark font-weight-medium mb-1">

Payment Verification

</h4>


</div>

</div>

</div>





<div class="container-fluid">



<?php

if(isset($_SESSION['success'])){

?>

<div class="alert alert-success">

<?php

echo $_SESSION['success'];

unset($_SESSION['success']);

?>

</div>


<?php

}

?>

<div class="row">

<div class="col-12">


<div class="card">


<div class="card-body">


<h5 class="card-title mb-4">

Student Payment Records

</h5>



<div class="table-responsive">


<table id="zero_config" 
class="table table-striped table-bordered no-wrap">


<thead class="thead-dark">


<tr>

<th>#</th>

<th>Registration No</th>

<th>Room No</th>

<th>Amount</th>

<th>Receipt</th>

<th>Status</th>

<th>Action</th>


</tr>


</thead>



<tbody>



<?php


$count=1;


$query=$mysqli->query(
"SELECT * FROM payments ORDER BY id DESC"
);



while($row=$query->fetch_assoc()){


?>



<tr>


<td>

<?php echo $count++; ?>

</td>



<td>

<?php echo $row['regNo']; ?>

</td>




<td>

<?php echo $row['roomNo']; ?>

</td>




<td>

₦<?php echo number_format($row['amount']); ?>

</td>




<td>


<a 
href="../student/receipts/<?php echo $row['receipt']; ?>"
target="_blank"
class="btn btn-primary btn-sm">


<i class="fas fa-file"></i>

View Receipt


</a>


</td>




<td>


<?php


if($row['paymentStatus']=="Pending"){


?>


<span class="badge badge-warning">

Pending

</span>


<?php


}else{


?>


<span class="badge badge-success">

Approved

</span>


<?php

}


?>


</td>




<td>



<?php


if($row['paymentStatus']=="Pending"){


?>


<a href="verify-payment.php?approve=<?php echo $row['id']; ?>"

class="btn btn-success btn-sm"

onclick="return confirm('Approve this payment?')">


Approve


</a>



<?php


}else{


?>


<span class="text-success font-weight-bold">

Completed

</span>


<?php


}


?>


</td>




</tr>



<?php


}


?>



</tbody>


</table>


</div>



</div>


</div>


</div>


</div>





</div>


<!-- Footer -->

<?php include '../includes/footer.php'; ?>




</div>


</div>





<script src="../assets/libs/jquery/dist/jquery.min.js"></script>

<script src="../assets/libs/popper.js/dist/umd/popper.min.js"></script>

<script src="../assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>


<script src="../dist/js/app-style-switcher.js"></script>

<script src="../dist/js/feather.min.js"></script>

<script src="../assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>

<script src="../dist/js/sidebarmenu.js"></script>


<script src="../dist/js/custom.min.js"></script>



<script src="../assets/extra-libs/datatables.net/js/jquery.dataTables.min.js"></script>

<script src="../assets/extra-libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>


<script src="../dist/js/pages/datatable/datatable-basic.init.js"></script>



</body>

</html>