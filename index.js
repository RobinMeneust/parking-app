let map, infoWindow, locationButton, buttonPos, buttonSearchParam;
window.initMap = initMap;
let prevInfoWindow = null;

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

function addEvents(){
	locationButton = document.getElementById("locationButton");
	buttonPos = document.getElementById("getUserLocation");
	buttonSearchParam = document.getElementById("getSearchParams");
	
	locationButton.addEventListener("click", () => {
		// Try HTML5 geolocation.
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition((position) => {
				const pos = {
					lat: position.coords.latitude,
					lng: position.coords.longitude,
				};
				map.setCenter(pos);
				map.setZoom(13);
			}, () => {
				handleLocationError(true, infoWindow, map.getCenter());
			});
		} else {
			// Browser doesn't support Geolocation
			handleLocationError(false, infoWindow, map.getCenter());
		}
	});
	
	
	buttonSearchParam.addEventListener("click", () => {
		let allMarkers = [];
		let userMarker = [];
		navigator.geolocation.getCurrentPosition((position) => {
			let lat = position.coords.latitude;
			let lng = position.coords.longitude;
			map.setCenter({
				lat,
				lng
			});
			map.setZoom(13);

			if(userMarker.length != 0 || userMarker != undefined) userMarker.pop();
            userMarker.push(placeUserMarker({lat, lng}));
			
			let selectElement = document.getElementById("selectSearchParams");
			let areaParams = 'area[name="' + selectElement.options[selectElement.selectedIndex].value + '"];';
			let maxElements = document.getElementById("nbMaxSlider").value;
			try{
				getParkingsData(lat, lng, areaParams, 1, maxElements).then((data) => {
					if(data.length == 0){
						alert("Aucun parking n'a été trouvé dans cette zone");
						return;
					}
					if (allMarkers.length != 0 || allMarkers != undefined){
						removeAllMarkers(allMarkers);
					}
					placeMarker(allMarkers, data);
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
		});
	});
	
	buttonPos.addEventListener("click", () => {
		let allMarkers = [];
        let userMarker = [];
		navigator.geolocation.getCurrentPosition((position) => {
			let lat = position.coords.latitude;
			let lng = position.coords.longitude;
			let areaParams = "";
			map.setCenter({
				lat,
				lng
			});
			map.setZoom(13);
			
			if(userMarker.length != 0 || userMarker != undefined) userMarker.pop();
            userMarker.push(placeUserMarker({lat, lng}));

			try{
				getParkingsData(lat, lng, areaParams, document.getElementById("distanceSlider").value, 100).then((data) => {
					if(data.length == 0){
						alert("Aucun parking n'a été trouvé dans cette zone");
						return;
					}
					if (allMarkers.length != 0 || allMarkers != undefined){
						removeAllMarkers(allMarkers);
					}
					placeMarker(allMarkers, data);
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
		});
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
}

function handleLocationError(browserHasGeolocation, infoWindow, pos) {
	infoWindow.setPosition(pos);
	infoWindow.setContent(browserHasGeolocation ?
		"Error: The Geolocation service failed." :
		"Error: Your browser doesn't support geolocation.");
	infoWindow.open(map);
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

function displaySelectedParking(parking){
	let row = document.getElementById("selectedParkingTableRowData");
	let table = document.getElementById("selectedParkingTable");

	table.style.visibility = "visible";
	//container.innerHTML = "Calcul du nombre de places disponibles...";
	let addressTd = document.getElementById("addressSelectedParking");
	let nbSlotsTd = document.getElementById("nbSlotsSelectedParking");
	let openingHoursTd = document.getElementById("openingHoursSelectedParking");
	let paymentTd = document.getElementById("paymentSelectedParking");

	nbSlotsTd.innerHTML = "Calcul en cours...";

	addressTd.innerHTML = parking.address;
	if(parking.opening_hours == ""){
		openingHoursTd.innerHTML = "non spécifié";
	} else{
		openingHoursTd.innerHTML = parking.opening_hours;
	}
	
	if(parking.fee == "no"){
		paymentTd.innerHTML = "gratuit"
	} else if(parking.fee == "yes"){
		paymentTd.innerHTML = "<ul><li>Espèces : "+parking.paymentMethod.cash+"</li><li>Cartes bancaires : "+parking.paymentMethod.card+"</li></ul>";
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

function placeMarker(allMarkers, data) {
	data.forEach((parking) => {
		if (parking.pos.lat != undefined && parking.pos.lng != undefined) {
			const marker = new google.maps.Marker({
				position: {
					lat: parking.pos.lat,
					lng: parking.pos.lng
				},
				map,
				title: parking.distance.toFixed(0).toString()+" m",
			});
			marker.id = allMarkers.length;
			var infoWindow = new google.maps.InfoWindow({
				content: "",
				ariaLabel: parking.distance.toFixed(0).toString()+" m",
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
				
				displaySelectedParking(parking);
			});
			allMarkers.push(marker);
			return 1;
		} else {
			return 0;
		}
	});
	return 0;
}

function removeAllMarkers(allMarkers) {
	allMarkers.forEach((marker) => {
		marker.setMap(null);
	});
}


function toggleMenuVisibility(){
	let element = document.getElementById("sideBarContent");

	element.classList.toggle("visible");
	element.classList.toggle("hidden");
}

function placeUserMarker(coords){
    const marker = new google.maps.Marker({
        position: {
            lat: coords.lat,
            lng: coords.lng
        },
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