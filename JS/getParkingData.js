/*
	Calculate the distance in meters between two points in polar coordinates by using the Haversine function
*/

function distMeters(x0, x1){
	//src : https://en.wikipedia.org/wiki/Haversine_formula
	const R = 6371e3; // Earth radius

	// Convert degrees to radians
	const phi1 = x0.lat * Math.PI/180;
	const phi2 = x1.lat * Math.PI/180;
	
	// Get lat and lng variation in radians
	const dphi = (x1.lat-x0.lat) * Math.PI/180;
	const dlambda = (x1.lng-x0.lng) * Math.PI/180;

	// Haversine function
	const h = Math.sin(dphi/2) ** 2 + Math.cos(phi1) * Math.cos(phi2) * (Math.sin(dlambda/2) ** 2);
	
	// Distance in meters = (Inverse haversine of h) * r = 2 * r * arcsin(sqrt(h))
	return 2 * R * Math.asin(Math.sqrt(h));
}

/*
	Get the capacity from the given data object
*/

function getCapacity(data, surface){
	/*
	Surface -> capacity : https://www.dimensions.com/element/90-degree-parking-spaces-layouts
	400m² -> 12 slots
	*/
	let capacity = {value:0, approx:false};

	if(data.hasOwnProperty('capacity')){
		// The capacity is in the data given so we can directly use it
		capacity = parseInt(data.capacity);
	}else{
		// The capacity isn't in the data, so we have to calculate it by using the surface
		capacity = surface * (3/100);
		capacity = Math.round(capacity);
	}
	// If there is an error (surface <= 0 for instance) then 0 is return
	if(capacity < 0){
		capacity = 0;
	}
	return capacity
}

/*
	Get the fee from the given data object
*/

function getFee(data){
	if(data.hasOwnProperty('fee')){
		if(data.fee == "yes"){
			return 1;
		} else if(data.fee == "no" || data.fee == "donation"){
			return 0;
		}
		return -1;
	}
}

/*
	Get the accepted payment methods from the given data object
*/

function getPaymentMethod(data){
	let payment = {cash:-1, card:-1};

	if(data.hasOwnProperty('payment:cash')){
		if(data["payment:cash"] == "yes" || data["payment:cash"] == "only"){
			payment.cash = 1;
		} else if(data["payment:cash"] == "no"){
			payment.cash = 0;
		}
	}
	if(data.hasOwnProperty('payment:coins')){
		if(data["payment:coins"] == "yes" || data["payment:cash"] == "only"){
			payment.cash = 1;
		} else if(payment.cash == -1 && data["payment:coins"] == "no"){
			payment.cash = 0;
		}
	}

	if(data.hasOwnProperty('payment:credit_card')){
		if(data["payment:credit_card"] == "yes" || data["payment:credit_card"] == "only"){
			payment.card = 1;
		} else if(data["payment:credit_card"] == "no"){
			payment.card = 0;
		}
	}
	if(data.hasOwnProperty('payment:debit_card')){
		if(data["payment:debit_card"] == "yes" || data["payment:credit_card"] == "only"){
			payment.card = 1;
		} else if(payment.card == -1 && data["payment:debit_card"] == "no"){
			payment.card = 0;
		}
	}

	return payment.card;
}

/*
	Get the address from a position defined by its latitude and longitude, by using Opencagedata's API
*/

async function getAddressFromPos(pos){
	const response = await fetch("https://api.opencagedata.com/geocode/v1/json?q="+pos.lat+"+"+pos.lng+"&key=6ed462e0c4a54f39a14230ff783fc470").catch((err)=>{
        console.error(err)
    });
	const json = await response.json();

	let street = "";
	if(json.results[0].components.hasOwnProperty("street")){
		street = json.results[0].components.street;
	} else{
		street = json.results[0].components.road;
	}

	let city = "";
	if(json.results[0].components.hasOwnProperty("city")){
		city = json.results[0].components.city;
	} else{
		city = json.results[0].components.town;
	}

	let address = {
		houseNumber:json.results[0].components.house_number,
		street:street,
		city:city,
		postalCode:json.results[0].components.postcode,
		country:json.results[0].components.country,
		formatted:json.results[0].formatted
	};
	return address;
}

/*
	Get the surface of the rectangle defined by 2 points defined by their latitude and longitude
*/

