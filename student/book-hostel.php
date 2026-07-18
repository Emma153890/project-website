<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    session_start();
    include('../includes/dbconn.php');
    include('../includes/check-login.php');
    check_login();

    $bookingSuccess = false;
    $nextLink = "";

    if(isset($_POST['submit'])){
        $roomno=$_POST['room'];
        $beds=$_POST['beds'];
        $feespm=$_POST['fpm'];
        $stayfrom=$_POST['stayf'];
        $duration=$_POST['duration'];
        $regno=$_POST['regno'];
        $sname=$_POST['sname'];
        $fname=$_POST['fname'];
        $lname=$_POST['lname'];
        $gender=$_POST['gender'];
        $contactno=$_POST['contact'];
        $emailid=$_POST['email'];
        $gurname=$_POST['gname'];
        $gurrelation=$_POST['grelation'];
        $gurcntno=$_POST['gcontact'];
        
        

        //Get room information
        $roomQuery = "SELECT beds, occupied_beds FROM rooms WHERE room_no=?";
        $roomStmt = $mysqli->prepare($roomQuery);
        $roomStmt->bind_param('i', $roomno);
        $roomStmt->execute();
        $roomResult = $roomStmt->get_result();
        
        if($roomResult->num_rows > 0){
            $roomData = $roomResult->fetch_assoc();

            $roomCapacity = $roomData['beds'];
            $occupiedBeds = $roomData['occupied_beds'];

            //Check if room is already full
            if($occupiedBeds >= $roomCapacity){
                echo "<script>alert('Selected room is already full!');</script>";
                exit();
            }
        }
        

        $amount = $feespm * $duration;

        $paymentQuery = "INSERT INTO payments
        (
        regNo,
        roomNo,
        beds,
        feespm,
        stayfrom,
        duration,
        surName,
        firstName,
        lastName,
        gender,
        contactNo,
        emailId,
        guardianName,
        guardianRelation,
        guardianContact,
        amount,
        paymentStatus
        )
        VALUES
        (
        ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,'Pending'
        )";
        $paymentStmt = $mysqli->prepare($paymentQuery);
        $paymentStmt->bind_param(
            "sidssisssssssssd",
            
            $regno,
            $roomno,
            $beds,
            $feespm,
            $stayfrom,
            $duration,
            $sname,
            $fname,
            $lname,
            $gender,
            $contactno,
            $emailid,
            $gurname,
            $gurrelation,
            $gurcntno,
            $amount
            );
        $paymentStmt->execute();
        
        $bookingSuccess = true;
        $nextLink = "payment.php?regno=".$regno."&room=".$roomno;
        
       
    }
?>

<!DOCTYPE html>
<html dir="ltr" lang="en">
<!-- By CodeAstro - codeastro.com -->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
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

    <script>
    function getSeater(val) {
        $.ajax({
        type: "POST",
        url: "get-seater.php",
        data:'roomid='+val,
        success: function(data){
        //alert(data);
        $('#beds').val(data);
        }
        });

        $.ajax({
        type: "POST",
        url: "get-seater.php",
        data:'rid='+val,
        success: function(data){
        //alert(data);
        $('#fpm').val(data);
        }
        });
    }

    function checkAvailability(val) {
        $.ajax({
            type: "POST",
            url: "check-availability.php",
            data: 'roomno=' + val,
            success: function(data){
                $("#room-availability-status").html(data);
            }
        });
    }

    function suggestBestRoom(){

        $.ajax({

            url: "best-room.php",
            type: "GET",

            success:function(response){

                var data =JSON.parse(response);

                if(data.room_no){

                    $('#room').val(data.room_no);

                    getSeater(data.room_no);
                    checkAvailability(data.room_no);

                    $('#best-room-result').html(
                        "Recommend Room: " + data.room_no + " (" + data.free_seats + " beds(s) remaining)"
                    );
                }else{
                    $('#best-room-result').html(
                        "No rooms available."
                    );
                }
            }
        });
    }
    </script>
    <!-- By CodeAstro - codeastro.com -->
