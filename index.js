let map, infoWindow, buttonPos, buttonSearchParam;
window.initMap = initMap;
let prevInfoWindow = null;
let _globalAllMarkers = [];
let _globalUserMarker = [];
let _selectedMarker = undefined;
let _globalDirectionsService = undefined;
let _globalDirectionsRenderer = [];

let userLocation = {lat:null,lng:null};
let searchRadius = 1;
let nbMaxResults = 50;

function addOption(selectField, value, name){
	var opt = document.createElement('option');
	opt.value = value;
	opt.innerHTML = name;
	selectField.appendChild(opt);
}

function initializeForms(){
	let arrondissements = document.getElementById("arrondissementsOptgroup");
	let departements = document.getElementById("departementsOptgroup");

	addOption(arrondissements, "Paris 1er Arrondissement", "Paris 1er Arrondissement");
	for(let i=2; i<=20; i++){
		let value = "Paris "+i+"e Arrondissement";
		addOption(arrondissements, value, value);
	}
	addOption(departements, "Paris", "75 Paris");
	addOption(departements, "Seine-et-Marne", "77 Paris Seine-et-Marne");
	addOption(departements, "Yvelines", "78 Yvelines");
	addOption(departements, "Essone", "91 Essone");
	addOption(departements, "Hauts-de-Seine", "92 Hauts-de-Seine");
	addOption(departements, "Seine-Saint-Denis", "93 Seine-Saint-Denis");
	addOption(departements, "Val-de-Marne", "94 Val-de-Marne");
	addOption(departements, "Val-d'Oise", "95 Val-d'Oise");
}


async function refreshUserLocation(){
	const posPromise = () => new Promise((resolve, error) => navigator.geolocation.getCurrentPosition(resolve, error));
	try {
		const pos = await posPromise();
		userLocation.lat = pos.coords.latitude;
		userLocation.lng = pos.coords.longitude;
	} catch (error) {
		console.log(error);
	}
}

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

async function searchNearUser(){
	let allMarkers = [];
	let userMarker = [];
	let areaParams = "";

	await refreshUserLocation();
	if(userLocation.lat == null || userLocation.lng == null){
		console.error("position is required but could not be fetched");
		alert("Votre position est requise");
		return;
	}

	if((userMarker.length != 0 || userMarker != undefined) && (_globalUserMarker != undefined || _globalUserMarker.length != 0)){
        userMarker.pop();
		_globalUserMarker.pop()
	}

	userMarker.push(placeUserMarker());
	if(userMarker != null){
		_globalUserMarker = userMarker;
	}

	try{
		getParkingsData(userLocation, userLocation, areaParams, searchRadius, nbMaxResults).then((data) => {
			if(data.length == 0){
				alert("Aucun parking n'a été trouvé dans cette zone");
				return;
			}
			if ((allMarkers.length != 0 || allMarkers != undefined) && (_globalAllMarkers.length != 0)|| _globalAllMarkers != undefined){
				removeAllMarkers(allMarkers);
                removeAllMarkers(_globalAllMarkers);
			}
			placeMarkers(allMarkers, data);
			if (allMarkers.length != 0 || allMarkers != undefined){
				const coordFirstMarker = new google.maps.LatLng(data[0].pos.lat, data[0].pos.lng);
				map.setCenter(coordFirstMarker);
				map.setZoom(15);
			}
		});
	} catch(err){
		console.error("Data could not be fetched or parsed from Overpass API");
		alert("Les données n'ont pas pu être récupérées. Vous avez peut-être fait trop de requêtes en peu de temps ou le service est surchargé. Veuillez réessayer ultérieuremnt.")
	}
}

