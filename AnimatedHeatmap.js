
function InitializeAnimatedHeatmap(all_frames, frame_labels, millisecondsBetweenAnimations, heatmapID, 
                                    loopMapButtonID, playMapButtonID, changeGradientButtonID, 
                                    changeRadiusButtonID, changeOpacityButtonID, sliderID,
                                    speedSelectDivID) {
    var map, pointArray, heatmap;
    var pa_counter = 0;
    var data_chunks = all_frames.length;
    var toggling;
    var slow_speed = millisecondsBetweenAnimations * 4;
    var medium_speed = millisecondsBetweenAnimations;
    var fast_speed = millisecondsBetweenAnimations / 4;
    var current_speed = medium_speed;
    
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
    
    var mapOptions = {
        //zoom: 10,
        //center: new google.maps.LatLng(41.83385, -87.68155),
        mapTypeId: google.maps.MapTypeId.ROAD
    };

    map = new google.maps.Map($(heatmapID)[0],
                              mapOptions);

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
    
    //Setup click events
    $(loopMapButtonID).click(function(){loopHeatmapAnimation();});
    $(playMapButtonID).click(function(){playHeatmapAnimation();});
    $(changeGradientButtonID).click(function(){changeGradient();});
    $(changeRadiusButtonID).click(function(){changeRadius();});
    $(changeOpacityButtonID).click(function(){changeOpacity();});
    $(sliderID).on("change mousemove", function(){sliderChanged($(this).val());});
    //$(sliderID).on("change", function(){sliderChanged($(this).val());});
    
    $(speedSelectDivID).children("input").each(function(){
        $(this).change(function(){changeSpeed($(this).val());});
    });
    
    
}







