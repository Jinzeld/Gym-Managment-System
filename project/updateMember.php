<?php
session_start();
require_once "config.php";

// Check if the member ID is provided in the URL
if (isset($_GET["member_id"]) && !empty(trim($_GET["member_id"]))) {
    // Get the member ID from the URL
    $member_id = $_GET["member_id"];

    // Fetch the member's current data
    $sql = "SELECT * FROM members WHERE member_id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $param_member_id);
        $param_member_id = $member_id;

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 1) {
                // Fetch member data
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
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

// Handle the update when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $membership_id = $_POST["membership_id"];
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $city = $_POST["city"];
    $state = $_POST["state"];
    $status = $_POST["status"];

    // Format the phone number
    $phone = preg_replace('/[^0-9]/', '', $phone);
    $phone = '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6);

    // Prepare the update query
    $sql = "UPDATE members SET membership_id = ?, first_name = ?, last_name = ?, email = ?, phone = ?, city = ?, state = ?, status = ? WHERE member_id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "isssssssi", $membership_id, $first_name, $last_name, $email, $phone, $city, $state, $status, $param_member_id);
        $param_member_id = $_GET["member_id"];

        if (mysqli_stmt_execute($stmt)) {
            // Redirect to the landing page after successful update
            header("location: index.php");
            exit();
        } else {
            echo "Error updating record.";
        }

        mysqli_stmt_close($stmt);
    }
}

// Close the database connection
mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Member</title>
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
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h2>Update Member</h2>
                    </div>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?member_id=' . $member_id; ?>" method="post">
                        <div class="form-group">
                            <label>Membership ID</label>
                            <input type="number" name="membership_id" class="form-control" value="<?php echo htmlspecialchars($membership_id); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($first_name); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($last_name); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($phone); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($city); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>State</label>
                            <input type="text" name="state" class="form-control" value="<?php echo htmlspecialchars($state); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="active" <?php echo ($status == 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo ($status == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
