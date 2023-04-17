// Maximal number of routes you want to process
const NUMBER_ROUTES = 2;

let map, buttonPos, buttonSearchParam;

// Initialization of the map in the window
window.initMap = initMap;

// Global variable for easier access through the application
let prevInfoWindow = null;
let _globalAllMarkers = [];
let _globalUserMarker = null;
let _selectedMarker = undefined;
let _selectedMarkerInfoWindow = undefined;
let _selectedMarkerAddress = "undefined";
let _globalDirectionsService = undefined;
let _globalDirectionsRenderer = [];
let _globalRouteDuration = [];
let _globalSelectedRoute = undefined;

// Default value for the search with the API
let userLocation = {lat:null,lng:null};
let searchRadius = 1;
let nbMaxResults = 50;


/*
    Function that the position of user's marker
*/
async function refreshUserLocation(){
	const posPromise = () => new Promise((resolve, error) => navigator.geolocation.getCurrentPosition(resolve, error));
	try {
		const pos = await posPromise();
		userLocation.lat = pos.coords.latitude;
		userLocation.lng = pos.coords.longitude;
	} catch (error) {
		console.error(error);
	}
}

/* 
    Center the map on the position of the user
*/
function centerMapToUserPos(){
	refreshUserLocation().then(() =>{
		if(userLocation.lat != null && userLocation.lng != null){
			map.setCenter(new google.maps.LatLng(userLocation.lat, userLocation.lng));
			map.setZoom(15);
		} else {
			console.error("position is required but could not be fetched");
			alert("Votre position est requise");
		}
	});
}


/*
    Delete old markers (parkings and address searched), create the new ones and recenter the map around one of them
*/
async function onFetchAddMarkers(data, allMarkers, coordAddress, address){
	if(data.length == 0){
		alert("Aucun parking n'a été trouvé dans cette zone");
		return;
	}
	if (allMarkers.length != 0 || allMarkers != undefined || _globalAllMarkers.length != 0 || _globalAllMarkers != undefined){
		removeAllMarkers(allMarkers);
		removeAllMarkers(_globalAllMarkers);
	}
	if(coordAddress != null && address != null){
		allMarkers.push(placeMarkerAddress(coordAddress, address));
	}
	placeMarkers(allMarkers, data);
	if (allMarkers.length != 0 || allMarkers != undefined){
		const coordFirstMarker = new google.maps.LatLng(data[0].pos.lat, data[0].pos.lng);
		map.setCenter(coordFirstMarker);
		map.setZoom(15);
	}
}

/*
    Research parkings near the user's location
*/
async function searchNearUser(){
	let allMarkers = [];
	let userMarker = null;
	let areaParams = "";

	let msgBox = document.getElementById('searchMsgBox');

	msgBox.innerHTML="Récupération de la position...";
	await refreshUserLocation();
	if(userLocation.lat == null || userLocation.lng == null){
		console.error("position is required but could not be fetched");
		alert("Votre position est requise");
		msgBox.innerHTML="";
		return;
	}

    // replace the user's marker
	userMarker = placeUserMarker();
	_globalUserMarker = userMarker;
	

	try{
		getParkingsData(userLocation, userLocation, areaParams, searchRadius, nbMaxResults).then((data) =>{
			onFetchAddMarkers(data, allMarkers, null, null);
		});
	} catch(err){
		msgBox.innerHTML="";
		console.error("Data could not be fetched or parsed from Overpass API");
		alert("Les données n'ont pas pu être récupérées. Vous avez peut-être fait trop de requêtes en peu de temps ou le service est surchargé. Veuillez réessayer ultérieuremnt.")
	}
}


