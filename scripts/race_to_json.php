<?php
  require_once('phpmpr.php');
  $qual = new phpmpr($argv[1]);//path to the qualifying.mpr as parameter
  $drivers = $qual->players;
  //print_r($drivers);  //uncomment to see the structure of the phpmpr-drivers-array
  $tidyArray = array();
  foreach ($drivers as $driver) {
    $driverelement = array(
      'name' => utf8_encode($driver->pname),
      'lfsname' => utf8_encode($driver->lfsuname),
      'time' => $driver->racetime,
      'car'  => $driver->scarname,
      'laps' => $driver->laps
    );
    array_push($tidyArray, $driverelement);
  }

  file_put_contents('../data/race.json', json_encode($tidyArray));
 ?>
