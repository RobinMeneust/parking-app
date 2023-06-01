window.onload = createFullTable;
let isFullMode = false;

async function getListOfVisits(fullMode) {
	let url = "./PHP/queryMysqliReadOnly.php?&data=allVisits";
	if(fullMode)
		url += "Full";
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

function addHeader(table, elements) {
	let newRow = document.createElement("tr");
	newRow.id = "headerTable";
	for(let i=0; i<elements.length; i++) {
		addToRow(newRow, elements[i], true);
	}
	table.appendChild(newRow);
}

function clearTable() {
	let table = document.getElementById('tableFullHistory');
	table.removeChild
	while(table.firstChild != null) {
		table.removeChild(table.firstChild);
	}
}

async function createTable(fullMode) {
	clearTable();
	let table = document.getElementById('tableFullHistory');
	let listOfVisits = await getListOfVisits(fullMode);
	if(listOfVisits != null && listOfVisits != "") {
		let first = "";
		if(fullMode)
			first = "Date de visite";
		else
			first = "Nombre de visites";

		addHeader(table, [first, "Montant dépensé", "Nom", "N° de rue", "Rue", "Ville", "Pays", "Code postal"]);
		for(let i=0; i<listOfVisits.length; i++) {
			addElementToHistory(table, listOfVisits[i]);
		}
	}
}

async function createFullTable() {
	if(!isFullMode) {
		createTable(true);
		isFullMode = true;
	}
}

async function createTableByParking() {
	if(isFullMode) {
		createTable(false);
		isFullMode = false;
	}
}