/*
    Research parkings using the user's filter
*/
async function getSearchFilters() {
	let allMarkers = [];
	let userMarker = null;
	
	//let areaParams = 'area[name="' + selectElement.options[selectElement.selectedIndex].value + '"];';
	let selectElement = document.getElementById("selectSearchParams");
	let areaParams = selectElement.options[selectElement.selectedIndex].value;
	let newNbMaxResults = Number(document.getElementById("nbMaxSlider").value);
	let newSearchRadius = Number(document.getElementById("distanceSlider").value);

	
	if(newNbMaxResults!=NaN && newNbMaxResults>=1){
		nbMaxResults = newNbMaxResults;
	} else {
		alert("La valeur entrée pour le nombre max de parkings à afficher est incorrecte");
	}
	
	if(newSearchRadius!=NaN && newSearchRadius>=0.5){
		searchRadius = newSearchRadius;
	} else {
		alert("La valeur entrée pour le rayon de recherche est incorrecte");
	}

	if(areaParams != "null"){
		let msgBox = document.getElementById('searchMsgBox');
		msgBox.innerHTML="Récupération de la position...";
		await refreshUserLocation();

		 // replace the user's marker
		userMarker = placeUserMarker();
		_globalUserMarker = userMarker;	
		
		try{
			getParkingsData(userLocation, userLocation, 'area[name="' + areaParams + '"];', searchRadius, nbMaxResults).then((data) =>{
				onFetchAddMarkers(data, allMarkers, null, null);
			});
		} catch(err){
			msgBox.innerHTML="";
			console.error("Data could not be fetched or parsed from Overpass API");
			alert("Les données n'ont pas pu être récupérées. Vous avez peut-être fait trop de requêtes en peu de temps ou le service est surchargé. Veuillez réessayer ultérieuremnt.")
		}
	}
}


/*
    Get the coordinates using an address provided by the user
*/
async function getCoordFromAddress(address){
	url = 'https://nominatim.openstreetmap.org/search?q='+address+'&format=json';
	return fetch(url).then((res) => res.json()).then((out) => {
		if(out.length == 0){
			throw new Error("Adresse non trouvée");
		}
		let coord = {lat:parseFloat(out[0].lat), lng:parseFloat(out[0].lon)};
		return coord;
	}).catch(error => { throw error });
}


/*
    Research parkings around the coordinates of the address provided by the user
*/
async function getParkingsNearAddress(address){
	let allMarkers = [];
	let userMarker = null;
	let areaParams = "";

	let msgBox = document.getElementById('searchMsgBox');
	msgBox.innerHTML="Récupération de la position...";
	
	await refreshUserLocation();

 	// replace the user's marker
	userMarker = placeUserMarker();
	_globalUserMarker = userMarker;
	
	if(address == ""){
		msgBox.innerHTML="";
		return -1;
	}
	
	msgBox.innerHTML="Récupération de l'adresse...";
	getCoordFromAddress(address).then((coord) =>{
		getParkingsData(coord, userLocation, areaParams, document.getElementById("distanceSlider").value, 100).then((data) =>{
			onFetchAddMarkers(data, allMarkers, coord, address);
		});
	}).catch((err) => {
		msgBox.innerHTML="";
		let searchBox = document.getElementById("search-address-text");
		searchBox.placeholder = 'Adresse inconnue';
		searchBox.style.color = "red";
		searchBox.style.borderColor = "red";
	});
}

/*
    Initialization of the map with the parameters, the events and the buttons
*/
function initMap() {
	map = new google.maps.Map(document.getElementById("map"), {
		center: {
			lat: 48,
			lng: 2
		},
		zoom: 8,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
	});
	map.addListener('click', () => {
        if(_globalAllMarkers != undefined || _globalAllMarkers.length != 0){
            for (let i = 0; i < _globalAllMarkers.length; i++) {
                if(_globalAllMarkers[i].getMap() == null && _globalAllMarkers[i] != _selectedMarker){
                    _globalAllMarkers[i].setMap(map);
                }
            }
        }
        if(_selectedMarkerInfoWindow != undefined){
            _selectedMarkerInfoWindow.close();
        }

        if (_globalDirectionsRenderer.length != 0 || _globalDirectionsRenderer != []) {
            for (let i = 0; i < _globalDirectionsRenderer.length; i++) {
                _globalDirectionsRenderer[i].setMap(null);                
            }
        }
    });
    const directionsService = new google.maps.DirectionsService();
    _globalDirectionsService = directionsService;
    
    const bottomRightDiv = document.createElement("div");
	const topCenterDiv = document.createElement("div");
    const locationButton = createMapButton(addLocationToMap, "Localisez-moi", "Cliquez pour recentrer la carte sur votre position");
	const searchNearPosButton = createMapButton(searchNearUser, "Parkings autour de moi", "Cliquez pour lancer la recherche à partir de votre position");

    bottomRightDiv.appendChild(locationButton);
    topCenterDiv.appendChild(searchNearPosButton);

    map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(bottomRightDiv);
    map.controls[google.maps.ControlPosition.TOP_CENTER].push(topCenterDiv);
}


/*
    Close the previous information window and open another information window
*/
function openInfoWindow(infoWindow, prevInfoWindow, marker, map){
	if(prevInfoWindow != null){
		prevInfoWindow.close();
	}
	infoWindow.open({
		anchor: marker,
		map,
	});
}

