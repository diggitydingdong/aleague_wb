<nav>
    <ul>
        <li id="logo"><a href="index.php">A-League</a></li>
        <li><a href="ladder.php">Ladder</a></li>
        <li><a href="fixtures.php">Fixtures</a></li>
        <?php if(isset($_SESSION["firstname"])):?>
            <li id="enter-results"><a href="scoreEntry.php">Enter Results</a></li>
        <?php endif; ?>
        <li id="log"><?php 
            if(!isset($_SESSION["firstname"])):?>
                <a href="login.php">Login</a> 
            <?php else:?>
                <span class="hide_2">Hello, <?php echo $_SESSION["firstname"]." ".$_SESSION["surname"]; ?> | </span><a href="logoff.php">Logoff</a>
            <?php endif;?></li>
    </ul>
</nav>