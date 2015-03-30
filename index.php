<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Heatmaps</title>
        <link rel="stylesheet" type="text/css" href="styles/style.css">        
        <script src="AnimatedHeatmap.js" type="text/javascript"></script>
        <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=visualization"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script>
            google.maps.event.addDomListener(window, 'load', function(){
                
                $.ajax({
                      url:'/DataHelper.php',
                      data:{param:"test param"},
                      type:'GET',
                      success: function (response) {
                          alert(response);
                      },
                      error: function () {
                          alert('Bummer: there was an error!');
                      }
                  });
                
//                $.get("data/CrimesMarch.csv", function(data, status){
//                    var all_frames = [];
//                    var frame_labels = [];
//                    var data_chunks = 10;
//                    var latlng = data.split("\n");
//                    for(i = 0; i < data_chunks; i++){
//                        all_frames.push([]);
//                        frame_labels.push("Frame: " + i);
//                    }
//
//                    //var bounds = new google.maps.LatLngBounds();
//                    var chunk_counter = 0;
//                    for(i = 0; i < latlng.length; i++){
//                        var split_lat_lng = latlng[i].split(",");
//                        var lat = parseFloat(split_lat_lng[0]);
//                        var lng = parseFloat(split_lat_lng[1]);
//                        if(isNaN(lat) || isNaN(lng)){continue;}
//                        var lat_lng = new google.maps.LatLng(lat, lng);
//                        all_frames[chunk_counter].push(lat_lng);                        
//                        chunk_counter = chunk_counter + 1;
//                        if(chunk_counter >= data_chunks){chunk_counter = 0;}
//                        //bounds.extend(lat_lng);
//                    }
//
//                    //map.setCenter(bounds.getCenter());
//                    //map.fitBounds(bounds);
//
//                    $("#frameLabel").text(frame_labels[0]);
//                    //pointArray = new google.maps.MVCArray(all_frames[0].slice(0));
//                    //heatmap = new google.maps.visualization.HeatmapLayer({data: pointArray});
//                    //heatmap.setMap(map);   
//                    InitializeAnimatedHeatmap(all_frames, frame_labels, 500, "#map-canvas", "#map1-toggle-button", 
//                                          "#map1-change-gradiant-button", "#map1-change-radius-button", 
//                                          "#map1-change-opacity-button", "#map1-slider");
//                });
            });
        </script>        
    </head>

    <body>
        <div class="panel" id="button-panel">
        <button id="map1-toggle-button">Toggle Heatmap Animation</button>
        <button id="map1-change-gradiant-button">Change gradient</button>
        <button id="map1-change-radius-button">Change radius</button>
        <button id="map1-change-opacity-button">Change opacity</button>
        </div>
        <div id="map-canvas"></div>
        <div class="panel" id="slider-panel">
            <span>Heatmap Animation Slider</span>
            <br>            
            <input id="map1-slider" type="range" name="slider" min="0" max="9" value="0"/>
            <br>
            <span id="frameLabel"></span>
        </div>        
    </body>
</html>