<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Heatmaps</title>
        <link rel="stylesheet" type="text/css" href="styles/style.css">        
        <script src="AnimatedHeatmap.js" type="text/javascript"></script>
        <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=visualization"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/themes/smoothness/jquery-ui.css" />
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
        <script>
            
            function SearchForLocation(){                
                var search_location = $("#SearchLocation")[0].value;                
                QueryDB(search_location);
            }
            
            var all_hotel_infos = {};
            
            function GetHotelInfo(){
                $.ajax({
                      url:'HotelInfo.php',
                      type:'GET',
                      success: function (response) {
                          var hotel_infos = JSON.parse(response);
                          for(var i = 0; i < hotel_infos.length; i++){
                              var hotel_id = hotel_infos[i]["HotelID"];
                              all_hotel_infos[hotel_id] = hotel_infos[i];
                          }
                          QueryDB("Chicago");
                      },
                      error: function(xhr, status, error) {
                          alert(xhr.responseText);
                      }
                  });
            }
            
            function QueryDB(location){
                $.ajax({
                      url:'QueryWithCache.php',
                      data:{query:location},
                      type:'GET',
                      success: function (response) {
                          var points = JSON.parse(response);
                          var quarters = $.map(points, function(p){return p["Date"]});
                          var unique_dates = $.unique(quarters);
                          
                          //var query_hotel_ids = $.map(points, function(p){return p["HotelID"]});
                          //var unique_hotel_ids = $.unique(query_hotel_ids);
                          
                          var markers = [];
                          var unique_hotels = {};
                          
                          var min_slider = unique_dates.length - 1;                          
                          document.getElementById("map1-slider").max = min_slider;
                          
                          var all_frames = [];
                          var frame_labels = [];
                          for(i in unique_dates){
                              all_frames.push([]);
                              frame_labels.push(unique_dates[i]);
                              
                              var points_in_range = points.filter(function(row){
                                  return row["Date"] == unique_dates[i];                                  
                              });
                              
                              for(point of points_in_range){
                                  var hotel_id = point["HotelID"];
                                  var lat_lng = new google.maps.LatLng(point["latitude"], point["longitude"]);
                                  var overall = point["Overall"];
                                  if(!(hotel_id in unique_hotels)){
                                      unique_hotels[hotel_id] = true;
                                      var marker = new google.maps.Marker({position: lat_lng, HotelID: hotel_id, 
                                           URL: all_hotel_infos[hotel_id]["URL"], Overall: overall,
                                          Name: all_hotel_infos[hotel_id]["Name"], Price: all_hotel_infos[hotel_id]["Price"]});
                                       
                                      
                                      markers.push(marker);
                                  }                                  
                                  
                                  var lat = point["latitude"];
                                  var lng = point["longitude"];
                                  if(isNaN(lat) || isNaN(lng)){continue;}
                                  var lat_lng = new google.maps.LatLng(lat, lng);
                                  all_frames[i].push({location: lat_lng, weight: Number(overall), HotelID: hotel_id});
                              }
                          }
                          
                          $("#frameLabel").text(frame_labels[0]);                          
                          
                          ChangeHeatmapData(all_frames, frame_labels);
                          
                          AddMarkers(markers);
                          
                          },
                      error: function(xhr, status, error) {
                          alert(xhr.responseText);
                      }
                  });
            }
            
            google.maps.event.addDomListener(window, 'load', function(){
                InitializeAnimatedHeatmap(500, "#map-canvas", "#map1-loop-button", 
                                                    "#map1-play-button", "#map1-change-gradiant-button", 
                                                    "#map1-change-radius-button", "#map1-change-opacity-button", "#map1-slider",
                                                    "#speedSelection", "#map1-toggle-markers-button");
                GetHotelInfo();
                
                
            });
        </script>        
    </head>

    <body>
        <div class="panel" id="button-panel">            
            <button id="map1-change-gradiant-button">Change gradient</button>
            <button id="map1-change-radius-button">Change radius</button>
            <button id="map1-change-opacity-button">Change opacity</button>
            <button id="map1-toggle-markers-button">Toggle Markers</button>
            <br>
            Enter search location: <input type="text" id="SearchLocation" name="searchLocation" value="Chicago" onkeydown="if (event.keyCode == 13) document.getElementById('locationSearch').click()">
            <input id="locationSearch" type="submit" value="Search" onclick="SearchForLocation()">
            
        </div>
        <div id="map-canvas"></div>
        <div class="panel" id="slider-panel">
            <span>Heatmap Animation Slider</span>
            <br>            
            <input id="map1-slider" type="range" name="slider" min="0" max="0" value="0"/>
            <br>
            <span id="frameLabel"></span>
            <div id="speedSelection">
                <input type="radio" name="speed" value="slow">Slow
                <input type="radio" name="speed" value="medium" checked>Medium
                <input type="radio" name="speed" value="fast">Fast
            </div>
            <br>
            <button id="map1-loop-button">Loop Heatmap Animation</button>
            <button id="map1-play-button">Play Heatmap Animation</button>
        </div>        
    </body>
</html>