<!DOCTYPE html>
<html>
<head>
    <title>Timeclock</title>
</head>
<body>

<?php
// Header for all pages
include 'css/common_header.php';
// Include the database connection file
include 'src/components/databaseConnection.php';

// Start a session
session_start(); // Start the PHP session

// Access and print session variables as well as any PHP issues. Uncomment for it to work
//foreach ($_SESSION as $key => $value) {
//    echo "$key = $value <br>";
//}
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

// Get the user's current clock status
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Create a prepared statement to check the user's current clock status
    $stmt = $conn->prepare("SELECT * FROM timesheets WHERE user_id = ? ORDER BY timesheet_id DESC LIMIT 1");
    $stmt->bind_param("i", $user_id); // "i" represents an integer placeholder
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $last_entry = $result->fetch_assoc();
        $is_clocked_in = ($last_entry['clock_out'] === null);
    } else {
        // Handle the case when there are no timesheet entries for the user
        $is_clocked_in = false;
    }
} else {
    // Handle the case when the user is not authenticated
    // You can redirect to a login page or display an error message
    die("User is not authenticated.");
}

// Handle user interactions
if (isset($_POST['clock_action'])) {
    $action = ($_POST['clock_action'] === 'Clock In') ? 'in' : 'out';

    // Insert or update the timesheet entry based on clock in or out
    if ($action === 'in') {
        // Create a prepared statement for clocking in
        $stmt = $conn->prepare("INSERT INTO timesheets (user_id, clock_in, latitude, longitude) VALUES (?, NOW(), ?, ?)");
        $stmt->bind_param("idd", $user_id, $_POST['latitude'], $_POST['longitude']);
        $stmt->execute();

        // Redirect to the tickets.php page after Clock In
        header('Location: tickets.php');
        exit();
    } else {
        // Create a prepared statement for clocking out
        $stmt = $conn->prepare("UPDATE timesheets SET clock_out = NOW(), latitude = ?, longitude = ? WHERE timesheet_id = ?");
        $stmt->bind_param("ddi", $_POST['latitude'], $_POST['longitude'], $last_entry['timesheet_id']);
        $stmt->execute();

        // Redirect to the timesheet.php page after Clock Out
        header('Location: timesheet.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Time Clock</title>
</head>
<body>
    <form method="post">
        <?php if ($is_clocked_in) : ?>
            <input type="hidden" name="clock_action" value="Clock Out">
            <button type="submit">Clock Out</button>
        <?php else : ?>
            <input type="hidden" name="clock_action" value="Clock In">
            <button type="submit">Clock In</button>
        <?php endif; ?>
    </form>

    <!-- Geolocation API from Javascript. No idea how it will work on phone/computer but one of the easiest ways to get geolocation. -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const clockButton = document.querySelector("button[type='submit']");

            clockButton.addEventListener("click", function () {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function (position) {
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;
                        document.querySelector("form").innerHTML +=
                            '<input type="hidden" name="latitude" value="' + latitude + '">' +
                            '<input type="hidden" name="longitude" value="' + longitude + '">';
                        document.querySelector("input[name='clock_action']").value = "Clock Out";
                        clockButton.innerText = "Clock Out";
                        document.querySelector("form").submit();
                    });
                } else {
                    alert("Geolocation is not supported by this browser.");
                }
            });
        });
    </script>
</body>
</html>