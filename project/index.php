<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gym Management System</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    
    <style type="text/css">

        *{
            /* background-color: red; */
        }

        .wrapper {
            width: 80%;
            margin: 20px auto;
        }
        table tr td:last-child a {
            margin-right: 15px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="page-header clearfix">
            <h2>Gym Management System</h2>
            <!-- <h3>members</h3>
            <h3>classes</h3> -->
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2 class="pull-left">Gym Members</h2>
                        <a href="newMembers.php" class="btn btn-success pull-right">Add New Member</a>
                    </div>
                    
                    <?php
                        // Include config file
                        require_once "config.php";

                        // Fetch members
                        $sql = "SELECT member_id, membership_id, first_name, last_name, email, phone, city, state, status FROM Member";
                        if ($result = mysqli_query($link, $sql)) {
                            if (mysqli_num_rows($result) > 0) {
                                echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                echo "<tr>";
                                echo "<th>Member ID</th>";
                                echo "<th>Membership ID</th>";
                                echo "<th>First Name</th>";
                                echo "<th>Last Name</th>";
                                echo "<th>Email</th>";
                                echo "<th>Phone</th>";
                                echo "<th>Location</th>";
                                echo "<th>Status</th>";
                                echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while ($row = mysqli_fetch_array($result)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['member_id'] . "</td>";
                                    echo "<td>" . $row['membership_id'] . "</td>";
                                    echo "<td>" . $row['first_name'] . "</td>";
                                    echo "<td>" . $row['last_name'] . "</td>";
                                    echo "<td>" . $row['email'] . "</td>";
                                    echo "<td>" . $row['phone'] . "</td>";
                                    echo "<td>" . $row['city'] . ", " . $row['state'] . "</td>";
                                    echo "<td>" . $row['status'] . "</td>";
                                    echo "<td>";
                                    echo "<a href='viewClasses.php?member_id=" . $row['member_id'] . "' title='View Classes' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
                                    echo "<a href='updateMember.php?member_id=" . $row['member_id'] . "' title='Update Record' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                                    echo "<a href='deleteMember.php?member_id=" . $row['member_id'] . "' title='Delete Record' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";
                                echo "</table>";
                                mysqli_free_result($result);
                            } else {
                                echo "<p class='lead'><em>No members found in the database.</em></p>";
                            }
                        } else {
                            echo "ERROR: Could not execute $sql. " . mysqli_error($link);
                        }
                        
                        // Fetch Membership Types
                        echo "<br><h2>Membership Types</h2>";
                        $sql2 = "SELECT membership_id, price, type FROM Membership";
                        if ($result2 = mysqli_query($link, $sql2)) {
                            if (mysqli_num_rows($result2) > 0) {
                                echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                echo "<tr>";
                                echo "<th>Membership ID</th>";
                                echo "<th>Price</th>";
                                echo "<th>Type</th>";
                                echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while ($row = mysqli_fetch_array($result2)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['membership_id'] . "</td>";
                                    echo "<td>" . "$" . $row['price'] . "</td>";
                                    echo "<td>" . $row['type'] . "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";
                                echo "</table>";
                                mysqli_free_result($result2);
                            } else {
                                echo "<p class='lead'><em>No membership stats available.</em></p>";
                            }
                        } else {
                            echo "ERROR: Could not execute $sql2. " . mysqli_error($link);
                        }

                        //Fetch Instructors
                        echo "<br><h2>Instructors</h2>";
                        $sql3 = "SELECT instructor_id, first_name, last_name, specialty, email FROM Instructor";
                        if ($result3 = mysqli_query($link, $sql3)){
                            if(mysqli_num_rows($result3) > 0){
                                echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                echo "<tr>";
                                echo "<th>Instructor ID</th>";
                                echo "<th>First Name</th>";
                                echo "<th>Last Name</th>";
                                echo "<th>Specialty</th>";
                                echo "<th>Email</th>";
                                echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while ($row = mysqli_fetch_array($result3)){
                                    echo "<tr>";
                                    echo "<td>" . $row['instructor_id'] . "</td>";
                                    echo "<td>" . $row['first_name'] . "</td>";
                                    echo "<td>" . $row['last_name'] . "</td>";
                                    echo "<td>" . $row['specialty'] . "</td>";
                                    echo "<td>" . $row['email'] . "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";
                                echo "</table>";
                                mysqli_free_result($result3);
                            } else {
                                echo "<p class='lead'><em>No instructor data available.</em></p>";
                            }
                        } else {
                            echo "ERROR: Could not execute $sql3. " . mysqli_error($link);
                        }

                        // Fetch classes
                        echo "<br><h2>Classes</h2>";
                        $sql4 = "SELECT class_id, class_name, schedule, capacity FROM Class";
                        if ($result4 = mysqli_query($link, $sql4)){
                            if(mysqli_num_rows($result4) > 0){
                                echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                echo "<tr>";
                                echo "<th>Class ID</th>";
                                echo "<th>Name</th>";
                                echo "<th>Schedule</th>";
                                echo "<th>Capacity</th>";
                                echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                            
                                while ($row = mysqli_fetch_array($result4)){
                                    echo "<tr>";
                                    echo "<td>" . $row['class_id'] . "</td>";
                                    echo "<td>" . $row['class_name'] . "</td>";
                                    echo "<td>" . $row['schedule'] . "</td>";
                                    echo "<td>" . $row['capacity'] . "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";
                                echo "</table>";
                                mysqli_free_result($result4);
                            } else {
                                echo "<p class='lead'><em>No class data available.</em></p>";
                            }
                        } else {
                            echo "ERROR: Could not execute $sql4. " . mysqli_error($link);
                        }

                        // Close connection
                        mysqli_close($link);
                    ?>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
