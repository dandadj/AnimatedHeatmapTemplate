<?php
    $servername = "yanen.li";
    $username = "wang296";
    $password = "Whn1984523";
    $dbname = "HotelReviews";
    $query = trim(strtolower($_GET["query"]));
    $query = preg_replace('!\s+!', ' ', $query);

    // Check if query has been cached already
    $cached = false;
    // Already cached, so read from file
    if($cached){
        exit("not implemented");
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

        $quarter_starts = [date('Y-m-d', strtotime("01/01/2010")),
                           date('Y-m-d', strtotime("04/01/2010")),
                           date('Y-m-d', strtotime("07/01/2010")),
                           date('Y-m-d', strtotime("10/01/2010")),
                           date('Y-m-d', strtotime("01/01/2011")),
                           date('Y-m-d', strtotime("04/01/2011")),
                           date('Y-m-d', strtotime("07/01/2011")),
                           date('Y-m-d', strtotime("10/01/2011"))];
        $json_response = array();
        
        for($i = 0; $i < length($quarter_starts) - 1, $i++){
            
        }

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $json_response[] = array("Rating"=>$row["Rating"], "latitude"=>$row["latitude"], "longitude"=>$row["longitude"]);
            }
        }
        echo json_encode($json_response);
        $conn->close();
    }
    
?>