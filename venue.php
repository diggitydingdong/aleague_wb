<?php
require_once("conn.php");
session_start();

if(!isset($_GET["venue"])) header("Location: fixtures.php");
$sql = "SELECT * FROM venues WHERE venueID = ?;";
$statement = $dbConn->prepare($sql) or die ('Problem preparing: ' . $dbConn->error);
$statement->bind_param('i', $_GET["venue"]);
$statement->execute();

$results = $statement->get_result();
if(mysqli_num_rows($results) == 0) header("Location: fixtures.php");
else {
    $venue = $results->fetch_assoc();
    
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>A-League - Score Entry</title>
    <link rel="stylesheet" href="css/projectMaster.css">

    <script type="text/javascript">
        function initMap() {
            const pos = {lat: <?php echo $venue["latitude"]; ?>, lng: <?php echo $venue["longitude"]; ?>};
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 15,
                center: pos,
            });
            const marker = new google.maps.Marker({
                position: pos,
                map: map,
            });
        }
    </script>
  </head>

  <body>
      <?php include("nav.php") ?>
      <main>
          <h1>Venue: <?php echo $venue["venueName"]; ?></h1>
          <h3>Address: <?php echo $venue["address"]; ?></h3>
          <div id="map"></div>
      </main>
      
      <script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC9Uv3GBri_zroYJx3XStQlF3etFM_9LHw&callback=initMap&libraries=&v=weekly"
      async
    ></script>
  </body>
</html>
