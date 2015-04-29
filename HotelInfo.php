<?php    
    $HotelInfoFile = "data/HotelInfo.csv";
    //$hotelids = trim(strtolower($_GET["hotelids"]));
    //$hotelids = $_GET["hotelids"];
    $results = array();

    if(file_exists($HotelInfoFile)){        
        $file = fopen($HotelInfoFile, 'r');
        
        while (($line = fgetcsv($file)) !== FALSE) {
          //$line is an array of the csv elements
            $results[] = array(array("HotelID"=>$line[0],
                           "latitude"=>$line[1],
                           "longitude"=>$line[2],
                           "URL"=>$line[3]));                
        }
        fclose($file);        
    }

    echo json_encode($results);
?>