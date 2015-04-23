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
            
            function GetHotelInfo(hotelIDs){
                $.ajax({
                      url:'HotelInfo.php',
                      data:{hotelids:hotelIDs},
                      type:'GET',
                      success: function (response) {
                          var hotel_infos = JSON.parse(response);
                          alert(hotel_infos.length);
                          
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
                                  var lat = point["latitude"];
                                  var lng = point["longitude"];
                                  if(isNaN(lat) || isNaN(lng)){continue;}
                                  var lat_lng = new google.maps.LatLng(lat, lng);
                                  all_frames[i].push({location: lat_lng, weight: Number(point["Overall"])});                                  
                              }
                          }
                          
                          $("#frameLabel").text(frame_labels[0]);
                          InitializeAnimatedHeatmap(all_frames, frame_labels, 500, "#map-canvas", "#map1-toggle-button", 
                                                    "#map1-change-gradiant-button", "#map1-change-radius-button", 
                                                      "#map1-change-opacity-button", "#map1-slider");
                          },
                      error: function(xhr, status, error) {
                          alert(xhr.responseText);
                      }
                  });
            }
            
            google.maps.event.addDomListener(window, 'load', function(){
                QueryDB("new york");
                
            });
        </script>        
    </head>

    <body>
        <div class="panel" id="button-panel">
            <button id="map1-toggle-button">Toggle Heatmap Animation</button>
            <button id="map1-change-gradiant-button">Change gradient</button>
            <button id="map1-change-radius-button">Change radius</button>
            <button id="map1-change-opacity-button">Change opacity</button>
            <br>
            Enter search location: <input type="text" id="SearchLocation" name="searchLocation" value="New York">
            <input type="submit" value="Search" onclick="SearchForLocation()">
            
        </div>
        <div id="map-canvas"></div>
        <div class="panel" id="slider-panel">
            <span>Heatmap Animation Slider</span>
            <br>            
            <input id="map1-slider" type="range" name="slider" min="0" max="0" value="0"/>
            <br>
            <span id="frameLabel"></span>
        </div>        
    </body>
</html>