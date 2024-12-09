<?php
session_start();
require_once "config.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_POST['type'];
    $price = $_POST['price'];

    if (!empty($type) && !empty($price)) {
        // Fetch the current maximum membership_id
        $sql_max_id = "SELECT MAX(membership_id) AS max_id FROM Membership";
        $result = mysqli_query($link, $sql_max_id);
        $new_membership_id = 1; // Default to 1 if the table is empty

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            if (!empty($row['max_id'])) {
                $new_membership_id = $row['max_id'] + 1;
            }
        }

        // Insert the new membership
        $sql_insert = "INSERT INTO Membership (membership_id, type, price) VALUES (?, ?, ?)";

        if ($stmt = $link->prepare($sql_insert)) {
            $stmt->bind_param("isd", $new_membership_id, $type, $price);

            if ($stmt->execute()) {
                echo "<p style='color: green;'>New membership added successfully! Membership ID: $new_membership_id</p>";
                header("location: index.php");
                exit();
            } else {
                echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            echo "<p style='color: red;'>Error: Unable to prepare the SQL statement.</p>";
        }
    } else {
        echo "<p style='color: red;'>Please fill in all required fields.</p>";
    }

    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Membership</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-4">Add New Membership</h1>
        <form method="POST" action="newMembership.php">
            <p><b>Instructions: </b>To add a new membership, enter the type and and enter a price.</p>
            <div class="form-group">
                <label for="type">Membership Type:</label>
                <input type="text" class="form-control" id="type" name="type" required>
            </div>
            <div class="form-group">
                <label for="Price">Price of Membership:</label>
                <input type="text" class="form-control" id="Price" name="price" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Membership</button>
            <button type="button" class="btn btn-secondary" onclick="window.location.href='index.php';">Cancel</button>
        </form>
        <br><br>
    </div>
</body>
</html>

