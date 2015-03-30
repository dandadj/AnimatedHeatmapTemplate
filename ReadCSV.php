<?php
    $file="c:/inetpub/wwwroot/Data/NewYorkReviews.csv";
    $csv= file_get_contents($file);    
    $array = array_map("str_getcsv", explode("\n", trim($csv)));
    echo json_encode($array);
?>