/*
    Place every parkings found on the map using markers
*/
function placeMarkers(allMarkers, data) {
    // Disable any direction renderer existing
    if (_globalDirectionsRenderer.length != 0 || _globalDirectionsRenderer != []) {
        for (let i = 0; i < _globalDirectionsRenderer.length; i++) {
            _globalDirectionsRenderer[i].setMap(null);                
        }
    }

    // Looping through the data received from the request
	data.forEach((parking) => {
		let distance = parking.distance;
		if(distance<0){
			distance = "";
		}else{
			distance = distance.toFixed(0).toString() + " m";
		}
		if (parking.pos.lat != undefined && parking.pos.lng != undefined) {
            // create a new marker for each parking
			const marker = new google.maps.Marker({
				position: {
					lat: parking.pos.lat,
					lng: parking.pos.lng
				},
				map,
				title: distance,
			});
            // create an information window for each parking
			var infoWindow = new google.maps.InfoWindow({
				content: "",
				ariaLabel: distance,
			});
			marker.setMap(map);

            // when the marker is clicked, show the address found in the information window
			marker.addListener("click", async function(){
				let promise = [];
				if(infoWindow.getContent()== ""){
					try{
						promise[0] = getAddressFromPos(parking.pos).then((address) => {
							parking.address = address;
							infoWindow.setContent(address.formatted);
                            _selectedMarkerAddress = address.formatted;
						});
					} catch(err){
						console.error("Data could not be fetched or parsed from Opencagedata API");
						alert("Les données n'ont pas pu être récupérées. Vous avez peut-être fait trop de requêtes en peu de temps ou le service est surchargé. Veuillez réessayer ultérieuremnt.")
					}
				}
				if(promise.length != 0){
					await Promise.all(promise);
				}

				openInfoWindow(infoWindow, prevInfoWindow, marker, map);
				prevInfoWindow = infoWindow;
                
                // add button to interact with the history of parking visited 
                if(_globalUserMarker == null){
                    infoWindow.setContent(_selectedMarkerAddress+ '<br>' + '<span class="invalid-box">Position requise pour l\'itinéraire</span> <br> <a href="addToHistory.php" class="map-button">Ajouter</a>');
                } else{
                    // add another button to join the parking from user's location
                    infoWindow.setContent(_selectedMarkerAddress + '<br>' + '<button type="button" class="map-button" onClick="goTo('+ _globalUserMarker.getPosition().lat() +','+ _globalUserMarker.getPosition().lng() +','+ marker.getPosition().lat() +','+marker.getPosition().lng()+');">Itinéraire</button> <br> <a href="addToHistory.php" class="map-button">Ajouter</a>');
				}

                _selectedMarkerInfoWindow = infoWindow;

				setCurrentParkingInSession(parking);
				displaySelectedParking(parking);
			});
            
            marker.addListener("click", () => {
                _selectedMarker = marker;
                displayMarker(allMarkers, infoWindow, prevInfoWindow, marker, map);
            });

            infoWindow.addListener('closeclick', ()=>{
                _selectedMarker = undefined;
                displayMarker(allMarkers, infoWindow, prevInfoWindow, marker, map);
            });

            // add the markers to an array of markers to keep a trace of it
			allMarkers.push(marker);
            _globalAllMarkers = allMarkers;
		}
	});
}

/*
    Display every markers on the map
*/
function displayMarker(allMarkers, infoWindow, prevInfoWindow, marker, map){
    for (let i = 0; i < allMarkers.length; i++) {
        if(allMarkers[i].getPosition() != marker.getPosition()){
            if(allMarkers[i].getMap() == null){
                allMarkers[i].setMap(map);
                infoWindow.close();
            }else{
                openInfoWindow(infoWindow, prevInfoWindow, marker, map);
                allMarkers[i].setMap(null);
            }
        }
    }
    if(_globalDirectionsRenderer.length != 0 || _globalDirectionsRenderer != undefined){
        _globalDirectionsRenderer.forEach((renderer) =>{
            if(renderer.getMap() == map){
                renderer.setMap(null);
            }
        });
    }
}

/*
    Function that process the duration of the travel and show the route on the map
*/
const goTo = async function (latOrigin, lngOrigin, latDestination, lngDestination) {
    const origin = new google.maps.LatLng(latOrigin, lngOrigin);
    const destination = new google.maps.LatLng(latDestination, lngDestination);
    processTravelTime(latOrigin, lngOrigin, latDestination, lngDestination);
    calculateAndDisplayRoute(_globalDirectionsService, origin, destination);
};


