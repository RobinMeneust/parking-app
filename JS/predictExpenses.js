let months=["janvier","février","mars","avril","mai","juin","juillet","août","septembre","octobre","novembre","décembre"];
let year = "2023";
let button = null;
const regex = new RegExp('^([0-9]{4})$'); // used to check if the given date (year) is correct

async function getDataProfile(startDate, endDate, dataProfile) {
    let url = "./PHP/queryMysqliReadOnly.php?start="+startDate+"&end="+endDate+"&data="+dataProfile;
    return fetch(url).then(function(response) {
        if(response.status >= 200 && response.status < 300) {
            return response.text();
        }
        throw new Error(response.statusText);
    });
}

async function predict() {
	let startDate = "2023-02-01";
    let endDate = "2024-04-30"; // 29 vs 30 vs 31
    
    let response = await getDataProfile(startDate, endDate, 'allExpensesByMonth');

	if(response == "") {
		console.log("You don't have any history so no prediction can be made");
		return;
	}
	let expenses = JSON.parse(response);

	
}