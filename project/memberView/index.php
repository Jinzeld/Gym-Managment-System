<?php
session_start();
require_once "../config.php";

$_SESSION['view'] = 'member';

if (isset($_SESSION['view']) && $_SESSION['view'] == 'member') {
    $member_id = 91775096;  // Example member ID
    $member_name = "";
    $row = [];

    $classTable = "";
    $availableTable = "";
    $membershipTable = "";
    $instructorTable = "";  // Initialize for instructor table

    // Fetch the full name of the member
    $member_sql = "SELECT first_name, last_name FROM Member WHERE member_id = ?";
    if ($stmt = mysqli_prepare($link, $member_sql)) {
        mysqli_stmt_bind_param($stmt, "i", $member_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $member_name = $row['first_name'] . ' ' . $row['last_name'];
        }
        mysqli_free_result($result);
        mysqli_stmt_close($stmt);
    }

    // Fetch class information
    $class_sql = "SELECT Class.class_id, Class.class_name, Class.days, Class.time_slot, Class.capacity
            FROM Takes 
            JOIN Class ON Takes.class_id = Class.class_id
            WHERE Takes.member_id = ?";

    if ($stmt = mysqli_prepare($link, $class_sql)) {
        mysqli_stmt_bind_param($stmt, "i", $member_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Check if classes exist
        if (mysqli_num_rows($result) > 0) {
            // HTML table content by concatenation
            $classTable = "<h2>Enrolled Classes</h2>";
            $classTable .= "<table class=\"table table-bordered table-striped\">";
            $classTable .= "<thead><tr><th>Class ID</th><th>Class Name</th><th>Days</th><th>Class Times</th><th>Capacity</th><th>Action</th></tr></thead><tbody>";

            while ($row = mysqli_fetch_assoc($result)) {
                $classTable .= "<tr><td>{$row['class_id']}</td>
                                <td>{$row['class_name']}</td>
                                <td>{$row['days']}</td>
                                <td>{$row['time_slot']}</td>
                                <td>{$row['capacity']}</td>
                                <td><a href=\"dropClass.php?class_id={$row['class_id']}&member_id={$member_id}\" class=\"btn btn-danger btn-sm\">Drop Class</a></td></tr>";
            }

            $classTable .= "</tbody></table>"; // Concatenate the HTML brackets for tbody and table
        } else {
            $classTable = "<p>You are not enrolled in any classes.</p>";
        }

        mysqli_free_result($result);
        mysqli_stmt_close($stmt);
    } else {
        $classTable = "<p>Error: Unable to fetch class data." . mysqli_error($link) . "</p>";
    }

    // Fetch the list of all classes that the member is not enrolled in
    $available_sql = "SELECT class_id, class_name, days, time_slot, capacity
            FROM Class
            WHERE class_id NOT IN (SELECT class_id FROM Takes WHERE member_id = ?)";

    if ($stmt = mysqli_prepare($link, $available_sql)) {
        mysqli_stmt_bind_param($stmt, "i", $member_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Check if classes exist
        if (mysqli_num_rows($result) > 0) {
            // HTML table content by concatenation
            $availableTable = "<br><h2>Available Classes</h2>";
            $availableTable .= "<table class=\"table table-bordered table-striped\">";
            $availableTable .= "<thead><tr><th>Class ID</th><th>Class Name</th><th>Days</th><th>Class Times</th><th>Capacity</th><th>Action</th></tr></thead><tbody>";

            while ($row = mysqli_fetch_assoc($result)) {
                $availableTable .= "<tr><td>{$row['class_id']}</td>
                                    <td>{$row['class_name']}</td>
                                    <td>{$row['days']}</td>
                                    <td>{$row['time_slot']}</td>
                                    <td>{$row['capacity']}</td>
                                    <td><a href=\"signUp.php?class_id={$row['class_id']}&member_id={$member_id}\" class=\"btn btn-primary\">Sign Up</a></td></tr>";
            }

            $availableTable .= "</tbody></table>"; // Concatenate the HTML brackets for tbody and table
        } else {
            $availableTable = "<p>No available classes to sign up for.</p>";
        }

        mysqli_free_result($result);
        mysqli_stmt_close($stmt);
    } else {
        $availableTable = "<p>Error: Unable to fetch class data." . mysqli_error($link) . "</p>";
    }

    // Fetch membership details
    $membership_sql = "SELECT Membership.membership_id, Membership.type, Membership.price
                        FROM Member 
                        JOIN Membership ON Member.membership_id = Membership.membership_id
                        WHERE Member.member_id = ?";

    if ($stmt = mysqli_prepare($link, $membership_sql)) {
        mysqli_stmt_bind_param($stmt, "i", $member_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Check if membership exists
        if (mysqli_num_rows($result) > 0) {
            // HTML table content by concatenation
            $membershipTable = "<br><h2>Membership Plan</h2>";
            $membershipTable .= "<table class=\"table table-bordered table-striped\">";
            $membershipTable .= "<thead><tr><th>Membership ID</th><th>Type</th><th>Price</th><th>Action</th></tr></thead><tbody>";

            while ($row = mysqli_fetch_assoc($result)) {
                $membershipTable .= "<tr><td>{$row['membership_id']}</td>
                                     <td>{$row['type']}</td>
                                     <td>{$row['price']}</td>
                                     <td><a href='changeMembership.php?member_id={$member_id}&current_membership={$row['membership_id']}' class='btn btn-primary'>Change Membership</a></td></tr>";
            }

            $membershipTable .= "</tbody></table>"; // Concatenate the HTML brackets for tbody and table
        } else {
            $membershipTable = "<p>No membership found.</p><br><br>";
        }

        mysqli_free_result($result);
        mysqli_stmt_close($stmt);
    } else {
        $membershipTable = "<p>Error: Unable to fetch membership data." . mysqli_error($link) . "</p>";
    }

    // Fetch instructors
    $instructor_sql = "SELECT instructor_id, first_name, last_name, specialty, email FROM Instructor";
    if ($stmt = mysqli_prepare($link, $instructor_sql)) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Check if instructors exist
        if (mysqli_num_rows($result) > 0) {
            // HTML table content for instructors
            $instructorTable = "<br><h2>Instructors</h2>";
            $instructorTable .= "<table class=\"table table-bordered table-striped\">";
            $instructorTable .= "<thead><tr><th>Instructor ID</th><th>First Name</th><th>Last Name</th><th>Specialty</th><th>Email</th></tr></thead><tbody>";

            while ($row = mysqli_fetch_assoc($result)) {
                $instructorTable .= "<tr><td>{$row['instructor_id']}</td>
                                     <td>{$row['first_name']}</td>
                                     <td>{$row['last_name']}</td>
                                     <td>{$row['specialty']}</td>
                                     <td>{$row['email']}</td></tr>";
            }

            $instructorTable .= "</tbody></table>"; // Concatenate the HTML brackets for tbody and table
        } else {
            $instructorTable = "<p>No instructors available.</p>";
        }

        mysqli_free_result($result);
        mysqli_stmt_close($stmt);
    } else {
        $instructorTable = "<p>Error: Unable to fetch instructor data." . mysqli_error($link) . "</p>";
    }

} else {
    $classTable = "<p>Unauthorized access.</p>";
    $availableTable = "<p>Unauthorized access.</p>";
    $membershipTable = "<p>Unauthorized access.</p>";
    $instructorTable = "<p>Unauthorized access.</p>";
}

// Close the connection
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Member View</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Welcome to Member View</h1><br>

        <h4>This is mainly used for testing purposes. You are currently viewing member <?php echo $member_name;?> </h4><br>

        <!-- Display enrolled classes -->
        <?php echo $classTable; ?>

        <!-- Display available classes -->
        <?php echo $availableTable; ?>

        <!-- Display membership details -->
        <?php echo $membershipTable; ?>

        <!-- Display instructors -->
        <?php echo $instructorTable; ?>

        <!-- Button to switch back to Admin view -->
        <a href="../index.php" class="btn btn-default">Back to Admin View</a><br><br><br>
    </div>
</body>
</html>
