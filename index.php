<?php
  session_start();
  require_once("conn.php");
  
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
          $_SESSION["weekID"] = $id;
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
    <?php include("nav.php"); ?>
    <main>
        <h1>A-League Ladder Assignment</h1>
        
        <?php if($error == 2):?>
            <p class="errorMsg">Invalid date - no weeks during this date.</p>
        <?php elseif(isset($_GET["set"])):?>
            <p class="errorMsg">You must choose a date first before viewing the site.</p>
        <?php elseif($error == 0): ?>
            <p class="succMsg">Success! Week changed to <?php echo "$id using date $today"; ?></p>
        <?php endif; ?>
        
        <form id="weekForm" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
            <div class="login_panel" style="text-align: left;">
              <p>Do you want to use the Server Date or User Input for the current week?</p>
              
              
              <p>
                <label for="Server">Server Date</label>
                <input type="radio" id="Server" name="choice" value="server" onclick="changeSelectionList();">
              </p>

              <p>
                <label for="User">User Input</label>
                <input type="radio" id="User" name="choice" value="user" onclick="changeSelectionList();">
              </p>

              <p>
                <label for="date">Date:</label>
                <input id="date" name="date" type="date" disabled>
              </p>
              <p><input type="submit" name="submit" value="Submit"></p>
          </div>
        </form>
    </main>
  </body>
</html>
