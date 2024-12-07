<?php
    session_start();
    require_once "config.php";

    if(!isset($_GET['member_id']) || empty(trim($_GET['member_id']))){
        die("Error: No Member ID");
    }
    //Get the member_id from the URL
    $member_id = intval($_GET['member_id']);

    $sql = "
        SELECT Member.member_id, Member.first_name, Member.last_name, 
               Class.class_id, Class.class_name, Class.days, Class.time_slot 
        FROM Takes
        JOIN Member ON Takes.member_id = Member.member_id
        JOIN Class ON Takes.class_id = Class.class_id
        WHERE Member.member_id = ? AND Member.status = 'active'
        ORDER BY Member.member_id, Class.days, Class.time_slot;
    ";

    //Prepare and execute query
    if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "i", $member_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        //Fetch the member's name
        $member_name = "";
        $status = "";
        $row = [];

        while($row = mysqli_fetch_assoc($result)){
            $member_name = $row['first_name'] . ' ' . $row['last_name'];
            $status = $row['status'];
            $row[] = $row;
        }

        if(empty($member_name)){
            echo "<br><p style='color: red;' class=\"lead\"><em>This member is inactive and cannot be assigned any classes.</em></p><br>";
            echo "<button class=\"btn btn-default\" onclick=\"window.location.href='index.php';\">Back</button>";
            exit();
        }
    } 
    else {
        die("Error: Unable to fetch query." . mysqli_error($link));
    }
    
?>

<!-- HTML for View Class-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Classes:</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Classes for <?php echo $member_name; ?></h1><br>
        <?php if(mysqli_num_rows($result) > 0): ?>
            <table class='table table-bordered table-striped'>
                <thead>
                    <tr>
                        <th>Class ID</th>
                        <th>Class Name</th>
                        <th>Class Days</th>
                        <th>Class Times</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        mysqli_data_seek($result, 0); //Reset pointer after fetching a member's name
                        // Fetch the result from the executed query and parse them into the table
                        while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $row['class_id']; ?></td>
                                <td><?php echo $row['class_name']; ?></td>
                                <td><?php echo $row['days']; ?></td>
                                <td><?php echo $row['time_slot']; ?></td>
                            </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="lead"><em>No classes assigned for this member.</em></p>
        <?php endif; ?>
        <?php mysqli_free_result($result); ?>

        <!-- Go back button -->
        <button class="btn btn-default" onclick="window.location.href='index.php';">Back</button>
    </div>
</body>
</html>

<?php
    //Close connection for database
    mysqli_stmt_close($stmt);
    mysqli_close($link);
?>