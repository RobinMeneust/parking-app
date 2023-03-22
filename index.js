let map, infoWindow, locationButton, buttonPos, buttonSearchParam;
window.initMap = initMap;
let prevInfoWindow = null;

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
			// example: area[name="Paris 20e Arrondissement"];
			let areaParams = 'area[name="' + document.getElementById("searchBox").value + '"];';
			getParkingsData(lat, lng, areaParams).then((data) => {
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
			getParkingsData(lat, lng, areaParams).then((data) => {
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

function addTD(tr, content){
	var td = document.createElement('td');
	td.innerHTML = content;
	tr.appendChild(td);
}

function displaySelectedParking(parking){
	let row = document.getElementById("selectedParkingTableRowData");

	//container.innerHTML = "Calcul du nombre de places disponibles...";
	getNbOfAvailableSlots(parking).then((nbFreeSlots) =>{
		addTD(row, nbFreeSlots + "/" + parking.capacity.value);
	});
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
				title: parking.address.toFixed(2).toString(),
			});
			marker.id = allMarkers.length;
			var infoWindow = new google.maps.InfoWindow({
				content: "",
				ariaLabel: parking.distance.toFixed(2).toString(),
			});
			marker.setMap(map);
			marker.addListener("click", () => {
				let promise = [];
				if(infoWindow.getContent()== ""){
					promise[0] = getAddressFromPos(parking.pos).then((address) => {
						parking.adress = address;
						infoWindow.setContent(address);
					});
				}
				Promise.all(promise);

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