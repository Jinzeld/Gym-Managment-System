<?php
session_start();
require_once "config.php";

// Initialize variables
$instructor_id = $first_name = $last_name = $email = $specialty = "";
$first_name_err = $last_name_err = $email_err = $specialty_err = "";

// Check if the `instructor_id` is provided in the URL
if (isset($_GET["instructor_id"]) && !empty(trim($_GET["instructor_id"]))) {
    $instructor_id = trim($_GET["instructor_id"]);

    // Fetch the current instructor's data
    $sql = "SELECT * FROM Instructor WHERE instructor_id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $param_instructor_id);
        $param_instructor_id = $instructor_id;

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                $first_name = $row["first_name"];
                $last_name = $row["last_name"];
                $email = $row["email"];
                $specialty = $row["specialty"];
            } else {
                // Redirect to an error page or display a message
                echo "<p>Error: No matching instructor found.</p>";
                exit();
            }
        } else {
            echo "<p>Error executing query.</p>";
        }
        mysqli_stmt_close($stmt);
    }
} else {
    echo "<p>Invalid request. Instructor ID is required.</p>";
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate first name
    $first_name = trim($_POST["first_name"]);
    if (empty($first_name)) {
        $first_name_err = "Please enter the instructor's first name.";
    }

    // Validate last name
    $last_name = trim($_POST["last_name"]);
    if (empty($last_name)) {
        $last_name_err = "Please enter the instructor's last name.";
    }

    // Validate email
    $email = trim($_POST["email"]);
    if (empty($email)) {
        $email_err = "Please enter the instructor's email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email address.";
    }

    // Validate specialty
    $specialty = trim($_POST["specialty"]);
    if (empty($specialty)) {
        $specialty_err = "Please enter the instructor's specialty.";
    }

    // Check for errors before updating the record
    if (empty($first_name_err) && empty($last_name_err) && empty($email_err) && empty($specialty_err)) {
        // Prepare the update query
        $sql = "UPDATE Instructor SET first_name = ?, last_name = ?, email = ?, specialty = ? WHERE instructor_id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssssi", $first_name, $last_name, $email, $specialty, $param_instructor_id);
            $param_instructor_id = $instructor_id;

            if (mysqli_stmt_execute($stmt)) {
                // Redirect to the main page after successful update
                header("location: index.php");
                exit();
            } else {
                echo "<p>Error updating record.</p>";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Close the database connection
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Instructor</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style>
        .wrapper {
            width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Update Instructor</h2>
        <p>Edit the instructor's details below and click submit to save the changes.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?instructor_id=' . $instructor_id; ?>" method="post">
            <div class="form-group <?php echo (!empty($first_name_err)) ? 'has-error' : ''; ?>">
                <label>First Name</label>
                <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($first_name); ?>" required>
                <span class="help-block"><?php echo $first_name_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($last_name_err)) ? 'has-error' : ''; ?>">
                <label>Last Name</label>
                <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($last_name); ?>" required>
                <span class="help-block"><?php echo $last_name_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                <span class="help-block"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($specialty_err)) ? 'has-error' : ''; ?>">
                <label>Specialty</label>
                <input type="text" name="specialty" class="form-control" value="<?php echo htmlspecialchars($specialty); ?>" required>
                <span class="help-block"><?php echo $specialty_err; ?></span>
            </div>
            <input type="submit" class="btn btn-primary" value="Submit">
            <a href="index.php" class="btn btn-default">Cancel</a>
        </form>
    </div>
</body>
</html>
