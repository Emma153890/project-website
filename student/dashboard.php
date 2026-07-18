<?php
    session_start();
    include('../includes/dbconn.php');
    include('../includes/check-login.php');
    check_login();

    $userId = $_SESSION['id'];

    $getStudent = $mysqli->prepare("SELECT regNo, surName, firstName FROM userregistration WHERE id=?");
    $getStudent->bind_param("i", $userId);
    $getStudent->execute();
    
    $studentResult = $getStudent->get_result();
    $student = $studentResult->fetch_assoc();
    
    $regno = $student['regNo'];
    $name = $student['surName']." ".$student['firstName'];

    $statusQuery = $mysqli->prepare("
    SELECT
    p.roomNo,
    p.paymentStatus,
    p.paymentDate,
    r.bookingStatus
    FROM payments p
    LEFT JOIN registration r ON p.regNo = r.regNo
    WHERE p.regNo=?
    ORDER BY p.id DESC
    LIMIT 1
    ");



    $statusQuery->bind_param("s",$regno);
    $statusQuery->execute();

    $result = $statusQuery->get_result();

    $status = $result->fetch_assoc();

    
?>

<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- By Netgoplus - Netgoplus.com -->
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/favicon.png">
    <title>Hostel Management System</title>
    <!-- Custom CSS -->
    <link href="../assets/extra-libs/c3/c3.min.css" rel="stylesheet">
    <link href="../assets/libs/chartist/dist/chartist.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../dist/css/style.min.css" rel="stylesheet">
    
</head>

<body>
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <header class="topbar" data-navbarbg="skin6">
            <?php include '../includes/student-navigation.php'?>
        </header>
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <aside class="left-sidebar" data-sidebarbg="skin6">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar" data-sidebarbg="skin6">
                <?php include '../includes/student-sidebar.php'?>
            </div>
            <!-- End Sidebar scroll-->
        </aside>
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-7 align-self-center">
                       <?php include '../includes/greetings.php'?>
                        <div class="d-flex align-items-center">
                            <!-- <nav aria-label="breadcrumb">
                                
                            </nav> -->
                        </div>
                    </div>
                    
                </div>
                <!-- By Netgoplus - Netgoplus.com -->
            </div>
            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">

                <div class="row">

                <div class="col-md-12">

                <div class="row mb-4">

                <div class="col-md-3">

                <div class="card border-left-primary shadow h-100">

                <div class="card-body">

                <h6 class="text-primary">

                Student

                </h6>

                <h4>

                <?php echo $name; ?>

                </h4>

                </div>

                </div>

                </div>

                <div class="col-md-3">

               

                </div>

                <div class="col-md-3">

                

                </div>

                <div class="col-md-3">

                

                </div>

                </div>

                <div class="card shadow">

                <div class="card-header bg-danger text-white">

                <h3 class="mb-0">

                Hostel Allocation Status

                </h3>

                </div>

                <div class="card-body">

                <?php

                if($status){

                ?>

                <div class="row">

                <div class="col-md-3">

                <div class="card border-success">

                <div class="card-body text-center">

                <h5>Room Number</h5>

                <h2 class="text-success">

                <?php echo $status['roomNo']; ?>

                </h2>

                </div>

                </div>

                </div>

                <div class="col-md-3">

                <div class="card border-primary">

                <div class="card-body text-center">

                <h5>Payment Status</h5>

                <h4>

                <?php

                if($status['paymentStatus']=="Approved"){

                echo "<span class='badge badge-success'>Approved</span>";

                }else{

                echo "<span class='badge badge-warning'>Awaiting verification</span>";

                }

                ?>

                </h4>

                </div>

                </div>

                </div>

                <div class="col-md-3">

                <div class="card border-info">

                <div class="card-body text-center">

                <h5>Booking Status</h5>

                <h4>

                <?php

                if(!empty($status['bookingStatus'])){

                echo "<span class='badge badge-success'>".$status['bookingStatus']."</span>";

                }else{

                echo "<span class='badge badge-warning'>Pending</span>";

                }

                ?>

                </h4>

                </div>

                </div>

                </div>

                <div class="col-md-3">

                <div class="card border-dark">

                <div class="card-body text-center">

                <h5>Payment Date</h5>

                <h6>

                <?php echo $status['paymentDate']; ?>

                </h6>

                </div>

                </div>

                </div>

                </div>

                <hr>

                <?php

                if($status['paymentStatus']=="Approved"){

                ?>

                <div class="alert alert-success">

                <h4>

                Congratulations!

                </h4>

                <p>

                Your payment has been verified successfully.

                Your hostel room has been allocated.

                Room Number:

                <strong>

                <?php echo $status['roomNo']; ?>

                </strong>

                </p>

                </div>

                <?php

                }else{

                ?>

                <div class="alert alert-warning">

                <h4>

                Payment Awaiting Verification

                </h4>

                <p>

                Your payment has been submitted successfully.

                Please wait while the hostel administrator verifies your payment.

                Your room will be allocated immediately after approval.

                </p>

                </div>

                <?php

                }

                ?>

                <?php

                }else{

                ?>

                <div class="alert alert-info text-center">

                <h3>

                No Hostel Booking Found

                </h3>

                <p>

                You have not booked any hostel yet.

                </p>

                <a href="book-hostel.php"

                class="btn btn-danger">

                Book Hostel Now

                </a>

                </div>

                <?php

                }

                ?>

                </div>

                </div>

                </div>

                </div>

                </div>
                <!-- *************************************************************** -->
                <!-- End First Cards -->
                <!-- *************************************************************** -->
                
               
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- footer -->
            <!-- ============================================================== -->
            <?php include '../includes/footer.php' ?>
            <!-- ============================================================== -->
            <!-- End footer -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../assets/libs/popper.js/dist/umd/popper.min.js"></script>
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- apps -->
    <!-- apps -->
    <!-- By Netgoplus - Netgoplus.com -->
    <script src="../dist/js/app-style-switcher.js"></script>
    <script src="../dist/js/feather.min.js"></script>
    <script src="../assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
    <script src="../dist/js/sidebarmenu.js"></script>
    <!--Custom JavaScript -->
    <script src="../dist/js/custom.min.js"></script>
    <!--This page JavaScript -->
    <script src="../assets/extra-libs/c3/d3.min.js"></script>
    <script src="../assets/extra-libs/c3/c3.min.js"></script>
    <script src="../assets/libs/chartist/dist/chartist.min.js"></script>
    <script src="../assets/libs/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js"></script>
    <script src="../dist/js/pages/dashboards/dashboard1.min.js"></script>
</body>

</html>