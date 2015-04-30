<?php    
    $HotelInfoFile = "data/HotelInfo.csv";
    //$hotelids = trim(strtolower($_GET["hotelids"]));
    //$hotelids = $_GET["hotelids"];
    $results = array();

    if(file_exists($HotelInfoFile)){        
        $file = fopen($HotelInfoFile, 'r');
        
        while (($line = fgetcsv($file)) !== FALSE) {
          //$line is an array of the csv elements
            $results[] = array("HotelID"=>$line[0],
                           "Name"=>$line[1],
                           "Price"=>$line[2],
                           "latitude"=>$line[3],                               
                           "longitude"=>$line[4],
                           "URL"=>$line[5]);
        }
        fclose($file);        
    }

    echo json_encode($results);
?>