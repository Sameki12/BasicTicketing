<!DOCTYPE html>
<html>
<head>
    <title>Carlisle Ticket System</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>
    <header>    
        <div class="header-content">
            <h1>Carlisle Ticket System</h1>
            <nav>
                <a href="create_ticket.php">Create Ticket</a>
                <a href="tickets.php">View Tickets</a>
                <a href="closed_tickets.php">Closed Tickets</a>
                <?php
                session_start(); // Start the PHP session
                
                // Check if the user is logged in and their role is "user"
                if ($_SESSION["role"] === "user") {
                    // Redirect them to tickets.php
                    header("Location: tickets.php");
                    exit();
                }
                
                // For users with roles other than "user" (e.g., admin or technician)
                echo '<a href="displaysites.php">Display Sites</a>';
                
                if ($_SESSION["role"] === "admin") {
                    echo '<a href="admin_interface.php">Users</a>';
                    echo '<a href="manage_inv.php">Manage Inventory</a>';
                    echo '<a href="timesheet.php">Manage Timesheets</a>';
                }
                
                if ($_SESSION["role"] === "technician") {
                    echo '<a href="timeclock.php">Clock In/Out</a>';
                }
                ?>
                <a href="src/logout.php">Logout</a>
            </nav>
        </div>
    </header>
</body>
</html>