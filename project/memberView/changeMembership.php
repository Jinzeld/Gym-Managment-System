<?php
    session_start();
    require_once "../config.php";

    $member_id = $_GET['member_id'];  // Get member ID from the URL
    $current_membership = $_GET['current_membership'];  // Get the current membership ID from the URL

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $new_membership_id = $_POST['new_membership'];

        if (!empty($new_membership_id)) {
            // Update the member's membership in the database
            $sql = "UPDATE Member SET membership_id = ? WHERE member_id = ?";
            
            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "ii", $new_membership_id, $member_id);
                if (mysqli_stmt_execute($stmt)) {
                    echo "<p style='color: green;'>Membership plan updated successfully!</p>";
                    
                    header("location: index.php");  // Redirect back to the member view
                    exit();
                } else {
                    echo "<p style='color: red;'>Error: Unable to update membership plan.</p>";
                }
                mysqli_stmt_close($stmt);
            } else {
                echo "<p style='color: red;'>Error: Unable to prepare the SQL statement.</p>";
            }
        } else {
            echo "<p style='color: red;'>Please select a new membership plan.</p>";
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Membership</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Change Membership Plan</h1>

        <form method="POST" action="changeMembership.php?member_id=<?php echo $member_id; ?>&current_membership=<?php echo $current_membership; ?>">
            <p>Select a new membership plan:</p>

            <!-- Fetch available membership plans, excluding the current one -->
            <?php
            // Fetch available membership plans, excluding the current plan
            $sql = "SELECT membership_id, type FROM Membership WHERE membership_id != ?";
            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "i", $current_membership);  // Exclude the current membership
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                echo '<select class="form-control" name="new_membership" required>';
                echo '<option value="">Select a plan</option>';
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<option value="' . $row['membership_id'] . '">' . $row['type'] . '</option>';
                }
                echo '</select>';
                mysqli_free_result($result);
                mysqli_stmt_close($stmt);
            } else {
                echo "<p>Error fetching membership plans: " . mysqli_error($link) . "</p>";
            }
            ?>

            <br>
            <button type="submit" class="btn btn-primary">Update Membership</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>

<?php
    mysqli_close($link);
?>
