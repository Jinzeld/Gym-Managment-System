<?php
session_start();
require_once "config.php";

// Check if class_id and member_id are provided
if (!isset($_GET['class_id']) || !isset($_GET['member_id'])) {
    die("Error: Missing class or member ID.");
}

$class_id = intval($_GET['class_id']);
$member_id = intval($_GET['member_id']);

// SQL to remove the class assignment
$sql = "DELETE FROM Takes WHERE member_id = ? AND class_id = ?";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "ii", $member_id, $class_id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<p style='color: green;'>Class dropped successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error dropping class: " . mysqli_error($link) . "</p>";
    }

    mysqli_stmt_close($stmt);
} else {
    echo "<p style='color: red;'>Error preparing the query.</p>";
}

mysqli_close($link);

// Redirect back to the member's class list
header("Location: viewClasses.php?member_id=$member_id");
exit();
?>

