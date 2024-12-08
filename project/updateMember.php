<?php
session_start();
require_once "config.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Initialize variables
$member_id = $membership_id = $first_name = $last_name = $email = $phone = $city = $state = $status = "";
$first_name_err = $last_name_err = $email_err = $phone_err = "";

// Check if the `member_id` is provided in the URL
if (isset($_GET["member_id"]) && !empty(trim($_GET["member_id"]))) {
    $member_id = trim($_GET["member_id"]);

    // Fetch member details
    $sql = "SELECT * FROM Member WHERE member_id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $param_member_id);
        $param_member_id = $member_id;

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);
                $membership_id = $row["membership_id"];
                $first_name = $row["first_name"];
                $last_name = $row["last_name"];
                $email = $row["email"];
                $phone = $row["phone"];
                $city = $row["city"];
                $state = $row["state"];
                $status = $row["status"];
            } else {
                echo "Error: No matching member found.";
                exit();
            }
        } else {
            echo "Error executing query.";
        }
        mysqli_stmt_close($stmt);
    }
} else {
    echo "Invalid request. Member ID is required.";
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and assign POST data
    $membership_id = $_POST["membership_id"];
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $email = trim($_POST["email"]);
    $phone = preg_replace('/[^0-9]/', '', $_POST["phone"]);
    $phone = '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6);
    $city = trim($_POST["city"]);
    $state = trim($_POST["state"]);
    $status = $_POST["status"];

    // Check for errors before updating
    if (empty($first_name)) {
        $first_name_err = "First name cannot be empty.";
    }
    if (empty($last_name)) {
        $last_name_err = "Last name cannot be empty.";
    }
    if (empty($email)) {
        $email_err = "Email cannot be empty.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid email format.";
    }

    if (empty($first_name_err) && empty($last_name_err) && empty($email_err)) {
        // Update the member details
        $sql = "UPDATE Member SET membership_id = ?, first_name = ?, last_name = ?, email = ?, phone = ?, city = ?, state = ?, status = ? WHERE member_id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "isssssssi", $membership_id, $first_name, $last_name, $email, $phone, $city, $state, $status, $param_member_id);
            $param_member_id = $member_id;

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

// Close the database connection
mysqli_close($link);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Member</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-4">Edit Member</h1>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?member_id=' . $member_id; ?>">
            <div class="form-group">
                <label for="membership_id">Membership Type:</label>
                <select class="form-control" id="membership_id" name="membership_id" required>
                    <option value="1" <?php echo ($membership_id == "1") ? "selected" : ""; ?>>Monthly</option>
                    <option value="2" <?php echo ($membership_id == "2") ? "selected" : ""; ?>>Yearly</option>
                    <!-- Add other options similarly -->
                </select>
            </div>
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>
            </div>
            <div class="form-group">
                <label for="city">City:</label>
                <input type="text" class="form-control" id="city" name="city" value="<?php echo htmlspecialchars($city); ?>" required>
            </div>
            <div class="form-group">
                <label for="state">State:</label>
                <input type="text" class="form-control" id="state" name="state" value="<?php echo htmlspecialchars($state); ?>" required>
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="active" <?php echo ($status == "active") ? "selected" : ""; ?>>Active</option>
                    <option value="inactive" <?php echo ($status == "inactive") ? "selected" : ""; ?>>Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>

