let map, infoWindow, locationButton, buttonPos, buttonSearchParam;
window.initMap = initMap;
let allMarkers = [];

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
		navigator.geolocation.getCurrentPosition((position) => {
			let lat = position.coords.latitude;
			let lng = position.coords.longitude;
			map.setCenter({
				lat,
				lng
			});
			map.setZoom(13);
			// example: area[name="Paris 20e Arrondissement"];
			let areaParams = 'area[name="' + document.getElementById("searchBox").value + '"];';
			getParkingsData(lat, lng, areaParams).then((data) => {
				if (allMarkers.length != 0 || allMarkers != undefined)
					removeAllMarkers(allMarkers);
				placeMarker(data);
				map.setCenter({
					lat,
					lng
				});
				map.setZoom(13);
			});
		});
	});
	
	buttonPos.addEventListener("click", () => {
		navigator.geolocation.getCurrentPosition((position) => {
			let lat = position.coords.latitude;
			let lng = position.coords.longitude;
			let areaParams = "";
			map.setCenter({
				lat,
				lng
			});
			map.setZoom(13);
			getParkingsData(lat, lng, areaParams).then((data) => {
				if (allMarkers.length != 0 || allMarkers != undefined)
					removeAllMarkers(allMarkers);
				placeMarker(data);
				const coordFirstMarker = new google.maps.LatLng(data[0].pos.lat, data[0].pos.lng);
				map.setCenter(coordFirstMarker);
				map.setZoom(15);
			});
		});
	});
}

/// <reference types="@types/google.maps" />
var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
	function adopt(value) {
		return value instanceof P ? value : new P(function (resolve) {
			resolve(value);
		});
	}
	return new(P || (P = Promise))(function (resolve, reject) {
		function fulfilled(value) {
			try {
				step(generator.next(value));
			} catch (e) {
				reject(e);
			}
		}

		function rejected(value) {
			try {
				step(generator["throw"](value));
			} catch (e) {
				reject(e);
			}
		}

		function step(result) {
			result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected);
		}
		step((generator = generator.apply(thisArg, _arguments || [])).next());
	});
};


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


function placeMarker(data) {
	return __awaiter(this, void 0, void 0, function* () {
		data.forEach((parking) => {
			if (parking.pos.lat != undefined && parking.pos.lng != undefined) {
				const marker = new google.maps.Marker({
					position: {
						lat: parking.pos.lat,
						lng: parking.pos.lng
					},
					map,
					title: parking.distance.toString(),
				});
				const infowindow = new google.maps.InfoWindow({
					content: parking.address,
					ariaLabel: parking.distance.toString(),
				});
				marker.setMap(map);
				marker.addListener("click", () => {
					infowindow.open({
						anchor: marker,
						map,
					});
				});
				allMarkers.push(marker);
				return 1;
			} else {
				return 0;
			}
		});
		return 0;
	});
}

function removeAllMarkers(allMarkers) {
	allMarkers.forEach((marker) => {
		marker.setMap(null);
	});
}