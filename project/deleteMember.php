<?php
session_start();

// Check if the member ID (`member_id`) is provided in the URL
if (isset($_GET["member_id"]) && !empty(trim($_GET["member_id"]))) {
    // Store the member's ID in the session for later use
    $_SESSION["member_id"] = $_GET["member_id"];
    $member_id = $_GET["member_id"];
}

require_once "config.php";

// Handle the deletion when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SESSION["member_id"]) && !empty($_SESSION["member_id"])) {
        $member_id = $_SESSION["member_id"];

        // Prepare the DELETE SQL statement
        $sql = "DELETE FROM members WHERE member_id = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind the `member_id` parameter to the prepared statement
            mysqli_stmt_bind_param($stmt, "i", $param_member_id);

            // Set the parameter
            $param_member_id = $member_id;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Member record deleted successfully
                header("location: index.php"); // Redirect to landing page
                exit();
            } else {
                echo "<p style='color: red;'>Error deleting the member record. Please try again.</p>";
            }

            // Close the prepared statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close the database connection
    mysqli_close($link);
} else {
    // Ensure the `member_id` is valid and exists in the URL
    if (empty(trim($_GET["member_id"]))) {
        // Redirect to an error page if no valid `member_id` is provided
        header("location: error.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Member</title>
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
                        <h1>Delete Member</h1>
                    </div>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="alert alert-danger fade in">
                            <input type="hidden" name="member_id" value="<?php echo ($_SESSION["member_id"]); ?>"/>
                            <p>Are you sure you want to delete the record for Member ID: 
                                <b><?php echo htmlspecialchars($_SESSION["member_id"]); ?></b>?</p><br>
                            <input type="submit" value="Yes" class="btn btn-danger">
                            <a href="index.php" class="btn btn-default">No</a>
                        </div>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
