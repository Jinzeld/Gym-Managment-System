<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $name_first = $_POST['name_first'];
    $name_last = $_POST['name_last'];
    $specialty = $_POST['specialty'];
    $email = $_POST['email'];

    $sql = "INSERT INTO Instructor (name_first, name_last, specialty, email) VALUES ('$name_first', '$name_last', '$specialty', '$email')";
    if ($conn->query($sql) === TRUE) {
        echo "New instructor added successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'retrieve') {
    $sql = "SELECT * FROM Instructor";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "ID: " . $row["instructor_id"] . " - Name: " . $row["name_first"] . " " . $row["name_last"] . " - Specialty: " . $row["specialty"] . " - Email: " . $row["email"] . "<br>";
        }
    } else {
        echo "No instructors found.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $instructor_id = $_POST['instructor_id'];
    $name_first = $_POST['name_first'];
    $name_last = $_POST['name_last'];
    $specialty = $_POST['specialty'];
    $email = $_POST['email'];

    $sql = "UPDATE Instructor SET name_first='$name_first', name_last='$name_last', specialty='$specialty', email='$email' WHERE instructor_id=$instructor_id";
    if ($conn->query($sql) === TRUE) {
        echo "Instructor updated successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $instructor_id = $_POST['instructor_id'];

    $sql = "DELETE FROM Instructor WHERE instructor_id=$instructor_id";
    if ($conn->query($sql) === TRUE) {
        echo "Instructor deleted successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

?>
