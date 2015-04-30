var map, pointArray, heatmap;
var pa_counter = 0;
var toggling;
var slow_speed = 0;
var medium_speed = 0;
var fast_speed = 0;
var current_speed = 0;
var all_frames = null;
var frame_labels = null;
var all_markers = [];
var current_info_window = null;
var markers_on = true;

function AddMarkers(markers) {
    for (var i = 0; i < markers.length; i++){
        var marker = markers[i];
        all_markers.push(marker);
        
        var hotel_id = marker["HotelID"];
        var name = marker["Name"];
        var price = marker["Price"];
        var url = marker["URL"];
        var overall = marker["Overall"];
        
        var info_content = '<h3>' + name + '</h3>' + 
            '<a href="http://tripadvisor.com' + url + '" target="_blank">Trip Advisor Link</a>' + 
            '<h4>Price: ' + price + '</h4>' +
            '<h4>Rating: ' + overall + '</h4>';
        var info_window = new google.maps.InfoWindow();

        google.maps.event.addListener(marker, 'mouseover', (function(marker, info_content, info_window){
                                    return function(){
                                        info_window.setContent(info_content);
                                        info_window.open(map,marker);
                                        
                                        var out_listener = google.maps.event.addListener(marker, 'mouseout', (function(marker,info_content,info_window){ 
                                            return function() {
                                                info_window.close();
                                            };
                                        })(marker,info_content,info_window));
                                        
                                        google.maps.event.addListener(marker, 'click', (function(marker,info_content,info_window, out_listener){ 
                                            return function() {
                                                google.maps.event.removeListener(out_listener);
                                            };
                                        })(marker,info_content,info_window, out_listener));                                        
                                    };
        })(marker, info_content, info_window));
    }
}

function loopHeatmapAnimation() {
    if(toggling == null){
        toggling = setInterval(function (){
            pa_counter += 1;
            if(pa_counter >= all_frames.length){pa_counter = 0;}
            pointArray = [];        
            pointArray = all_frames[pa_counter];
            heatmap.setData(pointArray);

            $("input[name=slider]")[0].value = pa_counter;
            $("#frameLabel").text(frame_labels[pa_counter]);

        }, current_speed);
    }
    else{
        clearInterval(toggling);
        toggling = null;
    }
}

function playHeatmapAnimation() {
    clearInterval(toggling);
    toggling = null;
    pa_counter = 0;
    pointArray = all_frames[pa_counter];
    heatmap.setData(pointArray);
    toggling = setInterval(function (){
        pa_counter += 1;
        if(pa_counter >= all_frames.length){
            pa_counter -= 1;
            clearInterval(toggling);
            toggling = null;
            return;
        }
        pointArray = [];        
        pointArray = all_frames[pa_counter];
        heatmap.setData(pointArray);

        $("input[name=slider]")[0].value = pa_counter;
        $("#frameLabel").text(frame_labels[pa_counter]);

    }, current_speed);                
}

function changeGradient() {
    var gradient = [
        'rgba(0, 255, 255, 0)', 'rgba(0, 255, 255, 1)', 'rgba(0, 191, 255, 1)',
        'rgba(0, 127, 255, 1)', 'rgba(0, 63, 255, 1)', 'rgba(0, 0, 255, 1)',
        'rgba(0, 0, 223, 1)', 'rgba(0, 0, 191, 1)', 'rgba(0, 0, 159, 1)',
        'rgba(0, 0, 127, 1)', 'rgba(63, 0, 91, 1)', 'rgba(127, 0, 63, 1)',
        'rgba(191, 0, 31, 1)', 'rgba(255, 0, 0, 1)'];

    heatmap.set('gradient', heatmap.get('gradient') ? null : gradient); 
}

function changeRadius() {
    heatmap.set('radius', heatmap.get('radius') ? null : 20);
}

function changeOpacity() {
    heatmap.set('opacity', heatmap.get('opacity') ? null : 0.2);    
}

