let currentParking = null;

/*
	Get the user id of the current connected user from the database
*/

function getUserId(){
	let url  = "./PHP/queryMysqliWriteRead.php?d=idUser";
	return fetch(url).then(function(response) {
        if(response.status >= 200 && response.status < 300) {
            return response.text();
        }
        throw new Error(response.statusText);
    })
    .then(function(response) {
        if(response == "0"){
            alert("Vous n'êtes pas connecté");
			throw new Error("Connection required");
        } else{
            return parseInt(response);
        }
    }).catch((err) => {
		throw err;
	});
}

/*
	Add the given address in the database if it doesn't exist and return its id
*/
/*
houseNumber "h"
street = "s"
city = "i"
country = "o"
postalCode = "p"
lat = "a"
lng = "n"
*/

async function addOrGetAddress(houseNumber, street, city, country, postalCode, lat, lng){
	let url  = "./PHP/addAddress.php?h="+houseNumber+"&s="+street+"&i="+city+"&o="+country+"&p="+postalCode+"&a="+lat+"&n="+lng;
	return fetch(url).then(function(response) {
		if(response.status >= 200 && response.status < 300) {
			return response.text();
        }
        throw new Error(response.statusText);
    }).then(function(response) {
        if(response !=""){
            return parseInt(response);
        } else {
			throw Error("Address id could not be fetched");
		}
    }).catch((err) => {
		throw err;
	});
}


/*
	Add the given parking in the database if it doesn't exist and return its id
*/

/*
idAddress = "i"/
name = "n"
*/

async function addOrGetParking(idAddress, parkingName){
	let url  = "./PHP/addParking.php?i="+idAddress+"&n="+parkingName;
	return fetch(url).then(function(response) {
		if(response.status >= 200 && response.status < 300) {
			return response.text();
        }
        throw new Error(response.statusText);
    })
	.then(function(response) {
        if(response !=""){
            return parseInt(response);
        } else {
			throw Error("Parking id could not be fetched");
		}
    }).catch((err) => {
		throw err;
	});
}


/*
	Add the given parking visite in the database if it doesn't exist and return its id
*/

/*
idUser = "u"
idParking = "p"
dateVisited = "d"
expenses = "e"
*/

async function addParkingVisite(idUser, idParking, dateVisited, expenses){
	let url  = "./PHP/addParkingVisited.php?u="+idUser+"&p="+idParking+"&d="+dateVisited+"&e="+expenses;
	return fetch(url).then(function(response) {
        if(response.status >= 200 && response.status < 300) {
            return response.text();
        }
        throw new Error(response.statusText);
    })
    .then(function(response) {
        if(response == ""){
            return true;
        } else{
			return false;
		}
    });
}

/*
	Get the data about the currently selected parking from the session variable
*/

async function getCurrentParkingFromSession(){
	let url  = "./PHP/getCurrentParkingSession.php";
	let response = await fetch(url);
	let json = await response.json();
	currentParking = json;
}

/*
	Add the selected parking visite to the database
*/
async function addToHistory(){
	document.getElementById('infoBox').innerText = "";

	let expenses = document.getElementById('expenses').value;
	let dateVisited = new Date().toJSON().slice(0,10);
	let name = document.getElementById('name').value;
	let houseNumber = document.getElementById('houseNumber').value;
	let street = document.getElementById('street').value;
	let city = document.getElementById('city').value;
	let country = document.getElementById('country').value;
	let postalCode = document.getElementById('postalCode').value;

	try{
		let idUser = await getUserId();
		let idAddress = await addOrGetAddress(houseNumber, street, city, country, postalCode, currentParking.pos.lat, currentParking.pos.lng);
		let idParking = await addOrGetParking(idAddress, name);
		
		let infoBox = document.getElementById('infoBox');
		if(addParkingVisite(idUser, idParking, dateVisited, expenses)){
			infoBox.innerText = "Le parking a bien été ajouté à votre historique";
			infoBox.style.color = "green";
		} else{
			infoBox.innerText = "Une erreur est survenue, l'opération n'a pas été enregistrée";
			infoBox.style.color = "red";
		}
	} catch(err){
		console.error(err);
	}
}