function getSurface(x0,x1){
	let lowerLeft = x0;
	let upperLeft = {lat:x1.lat, lng:x0.lng};
	let lowerRight = {lat:x0.lat, lng:x1.lng};
	let latLength = distMeters(lowerLeft,upperLeft);
	let lngLength = distMeters(lowerLeft,lowerRight);

	return latLength * lngLength;
}

/*
	Get the parking name from the data if it's defined
*/

function getParkingName(data){
	if(data.hasOwnProperty('name')){
		return data.name;
	} else{
		return "";
	}
}

/*
	Get all of the parkings in the searched area and get or calculate their associated data
*/

async function getParkingsData(searchPos, userPos, areaParams, maxDistance, maxElements){
	let msgBox = document.getElementById('searchMsgBox');

	if(maxElements<1){
		maxElements = 10;
	}
	
	let searchRadius = maxDistance * 1000; // in meters

	if(searchRadius<0.5){
		searchRadius = 0.5;
	}
	// We only take nodes with the capacity tag because we don't have borders to get an surface used to get an approximation of this capacity

	let url ='';
	if(areaParams==""){
		// default query: search near a point (user or address)
		url = 'https://overpass-api.de/api/interpreter?data=[out:json];(way[amenity=parking](around:'+searchRadius+','+searchPos.lat+',' + searchPos.lng+');relation[amenity=parking](around:'+searchRadius+','+searchPos.lat+',' + searchPos.lng+');node[amenity=parking][capacity](around:'+searchRadius+','+searchPos.lat+',' + searchPos.lng+'););out bb '+maxElements+';';
	}
	else{
		// query with a specific area
		url = 'https://overpass-api.de/api/interpreter?data=[out:json];('+areaParams+'way[amenity=parking](area);relation[amenity=parking](area);node[amenity=parking][capacity](area););out bb '+maxElements+';';
	}
	
	let data = [];

	try{
		msgBox.innerHTML="Récupération des données..."; // Used to display in a span a message to the user
		const response = await fetch(url);
		const out = await response.json();
		let nbParkings = out.elements.length;
		if(areaParams!=""){
			nbParkings--; // to ignore the "area" element at the end of the json
		}

		msgBox.innerHTML="Traitement des données...";

		for(let i=0; i<nbParkings; i++){
			let parking = {
				capacity:0,
				nbFreeSlots:-1,
				fee:getFee(out.elements[i].tags), 
				address:null,
				name:"",
				distance:-1,
				pos:{lat:0.0,lng:0.0},
				paymentMethod:{card:-1,cash:-1},
				openingHours:"non spécifié"
			};

			let surface = 0;
			
			if(out.elements[i].type != "node"){
				// We consider that its position is at the the center of its area
				parking.pos.lat = (out.elements[i].bounds.maxlat + out.elements[i].bounds.minlat) / 2;
				parking.pos.lng = (out.elements[i].bounds.maxlon + out.elements[i].bounds.minlon) / 2;

				let x0 = {lat:out.elements[i].bounds.minlat, lng:out.elements[i].bounds.minlon};
				let x1 = {lat:out.elements[i].bounds.maxlat, lng:out.elements[i].bounds.maxlon};
				surface = getSurface(x0,x1);
			} else{
				parking.pos.lat = out.elements[i].lat;
				parking.pos.lng = out.elements[i].lon;
			}

			if(parking.pos.lat<-90 || parking.pos.lat>90 || parking.pos.lng<-180 || parking.pos.lng>180){
				continue; // skip this parking since it's nto valid
			}
			
			if(out.elements[i].hasOwnProperty("tags")){
				parking.paymentMethod = getPaymentMethod(out.elements[i].tags);
				if(parking.fee == -1){
					if(parking.paymentMethod == 1 || parking.paymentMethod == 1){
						parking.fee = 1;
					} else if(parking.paymentMethod == 0 && parking.paymentMethod == 0){
						parking.fee = 0;
					}
				}
				
				parking.capacity = getCapacity(out.elements[i].tags, surface);
				
				parking.name = getParkingName(out.elements[i].tags);

				if(userPos.lat != null && userPos.lng != null){
					parking.distance = distMeters(parking.pos, userPos);
				}
				// if capacity is invalid then we skip the parking
				if(parking.capacity>0){
					data.push(parking);
				}
				if(out.elements[i].tags.hasOwnProperty("opening_hours")){
					parking.openingHours = out.elements[i].tags.opening_hours;
				} else{
					parking.openingHours = "non spécifié";
				}

			}
		}
		msgBox.innerHTML="";
		return data;
	}
	catch(error){
		msgBox.innerHTML="";
		throw error;
	}
}