function toggleMarkers(){
    markers_on = !markers_on;
    if(!markers_on){
        for(var i = 0; i < all_markers.length; i++){
            all_markers[i].setMap(null);
        }
    }
    else{
        viewportMarkers();
    }
}

function sliderChanged(value){
    // If the slider value is the same as before, exit
    temp_slider_val = parseInt(value);        
    if(temp_slider_val == pa_counter){return;}
    if(toggling != null){
        loopHeatmapAnimation();
    }



    pa_counter = temp_slider_val;
    pointArray = [];        
    pointArray = all_frames[pa_counter];
    heatmap.setData(pointArray);
    $("#frameLabel").text(frame_labels[pa_counter]);
}

function changeSpeed(value){
    switch(value){
        case "slow":
            current_speed = slow_speed;
            break;
        case "medium":
            current_speed = medium_speed;
            break;
        case "fast":
            current_speed = fast_speed;
            break;
        default:
            current_speed = medium_speed;
    }
}

function viewportMarkers() {
    if(!markers_on){
        return;
    }
    var bounds = map.getBounds();

    var markers_in_viewport = [];
    for(var i = 0; i < all_markers.length; i++){
        // If it's in the bounds keep it to consider for top N
        if(bounds.contains(all_markers[i].getPosition())){
            markers_in_viewport.push(all_markers[i]);
        }
        all_markers[i].setMap(null);        
    }
    var hotel_ids_in_viewport = markers_in_viewport.map(function(m){return m["hotelID"]});
    
    markers_in_viewport.sort(function(a,b){return b["Overall"] - a["Overall"]});
    for(var i = 0; i < Math.min(10, markers_in_viewport.length); i++){
        markers_in_viewport[i].setMap(map);        
    }
    
  }

function ChangeHeatmapData(allFrames, frameLabels){
    all_frames = allFrames;
    frame_labels = frameLabels;
    
    $("#frameLabel").text(frame_labels[0]);
    pointArray = all_frames[0].slice(0);
    
    heatmap = new google.maps.visualization.HeatmapLayer({data: pointArray});
    heatmap.setMap(map);
    heatmap.set('maxIntensity', 5); 
    
    var bounds = new google.maps.LatLngBounds();
    for(i = 0; i < all_frames.length; i++){
        for(j = 0; j < all_frames[i].length; j++){
            bounds.extend(all_frames[i][j]["location"]);
        }
    }
    
    map.setCenter(bounds.getCenter());
    map.fitBounds(bounds);
}

function InitializeAnimatedHeatmap(millisecondsBetweenAnimations, heatmapID, 
                                    loopMapButtonID, playMapButtonID, changeGradientButtonID, 
                                    changeRadiusButtonID, changeOpacityButtonID, sliderID,
                                    speedSelectDivID, toggleMarkersButtonID) {    
    
    slow_speed = millisecondsBetweenAnimations * 4;
    medium_speed = millisecondsBetweenAnimations;
    fast_speed = millisecondsBetweenAnimations / 4;
    current_speed = medium_speed;
    
    
    
    var mapOptions = {
        mapTypeId: google.maps.MapTypeId.ROAD
    };

    map = new google.maps.Map($(heatmapID)[0],
                              mapOptions);
    
    //Setup click events
    $(loopMapButtonID).click(function(){loopHeatmapAnimation();});
    $(playMapButtonID).click(function(){playHeatmapAnimation();});
    $(changeGradientButtonID).click(function(){changeGradient();});
    $(changeRadiusButtonID).click(function(){changeRadius();});
    $(changeOpacityButtonID).click(function(){changeOpacity();});
    $(sliderID).on("change mousemove", function(){sliderChanged($(this).val());});
    $(speedSelectDivID).children("input").each(function(){
        $(this).change(function(){changeSpeed($(this).val());});
    });
    $(toggleMarkersButtonID).click(function(){toggleMarkers();});
    
    google.maps.event.addListener(map, 'idle', viewportMarkers);
    
}
