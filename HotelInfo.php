<?php    
    $HotelInfoFile = "data/HotelInfo.csv";
    $hotelids = trim(strtolower($_GET["hotelids"]));
    $results = array();

    if(file_exists($HotelInfoFile)){        
        $file = fopen($HotelInfoFile, 'r');
        
        while (($line = fgetcsv($file)) !== FALSE) {
          //$line is an array of the csv elements
            $line_hotel_id = $line[0];
            if(in_array($line_hotel, $hotelids)){
                $results[] = array(array("HotelID"=>$line[0],
                               "latitude"=>$line[1],
                               "longitude"=>$line[2],
                               "URL"=>$line[3]));                
            }
        }
        fclose($file);        
    }

    echo json_encode($results);
?>