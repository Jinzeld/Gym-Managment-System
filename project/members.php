<?php
require_once "config.php";

// CREATE: Add a new member
if (isset($_POST['add_member'])) {
    $sql = "INSERT INTO Member (membership_id, first_name, last_name, email, phone, city, state, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "isssssss", $_POST['membership_id'], $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['phone'], $_POST['city'], $_POST['state'], $_POST['status']);
        mysqli_stmt_execute($stmt);
        echo "New member added successfully.";
        mysqli_stmt_close($stmt);
    } else {
        echo "ERROR: Could not execute query: $sql. " . mysqli_error($link);
    }
}

// READ: Fetch all members
$sql = "SELECT * FROM Member";
if ($result = mysqli_query($link, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "ID: {$row['member_id']}, Name: {$row['first_name']} {$row['last_name']}, Status: {$row['status']}<br>";
    }
    mysqli_free_result($result);
}

// UPDATE: Update member details
if (isset($_POST['update_member'])) {
    $sql = "UPDATE Member SET first_name=?, last_name=?, email=?, status=? WHERE member_id=?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssssi", $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['status'], $_POST['member_id']);
        mysqli_stmt_execute($stmt);
        echo "Member updated successfully.";
        mysqli_stmt_close($stmt);
    } else {
        echo "ERROR: Could not execute query: $sql. " . mysqli_error($link);
    }
}

// DELETE: Remove a member
if (isset($_POST['delete_member'])) {
    $sql = "DELETE FROM Member WHERE member_id=?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $_POST['member_id']);
        mysqli_stmt_execute($stmt);
        echo "Member deleted successfully.";
        mysqli_stmt_close($stmt);
    } else {
        echo "ERROR: Could not execute query: $sql. " . mysqli_error($link);
    }
}

mysqli_close($link);
?>
