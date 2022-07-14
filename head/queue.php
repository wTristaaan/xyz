<?php 
include "functions.php";
session_start();
/**
 * launch js script who download spotify playlist and got all information needed to make a queue
 */
exec("node ../js/queueDl.js --spPlaylist=" . $_POST["spPlaylist"], $infosPlaylist, $error);
$queue = makeAQueue($infosPlaylist);
$_SESSION["queue"] = $queue;
header("Location: ../index.php");