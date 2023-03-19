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

function getCapacity(data, surface){
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

function getFee(data){
	if(data.hasOwnProperty('fee')){
		return data.fee;
	} else{
		return ""
	}
}

function getPaymentMethod(data){
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

async function getAddressFromPos(pos){
	const response = await fetch("https://api.opencagedata.com/geocode/v1/json?q="+pos.lat+"+"+pos.lng+"&key=6ed462e0c4a54f39a14230ff783fc470")
	const json = await response.json();
	return json.results[0].formatted;
}

function getSurface(x0,x1){
	let lowerLeft = x0;
	let upperLeft = {lat:x1.lat, lng:x0.lng};
	let lowerRight = {lat:x0.lat, lng:x1.lng};
	let latLength = distMeters(lowerLeft,upperLeft);
	let lngLength = distMeters(lowerLeft,lowerRight);

	return latLength * lngLength;
}

async function getParkingsData(latitude, longitude, areaParams){
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
	
	let data = [];

	try{
		const response = await fetch(url)
		const out = await response.json();
		let nbParkings = out.elements.length;
		if(areaParams!=""){
			nbParkings--; // to ignore the area element at the end of the json
		}

		console.log(out);
		
		for(let i=0; i<nbParkings; i++){
			let parking = {
				capacity:{value:0,approx:true}, 
				fee:getFee(out.elements[i].tags), 
				surface:0, 
				address:"",
				distance:0,
				pos:{lat:0.0,lng:0.0},
				paymentMethod:{cash:"", credit_card:"", coins:""}
			};
			
			if(out.elements[i].type != "node"){
				parking.pos.lat = (out.elements[i].bounds.maxlat + out.elements[i].bounds.minlat) / 2;
				parking.pos.lng = (out.elements[i].bounds.maxlon + out.elements[i].bounds.minlon) / 2;

				let x0 = {lat:out.elements[i].bounds.minlat, lng:out.elements[i].bounds.minlon};
				let x1 = {lat:out.elements[i].bounds.maxlat, lng:out.elements[i].bounds.maxlon};
				parking.surface = getSurface(x0,x1);
			}
			else{
				parking.pos.lat = out.elements[i].lat;
				parking.pos.lng = out.elements[i].lng;
			}
			parking.paymentMethod = getPaymentMethod(out.elements[i].tags);
			parking.capacity = getCapacity(out.elements[i].tags, parking.surface);
			//console.log(parking);
			parking.distance = distMeters(parking.pos, searchPos);
			if(parking.capacity.value>0){
				data.push(parking);
			}
		}
		return data;
	}
	catch(error){
		console.log("Error: could not fetch and parse data");
	}
}

// Used to get predict the number of free slots
async function fetchNearbyElements(pos, params, weightValue){
	url = 'https://overpass-api.de/api/interpreter?data=[out:json];(node'+params+'(around:500,'+pos.lat+','+pos.lng+');way'+params+'(around:500,'+pos.lat+','+pos.lng+');relation'+params+'(around:500,'+pos.lat+','+pos.lng+'););out count;';
	return fetch(url).then((res) => res.json()).then((out) => {
		let result = {weight:weightValue, count:parseInt(out.elements[0].tags.total)};
		return result;
	}).catch(error => { throw error });
}

async function getCityName(pos){
	const response = await fetch("https://api.opencagedata.com/geocode/v1/json?q="+pos.lat+"+"+pos.lng+"&key=6ed462e0c4a54f39a14230ff783fc470&limit=1")
	const json = await response.json();
	return json.results[0].components.city;
}

async function getCityPopulation(pos){			
	const city = await getCityName(pos);
	const response = await fetch('https://nominatim.openstreetmap.org/search.php?city="'+city+'"&format=json&extratags=1');
	const json = await response.json();
	return json[0].extratags.population;
}

//holidays ratio : 0 means that the date has no influence whereas 1 means that this place is only open during holidays. Values between 0 and 1 are accepted and gives the increase rate depending on if we are during holidays or not

function flatten(x, max, speed) {
	let newX = x * speed;
	let result = (newX /(1 + (Math.abs(newX)/max)));
	if(x<0){
		result*=-1;
	}
	return result
}

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

function getWeight(population, currentDate, holidays_ratio, opening_hours, importance){
	let weight = 1;
				
	if(opening_hours[getTimePeriod(currentDate.hour)] == false){
		weight+=0.1;
	}

	if(isHolidays(currentDate)){
		if(holidays_ratio>0.0)
			weight+=importance*holidays_ratio;
	} else if(holidays_ratio>0.0){
		weight*=(1-holidays_ratio);
	}

	weight += (population/1000000);
	weight *= Math.abs(importance);
	weight = flatten(weight, 1, 1);
	if(importance<0){
		weight*=-1;
	}
	return weight;
}

function getAvailability(data){
	let availability = 0.0;
	for(e of data){
		// We flatten x because, for instance, it doesn't make a lot of difference if there are 1 or 3 shops, especially since we don't know how big those shops are, so we want to limit the value between 0 and 10, to avoid getting way too big values
		availability += e.weight * flatten(e.count, 10, 1);
	}

	console.log(availability);

	availability = 1-flatten(availability, 1, 0.1);

	// Adjust values to be more realistic (we rarely have an empty or a completely full parking)
	if(availability<0.1){
		availability = 0.1;
	} else if(availability>0.9){
		availability = 0.9;
	}

	return availability;
}

async function testFreeSlotSim(parking){
	let capacity = parking.capacity.value;
	let searchPos = parking.pos;
	let promises = new Array();
	let date = new Date();
	let currentDate = {hour:date.getHours(), month:date.getMonth()};
	let cityPopulation = await getCityPopulation(searchPos);

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

	return numberOfSlots
}
