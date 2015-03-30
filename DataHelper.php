<?php
    $servername = "yanen.li";
    $username = "wang296";
    $password = "Whn1984523";
    $dbname = "HotelReviews";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 

    $sql = "select * from HotelReviews.HotelsNew where latitude <> 0 and longitude <> 0 and Address like '%New York%';";

    $result = $conn->query($sql);

    $json_response = array();

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $json_response[] = array("Rating"=>$row["Rating"], "latitude"=>$row["latitude"], "longitude"=>$row["longitude"]);
        }
    } else {
        echo "0 results";
    }
    echo json_encode($json_response);
    $conn->close();
?>