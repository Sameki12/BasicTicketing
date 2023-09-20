<!DOCTYPE html>
<html>
<head>
    <title>Timesheet</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>

<?php
include 'css/common_header.php';
include 'src/components/databaseConnection.php';

// Start a session
session_start(); // Start the PHP session

// Initialize variables for filter criteria
$username = '';
$start_date = '';
$end_date = '';
$userId = null; // Initialize user ID variable

// Check if the user has the role "technician"
if (isset($_SESSION['role']) && $_SESSION['role'] === 'technician') {
    // If the user is a technician, set the user ID based on their session
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
    } else {
        // Handle the case when the user ID is not available
        die("User ID is not available.");
    }
}

// Handle form submission to apply filters or export CSV
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['export_csv'])) {
        // Handle CSV export
        exportToCSV();
    } else {
        // Handle filter submission
        $username = $_POST['username'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
    }
}

// Build the SQL query based on the user's role
$sql = "SELECT * FROM timesheets WHERE 1";

// Apply username filter
if (!empty($username)) {
    $sql .= " AND user_id = (SELECT user_id FROM users WHERE username = '$username')";
}

// Apply date range filter
if (!empty($start_date) && !empty($end_date)) {
    $sql .= " AND clock_in BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'";
}

// Apply role-based filter
if ($userId !== null) {
    $sql .= " AND user_id = $userId";
}

$result = $conn->query($sql);

// Function to export data to CSV
function exportToCSV() {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="timesheet.csv"');

    $output = fopen('php://output', 'w');

    // CSV header
    fputcsv($output, array('User', 'Clock In', 'Clock Out', 'Latitude', 'Longitude'));

    // Retrieve timesheet data for export
    global $conn, $sql;
    $exportResult = $conn->query($sql);

    if ($exportResult->num_rows > 0) {
        while ($row = $exportResult->fetch_assoc()) {
            // CSV data rows
            fputcsv($output, array($row['user_id'], $row['clock_in'], $row['clock_out'], $row['latitude'], $row['longitude']));
        }
    }

    fclose($output);
    exit();
}
?>

<h2>Timesheet</h2>

<!-- Filter Form -->
<form method="post">
<?php if ($_SESSION['role'] === 'admin') : ?>
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" value="<?php echo $username; ?>">
<?php endif; ?>
    
    <label for="start_date">Start Date:</label>
    <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
    
    <label for="end_date">End Date:</label>
    <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
    
    <input type="submit" value="Apply Filters">
    <input type="submit" name="export_csv" value="Export to CSV">
</form>

<!-- Display Timesheet Data -->
<table border="1">
    <tr>
        <th>User</th>
        <th>Clock In</th>
        <th>Clock Out</th>
        <th>Latitude</th>
        <th>Longitude</th>
        <th>Time Difference (Rounded)</th>
    </tr>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Retrieve the username by joining the users table
            $userQuery = "SELECT username FROM users WHERE user_id = " . $row['user_id'];
            $userResult = $conn->query($userQuery);
            $userRow = $userResult->fetch_assoc();
            $username = $userRow['username'];

            $clockIn = new DateTime($row['clock_in']);
            $clockOut = new DateTime($row['clock_out']);

            // Calculate the time difference in minutes
            $interval = $clockIn->diff($clockOut);
            $totalMinutes = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;

            // Round the time difference to the nearest half-hour
            $roundedMinutes = round($totalMinutes / 30) * 30;

            // Format the rounded time difference
            $roundedTime = floor($roundedMinutes / 60) . ' hours ' . ($roundedMinutes % 60) . ' minutes';

            echo "<tr>";
            echo "<td>" . $username . "</td>";
            echo "<td>" . $row['clock_in'] . "</td>";
            echo "<td>" . $row['clock_out'] . "</td>";
            echo "<td>" . $row['latitude'] . "</td>";
            echo "<td>" . $row['longitude'] . "</td>";
            echo "<td>" . $roundedTime . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No records found.</td></tr>";
    }
    ?>
</table>
</body>
</html>