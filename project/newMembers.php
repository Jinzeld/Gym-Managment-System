<?php
session_start();
require_once "config.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Gather post data for membership attributes
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $member_id = uniqid('', true);
    $membership_id = $_POST['membership_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $status = $_POST['status'];

    if (!empty($first_name) && !empty($last_name) && !empty($email) && !empty($phone) && !empty($membership_id)) {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        $phone = '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6);

        //Insert a new row of the following attributes when adding
        $sql = "INSERT INTO Member (membership_id, first_name, last_name, email, phone, city, state, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = $link->prepare($sql)) {
            $stmt->bind_param("isssssss", $membership_id, $first_name, $last_name, $email, $phone, $city, $state, $status);

            if ($stmt->execute()) {
                echo "<p style='color: green;'>New member added successfully!</p>";
            } else {
                echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            echo "<p style='color: red;'>Error: Unable to prepare the SQL statement.</p>";
        }
    } else {
        echo "<p style='color: red;'>Please fill in all required fields and select a membership type.</p>";
    }

    $link->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Member</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-4">Add New Member</h1>
        <form method="POST" action="newMembers.php">
            <div class="form-group">
                <label for="membership_id">Membership Type:</label>
                <select class="form-control" id="membership_id" name="membership_id" required>
                    <option value = "">Select a membership type</option>
                    <option value = "1">Monthly</option>
                    <option value = "2">Yearly</option>
                    <option value = "3">Trial</option>
                    <option value = "4">Quarterly</option>
                    <option value = "5">Premium Monthly</option>
                    <option value = "6">Premium Yearly</option>
                    <option value = "7">Student Monthly</option>
                    <option value = "8">Student Yearly</option>
                    <option value = "9">Couples Monthly</option>
                    <option value = "10">Couples Yearly</option>
                </select>
            </div>
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" class="form-control" id="first_name" name="first_name" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" class="form-control" id="last_name" name="last_name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" class="form-control" id="phone" name="phone" placeholder="e.g., (123) 456-7890" required>
            </div>
            <div class="form-group">
                <label for="city">City:</label>
                <input type="text" class="form-control" id="city" name="city" required>
            </div>
            <div class="form-group">
                <label for="state">State:</label>
                <input type="text" class="form-control" id="state" name="state" required>
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Member</button>
            <button type="button" class="btn btn-secondary" onclick="window.location.href='index.php';">Cancel</button>
        </form>
        <br><br>
    </div>
</body>
</html>
