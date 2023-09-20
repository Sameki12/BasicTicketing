<!DOCTYPE html>
<html>
<head>
    <title>Carlisle Ticket System</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>
    <?php include 'css/common_header.php'; ?>

    <h2>Create a New Ticket</h2>
    
    <form method="post" action="src/process_ticket.php" onsubmit="return validateSiteID()">
        <?php
        include 'src/components/databaseConnection.php';

        // Execute query to fetch distinct values from the "site_id" column
        $query = "SELECT DISTINCT site_id FROM site_info";
        $results = $conn->query($query);

        if (!$results) {
            echo "Error: " . $conn->error;
        }
        ?>
        <label for="site_id">Site #</label>
        <input type="text" id="site_id_input" name="site_id" placeholder="Type to select Site #" list="site_id_list" required>
        <datalist id="site_id_list">
            <?php while($row = $results->fetch_assoc()){ ?>
                <option value="<?= $row['site_id']?>">
            <?php }?>
        </datalist>
        
        <label for="category"><br>Category:</label>
        <select name="category">
            <option value="Internet">Internet</option>
            <option value="MWS">MWS</option>
            <option value="POS">POS</option>
        </select><br><br>

        <label for="priority">Priority:</label>
        <select name="priority">
            <option value="Low">Low</option>
            <option value="Med">Medium</option>
            <option value="High">High</option>
        </select><br><br>
        
        <label for="tech_assigned">Tech Assigned:</label>
        <select name="tech_assigned">
            <?php
            // Retrieve the list of usernames from the "users" table for technicians
            $query = "SELECT username FROM users WHERE role = 'technician'";
            $techResults = $conn->query($query);

            if ($techResults) {
                while ($techRow = $techResults->fetch_assoc()) {
                    echo '<option value="' . $techRow['username'] . '">' . $techRow['username'] . '</option>';
                }
            }
            ?>
        </select><br><br>
        
        <label for="issue_desc">Issue Description:</label><br>
        <textarea name="issue_desc" rows="4" cols="50"></textarea><br><br>
        
        <input type="submit" value="Submit Ticket">
    </form>

    <script>
        function validateSiteID() {
            var input = document.getElementById('site_id_input').value;
            var datalist = document.getElementById('site_id_list');
            var options = datalist.getElementsByTagName('option');
            var valid = false;

            for (var i = 0; i < options.length; i++) {
                if (options[i].value === input) {
                    valid = true;
                    break;
                }
            }

            if (!valid) {
                alert('Site ID not found in the list.');
                return false; // Prevent form submission
            }

            return true; // Allow form submission
        }
    </script>
</body>
</html>