/*
    Fetch the duration for a route from the user's location to the parking desired
*/
async function processTravelTime(latOrigin, lngOrigin, latDestination, lngDestination){
    _globalRouteDuration = [];
    _globalSelectedRoute = undefined;

    let headers = new Headers();
    headers.append("Content-Type", "application/json");
    headers.append("X-Goog-Api-Key", "AIzaSyCSd09yCGbrayGGablBGR4JaFP04nTfP5M");
    headers.append("X-Goog-FieldMask", "routes.duration,routes.distanceMeters,routes.polyline.encodedPolyline");

    let myRequest = { method: 'POST',
    headers: headers,
    body: JSON.stringify({origin:{location:{latLng:{latitude: latOrigin,longitude: lngOrigin}}},destination:{location:{latLng:{latitude: latDestination,longitude: lngDestination}}},travelMode: "DRIVE",computeAlternativeRoutes: true,languageCode: "en-US",units: "METRIC"}),
    };

    const response = await fetch("https://routes.googleapis.com/directions/v2:computeRoutes", myRequest);
    const jsonResponse = response.json();
    jsonResponse.then((out) => {
        if(out.routes.length > 1 ){
            for (let i = 0; i < NUMBER_ROUTES; i++) {
                const date = new Date(parseInt(out.routes[i].duration) * 1000).toISOString().substring(11, 16);
                _globalRouteDuration.push(date);
            }
        }else{
            const date = new Date(parseInt(out.routes[0].duration) * 1000).toISOString().substring(11, 16);
            _globalRouteDuration.push(date);
        }

        let text = '<br>';
        let j = 1;
        for (let i = 0; i < NUMBER_ROUTES; i++) {
            if(i%2==0){
                color = '#00458E';
            }else{
                color = '#ED1C24';
            }
            j = j + i;  
            if(_globalRouteDuration[i] != undefined){
            	text = text + '<label><mark style="color: white; background-color:'+color+'";>'+'Trajet'+ j + " : </mark></label>" + _globalRouteDuration[i] + '<button onClick="selectedRoute('+i+');">Sélectionner</button><br>';
            }
        }
		text += '<br> <a href="addToHistory.php" class="map-button">Ajouter</a>';
        _selectedMarkerInfoWindow.setContent(_selectedMarkerAddress + text);
    });
}

/*
    Hide every other routes except the selected one
*/
function selectedRoute(index){
    for (let i = 0; i < _globalDirectionsRenderer.length; i++) {
        _globalDirectionsRenderer[i].setMap(map);
    }
    _globalSelectedRoute = _globalDirectionsRenderer[index];
    for (let i = 0; i < _globalDirectionsRenderer.length; i++) {
        if(_globalDirectionsRenderer[i] != _globalSelectedRoute){
            _globalDirectionsRenderer[i].setMap(null);
        }
    }
}

/*
    Remove all markers from the map and in the data
*/
function removeAllMarkers(allMarkers) {
	allMarkers.forEach((marker) => {
		marker.setMap(null);
	});

    allMarkers = [];
}

/*
    Place a marker on the user's location
*/
function placeUserMarker(){
	if(_globalUserMarker != null){
		_globalUserMarker.setMap(null);
	}
	let icon = {
		path: "M13 4.069V2h-2v2.069A8.01 8.01 0 0 0 4.069 11H2v2h2.069A8.008 8.008 0 0 0 11 19.931V22h2v-2.069A8.007 8.007 0 0 0 19.931 13H22v-2h-2.069A8.008 8.008 0 0 0 13 4.069zM12 18c-3.309 0-6-2.691-6-6s2.691-6 6-6 6 2.691 6 6-2.691 6-6 6z",
		fillColor: "blue",
		fillOpacity: 1.0,
		strokeWeight: 0,
		rotation: 0,
		scale: 2,
		anchor: new google.maps.Point(12,12),
	};
    return placeMarker(userLocation, icon, "Ma position actuelle", null);
}

/*
    Place a marker(a blue flag) on the address desired
*/
function placeMarkerAddress(coordAddress, address){
	let icon = {
		path: "M 0 0 V 15 H 0.5 V 0 H 0 M 0.5 1 L 10 4 L 0.5 8",
		fillColor: "blue",
		fillOpacity: 1.0,
		strokeWeight: 0,
		rotation: 0,
		scale: 3,
		anchor: new google.maps.Point(12,12),
	};

	return placeMarker(coordAddress, icon, "Addresse recherchée", address);
}

