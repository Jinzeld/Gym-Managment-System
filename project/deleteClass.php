<?php
    session_start();

    // Check if the Class ID (Class_id) is provided in the URL
    if (isset($_GET["class_id"]) && !empty(trim($_GET["class_id"]))) {
        // Store the Class's ID in the session for later use
        $_SESSION["class_id"] = $_GET["class_id"];
        $class_id = $_GET["class_id"];
    }

    require_once "config.php";

    // Handle the deletion when the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        // DELETE Class
        if (isset($_POST['delete_class'])) {
            $class_id = intval($_POST['class_id']);

            $sql = "DELETE FROM Class WHERE class_id=?";
            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "i", $class_id);

                if (mysqli_stmt_execute($stmt)) {
                    echo "Class deleted successfully.";
                    $param_class_id = $class_id;

                    // Attempt to execute the prepared statement
                    if (mysqli_stmt_execute($stmt)) {
                        header("location: index.php");
                        exit();
                        
                    } else {
                        echo "<p style='color: red;'>Error deleting the Class record. Please try again.</p>";
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
        <title>Delete Class</title>
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
                            <h1>Delete Class</h1>
                        </div>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="alert alert-danger fade in">
                                <input type="hidden" name="class_id" value="<?php echo ($_SESSION["class_id"]); ?>"/>
                                <p>Are you sure you want to delete the record for Class ID: 
                                    <b><?php echo htmlspecialchars($_SESSION["class_id"]); ?></b>?</p><br>
                                <button type="submit" name="delete_class"class="btn btn-danger">Yes</button>
                                <a href="index.php" class="btn btn-default">No</a>
                            </div>
                        </form>
                    </div>
                </div>        
            </div>
        </div>
    </body>
</html>