async function getSearchFilters() {
	let allMarkers = [];
	let userMarker = [];
	
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
		if((userMarker.length != 0 || userMarker != undefined) && (_globalUserMarker != undefined || _globalUserMarker.length != 0)){
			userMarker.pop();
			_globalUserMarker.pop()
		}

		await refreshUserLocation();

		userMarker.push(placeUserMarker());
		if(userMarker != null){
			_globalUserMarker = userMarker;
		}		
		
		try{
			getParkingsData(userLocation, userLocation, 'area[name="' + areaParams + '"];', searchRadius, nbMaxResults).then((data) => {
				if(data.length == 0){
					alert("Aucun parking n'a été trouvé dans cette zone");
					return;
				}
				if ((allMarkers.length != 0 || allMarkers != undefined) && (_globalAllMarkers.length != 0 || _globalAllMarkers != undefined)){
					removeAllMarkers(allMarkers);
                    removeAllMarkers(_globalAllMarkers);
				}
				placeMarkers(allMarkers, data);
				if (allMarkers.length != 0 || allMarkers != undefined){
					const coordFirstMarker = new google.maps.LatLng(data[0].pos.lat, data[0].pos.lng);
					map.setCenter(coordFirstMarker);
					map.setZoom(15);
				}
			});
		} catch(err){
			console.error("Data could not be fetched or parsed from Overpass API");
			alert("Les données n'ont pas pu être récupérées. Vous avez peut-être fait trop de requêtes en peu de temps ou le service est surchargé. Veuillez réessayer ultérieuremnt.")
		}
	}
}

async function getCoordFromAddress(address){
	url = 'https://nominatim.openstreetmap.org/search?q='+address+'&format=json';
	return fetch(url).then((res) => res.json()).then((out) => {
		let coord = {lat:parseFloat(out[0].lat), lng:parseFloat(out[0].lon)};
		return coord;
	}).catch(error => { throw error });
}

async function getParkingsNearAddress(address){
	let allMarkers = [];
	let userMarker = [];
	let areaParams = "";

	if((userMarker.length != 0 || userMarker != undefined) && (_globalUserMarker != undefined || _globalUserMarker.length != 0)){
		userMarker.pop();
		_globalUserMarker.pop()
	}
	
	await refreshUserLocation();

	userMarker.push(placeUserMarker());
	if(userMarker != null){
		_globalUserMarker = userMarker;
	}
	
	if(address == ""){
		return -1;
	}
	
	getCoordFromAddress(address).then((coord) =>{
		getParkingsData(coord, userLocation, areaParams, document.getElementById("distanceSlider").value, 100).then((data) => {
			if(data.length == 0){
				alert("Aucun parking n'a été trouvé dans cette zone");
				return;
			}
			if ((allMarkers.length != 0 || allMarkers != undefined)&&(_globalAllMarkers.length != 0 || _globalAllMarkers != undefined)){
				removeAllMarkers(allMarkers);
				removeAllMarkers(_globalAllMarkers);
			}
			allMarkers.push(placeMarker(coord, address));
			placeMarkers(allMarkers, data);
			const coordFirstMarker = new google.maps.LatLng(coord.lat, coord.lng);
			map.setCenter(coordFirstMarker);
			map.setZoom(15);
			return 0;
		});
	}).catch((err) => {
		return -1;
	});
}

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
    });
    const directionsService = new google.maps.DirectionsService();
    _globalDirectionsService = directionsService;
    
    const bottomRightDiv = document.createElement("div");
	const topCenterDiv = document.createElement("div");
    const locationButton = createMapButton(addLocationToMap, "Localisez-moi", "Cliquez pour recentrer la carte sur votre position");
	const searchNearPosButton = createMapButton(searchNearUser, "Rechercher des parkings", "Cliquez pour lancer la recherche à partir de votre position");

    bottomRightDiv.appendChild(locationButton);
    topCenterDiv.appendChild(searchNearPosButton);

    map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(bottomRightDiv);
    map.controls[google.maps.ControlPosition.TOP_CENTER].push(topCenterDiv);
}

function openInfoWindow(infoWindow, prevInfoWindow, marker, map){
	if(prevInfoWindow != null){
		prevInfoWindow.close();
	}
	infoWindow.open({
		anchor: marker,
		map,
	});
}

