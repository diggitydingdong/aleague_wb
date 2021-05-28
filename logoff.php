<?php
   require_once("nocache.php");
   session_start();
   session_destroy();
   header("location: login.php");
?>