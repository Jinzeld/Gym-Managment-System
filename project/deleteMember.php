<?php
    session_start();
    require_once "config.php";

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Handle delete request
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_membership'])) {
        $membership_id = intval($_POST['membership_id']); // Retrieve the ID to delete

        // Delete query
        $sql = "DELETE FROM Membership WHERE membership_id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $membership_id);

            if (mysqli_stmt_execute($stmt)) {
                echo "<p style='color: green;'>Membership deleted successfully!</p>";
            } else {
                echo "<p style='color: red;'>Error: Unable to delete membership. " . mysqli_error($link) . "</p>";
            }

            mysqli_stmt_close($stmt);
        } else {
            echo "<p style='color: red;'>Error: Could not prepare the delete query.</p>";
        }
    }

    // Fetch all memberships
    $sql = "SELECT membership_id, type, price FROM Membership ORDER BY membership_id";
    $result = mysqli_query($link, $sql);

    if (!$result) {
        die("Error fetching memberships: " . mysqli_error($link));
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Manage Memberships</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container">
            <h1 class="mt-4">Manage Memberships</h1>

            <?php if (mysqli_num_rows($result) > 0): ?>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Membership ID</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['membership_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['type']); ?></td>
                                <td>$<?php echo htmlspecialchars($row['price']); ?></td>
                                <td>
                                    <!-- Delete Membership Form -->
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" style="display:inline;">
                                        <input type="hidden" name="membership_id" value="<?php echo htmlspecialchars($row['membership_id']); ?>">
                                        <button type="submit" name="delete_membership" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this membership?');">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="lead"><em>No memberships found.</em></p>
            <?php endif; ?>

            <button class="btn btn-default" onclick="window.location.href='index.php';">Back</button>
        </div>
    </body>
</html>

<?php
    // Free result set and close the connection
    mysqli_free_result($result);
    mysqli_close($link);
?>
