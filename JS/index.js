/*
	Initialize the forms in index.php (search filters)
*/

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

/*
	Save the given parking into a session variable
*/

async function setCurrentParkingInSession(parking){
	let url  = "./PHP/setCurrentParkingSession.php?lat="+parking.pos.lat+"&lng="+parking.pos.lng+"&name="+parking.name+"&nb="+parking.address.houseNumber+"&street="+parking.address.street+"&city="+parking.address.city+"&country="+parking.address.country+"&postal="+parking.address.postalCode;
	await fetch(url).catch((err)=>{
        console.error(err);
    });
}

/*
	Display the selected parking information
*/

function displaySelectedParking(parking){
	let details = document.getElementById("searchDetailsSideBar");
	
	details.style.visibility = "visible";
	//container.innerHTML = "Calcul du nombre de places disponibles...";
	let addressTd = document.getElementById("addressSelectedParking");
	let nbSlotsTd = document.getElementById("nbSlotsSelectedParking");
	let openingHoursTd = document.getElementById("openingHoursSelectedParking");
	let paymentTd = document.getElementById("paymentSelectedParking");

	nbSlotsTd.innerHTML = "Calcul en cours...";

	addressTd.innerHTML = parking.address.formatted;
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

/*
	Show or hide the left menu (search filters)
*/

function toggleMenuVisibility(){
	let element = document.getElementById("sideBarContent");

	element.classList.toggle("visible");
	element.classList.toggle("hidden");
}

/*
    Fill the table of instructions with the instructions to follow to go to the selected marker
*/
function displayRouteInstructions(table){
    let instructionTable = document.getElementsByClassName('instruction');

    for (let i = 0; i < table.length; i++) {
        let tr = document.createElement('tr');
        let tdInstruction = document.createElement('td');
        let tdDistance = document.createElement('td');

        tdInstruction.innerHTML = table[i].instruction;
        tdInstruction.style.width = "635px";
        //tdInstruction.style.padding = "50px 10px 20px 30px";

        tdDistance.innerHTML = table[i].distance;
        tdDistance.style.width = "95px";
        //tdDistance.style.padding = "50px 10px 20px 30px"; 

        tr.style.padding = "50px";
        tr.appendChild(tdInstruction);
        tr.appendChild(tdDistance);
        instructionTable[0].appendChild(tr);
    }
}

/*
    Remove existing instructions in the table
*/
function removeInstructions() {
    let instructionTable = document.getElementsByClassName('instruction');
    instructionTable[0].innerHTML = '';
}

/*
    Hide the table of instructions to follow
*/
function hideInstructions(){
    let instructionWrapper = document.getElementsByClassName('instruction-wrapper');
    instructionWrapper[0].style.visibility = "hidden";
}

/*
    Show the table of instructions to follow
*/
function showInstructions() {
    let instructionWrapper = document.getElementsByClassName('instruction-wrapper');
    instructionWrapper[0].style.visibility = "visible";
}