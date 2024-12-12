<?php 
session_start();
require_once "../config.php";

$message = ""; // Initialize message variable

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['class_id'])) {
    // Grab class_id and instructor_id from the session
    $class_id = intval($_GET['class_id']);
    $instructor_id = 468306; // Example instructor ID

    // Check if the instructor is assigned to the class
    $sql = "SELECT * FROM Class WHERE class_id = ? AND instructor_id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $class_id, $instructor_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            // Delete the class
            $delete_sql = "DELETE FROM Class WHERE class_id = ?";
            if ($stmt = mysqli_prepare($link, $delete_sql)) {
                mysqli_stmt_bind_param($stmt, "i", $class_id);
                if (mysqli_stmt_execute($stmt)) {
                    $message = "success"; // Deletion successful
                } else {
                    $message = "error_removal"; // Error during deletion
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            $message = "not_assigned"; // Class not found or not assigned to the instructor
        }
    } else {
        $message = "invalid_request"; // Missing class or instructor ID
    }
} else {
    $message = "invalid_request";
}

// Redirect back to index.php with the message
header("Location: index.php?message=$message");
exit();
?>
