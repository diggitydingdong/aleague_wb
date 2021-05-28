<?php

    session_start();
    require_once("conn.php");
    
    $sql = 
    "SELECT *, (
        SELECT 
        SUBSTRING_INDEX(GROUP_CONCAT(
            (score1-score2) * (CASE homeTeam WHEN teamID THEN 1 ELSE -1 END)
            
            ORDER BY matchDate DESC, matchTime DESC), ',', 5) 
        AS result FROM fixtures
        WHERE homeTeam = teamID OR awayTeam = teamID
    ) AS games
    FROM teams ORDER BY points DESC, goalDiff DESC;";
    $results = $dbConn->query($sql) or die ('Problem with query: ' . $dbConn->error);
    
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>A-League - Ladder</title>
    <link rel="stylesheet" href="css/projectMaster.css">
    <link rel="stylesheet" href="javascript/projectScript.js">

  </head>

  <body>
      <?php include("nav.php"); ?>
      
      <main>
          <?php if(mysqli_num_rows($results) != 0): ?>
              <h1>A-League Ladder</h1>
          <table class="ladder">
              <thead>
                  <tr>
                      <th class="table-tl">#</th>
                      <th></th>
                      <th>Team</th>
                      <th class="hide_2">P</th>
                      <th class="hide_2">W</th>
                      <th class="hide_2">D</th>
                      <th class="hide_2">L</th>
                      <th class="hide_2">GF</th>
                      <th class="hide_2">GA</th>
                      <th>GD</th>
                      <th>PTS</th>
                      <th class="table-tr hide_1">Last 5</th>
                  </tr>
              </thead>
              <tbody>
                  <?php $count = 0; 
                  while($row = $results->fetch_assoc()) { ?>
                      <tr>
                          <td><?php ++$count;
                          if($count == 1) echo '<img src="images/crown.svg" height=30 title="Win" style="padding: 5px 0;">';
                          else echo $count; ?></td>
                          <td><img src="images/<?php echo $row['emblem']?>" height="40"></td>
                          <td class="ladder_tn"><?php echo $row['teamName']; ?></td>
                          <td class="hide_2"><?php echo $row['played'] ?></td>
                          <td class="hide_2"><?php echo $row['won'] ?></td>
                          <td class="hide_2"><?php echo $row['lost'] ?></td>
                          <td class="hide_2"><?php echo $row['drawn'] ?></td>
                          <td class="hide_2"><?php echo $row['goalsFor'] ?></td>
                          <td class="hide_2"><?php echo $row['goalsAgainst'] ?></td>
                          <td><?php echo $row['goalDiff'] ?></td>
                          <td><?php echo $row['points'] ?></td>
                          <td class="hide_1">
                            <?php 
                                $ga = explode (',', $row['games']);
                                foreach($ga as $g) {
                                    echo $g > 0 ? '<img src="images/check.svg" height=30 title="Win" style="padding: 0 5px;">' : 
                                        ($g == 0 ? '<img src="images/draw.svg" height=25 title="Draw" style="padding: 0 5px;">' : 
                                        '<img src="images/cross.svg" height=25 title ="Loss" style="padding: 0 5px;">');
                                }
                                
                            ?>
                          </td>
                      </tr>
                  <?php } ?>
              </tbody>
          </table>
      <?php else: ?>
          <p>Error displaying ladder: No results found.</p>
      <?php endif; ?>
      </main>
  </body>
</html>
