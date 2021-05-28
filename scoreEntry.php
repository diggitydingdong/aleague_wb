<?php
session_start();
require_once("conn.php");
if(!isset($_SESSION["firstname"])) header("Location: login.php?set=1");
if(!isset($_SESSION["weekID"])) header("Location: index.php?set=1");

$sql = "SELECT matchID, weekID, home.emblem as homeEmbl, home.teamName as homeName, away.emblem as awayEmbl, away.teamName as awayName, matchDate, matchTime, venues.venueName as venueName, score1, score2, home.teamID as homeID, away.teamID as awayID, venues.venueID as venueID FROM fixtures 
        INNER JOIN teams as home ON homeTeam = home.teamID
        INNER JOIN teams as away ON awayTeam = away.teamID
        INNER JOIN venues ON fixtures.venueID = venues.venueID;";
$statement = $dbConn->prepare($sql) or die ('Problem preparing: ' . $dbConn->error);
$statement->execute();
$results = $statement->get_result();

$error = 0;
if(isset($_POST["submit"])) {
    $score1 = $_POST["score1"];
    $score2 = $_POST["score2"];
    if(!is_numeric($score1) || !is_numeric($score2) || $score1 < 0 || $score2 < 0) $error = 1;
    else if(empty($_POST["matchID"])) $error = 2;
    else {
        $matchID = $_POST["matchID"];
        
        $sql = "UPDATE fixtures SET score1 = ?, score2 = ? WHERE matchID = ?;";
        $statement = $dbConn->prepare($sql) or die ('Problem preparing: ' . $dbConn->error);
        $statement->bind_param('iii', $score1, $score2, $matchID);
        $statement->execute();
        
        $sql = "UPDATE teams 
                SET played = played+1, 
                    won = won+?,
                    lost = lost+?,
                    drawn = drawn+?,
                    goalsFor = goalsFor+?,
                    goalsAgainst = goalsAgainst+?,
                    goalDiff = goalDiff+?,
                    points = points+?
                WHERE teamID = ?;";
        $statement = $dbConn->prepare($sql) or die ('Problem preparing: ' . $dbConn->error);
        
        $won = 0; 
        $lost = 0; 
        $drawn = 0;
        if($score1 > $score2) $won = 1;
        else if($score1 == $score2) $drawn = 1;
        else $lost = 1;
        $goalDiff = $score1 - $score2;
        $points = $won*3 + $lost;
        
        $statement->bind_param('iiiiiiii', $won, $lost, $drawn, $score1, $score2, $goalDiff, $points, $_POST["homeID"]);
        $statement->execute();
        
        $statement = $dbConn->prepare($sql) or die ('Problem preparing: ' . $dbConn->error);

        $won = 0;
        $lost = 0; 
        $drawn = 0;
        if($score2 > $score1) $won = 1;
        else if($score2 == $score1) $drawn = 1;
        else $lost = 1;
        $goalDiff = $score2 - $score1;
        $points = $won*3 + $lost;

        $statement->bind_param('iiiiiiii', $won, $lost, $drawn, $score2, $score1, $goalDiff, $points, $_POST["awayID"]);
        $statement->execute();
        
        $URI = $_SERVER['REQUEST_URI'];
        header("location:$URI?success=".$_POST["weekID"]);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>A-League - Score Entry</title>
    <link rel="stylesheet" href="css/projectMaster.css">
    <link rel="stylesheet" href="css/fixtures.css">
    <script type="text/javascript" src="javascript/projectScript.js" /></script>

  </head>

  <body>
      <?php include("nav.php") ?>
      
      <main><?php if(mysqli_num_rows($results) != 0): ?>
          <h1>A-League Score Entry</h1>
          
          <select style="width: 100px;" id="fixtures_week" onchange="updateFixturesWithWeek(this.value, true)">
              <?php
              for($i = 1; $i <= 24; $i++) {
                  $sel = false;
                  if(isset($_GET["success"])) $sel = ($i == $_GET["success"]); 
                  else $sel = ($i == $_SESSION["weekID"]); 
                  echo '<option value="'.$i.'"';
                  if($sel) echo ' selected';
                  echo '>WK'.$i.'</option>';
              }
              ?>
              
          </select>
          
          <form onsubmit="return validateScore()" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
              <div class="ui">
                  <div class="errorMsg"  id="score_err" <?php if($error != 1) echo "hidden"; ?>>Both scores should be a positive, integer value (or 0).</div>
                  <div class="errorMsg" <?php if($error != 2) echo "hidden"; ?>>A matchID was not chosen.</div>
                  <div class="succMsg"  id="score_succ" <?php if(!isset($_GET["success"])) echo "hidden"; ?>>Successfully updated.</div>
                  <input class="inputstohide" type="text" name="score1" id="score1" hidden>
                  <input class="inputstohide" type="text" name="score2"  id="score2" hidden>
                  <input type="hidden" name="homeID" id="homeID">
                  <input type="hidden" name="awayID" id="awayID">
                  <input type="hidden" name="weekID" id="weekID">
                  <button class="inputstohide" type="submit" name="submit" hidden>Submit</button>
              </div>
              
              <div class="fixtures_view"><div id="fixtures_body" class="fixtures_grid_wrapper">
              <?php
              while($row = $results->fetch_assoc()) {
                  $date = date_create($row["matchDate"]);
                  $time = date_create($row["matchTime"]);
                  echo '<div class="fixtures_item" data-week="'.$row["weekID"].'" data-home="'.$row["homeID"].'" data-away="'.$row["awayID"].'">
                    <p class="f_wid">WK'.$row["weekID"].'</p>';
                  
                  if($row["score1"] === NULL && $row["score2"] === NULL) {
                      echo '<input class="fixtures_item_radio" type="radio" name="matchID" value="'.$row["matchID"].'" onchange="enableInputs('.$row["homeID"].', '.$row["awayID"].', '.$row["weekID"].')">';
                  }
                  
                  echo '<div class="fixtures_top">
                    '.date_format($date,"d/m/y").' '.date_format($time,"g:ia").' @ <a href="venue.php?venue='.$row["venueID"].'">'.$row["venueName"].'</a>
                      </div><div class="fixtures_i">
                          <div class="fixtures_left">
                              <div class="fixtures_inner">
                                  <p class="f_h">HOME</p>
                                  <img src="images/'.$row["homeEmbl"].'">
                                  <p class="f_n">'.$row["homeName"].'</p>
                              </div>
                          </div><div class="fixtures_right">
                              <div class="fixtures_inner">
                                  <p class="f_h">AWAY</p>
                                  <img src="images/'.$row["awayEmbl"].'">
                                  <p class="f_n">'.$row["awayName"].'</p>
                              </div>
                          </div>
                          <p class="f_s">'.$row["score1"]." - ".$row["score2"].'</p>
                      </div>
                  </div>'; 
              } ?>
                  
              </div></div>
              
              <!-- <table class="fixtures_view">
                  <thead>
                      <tr>
                          <th></th>
                          <th>Week</th>
                          <th></th>
                          <th>Home Team</th>
                          <th></th>
                          <th>Away Team</th>
                          <th>Date</th>
                          <th>Kick-Off</th>
                          <th>Venue</th>
                          <th>Score</th>
                      </tr> 
                  </thead>
                  <tbody id="fixtures_body">
                      
                      // while($row = $results->fetch_assoc()) { 
                      //     echo "<tr data-week=\"".$row["weekID"]."\" data-home=\"".$row["homeID"]."\" data-away=\"".$row["awayID"]."\">
                      //       <td><input type=\"radio\" name=\"matchID\" value=\"".$row["matchID"]."\" onchange=\"enableInputs(".$row["homeID"].", ".$row["awayID"].")\"";
                      //     if($row["score1"] != NULL || $row["score2"] != NULL) echo " disabled";
                      //     echo "> </td>
                      //       <td>".$row["weekID"]."</td>
                      //       <td><img src=\"images/".$row["homeEmbl"]."\" height=40></td>
                      //       <td>".$row["homeName"]."</td>
                      //       <td><img src=\"images/".$row["awayEmbl"]."\" height=40></td>
                      //       <td>".$row["awayName"]."</td>
                      //       <td>".$row["matchDate"]."</td>
                      //       <td>".$row["matchTime"]."</td>
                      //       <td><a href=\"venue.php?venue=".$row["venueID"]."\">".$row["venueName"]."</a></td>
                      //       <td>".$row["score1"]."-".$row["score2"]."</td>
                      //     </tr>";
                      } 
                  </tbody>
              </table> -->
          </form>
      <?php else: ?>
          <p>Error displaying fixtures: No results found.</p>
      <?php endif; ?>
      <script type="text/javascript">
          updateFixturesWithWeek(<?php
            $sel = false;
            if(isset($_GET["success"])) echo $_GET["success"];
            else echo $_SESSION["weekID"]; 
          
          ?>);
      </script>
          
      </main>
  </body>
</html>