/*
	Fetch some activities that can have an impact on the number of available slots in the parking lot
*/

async function fetchNearbyElements(pos, params, weightValue){
	url = 'https://overpass-api.de/api/interpreter?data=[out:json];(node'+params+'(around:500,'+pos.lat+','+pos.lng+');way'+params+'(around:500,'+pos.lat+','+pos.lng+');relation'+params+'(around:500,'+pos.lat+','+pos.lng+'););out count;';
	return fetch(url).then((res) => res.json()).then((out) => {
		let result = {weight:weightValue, count:parseInt(out.elements[0].tags.total)};
		return result;
	}).catch(error => { throw error });
}

/*
	Get the name of the city where is located the given point defined by its latitude and longitude
*/
async function getCityName(pos){
	try{
		const response = await fetch("https://api.opencagedata.com/geocode/v1/json?q="+pos.lat+"+"+pos.lng+"&key=6ed462e0c4a54f39a14230ff783fc470&limit=1")
		const json = await response.json();
		if(json.results[0].components.hasOwnProperty("city")){
			return json.results[0].components.city;
		} else{
			return json.results[0].components.town;
		}
	} catch(err){
		throw err;
	}
}

/*
	Get the populatio nof the city where is located the given point defined by its latitude and longitude
*/

async function getCityPopulation(pos){	
	try{
		const city = await getCityName(pos);
		const response = await fetch('https://nominatim.openstreetmap.org/search.php?city="'+city+'"&format=json&extratags=1');
		const json = await response.json();
        if(json != undefined && json.length != 0){
		    return json[0].extratags.population;
        }
	} catch(err){
		throw err;
	}
}


/*
	Flatten the given data so that it's between 0 and max. And change the curve slope by changing the speed arguments (a higher speed means a faster convergence to max)
*/

function flatten(x, max, speed) {
	let newX = x * speed;
	let result = (newX /(1 + (Math.abs(newX)/max)));
	if(x<0){
		result*=-1;
	}
	return result
}

/*
	Get the time period (morning, noon,...) from the given hour of the day
*/

function getTimePeriod(hour){

	/*
	9 - 11 morning
	11 - 14 noon
	14 - 18 afternoon
	18 - 21 evening
	21 - 3 night
	*/
	
	if(hour>=9){
		if(hour>=11){
			if(hour>=14){
				if(hour>=18){
					if(hour>=21){
						return "night";
					}
					else{
						return "evening";
					}
				}
				else{
					return "afternoon";
				}
			}
			else{
				return "noon";
			}
		}
		else{
			return "morning";
		}
	} else if(hour<=3){
		return "night";
	} else{
		return "ignore";
	}
}

/*
	Return true if we are during holidays in France (2023 dates only)
*/

function isHolidays(date){
	if((date.month==10 && date.day>=21)
	|| (date.month==11 && date.day<7)
	|| (date.month==12 && date.day>=17)
	|| (date.month==1 && date.day<3)
	|| (date.month==2 && date.day>=18)
	|| (date.month==3 && date.day<6)
	|| (date.month==4 && date.day>=22)
	|| (date.month==5 && date.day<9)
	|| (date.month==7 && date.day>=8)
	|| (date.month==8)){
		return true;
	}
	return false;
}

/*
	Get the weight of an element by using its data. We use the population density, the opening hours and the default weight of the object.
	The date is also used, for instance during holidays there is more tourists, so touristic activities will get a higher weight
*/

function getWeight(population, currentDate, holidays_ratio, openingHours, importance){
	let weight = 1;
				
	if(openingHours[getTimePeriod(currentDate.hour)] == false){
		weight+=0.1;
	}

	//holidays ratio : 0 means that the date has no influence whereas 1 means that this place is only open during holidays. Values between 0 and 1 are accepted and gives the increase rate depending on if we are during holidays or not
	if(isHolidays(currentDate)){
		if(holidays_ratio>0.0)
			weight+=importance*holidays_ratio;
	} else if(holidays_ratio>0.0){
		weight*=(1-holidays_ratio);
	}

	// for area with a very low density, there isn't a big difference between 10 and 100 inhabitants. And if population <= 0 then that's an error so we should not use it in our calculations
	if(population>100){
		weight += (population/1000000);
	}
	weight *= Math.abs(importance);
	weight = flatten(weight, 1, 1);
	if(importance<0){
		weight*=-1;
	}
	return weight;
}

