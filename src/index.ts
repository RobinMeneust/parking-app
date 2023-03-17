/// <reference types="@types/google.maps" />

type parking = {
    capacity:{value:number,approx:boolean}, 
    fee:string, 
    surface:number, 
    address:string,
    distance:number,
    pos:{lat:number,lng:number},
    paymentMethod:{cash:string, credit_card:string, coins:string}
};

let map: google.maps.Map, infoWindow: google.maps.InfoWindow;
const locationButton = document.getElementById("locationButton") as HTMLElement;
const buttonPos = document.getElementById("getUserLocation") as HTMLElement;
const buttonSearchParam = document.getElementById("getSearchParams") as HTMLElement;

function initMap(): void {
    map = new google.maps.Map(document.getElementById("map") as HTMLElement, {
      center: { lat: 48, lng: 2 },
      zoom: 8,
      mapTypeId : google.maps.MapTypeId.ROADMAP,
    });
}

function handleLocationError(browserHasGeolocation: any, infoWindow: google.maps.InfoWindow, pos: any) {
    infoWindow.setPosition(pos);
    infoWindow.setContent(
      browserHasGeolocation
        ? "Error: The Geolocation service failed."
        : "Error: Your browser doesn't support geolocation."
    );
    infoWindow.open(map);
}

locationButton.addEventListener("click", () => {
    // Try HTML5 geolocation.
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (position: any) => {
          const pos = {
            lat: position.coords.latitude,
            lng: position.coords.longitude,
          };
          map.setCenter(pos);
          map.setZoom(13);
        },
        () => {
          handleLocationError(true, infoWindow, map.getCenter());
        }
      );
    } else {
      // Browser doesn't support Geolocation
      handleLocationError(false, infoWindow, map.getCenter());
    }
  });


window.initMap = initMap;

function distMeters(lat1: number, lng1: number, lat2: number, lng2:number){
    //src : https://en.wikipedia.org/wiki/Haversine_formula
    const R = 6371e3; // Earth radius

    // Convert degrees to radians
    const phi1 = lat1 * Math.PI/180;
    const phi2 = lat2 * Math.PI/180;
    
    // Get lat and lng variation in radians
    const dphi = (lat2-lat1) * Math.PI/180;
    const dlambda = (lng2-lng1) * Math.PI/180;

    // Haversine function
    const h = Math.sin(dphi/2) ** 2 + Math.cos(phi1) * Math.cos(phi2) * (Math.sin(dlambda/2) ** 2);
    
    // Distance in meters = (Inverse haversine of h) * r = 2 * r * arcsin(sqrt(h))
    return 2 * R * Math.asin(Math.sqrt(h));
}

function getCapacity(data: any, surface: any){
    /*
    Surface -> capacity : https://www.dimensions.com/element/90-degree-parking-spaces-layouts
    400m² -> 12 slots
    */
    let capacity = {value:0, approx:false};

    if(data.hasOwnProperty('capacity')){
        capacity.value = parseInt(data.capacity);
    }else{
        capacity.value = surface * (3/100);
        capacity.value = Math.round(capacity.value);
        capacity.approx = true;
    }
    if(capacity.value < 0){
        capacity.value = 0;
    }
    return capacity
}

function getFee(data: any){
    if(data.hasOwnProperty('fee')){
        return data.fee;
    } else{
        return ""
    }
}

function getPaymentMethod(data: any){
    let payment = {cash:"", credit_card:"", coins:""}
    if(data.hasOwnProperty('payment:cash')){
        payment.cash = data['payment:cash'];
    }
    if(data.hasOwnProperty('payment:credit_card')){
        payment.cash = data['payment:credit_card'];
    }
    if(data.hasOwnProperty('payment:coins')){
        payment.cash = data['payment:coins'];
    }
    return payment;
}

async function getAddressFromPos(pos: any){
    const response = await fetch("https://api.opencagedata.com/geocode/v1/json?q="+pos.lat+"+"+pos.lng+"&key=6ed462e0c4a54f39a14230ff783fc470")
    const json = await response.json();
    return json.results[0].formatted;
}

