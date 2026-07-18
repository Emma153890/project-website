<?php
session_start();
include('../includes/dbconn.php');

if(!isset($_GET['regno']) || !isset($_GET['room'])){
    die("Invalid Access");
}

$regno = $_GET['regno'];
$room = $_GET['room'];

$query = "SELECT * FROM payments WHERE regno=?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("s",$regno);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$name = $row['surName']." ".$row['firstName']." ".$row['lastName'];
$amount = $row['amount'];
?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Hostel Payment</title>

<link rel="stylesheet" href="../vendor/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="../dist/css/style.css">
<link rel="stylesheet" href="../dist/css/custom.css">

</head>

<body style="background:#f4f6f9;">

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-lg-8">

<div class="card shadow-lg border-0">

<div class="card-header bg-danger text-white text-center">

<h2 class="mb-0">
Hostel Payment Portal
</h2>

</div>

<div class="card-body p-4">

<div class="alert alert-info">

<strong>Notice:</strong>

Your hostel room will only be allocated after your payment has been verified and approved by the hostel administrator.

</div>

<div class="card mb-4">

<div class="card-header bg-light">

<h5 class="mb-0">
Student Information
</h5>

</div>

<div class="card-body">

<table class="table table-bordered">

<tr>
<th width="35%">Full Name</th>
<td><?php echo $name; ?></td>
</tr>

<tr>
<th>Registration Number</th>
<td><?php echo $regno; ?></td>
</tr>

<tr>
<th>Room Number</th>
<td><?php echo $room; ?></td>
</tr>

<tr>

<th>Hostel Fee</th>

<td>

<h4 class="text-success mb-0">

₦<?php echo number_format($amount); ?>

</h4>

</td>

</tr>

</table>

</div>

</div>

<div class="card mb-4">

<div class="card-header bg-light">

<h5 class="mb-0">

Payment Instructions

</h5>

</div>

<div class="card-body">

<p>

<strong>Bank Name:</strong>
First Bank

</p>

<p>

<strong>Account Name:</strong>

University Hostel Management

</p>

<p>

<strong>Account Number:</strong>

1234567890

</p>

<p class="text-danger mb-0">


</p>

</div>

</div>

<form method="POST" enctype="multipart/form-data">

<div class="mb-3">

<label class="form-label">

Transaction Reference

</label>

<input
type="text"
name="reference"
class="form-control"
placeholder="Enter Transaction Reference"
required>

</div>

<div class="mb-4">

<label class="form-label">

Upload Payment Receipt

</label>

<input
type="file"
name="receipt"
class="form-control"
required>

</div>

<div class="d-grid gap-2">

<button
type="submit"
name="submit"
class="btn btn-danger btn-lg">

Submit Payment

</button>

<a
href="dashboard.php"
class="btn btn-secondary">

Back to Dashboard

</a>

</div>

</form>

</div>

<div class="card-footer text-center">

© <?php echo date('Y'); ?>

Hostel Management System

</div>

</div>

</div>

</div>

</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>

</html>

<?php

if(isset($_POST['submit'])){

    $reference=$_POST['reference'];
    
    if(empty($_FILES['receipt']['name'])){
        die("Please select a receipt.");
    }

    $filename=$_FILES['receipt']['name'];
    
    $tempname=$_FILES['receipt']['tmp_name'];
    
    $target="receipts/".$filename;

    if(!is_writable("receipts")){
    die("Receipts folder is not writable.");
    }

    if(!move_uploaded_file($tempname,$target)){
    die("Receipt upload failed.");
    }
    
    $status="Pending";
    
    $sql="UPDATE payments 
SET transactionRef=?, receipt=?, paymentStatus=?
WHERE regNo=?";

$stmt=$mysqli->prepare($sql);

$stmt->bind_param(
"ssss",
$reference,
$filename,
$status,
$regno
);

$stmt->execute();
    
    echo "<script>
    alert('Payment submitted successfully. Waiting for Admin Approval.');
    
    </script>";
    header("dashboard");
    
    }

?>