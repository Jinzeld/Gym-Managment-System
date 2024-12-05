<?php
require_once "config.php";

// CREATE: Add a new class
if (isset($_POST['add_class'])) {
    $sql = "INSERT INTO Class (class_id, instructor_id, class_name, schedule, capacity) 
            VALUES (?, ?, ?, ?, ?)";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "iissi", $_POST['class_id'], $_POST['instructor_id'], $_POST['class_name'], $_POST['schedule'], $_POST['capacity']);
        mysqli_stmt_execute($stmt);
        echo "Class added successfully.";
        mysqli_stmt_close($stmt);
    } else {
        echo "ERROR: Could not execute query: $sql. " . mysqli_error($link);
    }
}

// READ: Fetch all classes
$sql = "SELECT * FROM Class";
if ($result = mysqli_query($link, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "ID: {$row['class_id']}, Name: {$row['class_name']}, Capacity: {$row['capacity']}<br>";
    }
    mysqli_free_result($result);
}

// DELETE: Remove a class
if (isset($_POST['delete_class'])) {
    $sql = "DELETE FROM Class WHERE class_id=?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $_POST['class_id']);
        mysqli_stmt_execute($stmt);
        echo "Class deleted successfully.";
        mysqli_stmt_close($stmt);
    } else {
        echo "ERROR: Could not execute query: $sql. " . mysqli_error($link);
    }
}

mysqli_close($link);
?>