function zoomIn(element){
	element.style.width = "120px";
	element.setAttribute("onclick","zoomOut(this)");
}

function zoomOut(element){
	element.style.width = "45px";
	element.setAttribute("onclick","zoomIn(this)");
}

function displaySelectedParking(parking){
	let table = document.getElementById("selectedParkingTable");

	table.style.visibility = "visible";
	//container.innerHTML = "Calcul du nombre de places disponibles...";
	let addressTd = document.getElementById("addressSelectedParking");
	let nbSlotsTd = document.getElementById("nbSlotsSelectedParking");
	let openingHoursTd = document.getElementById("openingHoursSelectedParking");
	let paymentTd = document.getElementById("paymentSelectedParking");

	nbSlotsTd.innerHTML = "Calcul en cours...";

	addressTd.innerHTML = parking.address;
	if(parking.openingHours == ""){
		openingHoursTd.innerHTML = "non spécifié";
	} else{
		openingHoursTd.innerHTML = parking.openingHours;
	}
	
	if(parking.fee == 0){
		paymentTd.innerHTML = "gratuit"
	} else if(parking.fee == 1){
		paymentTd.innerHTML = "<b>Payant : </b><br>Modes de paiement : <br>";
		let imgCash = "error";
		let imgCard = "error";
		switch(parking.paymentMethod.cash){
			case 0:imgCash = "cash_no";break;
			case 1:imgCash = "cash_yes";break;
			default:imgCash = "cash_unknown";
		}
		switch(parking.paymentMethod.card){
			case 0:imgCard = "card_no";break;
			case 1:imgCard = "card_yes";break;
			default:imgCard = "card_unknown";
		}
		paymentTd.innerHTML += "<img id=\"icon_cash\" onclick=\"zoomIn(this)\" src=\"assets/img/"+imgCash+".png\" alt=\""+imgCash+"\"><br>";
		paymentTd.innerHTML += "<img id=\"icon_card\" onclick=\"zoomIn(this)\" src=\"assets/img/"+imgCard+".png\" alt=\""+imgCard+"\">";	
	} else{
		paymentTd.innerHTML = "non spécifié";
	}



	if(parking.nbFreeSlots == -1){
		try{
			getNbOfAvailableSlots(parking).then((nbAvailableSlots) => {
				parking.nbFreeSlots = nbAvailableSlots;
				nbSlotsTd.innerHTML = parking.nbFreeSlots +" / "+ parking.capacity;
			});
		} catch(err){
			console.error("Data could not be fetched or parsed from Overpass API");
			alert("Les données n'ont pas pu être récupérées. Vous avez peut-être fait trop de requêtes en peu de temps ou le service est surchargé. Veuillez réessayer ultérieuremnt.")
		}
	} else{
		nbSlotsTd.innerHTML = parking.nbFreeSlots +" / "+ parking.capacity;
	}
}

