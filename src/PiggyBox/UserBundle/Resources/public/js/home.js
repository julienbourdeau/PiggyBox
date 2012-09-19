$(document).ready(function(){
 
	$("#map-gmap-content").gmap3({
		action: 'init',
		options:{
			center:[47.21094, -1.538086],
			zoom: 12
		},
		callback: function(){
			$('#dropMarkers').click(dropMarkers);
			//$('#startDance').click(startDance);
			//$('#stopDance').click(stopDance);
		}
	});
});

/*
function dropMarkers(){
	var shops = [ ['47.21396', '-1.58573'], ['47.22916', '-1.57188'], ['47.21488', '-1.55381'] ];
	var i = 1;
	for (key in shops) {
		console.log(key);
		console.log(shops[key][0] + " and " + shops[key][1] + "and i=" + i); 

		$('#map-gmap-content').gmap3({
			action: 'addMarker',
			latLng:[shops[key][0], shops[key][1]],
			options:{
				animation: google.maps.Animation.DROP
			}
		});

		i++;
	}
}  
*/
function dropMarkers(){
	$('#map-gmap-content').gmap3(
		{ action: 'addMarkers',
			markers:[
				{lat:47.21396, lng:-1.58573, data:'Paris !'},
				{lat:47.22916,lng:-1.57188, data:'Poitiers : great city !'},
				{lat:47.21488, lng:-1.55381, data:'Perpignan ! GO USAP !'}
			],
			marker:{
				options:{
					draggable: false
				},
				events:{
					click: function(marker, event, data){
						alert(data);
					}
				}
			}
		}
	);
}


