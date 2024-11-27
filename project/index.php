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
            background-color: grey;
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
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2 class="pull-left">Gym Members</h2>
                        <a href="addMember.php" class="btn btn-success pull-right">Add New Member</a>
                    </div>
                    
                    <?php
                        // Include config file
                        require_once "config.php";

                        // Fetch members
                        $sql = "SELECT member_id, name_first, name_last, status FROM Member";
                        if ($result = mysqli_query($link, $sql)) {
                            if (mysqli_num_rows($result) > 0) {
                                echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                echo "<tr>";
                                echo "<th>Member ID</th>";
                                echo "<th>First Name</th>";
                                echo "<th>Last Name</th>";
                                echo "<th>Status</th>";
                                echo "<th>Action</th>";
                                echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while ($row = mysqli_fetch_array($result)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['member_id'] . "</td>";
                                    echo "<td>" . $row['name_first'] . "</td>";
                                    echo "<td>" . $row['name_last'] . "</td>";
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
                        
                        // Membership Stats
                        echo "<br><h2>Membership Stats</h2>";
                        $sql2 = "SELECT membership_id, COUNT(member_id) AS member_count FROM Member GROUP BY membership_id";
                        if ($result2 = mysqli_query($link, $sql2)) {
                            if (mysqli_num_rows($result2) > 0) {
                                echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                echo "<tr>";
                                echo "<th>Membership ID</th>";
                                echo "<th>Number of Members</th>";
                                echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while ($row = mysqli_fetch_array($result2)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['membership_id'] . "</td>";
                                    echo "<td>" . $row['member_count'] . "</td>";
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

                        // Close connection
                        mysqli_close($link);
                    ?>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
