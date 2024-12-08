<?php
    session_start();

    // Check if the Instructor ID (`Instructor_id`) is provided in the URL
    if (isset($_GET["instructor_id"]) && !empty(trim($_GET["instructor_id"]))) {
        // Store the Instructor's ID in the session for later use
        $_SESSION["instructor_id"] = $_GET["instructor_id"];
        $instructor_id = $_GET["instructor_id"];
    }

    require_once "config.php";

    // Handle the deletion when the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        // DELETE Instructor
        if (isset($_POST['delete_instructor'])) {
            $instructor_id = intval($_POST['instructor_id']);

            $sql = "DELETE FROM Instructor WHERE instructor_id=?";
            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "i", $instructor_id);

                if (mysqli_stmt_execute($stmt)) {
                    echo "Instructor deleted successfully.";
                    $param_instructor_id = $instructor_id;

                    // Attempt to execute the prepared statement
                    if (mysqli_stmt_execute($stmt)) {
                        header("location: index.php");
                        exit();
                        
                    } else {
                        echo "<p style='color: red;'>Error deleting the Instructor record. Please try again.</p>";
                    }
                // Close the prepared statement
                    mysqli_stmt_close($stmt);
                } else {
                    echo "Error: Could not execute query: " . mysqli_error($link);
                }

                mysqli_stmt_close($stmt);
            } else {
                echo "Error: Unable to prepare the SQL statement.";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Delete Instructor</title>
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
                            <h1>Delete Instructor</h1>
                        </div>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="alert alert-danger fade in">
                                <input type="hidden" name="instructor_id" value="<?php echo ($_SESSION["instructor_id"]); ?>"/>
                                <p>Are you sure you want to delete the record for Instructor ID: 
                                    <b><?php echo htmlspecialchars($_SESSION["instructor_id"]); ?></b>?</p><br>
                                <button type="submit" name="delete_instructor"class="btn btn-danger">Yes</button>
                                <a href="index.php" class="btn btn-default">No</a>
                            </div>
                        </form>
                    </div>
                </div>        
            </div>
        </div>
    </body>
</html>