<?php
require_once("nocache.php");

$error = "";

if(isset($_GET["set"])) $error = "You must log in first before viewing that page.";

if(isset($_POST["submit"])) {
    if(empty($_POST['email']) || empty($_POST['password'])) $error = "You must enter both a username and password.";
    else {
        require_once("conn.php");
        
        $email = $_POST['email'];
        $pwd = $_POST['password'];
        
        $hash = hash('sha256', $pwd);
        
        $sql = "SELECT firstname, surname, password FROM leagueadmin WHERE email = ?";
        $statement = $dbConn->prepare($sql) or die ('Problem preparing: ' . $dbConn->error);
        $statement->bind_param('s', $email);
        $statement->execute();
        $results = $statement->get_result() or die ('Problem querying: ' . $dbConn->error);
        
        if(!$results || mysqli_num_rows($results) == 0) $error = "Invalid email.";
        else {
            $row = $results->fetch_assoc();
            if($row["password"] != $hash) $error = "Invalid password.";
            else {
                // logged in
                session_start();
                $_SESSION["firstname"] = $row["firstname"];
                $_SESSION["surname"] = $row["surname"];
                
                header("Location: scoreEntry.php");
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>A-League - Login</title>
    <link rel="stylesheet" href="css/projectMaster.css">

  </head>

  <body>
      <?php include("nav.php") ?>
      
      <main>
          <h1>A-League - Login</h1>
          <?php if($error != '') echo '<p class="errorMsg"> '.$error.'</p>'; ?>
          <form class="" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
              <div class="login_panel">
                  <!-- <div class="login_panel_inner"> -->
                      <label for="email">EMAIL:</label>
                      <input type="text" id="email" name="email" value="" placeholder="bobjane@tmart.com">
                      <label style="margin-top: 20px;" for="password">PASSWORD:</label>
                      <input type="password" id="password" name="password" value=""><br>
                      <input class="login_button" type="submit" name="submit" value="Login">
                  <!-- </div> -->
              </div>
          </form>
      </main>
  </body>
</html>