</head>

<body>
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
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
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">

            <?php
                    if($bookingSuccess){
                    ?>

                    <div class="alert alert-success text-center">

                    <h4>Hostel Booking Successful</h4>

                    <p>
                    Your hostel booking has been submitted successfully.
                    </p>

                    <p>
                    The room will only be allocated after your payment has been verified by the administrator.
                    </p>

                    <a href="<?php echo $nextLink; ?>" class="btn btn-danger btn-lg">

                    Next → Proceed to Payment

                    </a>

                    </div>

                    <?php
                    }
                    ?>
                <?php if(!$bookingSuccess){ ?>
                
                <form method="POST">
                
                <?php
                    $uid=$_SESSION['login'];
                    $stmt=$mysqli->prepare("SELECT emailid FROM registration WHERE emailid=? ");
                    $stmt->bind_param('s',$uid);
                    $stmt->execute();
                    $stmt -> bind_result($email);
                    $rs=$stmt->fetch();
                    $stmt->close();

                    if($rs){ ?>
                    <div class="alert alert-primary alert-dismissible bg-danger text-white border-0 fade show"
                        role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                        </button>
                                <strong>Info: </strong> You have already booked a hostel!
                    </div>
                    <?php }
                    else{
						echo "";
					}			
				?>	


                <div class="col-7 align-self-center">
                        <h4 class="page-title text-truncate text-dark font-weight-medium mb-1">Hostel Bookings</h4>
                    </div>

                
                <div class="row">


                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Room Number</h4>
                                    <div class="form-group mb-4">
                                        <select class="custom-select mr-sm-2" name="room" id="room" onChange="getSeater(this.value); checkAvailability(this.value)" required id="inlineFormCustomSelect">
                                            <option selected>Select...</option>
                                            <?php $query ="SELECT * FROM rooms";
                                            $stmt2 = $mysqli->prepare($query);
                                            $stmt2->execute();
                                            $res=$stmt2->get_result();
                                            while($row=$res->fetch_object())
                                            {
                                            ?>
                                            <option value="<?php echo $row->room_no;?>"> <?php echo $row->room_no;?></option>
                                            <?php } ?>
                                        </select>
                                        <span id="room-availability-status" style="font-size:12px;"></span>

                                        <br><br>
                                        <button type="button" class="btn btn-info btn-sm" onclick="suggestBestRoom()">Suggest Best Room</button>
                                        <div id="best-room-result" style="margin-top:10px;font-size:13px;font-weight:bold;">
                            
                                        </div>
                                    </div>
                              
                            </div>
                        </div>
                    </div>
<!-- By CodeAstro - codeastro.com -->
                
 
                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Start Date</h4>
                                    <div class="form-group">
                                        <input type="date" name="stayf" id="stayf" class="form-control" required>
                                    </div>
                                
                            </div>
                        </div>
                    </div>



                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">No. of Bed(s)</h4>
                                    <div class="form-group">
                                        <input type="text" id="beds" name="beds" placeholder="" required class="form-control">
                                    </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Total Duration</h4>
                                    <div class="form-group mb-4">
                                        <select class="custom-select mr-sm-2" id="duration" name="duration">
                                            <option selected>Choose...</option>
                                            <option value="1">One Month</option>
                                            <option value="2">Two Month</option>
                                            <option value="3">Three Month</option>
                                            <option value="4">Four Month</option>
                                            <option value="5">Five Month</option>
                                            <option value="6">Six Month</option>
                                            <option value="7">Seven Month</option>
                                            <option value="8">Eight Month</option>
                                            <option value="9">Nine Month</option>
                                            <option value="10">Ten Month</option>
                                            <option value="11">Eleven Month</option>
                                            <option value="12">Twelve Month</option>
                                        </select>
                                    </div>
                              
                            </div>
                        </div>
                    </div>
                    


                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Total Fees Per Month</h4>
                                    <div class="form-group">
                                        <input type="text" name="fpm" id="fpm" placeholder="Your total fees" class="form-control">
                                    </div>
                            </div>
                        </div>
                    </div>
                  
                
                </div>

                <h4 class="card-title mt-5">Student's Personal Information</h4>

                <div class="row">

                <?php	
                $aid=$_SESSION['id'];
                    $ret="select * from userregistration where id=?";
                        $stmt= $mysqli->prepare($ret) ;
                    $stmt->bind_param('i',$aid);
                    $stmt->execute();
                    $res=$stmt->get_result();

                    while($row=$res->fetch_object())
                    {
                        ?>
                
                    <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Registration Number</h4>
                                        <div class="form-group">
                                            <input type="text" name="regno" id="regno" value="<?php echo $row->regNo;?>" class="form-control" readonly>
                                        </div>
                                </div>
                            </div>
                        </div>


                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Surname</h4>
                                    <div class="form-group">
                                        <input type="text" name="sname" id="sname" value="<?php echo $row->surName;?>" class="form-control" readonly>
                                    </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">First Name</h4>
                                    <div class="form-group">
                                        <input type="text" name="fname" id="fname" value="<?php echo $row->firstName;?>" class="form-control" readonly>
                                    </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Last Name</h4>
                                    <div class="form-group">
                                        <input type="text" name="lname" id="lname" value="<?php echo $row->lastName;?>" class="form-control" readonly>
                                    </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Email</h4>
                                    <div class="form-group">
                                        <input type="email" name="email" id="email" value="<?php echo $row->email;?>" class="form-control" readonly>
                                    </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Gender</h4>
                                    <div class="form-group">
                                        <input type="text" name="gender" id="gender" value="<?php echo $row->gender;?>" class="form-control" readonly>
                                    </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Contact Number</h4>
                                    <div class="form-group">
                                        <input type="number" name="contact" id="contact" value="<?php echo $row->contactNo;?>" class="form-control" readonly>
                                    </div>
                            </div>
                        </div>
                    </div>

                    <?php }?>

                    
                              
                </div>

                <h4 class="card-title mt-5">Guardian's Information</h4>

                    <div class="row">
                    
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Guardian Name</h4>
                                        <div class="form-group">
                                            <input type="text" name="gname" id="gname" class="form-control" placeholder="Enter Guardian's Name" required>
                                        </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Relation</h4>
                                        <div class="form-group">
                                            <input type="text" name="grelation" id="grelation" required class="form-control" placeholder="Student's Relation with Guardian">
                                        </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Contact Number</h4>
                                        <div class="form-group">
                                            <input type="text" name="gcontact" id="gcontact" required class="form-control" placeholder="Enter Guardian's Contact No.">
                                        </div>
                                </div>
                            </div>
                        </div>
                    
                    </div>


                    <div class="form-actions">
                        <div class="text-center">
                            <button type="submit" name="submit" class="btn btn-success">Submit</button>
                            <button type="reset" class="btn btn-dark">Reset</button>
                        </div>
                    </div>

                
                </form>

                <?php } ?>

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

<!-- Custom Ft. Script Lines -->
<script type="text/javascript">
	$(document).ready(function(){
        $('input[type="checkbox"]').click(function(){
            if($(this).prop("checked") == true){
                $('#paddress').val( $('#address').val() );
                $('#pcity').val( $('#city').val() );
                $('#ppincode').val( $('#pincode').val() );
            } 
            
        });
    });
    </script>
    
    <script>
        function checkAvailability() {
        $("#loaderIcon").show();
        jQuery.ajax({
        url: "check-availability.php",
        data:'roomno='+$("#room").val(),
        type: "POST",
        success:function(data){
            $("#room-availability-status").html(data);
            $("#loaderIcon").hide();
        },
            error:function (){}
            });
        }
    </script>


    <script type="text/javascript">

    $(document).ready(function() {
        $('#duration').keyup(function(){
            var fetch_dbid = $(this).val();
            $.ajax({
            type:'POST',
            url :"ins-amt.php?action=userid",
            data :{userinfo:fetch_dbid},
            success:function(data){
            $('.result').val(data);
            }
            });
            

    })});
    </script>

</html>