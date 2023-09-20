	<link rel="stylesheet" type="text/css" href="css/styles.css">
 
<?php
include 'components/databaseConnection.php';

$site_id = $_GET["site_id"];
 
if(isset($_GET['wen_num']) and isset($_GET['site_name'])) {
  // prepare the update statement
  $update = $conn->prepare("UPDATE site_info SET site_info.wen_num = ?, site_info.site_name = ? WHERE site_info.site_id = ?;");
  //bind the paremeters
  $update->bind_param("ssi", $_GET['wen_num'], $_GET['site_name'], $site_id);
  //execute the statement
  $update->execute();
 
  if($update->affected_rows === 0) {
    echo '<script>alert("No rows updated")</script>';
  }else{
    echo ("{$update->affected_rows} row/s updated");
  }
}
 
//prepared prepared statement
$stmt = $conn->prepare("SELECT * FROM site_info WHERE site_id = ?");
//bind the paremeters
$stmt->bind_param("i", $site_id);
//execute the statement
$stmt->execute();
 
if($stmt->affected_rows === 0) {
  echo ('NO! No rows updated');
}else{
  $result = $stmt->get_result();
}
 
$stmt->close();
$conn->close();
 
$row = $result->fetch_assoc();
?>
<h2>Edit Record</h2>
 
<form method="get" action="edit.php">
  <div><label>Carlisle #</label><input type="text" name="site_id" id="site_id" value="<?= $row['site_id']?>" readonly/></div>
  <div><label>Wendy's #</label><input type="text" name="wen_num" id="wen_num" value="<?= $row['wen_num']?>" readonly/></div>
  <div><label>Site Name</label><input type="text" name="site_name" id="site_name" value="<?= $row['site_name']?>"/></div>
  <input type="submit" value="Submit">
</form>
<a href="../displaysites.php?site_id=<?= $row['site_id']?>">Back to Site List</a>