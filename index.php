<?php

  //start session here

  if (isset($_POST["submit"])){
    $choice = $_POST["choice"];

    //set the week number based upon the users choice
    if ($choice == "server"){
      $today = date("Y-m-d");   //get the server date in the correct format
      //query the db here for the week number based on the server date

    }
    else {
      $week = $_POST["weekNum"];
    }

      //set up a session variable here to identify the week
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
            document.getElementById("weekNum").disabled = true;
          else
            document.getElementById("weekNum").disabled = false;
      }
    </script>

  </head>

  <body>

    <h1>A-League Ladder Assignment</h1>

    <form id="weekForm" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
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
        <label for="weekNum">Week Number:</label>
        <select id="weekNum" name="weekNum" size="1" disabled>
          <script>
             for (i = 1; i <= 24; i++)
               document.write('<option value="' + i + '">' + i + '</option>');
          </script>
        </select>
      </p>
      <p><input type="submit" name="submit" value="submit"></p>
    </form>

  </body>
</html>
