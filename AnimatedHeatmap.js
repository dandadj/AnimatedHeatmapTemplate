
function InitializeAnimatedHeatmap(all_frames, frame_labels, millisecondsBetweenAnimations, heatmapID, 
                                    toggleMapButtonID, changeGradientButtonID, 
                                    changeRadiusButtonID, changeOpacityButtonID, sliderID){
    var map, pointArray, heatmap;
    var pa_counter = 0;
    var data_chunks = all_frames.length;
    var toggling;
    
    function toggleHeatmapAnimation() {
        if(toggling == null){
            toggling = setInterval(function (){
                pa_counter += 1;
                if(pa_counter >= all_frames.length){pa_counter = 0;}
                pointArray.clear();
                for(i = 0; i < all_frames[pa_counter].length; i++){
                    pointArray.push(all_frames[pa_counter][i]);
                }

                $("input[name=slider]")[0].value = pa_counter;
                $("#frameLabel").text(frame_labels[pa_counter]);

            }, millisecondsBetweenAnimations);
        }
        else{
            clearInterval(toggling);
            toggling = null;
        }
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
        if(toggling != null){
            toggleHeatmapAnimation();
        }
        pa_counter = parseInt(value);
        pointArray.clear();
        for(i = 0; i < all_frames[pa_counter].length; i++){
            pointArray.push(all_frames[pa_counter][i]);
        }
        $("#frameLabel").text(frame_labels[pa_counter]);
    }
    
    var mapOptions = {
        //zoom: 10,
        //center: new google.maps.LatLng(41.83385, -87.68155),
        mapTypeId: google.maps.MapTypeId.ROAD
    };

    map = new google.maps.Map($(heatmapID)[0],
                              mapOptions);

    $("#frameLabel").text(frame_labels[0]);
    pointArray = new google.maps.MVCArray(all_frames[0].slice(0));
    heatmap = new google.maps.visualization.HeatmapLayer({data: pointArray});
    heatmap.setMap(map);
    
    var bounds = new google.maps.LatLngBounds();
    for(i = 0; i < all_frames.length; i++){
        for(j = 0; j < all_frames[i].length; j++){
            bounds.extend(all_frames[i][j]);
        }
    }
    
    map.setCenter(bounds.getCenter());
    map.fitBounds(bounds);
    
    //Setup click events
    $(toggleMapButtonID).click(function(){toggleHeatmapAnimation();});
    $(changeGradientButtonID).click(function(){changeGradient();});
    $(changeRadiusButtonID).click(function(){changeRadius();});
    $(changeOpacityButtonID).click(function(){changeOpacity();});
    $(sliderID).on("change mousemove", function(){sliderChanged($(this).val());});
}







