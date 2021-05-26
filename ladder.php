<?php
    
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
          <table class="ladder">
              <thead>
                  <tr>
                      <th>#</th>
                      <th></th>
                      <th>Team</th>
                      <th>P</th>
                      <th>W</th>
                      <th>D</th>
                      <th>L</th>
                      <th>GF</th>
                      <th>GA</th>
                      <th>GD</th>
                      <th>PTS</th>
                      <th>Last 5</th>
                  </tr>
              </thead>
              <tbody>
                  <?php $count = 0; 
                  while($row = $results->fetch_assoc()) { ?>
                      <tr>
                          <td><?php echo ++$count; ?></td>
                          <td><img src="images/<?php echo $row['emblem']?>" height="40"></td>
                          <td class="ladder_tn"><?php echo $row['teamName']; ?></td>
                          <td><?php echo $row['played'] ?></td>
                          <td><?php echo $row['won'] ?></td>
                          <td><?php echo $row['lost'] ?></td>
                          <td><?php echo $row['drawn'] ?></td>
                          <td><?php echo $row['goalsFor'] ?></td>
                          <td><?php echo $row['goalsAgainst'] ?></td>
                          <td><?php echo $row['goalDiff'] ?></td>
                          <td><?php echo $row['points'] ?></td>
                          <td>
                            <?php 
                                $ga = explode (',', $row['games']);
                                foreach($ga as $g) echo $g > 0 ? 'W' : ($g == 0 ? 'D' : 'L');
                                 
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
