<?php
    session_start();
    if(!isset($_SESSION["weekID"])) header("Location: index.php?set=1");
    require_once("conn.php");
    
    $sql = "SELECT weekID, home.emblem as homeEmbl, home.teamName as homeName, away.emblem as awayEmbl, away.teamName as awayName, matchDate, matchTime, venues.venueName as venueName, score1, score2, home.teamID as homeID, away.teamID as awayID, venues.venueID as venueID FROM fixtures 
            INNER JOIN teams as home ON homeTeam = home.teamID
            INNER JOIN teams as away ON awayTeam = away.teamID
            INNER JOIN venues ON fixtures.venueID = venues.venueID;";
    $statement = $dbConn->prepare($sql) or die ('Problem preparing: ' . $dbConn->error);
    $statement->execute();
    
    $results = $statement->get_result();
    
    $sql = "SELECT teamID, teamName FROM teams ORDER BY teamName;";
    $teams = $dbConn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>A-League - Fixtures</title>
    <link rel="stylesheet" href="css/projectMaster.css">
    <link rel="stylesheet" href="css/fixtures.css">
    
    <script type="text/javascript" src="javascript/projectScript.js" /></script>
    <!-- <link rel="stylesheet" href="css/fixtures.css"> -->
  </head>

  <body>
      <?php include("nav.php") ?>
      
      <main><?php if(mysqli_num_rows($results) != 0): ?>
          <h1>A-League Fixtures</h1>
          
          <div class="fixtures_selections">
              <div class="fixtures_radios">
                  <label for="fr_week">Week Fixture View</label>
                  <input type="radio" id="fr_week" name="view_type" value="week" onchange="updateView(true, <?php echo $_SESSION["weekID"]; ?>)" checked>
                  <br>
                  <label for="fr_team">Team Fixture View</label>
                  <input type="radio" id="fr_team" name="view_type" value="team" onchange="updateView(false, <?php echo $_SESSION["weekID"]; ?>)">
              
              <div id="fixtures_week_group">
                  <label for="fixtures_week"><h4>Week:</h4></label>
                  <select style="width: 100px;" id="fixtures_week" onchange="updateFixturesWithWeek(this.value)">
                      <?php
                      for($i = 1; $i <= 24; $i++) {
                          
                          echo '<option value="'.$i.'"';
                          if($i == $_SESSION["weekID"]) echo ' selected';
                          echo '>WK'.$i.'</option>';
                      }
                      ?>
                      
                  </select>
              </div>
              
              <div id="fixtures_team_group" hidden>
                  <label for="fixtures_team"><h4>Team:</h4></label>
                  <select style="width: 200px;" id="fixtures_team" onchange="updateFixturesWithTeam(this.value, <?php echo $_SESSION["weekID"]; ?>)">
                      <?php 
                         while($row = $teams->fetch_assoc()) {
                             echo '<option value="'.$row["teamID"].'">'.$row["teamName"].'</option>';
                         } 
                      ?>
                  </select>
              </div>
          </div>
              
          </div>
          
          <div class="fixtures_view"><div id="fixtures_body" class="fixtures_grid_wrapper">
              <?php
              while($row = $results->fetch_assoc()) {
                  $date = date_create($row["matchDate"]);
                  $time = date_create($row["matchTime"]);
                  echo '<div class="fixtures_item" data-week="'.$row["weekID"].'" data-home="'.$row["homeID"].'" data-away="'.$row["awayID"].'">
                      <p class="f_wid">WK'.$row["weekID"].'</p>
                      <div class="fixtures_top">
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
                  <?php
                  // while($row = $results->fetch_assoc()) { 
                  //     echo "<tr data-week=\"".$row["weekID"]."\" data-home=\"".$row["homeID"]."\" data-away=\"".$row["awayID"]."\">
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
                  // } ?>
              </tbody>
          </table> -->
      <?php else: ?>
          <p>Error displaying fixtures: No results found.</p>
      <?php endif; ?>
      <script type="text/javascript">
          updateFixturesWithWeek(<?php echo $_SESSION["weekID"];?>);
      </script>
      </main>
  </body>
</html>