async function getParkingsData(latitude: number, longitude: number, areaParams: any): Promise<parking[]>{
    //let searchPos = {lat:49.023079,lng:2.047221};
    let searchPos = {lat:latitude,lng:longitude};
    let searchRadius = 600;
    
    // We only take nodes with the capacity tag because we don't have borders to get an surface used to get an approximation of this capacity

    let url ='';
    if(areaParams==""){
        url = 'https://overpass-api.de/api/interpreter?data=[out:json];(way[amenity=parking](around:'+searchRadius+','+searchPos.lat+',' + searchPos.lng+');relation[amenity=parking](around:'+searchRadius+','+searchPos.lat+',' + searchPos.lng+');node[amenity=parking][capacity](around:'+searchRadius+','+searchPos.lat+',' + searchPos.lng+'););out bb 200;';
    }
    else{
        url = 'https://overpass-api.de/api/interpreter?data=[out:json];('+areaParams+'way[amenity=parking](area);relation[amenity=parking](area);node[amenity=parking][capacity](area););out bb 200;';
    }
    
    let data: parking[] = [];

    try{
        const response = await fetch(url)
        const out = await response.json();
        let nbParkings = out.elements.length;
        if(areaParams!=""){
            nbParkings--; // to ignore the area element at the end of the json
        }

        console.log(out);
        
        for(let i=0; i<nbParkings; i++){
            let parking: parking = {
                capacity:{value:0,approx:true}, 
                fee:getFee(out.elements[i].tags), 
                surface:0, 
                address:"",
                distance:0,
                pos:{lat:0.0,lng:0.0},
                paymentMethod:{cash:"", credit_card:"", coins:""}
            };
            
            if(out.elements[i].type != "node"){
                let lngDiff = out.elements[i].bounds.maxlon -out.elements[i].bounds.minlon;
                let latDiff = out.elements[i].bounds.maxlat -out.elements[i].bounds.minlat;
                parking.pos.lat = (out.elements[i].bounds.maxlat + out.elements[i].bounds.minlat) / 2;
                parking.pos.lng = (out.elements[i].bounds.maxlon + out.elements[i].bounds.minlon) / 2;
                let lat1 = parking.pos.lat;
                let lat2 = searchPos.lat;
                let lng1 = parking.pos.lng;
                let lng2 = searchPos.lng;
                let latWidth = distMeters(out.elements[i].bounds.minlat, out.elements[i].bounds.minlon, out.elements[i].bounds.maxlat, out.elements[i].bounds.minlon);
                let lonWidth = distMeters(out.elements[i].bounds.minlat, out.elements[i].bounds.minlon, out.elements[i].bounds.minlat, out.elements[i].bounds.maxlon);
                parking.surface = latWidth * lonWidth;
            }
            else{
                parking.pos.lat = out.elements[i].lat;
                parking.pos.lng = out.elements[i].lng;
            }
            parking.paymentMethod = getPaymentMethod(out.elements[i].tags);
            parking.capacity = getCapacity(out.elements[i].tags, parking.surface);
            parking.address = await getAddressFromPos(parking.pos);
            //console.log(parking);
            parking.distance = distMeters(parking.pos.lat, parking.pos.lng, searchPos.lat, searchPos.lng);
            if(parking.capacity.value>0){
                data.push(parking);
            }
        }
    }
    catch(error){
        console.log("Error: could not fetch and parse data");
    }
    return new Promise((resolve, reject) =>{
        resolve(data);
    });
}

buttonSearchParam.addEventListener("click", () => {
    navigator.geolocation.getCurrentPosition((position) => {
        let lat = position.coords.latitude;
        let lng = position.coords.longitude;
        map.setCenter({lat, lng});
        map.setZoom(13);
        // example: area[name="Paris 20e Arrondissement"];
        let areaParams = 'area[name="'+(document.getElementById("searchBox") as HTMLInputElement).value+'"];';
        getParkingsData(lat,lng,areaParams).then((data) => {
            if(allMarkers.length != 0 || allMarkers != undefined) removeAllMarkers(allMarkers);
            placeMarker(data);
            const coordFirstMarker = new google.maps.LatLng(data[0].pos.lat, data[0].pos.lng);
            map.setCenter(coordFirstMarker);
            map.setZoom(15);
        });
    });
});

buttonPos.addEventListener("click", () => {
    navigator.geolocation.getCurrentPosition((position) => {
        let lat = position.coords.latitude;
        let lng = position.coords.longitude;
        let areaParams = "";
        map.setCenter({lat, lng});
        map.setZoom(13);
        getParkingsData(lat,lng,areaParams).then((data) => {
            if(allMarkers.length != 0 || allMarkers != undefined) removeAllMarkers(allMarkers);
            placeMarker(data);
            const coordFirstMarker = new google.maps.LatLng(data[0].pos.lat, data[0].pos.lng);
            map.setCenter(coordFirstMarker);
            map.setZoom(15);
        });
    });
});

let allMarkers: google.maps.Marker[] = [];

async function placeMarker(data: parking[]): Promise<number> {
    data.forEach((parking)=>{
        if(parking.pos.lat != undefined && parking.pos.lng != undefined){
        const marker = new google.maps.Marker({
            position: {lat: parking.pos.lat, lng: parking.pos.lng},
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
        }else{
            return 0;
        }
    })
    return 0;
}

function removeAllMarkers(allMarkers: google.maps.Marker[]): void{
    allMarkers.forEach((marker) =>{
        marker.setMap(null);
    });
}