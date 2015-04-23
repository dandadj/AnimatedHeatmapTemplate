<?php
    $servername = "yanen.li";
    $username = "wang296";
    $password = "Whn1984523";
    $dbname = "HotelReviews";
    $CACHED_EXT = "_cached.csv";
    $query = trim(strtolower($_GET["query"]));
    $query = preg_replace('!\s+!', ' ', $query);

    $cached_file_name = "data/". $query. $CACHED_EXT;

    // Already cached, so read from file
    if(file_exists($cached_file_name)){        
        $file = fopen($cached_file_name, 'r');
        $results = array();
        while (($line = fgetcsv($file)) !== FALSE) {
          //$line is an array of the csv elements
            $results[] = array("Date"=>$line[0],
                               "HotelID"=>$line[1],
                               "latitude"=>$line[2],
                               "longitude"=>$line[3],
                               "Overall"=>$line[4]);
        }
        fclose($file);
        echo json_encode($results);
    }
    // Not cached, so perform query
    else{
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 

        $sql = "SELECT HotelReviews.HotelsNew.HotelID, Rating, latitude, longitude, Date, Overall, Value, Rooms, Location, Cleanliness, Service
            FROM HotelReviews.HotelsNew 
            JOIN HotelReviews.ReviewsNew ON HotelReviews.HotelsNew.HotelID=HotelReviews.ReviewsNew.HotelID
            WHERE HotelReviews.HotelsNew.latitude <> 0 and HotelReviews.HotelsNew.longitude <> 0 and HotelReviews.HotelsNew.Address like '%" . $query . "%'";

        $result = $conn->query($sql);

        $date_starts = [date('Y-m-d', strtotime("01/01/2010")),
                           date('Y-m-d', strtotime("04/01/2010")),
                           date('Y-m-d', strtotime("07/01/2010")),
                           date('Y-m-d', strtotime("10/01/2010")),
                           date('Y-m-d', strtotime("01/01/2011")),
                           date('Y-m-d', strtotime("04/01/2011")),
                           date('Y-m-d', strtotime("07/01/2011")),
                           date('Y-m-d', strtotime("10/01/2011")),
                           date('Y-m-d', strtotime("01/01/2012")),
                           date('Y-m-d', strtotime("04/01/2012")),
                           date('Y-m-d', strtotime("07/01/2012"))];
        
        $json_response = array();
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $json_response[] = array("Rating"=>$row["Rating"], 
                                        "latitude"=>$row["latitude"], 
                                        "longitude"=>$row["longitude"],
                                        "HotelID"=>$row["HotelID"],
                                        "Date"=>$row["Date"],
                                        "Overall"=>$row["Overall"]
                                        );
            }
        }
        
        $all_average_rows = array();
        // Loop over each set of date ranges(quarters in this case)
        for($i = 0; $i < count($date_starts) - 1; $i++){
            $averages = array();
            // Loop over each query row
            foreach($json_response as $jrow){
                // See if the row is in the date range, and if so add scores to array
                if($jrow["Date"] > $date_starts[$i] && $jrow["Date"] <= $date_starts[$i + 1]){
                    if(array_key_exists($jrow["HotelID"], $averages) == false){
                        $averages[$jrow["HotelID"]] = array();
                    }
                    $averages[$jrow["HotelID"]][] = $jrow;
                }                
            }            
            
            // Loop over each hotel found in this quarter
            // Going to make score averages
            foreach($averages as $key => $value){
                $overall_sum = 0;
                foreach($value as $vals){
                    $overall_sum = $overall_sum + $vals["Overall"];
                }
                //echo $overall_sum / count($value);
                $all_average_rows[] = array("Date"=>$date_starts[$i + 1],
                                            "HotelID"=>$key,
                                            "latitude"=>$value[0]["latitude"],
                                            "longitude"=>$value[0]["longitude"],
                                            "Overall"=>$overall_sum / count($value));
            }
        }
        
        $cache_file = fopen($cached_file_name, 'w');
        foreach($all_average_rows as $row){
            fputcsv($cache_file, $row);
        }
        fclose($cache_file);
        
        echo json_encode($json_response);
        $conn->close();
    }
    
?>
