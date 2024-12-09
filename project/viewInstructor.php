<?php
    session_start();
    require_once "config.php";

    // Check if `instructor_id` is passed in the URL
    if (isset($_GET["instructor_id"]) && !empty(trim($_GET["instructor_id"]))) {
        $instructor_id = intval($_GET["instructor_id"]);
        $instructor_name = "";

        // Fetch the instructor's name
        $sql = "SELECT first_name, last_name FROM Instructor WHERE instructor_id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $instructor_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($result)) {
                $instructor_name = $row["first_name"] . " " . $row["last_name"];
            } else {
                die("Error: No instructor found with the given ID.");
            }
            mysqli_stmt_close($stmt);
        } else {
            die("Error fetching instructor details: " . mysqli_error($link));
        }

        // Fetch classes assigned to the instructor
        $sql_classes = "SELECT class_id, class_name, days, time_slot, capacity 
                        FROM Class WHERE instructor_id = ?";
        $classes = [];
        if ($stmt = mysqli_prepare($link, $sql_classes)) {
            mysqli_stmt_bind_param($stmt, "i", $instructor_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                $classes[] = $row;
            }
            mysqli_stmt_close($stmt);
        } else {
            die("Error fetching class details: " . mysqli_error($link));
        }
    } else {
        die("Invalid instructor ID.");
    }

    // Close connection
    mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Classes</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Classes for <?php echo htmlspecialchars($instructor_name); ?></h1>
        <p><b>Instructor ID:</b> <?php echo htmlspecialchars($instructor_id); ?></p>

        <?php if (!empty($classes)): ?>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Class ID</th>
                        <th>Class Name</th>
                        <th>Days</th>
                        <th>Time Slot</th>
                        <th>Capacity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($classes as $class): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($class["class_id"]); ?></td>
                            <td><?php echo htmlspecialchars($class["class_name"]); ?></td>
                            <td><?php echo htmlspecialchars($class["days"]); ?></td>
                            <td><?php echo htmlspecialchars($class["time_slot"]); ?></td>
                            <td><?php echo htmlspecialchars($class["capacity"]); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="lead"><em>No classes assigned to this instructor.</em></p>
        <?php endif; ?>

        <a href="index.php" class="btn btn-default">Back to Admin View</a>
    </div>
</body>
</html>

