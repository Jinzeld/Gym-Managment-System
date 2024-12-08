<?php
session_start();
require_once "config.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Gather post data for membership attributes
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class_id = mt_rand(10, 999); // Generate random 3-digit class ID
    //$class_id = 'FIT' . str_pad(mt_rand(10, 999), 3, '0', STR_PAD_LEFT); // Generate class ID with "FIT" prefix 
    // ^ need to change this to varchar in class_id
    $instructor_id = $_POST['instructor_id'];
    $class_name = $_POST['class_name'];
    $days = $_POST['days'];
    $time_slot = $_POST['time_slot'];
    $capacity = $_POST['capacity'];

    //Logic for selecting days when adding a new class
    if(isset($_POST['days']) && is_array($_POST['days'])){
        $days_arr = $_POST['days']; //Array for the class days
        $valid = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

        //Count how many days are selected
        if(count($days_arr) > 3){
            die("You can only select up to 3 days.");
        }

        //Check if the selected days are valid
        foreach($days_arr as $days){
            if(!in_array($days, $valid)){
                die("Invalid day(s) selected. Please try again.");
            }
        }
        //Convert selected days into comma-seperated string
        $days = implode(', ', $days_arr);
    }

    if (!empty($class_name) && !empty($days) && !empty($time_slot) && !empty($capacity) && !empty($instructor_id)) {

        //Insert a new row of the following attributes when adding
        $sql = "INSERT INTO Class (class_id, instructor_id, class_name, days, time_slot, capacity) 
                VALUES (?, ?, ?, ?, ?, ?)";

        if ($stmt = $link->prepare($sql)) {
            $stmt->bind_param("iissss", $class_id, $instructor_id, $class_name, $days, $time_slot, $capacity);

            if ($stmt->execute()) {
                echo "<p style='color: green;'>New class added successfully! Member ID: $class_id</p>";

                header("location: index.php");
                exit();   

            } else {
                echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            echo "<p style='color: red;'>Error: Unable to prepare the SQL statement.</p>";
        }
    } else {
        echo "<p style='color: red;'>Please fill in all required fields.</p>";
    }

    $link->close();
}

// Fetch all instructors for the dropdown
$sql_instructors = "SELECT instructor_id, first_name, last_name FROM Instructor";
$result_instructors = mysqli_query($link, $sql_instructors);

if (!$result_instructors) {
    die("Error fetching instructors: " . mysqli_error($link));
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Classes</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-4">Add New Class</h1>
        <form method="POST" action="newClass.php">
            <!--Assigning Instructor-->
            <div class="form-group">
                <label for="instructor_id">Assign Instructor:</label>
                <select class="form-control" id="instructor_id" name="instructor_id" required>
                    <option value = "">Select an instructor</option>
                    <!-- PHP for fetching instructor info for assignment dropdown-->
                    <!-- Instructors have randomized, long id #s, so inputting each one wouldn't be efficient -->
                    <?php while($row = mysqli_fetch_assoc($result_instructors)): ?>
                        <option value="<?php echo $row['instructor_id']; ?>">
                        <?php echo $row['first_name'] . ' ' . $row['last_name']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!--Inputting class name-->
            <div class="form-group">
                <label for="class_name">Class Name:</label>
                <input type="text" class="form-control" id="class_name" name="class_name" required>
            </div>
            <!--Selecting class days-->
            <div class="form-group">
                <label for="days">Select Class Days (Up to 3):</label><br>
                <label><input type ="checkbox" name = days[] value = "Monday">Monday</input></label><br>
                <label><input type ="checkbox" name = days[] value = "Tuesday">Tuesday</input></label><br>
                <label><input type ="checkbox" name = days[] value = "Wednesday">Wednesday</input></label><br>
                <label><input type ="checkbox" name = days[] value = "Thursday">Thursday</input></label><br>
                <label><input type ="checkbox" name = days[] value = "Friday">Friday</input></label><br>
                <label><input type ="checkbox" name = days[] value = "Saturday">Saturday</input></label><br>
                <label><input type ="checkbox" name = days[] value = "Sunday">Sunday</input></label><br>
            </div>

            <!-- JS for error handling for selecting class days-->
            <script>
                document.querySelectorAll('input[name="days[]"]').forEach(function(checkbox) {
                    checkbox.addEventListener('change', function() {
                        const checkBox = document.querySelectorAll('input[name="days[]"]:checked');
                        if(checkBox.length > 3){
                            alert("You can only select up to 3 days.");
                            this.checked = false;
                        }
                    });
                });
            </script>

            <!--Selecting class times-->
            <div class="form-group">
                <label for="time_slot">Class Times:</label>
                <select type="text" class="form-control" id="time_slot" name="time_slot" required>
                    <option value="">Select a time slot: </option>
                    <option value="7:00 AM - 8:50 AM">7:00 AM - 8:50 AM</option>
                    <option value="8:00 AM - 9:50 AM">8:00 AM - 9:50 AM</option>
                    <option value="9:00 AM - 10:50 AM">9:00 AM - 10:50 AM</option>
                    <option value="11:00 AM - 12:50 PM">11:00 AM - 12:50 PM</option>
                    <option value="12:00 PM - 1:50 PM">12:00 PM - 1:50 PM</option>
                    <option value="2:00 PM - 3:50 PM">2:00 PM - 3:50 PM</option>
                    <option value="4:00 PM - 5:50 PM">4:00 PM - 5:50 PM</option>
                </select>
            </div>

            <!--Inputting capacity-->
            <div class="form-group">
                <label for="capacity">Capacity (enter a number from 10 to 30):</label>
                <input type="number" class="form-control" id="capacity" name="capacity" min="10" max= "30" required>
            </div>
            
            <!--JS for setting capacity limit-->
            <script>
                document.querySelector("form").addEventListener("submit", function(event){
                    var capacity = document.getElementById("capacity").value;
                    if(capacity > 30){
                        alert("Capacity cannot exceed 30.");
                        event.preventDefault(); 
                    }
                    else if (capacity < 10){
                        alert("Capacity cannot be less than 10.");
                        event.preventDefault(); 
                    }
                });
            </script>

            <!--Buttons-->
            <button type="submit" class="btn btn-primary">Add Class</button>
            <button type="button" class="btn btn-secondary" onclick="window.location.href='index.php';">Cancel</button>
        </form>
        <br><br>
    </div>
</body>
</html>