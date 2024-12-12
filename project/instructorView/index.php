<?php
    session_start();
    require_once "../config.php";

    $_SESSION['view'] = 'instructor';

    $message = isset($_GET['message']) ? $_GET['message'] : "";

    //Displays a corresponding message when attempting to remove any classes.
    if ($message == "success") {
        echo "<div class='alert alert-success'>Class has been successfully removed.</div>";
    } elseif ($message == "error_removal") {
        echo "<div class='alert alert-danger'>Error removing class. Please try again.</div>";
    } elseif ($message == "not_assigned") {
        echo "<div class='alert alert-warning'>Error: Class not found or not assigned to you.</div>";
    } elseif ($message == "invalid_request") {
        echo "<div class='alert alert-danger'>Invalid request. Missing class or instructor ID.</div>";
    }

    $full_name = ""; // Initialize the full_name variable
    $unassignedClassTable = ""; // For unassigned classes

    if (isset($_SESSION['view']) && $_SESSION['view'] == 'instructor') {
        $instructor_id = 468306;  // Example instructor ID

        // Fetch the instructor's full name
        $sql = "SELECT first_name, last_name FROM Instructor WHERE instructor_id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $instructor_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);
                $full_name = $row['first_name'] . " " . $row['last_name'];
            } else {
                $full_name = "Instructor Not Found";
            }
            mysqli_stmt_close($stmt);
        } else {
            $full_name = "Error fetching instructor name.";
        }

        // Fetch the classes assigned to this instructor
        $sql = "SELECT class_id, class_name, days, time_slot, capacity
                FROM Class WHERE instructor_id = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $instructor_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            // Check if classes exist
            if (mysqli_num_rows($result) > 0) {
                // HTML table content by concatenation
                $classTable = "<h2>Classes Assigned: </h2>";
                $classTable .= "<table class=\"table table-bordered table-striped\">";
                $classTable .= "<thead><tr><th>Class ID</th><th>Class Name</th><th>Days</th><th>Class Times</th><th>Capacity</th><th>Action</th></tr></thead><tbody>";

                while ($row = mysqli_fetch_assoc($result)) {
                    $classTable .= "<tr><td>" . $row['class_id'] . "</td><td>" . $row['class_name'] . "</td><td>" . $row['days'] . "</td><td>" . $row['time_slot'] . "</td><td>" . $row['capacity'] . "</td><td>" .
                    "<a href='cancelClass.php?class_id=" . urlencode($row['class_id']) . "'class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to cancel this class?\");'>Delete</a>" . " " .
                    "<a href='updateClass.php?class_id=" . urlencode($row['class_id']) . "' class='btn btn-warning btn-sm'>Update</a>" . "</td></tr>";
                }
                $classTable .= "</tbody></table>";
            } else {
                $classTable = "<p>You are not assigned to any classes.</p>";
            }

            mysqli_free_result($result);
        } else {
            $classTable = "<p>Error: Unable to fetch class data." . mysqli_error($link) . "</p>";
        }

        // Fetch the classes the instructor is NOT assigned to
        $sql = "SELECT class_id, class_name, days, time_slot, capacity
                FROM Class WHERE instructor_id != ? OR instructor_id IS NULL";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $instructor_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            // Check if unassigned classes exist
            if (mysqli_num_rows($result) > 0) {
                // HTML table content by concatenation
                $unassignedClassTable = "<h2>Classes Not Assigned to You</h2>";
                $unassignedClassTable .= "<table class=\"table table-bordered table-striped\">";
                $unassignedClassTable .= "<thead><tr><th>Class ID</th><th>Class Name</th><th>Days</th><th>Class Times</th><th>Capacity</th><th>Action</th></tr></thead><tbody>";

                while ($row = mysqli_fetch_assoc($result)) {
                    $unassignedClassTable .= "<tr><td>" . $row['class_id'] . "</td><td>" . $row['class_name'] . "</td><td>" . $row['days'] . "</td><td>" . $row['time_slot'] . "</td><td>" . $row['capacity'] . "</td><td>" .
                    "<a href='cancelClass.php?class_id=" . urlencode($row['class_id']) . "'class='btn btn-warning btn-sm' onclick='return confirm(\"Are you sure you want to try deleting this unassigned class?\");'>Delete</a>" . "</td></tr>";
                }
                $unassignedClassTable .= "</tbody></table>";
            } else {
                $unassignedClassTable = "<p>All classes are assigned to you or other instructors.</p>";
            }

            mysqli_free_result($result);
        } else {
            $unassignedClassTable = "<p>Error: Unable to fetch unassigned class data." . mysqli_error($link) . "</p>";
        }
    } else {
        $classTable = "<p>Unauthorized access.</p>";
        $unassignedClassTable = "<p>Unauthorized access.</p>";
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Instructor View</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <!-- Display the instructor's full name -->
        <h1>Welcome, <?php echo htmlspecialchars($full_name); ?></h1><br>

        <h4>This is mainly used for testing purposes.</h4>
        <p><b>Instructions:</b> To create a new class, click on the 'Add New Class' button.
        This will redirect you to a page <br> where you need to add the necessary information for your class.
        For cancelling/deleting <br> any classes, click on the 'Delete' button next to the corresponding class you wish to delete.   
        <br> If you wish to switch back to Admin view, click the 'Back to Admin View' button.</p>
        <p style='color: red';><b>You can only delete classes that are assigned to you. The 'Classes Not Assigned to You Table' is used to test this trigger.</b></p>

        <div class="page-header clearfix">
            <a href="newClass.php" class="btn btn-success pull-right">Add New Class</a>
        </div>

        <!-- Dynamically render the table or message from PHP -->
        <?php echo $classTable; ?>

        <!-- Display unassigned classes -->
        <?php echo $unassignedClassTable; ?>

        <!-- Button to switch back to Admin view -->
        <a href="../index.php" class="btn btn-default">Back to Admin View</a>
    </div><br>
</body>
</html>