/*
	Calculate the availability of a parking, which is number between 0 (full) and 1 (empty)
*/

function getAvailability(data){
	let availability = 0.0;
	
	// for each activity types (shops, ...), we add its associated value (weight * number of occurrences) to the final score (unavailability here)
	for(e of data){
		// We flatten x because, for instance, it doesn't make a lot of difference if there are 1 or 3 shops, especially since we don't know how big those shops are, so we want to limit the value between 0 and 10, to avoid getting way too big values
		availability += e.weight * flatten(e.count, 10, 1);
	}

	// Flatten the result, so that it stays in [0,1]
	availability = 1-flatten(availability, 1, 0.1);

	// Readjust the values (we rarely have an empty or a completely full parking)
	if(availability<0.1){
		availability = 0.1;
	} else if(availability>0.9){
		availability = 0.9;
	}

	return availability;
}

/*
	Predict the number of available slots in the given parking
*/

async function getNbOfAvailableSlots(parking){
	let capacity = parking.capacity;
	let searchPos = parking.pos;
	let promises = new Array();
	let date = new Date();
	let currentDate = {hour:date.getHours(), month:date.getMonth()};

	// the following line isn't in the try/catch since the size of the population in the current area is more optional than necessary for our calculations, it give a more accurate value but we should not stop our function if we don't have it
	let cityPopulation = await getCityPopulation(searchPos);
	
	try{

		promises.push(await fetchNearbyElements(searchPos, '[amenity~"^(restaurant|fast_food|food_court)$"]', getWeight(cityPopulation, currentDate, 0.0, {morning:false, noon:true, afternoon:false, evening:true, night:false}, 0.5)));
		promises.push(await fetchNearbyElements(searchPos, '[amenity~"^(college|library|music_school|university)$"]', getWeight(cityPopulation, currentDate, 0.0, {morning:true, noon:true, afternoon:true, evening:true, night:false}, 0.3))); // we don't consider that it's closed during week-end here
		promises.push(await fetchNearbyElements(searchPos, '[amenity~"hospital"]', getWeight(cityPopulation, currentDate, 0.0, {morning:true, noon:true, afternoon:true, evening:true, night:true}, 1)));
		promises.push(await fetchNearbyElements(searchPos, '[amenity~"^(cinema|theatre)"]', getWeight(cityPopulation, currentDate, 0.0, {morning:true, noon:true, afternoon:true, evening:true, night:false}, 1)));
		promises.push(await fetchNearbyElements(searchPos, '[shop]', getWeight(cityPopulation, currentDate, 0.0, {morning:true, noon:true, afternoon:true, evening:true, night:false}, 1)));
		promises.push(await fetchNearbyElements(searchPos, '[airway="airport"]', getWeight(cityPopulation, currentDate, 0.2, {morning:true, noon:true, afternoon:true, evening:true, night:true}, 5)));
		promises.push(await fetchNearbyElements(searchPos, '[amenity="train_station"]', getWeight(cityPopulation, currentDate, 0.2, {morning:true, noon:true, afternoon:true, evening:true, night:false}, 0.7)));
		promises.push(await fetchNearbyElements(searchPos, '[tourism]', getWeight(cityPopulation, currentDate, 0.8, {morning:true, noon:true, afternoon:true, evening:false, night:false}, 0.5)));
		promises.push(await fetchNearbyElements(searchPos, '[leisure]', getWeight(cityPopulation, currentDate, 0.0, {morning:true, noon:true, afternoon:true, evening:true, night:false}, 1)));
		promises.push(await fetchNearbyElements(searchPos, '[office]', getWeight(cityPopulation, currentDate, 0.0, {morning:true, noon:false, afternoon:true, evening:false, night:false}, 0.7)));

		promises.push(fetchNearbyElements(searchPos, '[parking]', getWeight(cityPopulation, currentDate, 0.0, {morning:true, noon:true, afternoon:true, evening:true, night:true}, -0.8)));
		
		data = await Promise.all(promises);

		let availability = getAvailability(data);
		let numberOfSlots = Math.floor(availability * capacity);

		return numberOfSlots;
	} catch(err){
		throw err;
	}
}
