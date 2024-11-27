<?php
require_once "config.php";

//CREATE: Add a new instructor
if (isset($_POST['add_instructor'])) {
    $sql = "INSERT INTO Instructor (instructor_id, first_name, last_name, specialty, email) VALUES (?, ?, ?, ?, ?)";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "issss", $_POST['instructor_id'], $_POST['first_name'], $_POST['last_name'], $_POST['specialty'], $_POST['email']);
        mysqli_stmt_execute($stmt);
        echo "Instructor added successfully.";
        mysqli_stmt_close($stmt);
    } else {
        echo "ERROR: Could not execute query: $sql. " . mysqli_error($link);
    }
}

// READ: Fetch all memberships
$sql = "SELECT * FROM Instructor";
if ($result = mysqli_query($link, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "ID: {$row['instructor_id']}, First Name: {$row['first_name']}, Last Name: {$row['last_name']}, Specialty: {$row['specialty']}, {$row['email']}<br>";
    }
    mysqli_free_result($result);
}

//UPDATE: Updates the instructor information
if (isset($_POST['update_instructor'])) {
    $sql = "UPDATE Instructor SET first_name=?, last_name=?, specialty=?, email=? WHERE instructor_id=?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "sssii", $_POST['first_name'], $_POST['last_name'], $_POST['specialty'], $_POST['email'], $_POST['instructor_id']);
        if (mysqli_stmt_execute($stmt)) {
            echo "Instructor updated successfully.";
        } else {
            echo "ERROR: Could not execute query: $sql. " . mysqli_error($link);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "ERROR: Could not prepare query: $sql. " . mysqli_error($link);
    }
}

// DELETE: Remove a instructor
if (isset($_POST['delete_instructor'])) {
    $sql = "DELETE FROM Instructor WHERE instructor_id=?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $_POST['instructor_id']);
        mysqli_stmt_execute($stmt);
        echo "Instructor deleted successfully.";
        mysqli_stmt_close($stmt);
    } else {
        echo "ERROR: Could not execute query: $sql. " . mysqli_error($link);
    }
}

mysqli_close($link);
?>
