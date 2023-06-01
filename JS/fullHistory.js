window.onload = createTable;

async function getListOfVisits() {
	let url = "./PHP/queryMysqliReadOnly.php?&data=allVisits";
	return fetch(url).then(function(response) {
		if(response.status >= 200 && response.status < 300) {
			return response.json();
		}
		throw new Error(response.statusText);
	});
}

function addToRow(row, value, isTh) {
	let element = null;
	if(isTh)
		element = document.createElement("th");
	else
		element = document.createElement("td");
	let text = document.createTextNode((value+"").substring(0, 100));
	element.appendChild(text);
	row.appendChild(element);
}

function addElementToHistory(table, parking) {
	let newRow = document.createElement("tr");
	Object.entries(parking).forEach(function(element) {
		if(element[1] == null || element[1] == "null")
			addToRow(newRow, "inconnu", false);
		else {
			if(element[0] == "expenses")
				addToRow(newRow, element[1]+" €", false);
			else {
				addToRow(newRow, element[1], false);
			}
		}
	});
	table.appendChild(newRow);
}

function addHeader(table) {
	let newRow = document.createElement("tr");
	addToRow(newRow, "Date de visite", true);
	addToRow(newRow, "Montant dépensé", true);
	addToRow(newRow, "Nom", true);
	addToRow(newRow, "N° de rue", true);
	addToRow(newRow, "Rue", true);
	addToRow(newRow, "Ville", true);
	addToRow(newRow, "Code postal", true);
	addToRow(newRow, "Pays", true);
	table.appendChild(newRow);
}

async function createTable() {
	let table = document.getElementById('tableFullHistory');
	let listOfVisits = await getListOfVisits();
	if(listOfVisits != null) {
		addHeader(table, listOfVisits[0]);

		for(let i=0; i<listOfVisits.length; i++) {
			addElementToHistory(table, listOfVisits[i]);
		}
	}
}