function placeMarkers(allMarkers, data) {
	data.forEach((parking) => {
		let addedButton;
		let distance = parking.distance;
		if(distance<0){
			distance = "";
		} else{
			distance = distance.toFixed(0).toString() + " m";
		}
		if (parking.pos.lat != undefined && parking.pos.lng != undefined) {
			const marker = new google.maps.Marker({
				position: {
					lat: parking.pos.lat,
					lng: parking.pos.lng
				},
				map,
				title: distance,
			});
			var infoWindow = new google.maps.InfoWindow({
				content: "",
				ariaLabel: distance,
			});
			marker.setMap(map);
			marker.addListener("click", async function(){
				let promise = [];
				if(infoWindow.getContent()== ""){
					try{
						promise[0] = getAddressFromPos(parking.pos).then((address) => {
							parking.address = address;
							infoWindow.setContent(address);
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

                if(addedButton != true){
                    const button = document.createElement("button");
                    button.style.backgroundColor = "#fff";
                    button.style.border = "2px solid #fff";
                    button.style.borderRadius = "3px";
                    button.style.boxShadow = "0 2px 6px rgba(0,0,0,.3)";
                    button.style.color = "rgb(25,25,25)";
                    button.style.cursor = "pointer";
                    button.style.fontFamily = "Roboto,Arial,sans-serif";
                    button.style.fontSize = "16px";
                    button.style.lineHeight = "38px";
                    button.style.margin = "8px 0 22px";
                    button.style.padding = "0 5px";
                    button.style.textAlign = "center";
                    button.textContent = "Itinéraire";
                    button.title = "Cliquez pour rejoindre le parking.";
                    button.type = "button";
					
					if(_globalUserMarker[0] == null){
						infoWindow.setContent(infoWindow.getContent() + '<br>' +
						'<span style="background-color:#fff; color:red; font-family:Roboto,Arial,sans-serif;font-size:16px;line-height:38px; margin:8px 0 22px;padding:0 5px;test-align:center;">Position requise pour l\'itinéraire</span>');
						addedButton = true;
					} else{
						infoWindow.setContent(infoWindow.getContent() + '<br>' +
						'<button type="button" style="background-color:#fff; border:2px solid #fff; border-radius:3px; box-shadow:0 2px 6px rgba(0,0,0,.3); color:rgb(25,25,25); cursor:pointer; font-family:Roboto,Arial,sans-serif;font-size:16px;line-height:38px; margin:8px 0 22px;padding:0 5px;test-align:center;" onClick="goTo('+ _globalUserMarker[0].getPosition().lat() +','+ _globalUserMarker[0].getPosition().lng() +','+ marker.getPosition().lat() +','+marker.getPosition().lng()+');">Itinéraire</button>');
						addedButton = true;
					}
                }
				
				displaySelectedParking(parking);
			});
            
            marker.addListener("click", () => {
                _selectedMarker = marker;
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
            });

            infoWindow.addListener('closeclick', ()=>{
                _selectedMarker = marker;
                for (let i = 0; i < allMarkers.length; i++) {
                    if(allMarkers[i].getPosition() != marker.getPosition()){
                        if(allMarkers[i].getMap() == null){
                            allMarkers[i].setMap(map);
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
            });


			allMarkers.push(marker);
            _globalAllMarkers = allMarkers;
			return 1;
		} else {
			return 0;
		}
	});
	return 0;
}

const goTo = async function (latOrigin, lngOrigin, latDestination, lngDestination) {
    const origin = new google.maps.LatLng(latOrigin, lngOrigin);
    const destination = new google.maps.LatLng(latDestination, lngDestination);
    calculateAndDisplayRoute(_globalDirectionsService, origin, destination);


    let headers = new Headers();
    headers.append("Content-Type", "application/json");
    headers.append("X-Goog-Api-Key", "AIzaSyCSd09yCGbrayGGablBGR4JaFP04nTfP5M");
    headers.append("X-Goog-FieldMask", "routes.duration,routes.distanceMeters,routes.polyline.encodedPolyline");

    let myRequest = { method: 'POST',
    headers: headers,
    body: JSON.stringify({origin:{location:{latLng:{latitude: latOrigin,longitude: lngOrigin}}},destination:{location:{latLng:{latitude: latDestination,longitude: lngDestination}}},travelMode: "DRIVE",computeAlternativeRoutes: true,languageCode: "en-US",units: "METRIC"}),
    };
    
    console.log(myRequest);
    const response = await fetch("https://routes.googleapis.com/directions/v2:computeRoutes", myRequest);
    const jsonResponse = response.json();
    jsonResponse.then((out) => {
        for (let i = 0; i < 2; i++) {
            convertSeconds(out.routes[i].duration);
        }
    });

};

function convertSeconds(seconds){
    const date = new Date(parseInt(seconds) * 1000).toISOString().substring(11, 16)
    console.log(date);
}

function removeAllMarkers(allMarkers) {
	allMarkers.forEach((marker) => {
		marker.setMap(null);
	});

    allMarkers = [];
}


function placeUserMarker(){
	if(userLocation.lat == null || userLocation.lng == null){
		return null;
	}
	const marker = new google.maps.Marker({
		position: userLocation,
		map,
		icon: {
			path: "M13 4.069V2h-2v2.069A8.01 8.01 0 0 0 4.069 11H2v2h2.069A8.008 8.008 0 0 0 11 19.931V22h2v-2.069A8.007 8.007 0 0 0 19.931 13H22v-2h-2.069A8.008 8.008 0 0 0 13 4.069zM12 18c-3.309 0-6-2.691-6-6s2.691-6 6-6 6 2.691 6 6-2.691 6-6 6z",
			fillColor: "blue",
			fillOpacity: 1.0,
			strokeWeight: 0,
			rotation: 0,
			scale: 2,
			anchor: new google.maps.Point(12,12),
		},
		title: "Ma position actuelle",
	});
	marker.setMap(map);
    return marker;
}

function placeMarker(coords, description){		
    const marker = new google.maps.Marker({
        position: {
			lat: coords.lat,
            lng: coords.lng
        },
		icon:{
			path: "M 0 0 V 15 H 0.5 V 0 H 0 M 0.5 1 L 10 4 L 0.5 8",
			fillColor: "blue",
            fillOpacity: 1.0,
            strokeWeight: 0,
            rotation: 0,
            scale: 3,
            anchor: new google.maps.Point(12,12),
		},
		title: "Addresse recherchée",
    });

	var infoWindow = new google.maps.InfoWindow({
		content: description,
	});

	marker.addListener("click", async function(){
		openInfoWindow(infoWindow, prevInfoWindow, marker, map);
		prevInfoWindow = infoWindow;
	});
	
	
	marker.setMap(map);
    return marker;
}
	
function toggleMenuVisibility(){
	let element = document.getElementById("sideBarContent");

	element.classList.toggle("visible");
	element.classList.toggle("hidden");
}

function addLocationToMap(){
	refreshUserLocation().then(() =>{
		if(userLocation.lat != null && userLocation.lng != null){
			map.setCenter(new google.maps.LatLng(userLocation.lat, userLocation.lng));
			map.setZoom(15);

			if(_globalUserMarker != undefined || _globalUserMarker.length != 0){
				_globalUserMarker.pop();
			}
			_globalUserMarker.push(placeUserMarker());
		} else {
			console.error("position is required but could not be fetched");
			alert("Votre position est requise");
		}
	});
}

function createMapButton(action, textContent, title, ) {
    const controlButton = document.createElement("button");

    // Set CSS for the control.
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
	
	return controlButton;
  }


function calculateAndDisplayRoute(directionsService, origin, destination/*, allMarkers, map*/) {
    let color;
    removeAllDirectionsRenderer();
    directionsService
      .route({
        origin: origin,
        destination: destination,
        travelMode: google.maps.TravelMode.DRIVING,
        provideRouteAlternatives: true,
      })
      .then((result) => {
        for(let i = 0; i < 2; i++){
            if(i%2==0){
                color = '#00458E';
            }else{
                color = '#ED1C24';
            }
            let directionsRenderer = new google.maps.DirectionsRenderer({markerOptions:{visible:false}, polylineOptions:{strokeColor: color, strokeWeight: 10}});
            directionsRenderer.setMap(map);
            directionsRenderer.setDirections(result);
            directionsRenderer.setRouteIndex(i);
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

function removeAllDirectionsRenderer(){
    for (let i = 0; i < _globalDirectionsRenderer.length; i++) {
        _globalDirectionsRenderer[i].setMap(null);
        _globalDirectionsRenderer.pop();
    }
    _globalDirectionsRenderer = [];
}


function computeTravelTime(){

}

/*
curl -L -X GET 'https://maps.googleapis.com/maps/api/distancematrix/json?origins=49.023079%2C2.047221&destinations=48.8566%2C2.3522&units=metric&key=AIzaSyCSd09yCGbrayGGablBGR4JaFP04nTfP5M'
*/