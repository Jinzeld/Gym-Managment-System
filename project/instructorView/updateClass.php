<?php
    session_start();
    require_once "../config.php";

    // Get the class ID from the URL
    if (isset($_GET['class_id']) && !empty($_GET['class_id'])) {
        $class_id = $_GET['class_id'];

        // Fetch class data based on the class_id
        $sql = "SELECT class_name, days, time_slot, capacity FROM Class WHERE class_id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $class_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                $class_name = $row['class_name'];
                $days = $row['days'];
                $time_slot = $row['time_slot'];
                $capacity = $row['capacity'];
            } else {
                die("Class not found.");
            }
            mysqli_free_result($result);
            mysqli_stmt_close($stmt);
        } else {
            die("Error fetching class details: " . mysqli_error($link));
        }
    } else {
        die("Invalid class ID.");
    }

    // If the form is submitted, update the class data
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $class_name = $_POST['class_name'];
        $days = $_POST['days'];
        $time_slot = $_POST['time_slot'];
        $capacity = $_POST['capacity'];

        // Update class details in the database
        $sql = "UPDATE Class SET class_name = ?, days = ?, time_slot = ?, capacity = ? WHERE class_id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssii", $class_name, $days, $time_slot, $capacity, $class_id);
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to the instructor view with a success message
                header("Location: index.php?message=class_updated");
                exit();
            } else {
                echo "<p>Error: " . mysqli_stmt_error($stmt) . "</p>";
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "<p>Error: Unable to prepare the SQL statement.</p>";
        }
    }

    mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Class</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Update Class</h1>
        <form method="POST" action="updateClass.php?class_id=<?php echo $class_id; ?>">
            <div class="form-group">
                <label for="class_name">Class Name:</label>
                <input type="text" class="form-control" id="class_name" name="class_name" value="<?php echo $class_name; ?>" required>
            </div>

            <div class="form-group">
                <label for="days">Class Days:</label>
                <input type="text" class="form-control" id="days" name="days" value="<?php echo $days; ?>" required>
            </div>

            <div class="form-group">
                <label for="time_slot">Time Slot:</label>
                <select name="time_slot" class="form-control" required>
                    <option value="">Select a time slot</option>    
                    <option value="7:00 AM - 8:50 AM">7:00 AM - 8:50 AM</option>
                    <option value="8:00 AM - 9:50 AM">8:00 AM - 9:50 AM</option>
                    <option value="9:00 AM - 10:50 AM">9:00 AM - 10:50 AM</option>
                    <option value="11:00 AM - 12:50 PM">11:00 AM - 12:50 PM</option>
                    <option value="12:00 PM - 1:50 PM">12:00 PM - 1:50 PM</option>
                    <option value="2:00 PM - 3:50 PM">2:00 PM - 3:50 PM</option>
                    <option value="4:00 PM - 5:50 PM">4:00 PM - 5:50 PM</option>
                </select>
            </div>

            <div class="form-group">
                <label for="capacity">Capacity:</label>
                <input type="number" class="form-control" id="capacity" name="capacity" value="<?php echo $capacity; ?>" required min="10" max="30">
            </div>

            <button type="submit" class="btn btn-primary">Update Class</button>
            <a href="index.php" class="btn btn-default">Cancel</a>
        </form>
    </div>
</body>
</html>
