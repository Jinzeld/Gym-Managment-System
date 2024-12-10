<?php
    session_start();
    require_once "config.php";

    $member_id = null;
    $member_status = null;

    if (isset($_GET["member_id"]) && !empty(trim($_GET["member_id"]))) {
        $member_id = $_GET["member_id"];

        // Check member status
        $sql_status = "SELECT status FROM Member WHERE member_id = ?";
        if ($stmt = mysqli_prepare($link, $sql_status)) {
            mysqli_stmt_bind_param($stmt, "i", $member_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                $member_status = $row["status"];
            } else {
                echo "Invalid member ID.";
                exit();
            }
            mysqli_stmt_close($stmt);
        }

        // If the member is inactive, stop further processing
        if ($member_status !== "active") {
            echo "<div class='container'><link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css\">
                  <h1>Cannot Add Classes</h1><p>Member is inactive. Classes cannot be added for inactive members.</p>";
            echo "<a href='index.php' class='btn btn-primary'>Back to Member List</a></div>";
            exit();
        }

        // Fetch available classes
        $sql_classes = "SELECT class_id, class_name, days, time_slot FROM Class";
        $result_classes = mysqli_query($link, $sql_classes);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Get the selected class ID
            $class_id = $_POST["class_id"];

            // Insert the member-class relationship into the Takes table
            $sql_insert = "INSERT INTO Takes (member_id, class_id) VALUES (?, ?)";
            if ($stmt = mysqli_prepare($link, $sql_insert)) {
                mysqli_stmt_bind_param($stmt, "ii", $member_id, $class_id);

                if (mysqli_stmt_execute($stmt)) {
                    echo "<p style='color: green;'>Class added successfully!</p>";
                    header("location: index.php");
                    exit();
                } else {
                    echo "<p style='color: red;'>Error adding class: " . mysqli_error($link) . "</p>";
                }
                mysqli_stmt_close($stmt);
            }
        }
    } else {
        echo "Invalid member ID.";
        exit();
    }

    // Close connection
    mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Add Class to Member</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container">
            <h1 class="mt-4">Add Class for Member ID: <?php echo htmlspecialchars($member_id); ?></h1>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="class_id">Available Classes:</label>
                    <select class="form-control" id="class_id" name="class_id" required>
                        <option value="">Select a class</option>
                        <?php
                        if ($result_classes && mysqli_num_rows($result_classes) > 0) {
                            while ($class = mysqli_fetch_assoc($result_classes)) {
                                echo "<option value='" . $class["class_id"] . "'>" 
                                    . $class["class_name"] . " | " 
                                    . $class["days"] . " | " 
                                    . $class["time_slot"] . "</option>";
                            }
                        } else {
                            echo "<option value=''>No classes available</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Add Class</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </body>
</html>
