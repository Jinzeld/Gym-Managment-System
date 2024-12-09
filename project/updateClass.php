<?php
    session_start();
    require_once "config.php";

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Initialize variables
    $class_id = $instructor_id = $class_name = $days = $time_slot = $capacity = "";
    $class_name_err = $days_err = $time_slot_err = $capacity_err = "";

    // Check if `class_id` is provided in the URL
    if (isset($_GET["class_id"]) && ctype_digit($_GET["class_id"])) {
        $class_id = $_GET["class_id"];

        // Fetch class details
        $sql = "SELECT * FROM Class WHERE class_id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $class_id);

            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) == 1) {
                    $row = mysqli_fetch_assoc($result);
                    $instructor_id = $row["instructor_id"];
                    $class_name = $row["class_name"];
                    $days = $row["days"];
                    $time_slot = $row["time_slot"];
                    $capacity = $row["capacity"];
                } else {
                    echo "Error: No matching class found.";
                    exit();
                }
            } else {
                echo "Error executing query.";
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        echo "Invalid request. Class ID is required.";
        exit();
    }

    // Handle form submission for updates
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validate class name
        $class_name = trim($_POST["class_name"]);
        if (empty($class_name)) {
            $class_name_err = "Please enter the class name.";
        }

        // Validate days
        if (isset($_POST['days']) && is_array($_POST['days'])) {
            if (count($_POST['days']) > 3) {
                $days_err = "You can only select up to 3 days.";
            } else {
                $days = implode(', ', $_POST['days']);
            }
        } else {
            $days_err = "Please select at least one day.";
        }

        // Validate time slot
        $time_slot = trim($_POST["time_slot"]);
        if (empty($time_slot)) {
            $time_slot_err = "Please select a time slot.";
        }

        // Validate capacity
        $capacity = trim($_POST["capacity"]);
        if (empty($capacity) || $capacity < 10 || $capacity > 30) {
            $capacity_err = "Capacity must be between 10 and 30.";
        }

        // Check for errors before updating the record
        if (empty($class_name_err) && empty($days_err) && empty($time_slot_err) && empty($capacity_err)) {
            $sql = "UPDATE Class SET class_name = ?, days = ?, time_slot = ?, capacity = ? WHERE class_id = ?";
            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "sssii", $class_name, $days, $time_slot, $capacity, $class_id);

                if (mysqli_stmt_execute($stmt)) {
                    // Redirect to the main page after a successful update
                    header("location: index.php");
                    exit();
                } else {
                    echo "Error updating record.";
                }
                mysqli_stmt_close($stmt);
            }
        }
    }

    mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Update Class</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.css">
        <style>
            .wrapper {
                width: 500px;
                margin: 0 auto;
            }
        </style>
    </head>
    <body>
        <div class="wrapper">
            <h2>Update Class</h2>
            <p>Edit the class details below and click submit to save the changes.</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?class_id=' . $class_id; ?>" method="post">
                <div class="form-group <?php echo (!empty($class_name_err)) ? 'has-error' : ''; ?>">
                    <label>Class Name</label>
                    <input type="text" name="class_name" class="form-control" value="<?php echo htmlspecialchars($class_name); ?>" required>
                    <span class="help-block"><?php echo $class_name_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($days_err)) ? 'has-error' : ''; ?>">
                    <label>Days</label><br>
                    <?php
                    $all_days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
                    $selected_days = explode(', ', $days);
                    foreach ($all_days as $day) {
                        $checked = in_array($day, $selected_days) ? "checked" : "";
                        echo "<label><input type='checkbox' name='days[]' value='$day' $checked> $day</label><br>";
                    }
                    ?>
                    <span class="help-block"><?php echo $days_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($time_slot_err)) ? 'has-error' : ''; ?>">
                    <label>Time Slot</label>
                    <select name="time_slot" class="form-control" required>
                        <option value="" disabled>Select a time slot</option>
                        <option value="7:00 AM - 8:50 AM" <?php echo ($time_slot == "7:00 AM - 8:50 AM") ? "selected" : ""; ?>>7:00 AM - 8:50 AM</option>
                        <option value="8:00 AM - 9:50 AM" <?php echo ($time_slot == "8:00 AM - 9:50 AM") ? "selected" : ""; ?>>8:00 AM - 9:50 AM</option>
                        <option value="9:00 AM - 10:50 AM" <?php echo ($time_slot == "9:00 AM - 10:50 AM") ? "selected" : ""; ?>>9:00 AM - 10:50 AM</option>
                    </select>
                    <span class="help-block"><?php echo $time_slot_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($capacity_err)) ? 'has-error' : ''; ?>">
                    <label>Capacity</label>
                    <input type="number" name="capacity" class="form-control" min="10" max="30" value="<?php echo htmlspecialchars($capacity); ?>" required>
                    <span class="help-block"><?php echo $capacity_err; ?></span>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="index.php" class="btn btn-default">Cancel</a>
            </form>
        </div>
    </body>
</html>
