<?php
  $qual = file_get_contents($argv[1]);
  $tidyArray = explode("\r\n",$qual);
  $tidyArray = array_map('explode_tab',$tidyArray);

  file_put_contents('../data/standing1.json', json_encode($tidyArray,true));
  echo json_last_error_msg();

  function explode_tab($a){
    $tempArray = explode("\t",$a);
    $result = array();
    $result['name'] = $tempArray[2];
    $result['pts'] = $tempArray[13];    $result['first'] = $tempArray[15];
    $result['second'] = $tempArray[16];
    $result['third'] = $tempArray[17];

    return $result;  }
 ?>
