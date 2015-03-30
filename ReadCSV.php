<?php
    $file="c:/inetpub/wwwroot/Data/NewYorkReviews.csv";
    $csv= file_get_contents($file);    
    $array = array_map("str_getcsv", explode("\n", trim($csv)));
    $grouped = array();
    $counter = 0;
    foreach($array as $row){
        // Skip the first row
        if ($counter == 0){
            $counter = $counter + 1;
            continue;
        }
        if(array_key_exists($row[0], $grouped) == false){            
            $grouped[$row[0]] = array();            
        }
        $grouped[$row[0]][] = $row;
    }

    $results = array();
    foreach($grouped as $group){
        $working_sum = 0;
        foreach($group as $row){
            $working_sum = $working_sum + $row[5];
        }
        $results[] = [$group[0][0], $group[0][1], $group[0][2], $group[0][3], $group[0][4], $working_sum / count($group)];
    }

    echo json_encode($results);
?>