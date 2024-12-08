<?php
session_start();
require_once "config.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define variables and initialize with empty values
$first_name = $last_name = $email = $specialty = "";
$first_name_err = $last_name_err = $email_err = $specialty_err = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //instructor id
    $instructor_id = mt_rand(10000, 999999); // Generate random 6-digit instructor ID
    // Validate first name
    $first_name = trim($_POST["first_name"]);
    if (empty($first_name)) {
        $first_name_err = "Please enter the instructor's first name.";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $first_name)) {
        $first_name_err = "First name can only contain letters and spaces.";
    }

    // Validate last name
    $last_name = trim($_POST["last_name"]);
    if (empty($last_name)) {
        $last_name_err = "Please enter the instructor's last name.";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $last_name)) {
        $last_name_err = "Last name can only contain letters and spaces.";
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

    // Check for errors before inserting into the database
    if (empty($first_name_err) && empty($last_name_err) && empty($email_err) && empty($specialty_err)) {
        // Prepare the SQL query
        $sql = "INSERT INTO Instructor (instructor_id, first_name, last_name, email, specialty) VALUES (?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "issss", $param_instructor_id, $param_first_name, $param_last_name, $param_email, $param_specialty);

            // Set parameters
            $param_instructor_id = $instructor_id;
            $param_first_name = $first_name;
            $param_last_name = $last_name;
            $param_email = $email;
            $param_specialty = $specialty;

            // Execute the query
            if (mysqli_stmt_execute($stmt)) {
                echo "<div class='alert alert-success'>Instructor added successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Error: Could not execute the query. " . mysqli_error($link) . "</div>";
            }

            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Instructor</title>
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
        <h2>Add New Instructor</h2>
        <p>Please fill out the form to add a new instructor.</p>
        <form action="newInstructor.php" method="post">
            <div class="form-group <?php echo (!empty($first_name_err)) ? 'has-error' : ''; ?>">
                <label>First Name</label>
                <input type="text" name="first_name" class="form-control" value="<?php echo $first_name; ?>" required>
                <span class="help-block"><?php echo $first_name_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($last_name_err)) ? 'has-error' : ''; ?>">
                <label>Last Name</label>
                <input type="text" name="last_name" class="form-control" value="<?php echo $last_name; ?>" required>
                <span class="help-block"><?php echo $last_name_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo $email; ?>" required>
                <span class="help-block"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($specialty_err)) ? 'has-error' : ''; ?>">
                <label>Specialty</label>
                <input type="text" name="specialty" class="form-control" value="<?php echo $specialty; ?>" required>
                <span class="help-block"><?php echo $specialty_err; ?></span>
            </div>
            <button type="submit" class="btn btn-primary">Add Instructor</button>
            <a href="index.php" class="btn btn-default">Cancel</a>
        </form>
    </div>
</body>
</html>