/*
    General function to place a marker at a position desired
*/
function placeMarker(posMarker, icon, title, description){
	if(posMarker.lat == null || posMarker.lng == null){
		return null;
	}
	const marker = new google.maps.Marker({
        position: posMarker,
		icon:icon,
		title: title,
    });

	if(description != null){
		var infoWindow = new google.maps.InfoWindow({
			content: description,
		});
		marker.addListener("click", async function(){
			openInfoWindow(infoWindow, prevInfoWindow, marker, map);
			prevInfoWindow = infoWindow;
		});
	}

	marker.setMap(map);
    return marker;
}

/*
    Function used by a button on the map to locate the user's position and center the map on it
*/
function addLocationToMap(){
	refreshUserLocation().then(() =>{
		if(userLocation.lat != null && userLocation.lng != null){
			map.setCenter(new google.maps.LatLng(userLocation.lat, userLocation.lng));
			map.setZoom(15);
			_globalUserMarker = placeUserMarker();
		} else {
			console.error("position is required but could not be fetched");
			alert("Votre position est requise");
		}
	});
}

/* 
    General function to create button with 
*/
function createMapButton(action, textContent, title) {
    const controlButton = document.createElement("button");
    controlButton.style.backgroundColor = "#fff";
    controlButton.style.border = "2px solid #fff";
    controlButton.style.borderRadius = "3px";
    controlButton.style.boxShadow = "0 2px 6px rgba(0,0,0,.3)";
    controlButton.style.color = "rgb(25,25,25)";
    controlButton.style.cursor = "pointer";
    controlButton.style.fontFamily = "Roboto,Arial,sans-serif";
    controlButton.style.fontSize = "16px";
    controlButton.style.lineHeight = "38px";
    controlButton.style.margin = "8px 0 22px";
    controlButton.style.padding = "0 5px";
    controlButton.style.textAlign = "center";
    controlButton.textContent = textContent;
    controlButton.title = title;
    controlButton.type = "button";
    controlButton.addEventListener("click", action);
	return (controlButton);
}

/*
    Find multiple routes and show them on the map
*/
function calculateAndDisplayRoute(directionsService, origin, destination/*, allMarkers, map*/) {
    let color;
    removeAllDirectionsRenderer();

    // using google direction service to find multiple routes with parameters
    directionsService
      .route({
        origin: origin,
        destination: destination,
        travelMode: google.maps.TravelMode.DRIVING,
        provideRouteAlternatives: true,
      })
      .then((result) => {
        for(let i = 0; i < NUMBER_ROUTES; i++){
            // colors to differentiate between the routes
            if(i%2==0){
                color = '#00458E';
            }else{
                color = '#ED1C24';
            }
            // create a new direction renderer of each route found
            let directionsRenderer = new google.maps.DirectionsRenderer({markerOptions:{visible:false}, polylineOptions:{strokeColor: color, strokeWeight: 10}});
            directionsRenderer.setMap(map);
            directionsRenderer.setDirections(result);
            directionsRenderer.setRouteIndex(i);

            // global array to keep a trace of every renderer to be used in the application
            _globalDirectionsRenderer.push(directionsRenderer);
        }
        /*
        document.getElementById("warnings-panel").innerHTML =
          "<b>" + result.routes[0].warnings + "</b>";
        */
        //showSteps(result, allMarkers, map);
      })
      .catch((e) => {
        window.alert("Directions request failed due to " + e);
      });
}


function showSteps(directionResult, allMarkers, map) {
    const myRoute = directionResult.routes[0].legs[0];

    for (let i = 0; i < myRoute.steps.length; i++) {
      /*
        const marker = (allMarkers[i] =
        allMarkers[i] || new google.maps.Marker());
  
      marker.setMap(map);
      marker.setPosition(myRoute.steps[i].start_location);
      attachInstructionText(
        marker,
        myRoute.steps[i].instructions,
        map
      );
      */
  }
}

/*
    Remove every directions renderer of the map and in the data
*/
function removeAllDirectionsRenderer(){
    for (let i = 0; i < _globalDirectionsRenderer.length; i++) {
        _globalDirectionsRenderer[i].setMap(null);
        _globalDirectionsRenderer.pop();
    }
    _globalDirectionsRenderer = [];
}


/*
curl -L -X GET 'https://maps.googleapis.com/maps/api/distancematrix/json?origins=49.023079%2C2.047221&destinations=48.8566%2C2.3522&units=metric&key=AIzaSyCSd09yCGbrayGGablBGR4JaFP04nTfP5M'
*/