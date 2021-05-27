<?php
    require_once("conn.php");

  //start session here
  $error = 1;
  if (isset($_POST["choice"])){
      $choice = $_POST["choice"];
      
      $today = ($choice == "server" || $_POST["date"] == "") ? date("Y-m-d") : $_POST["date"];   //get the server date in the correct format
      $today = $dbConn->escape_string($today);
      
      $sql = "SELECT weekID FROM weeks WHERE startDate <= ? AND endDate >= ?;";
      $statement = $dbConn->prepare($sql) or die ('Problem preparing: ' . $dbConn->error);
      $statement->bind_param('ss', $today, $today);
      $statement->execute();
      
      $results = $statement->get_result();
      if(mysqli_num_rows($results)==0) $error = 2;
      else {
          $error = 0;
          $row = $results->fetch_assoc();
          $id = $row["weekID"];
          
          //set up a session variable here to identify the week
      }
      
  }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>A-League Assignment - Choose Week</title>
    <link rel="stylesheet" href="css/projectMaster.css">

    <script>
      function changeSelectionList(){
          if (document.getElementById("weekForm").choice.value == "server")
            document.getElementById("date").disabled = true;
          else
            document.getElementById("date").disabled = false;
      }
    </script>

  </head>

  <body>

    <h1>A-League Ladder Assignment</h1>

    <form id="weekForm" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
      <p>Do you want to use the Server Date or User Input for the current week?</p>
      
      <?php if($error == 2):?>
        <p>Invalid date - .</p>
      <?php else if($error == 1): ?>
          
      <?php endif; ?>
      
      <p>
        <label for="Server">Server Date</label>
        <input type="radio" id="Server" name="choice" value="server" onclick="changeSelectionList();">
      </p>

      <p>
        <label for="User">User Input</label>
        <input type="radio" id="User" name="choice" value="user" onclick="changeSelectionList();">
      </p>

      <p>
        <label for="date">Week Number:</label>
        <input id="date" name="date" type="date" disabled>
      </p>
      <p><input type="submit" name="submit" value="submit"></p>
    </form>

  </body>
</html>
