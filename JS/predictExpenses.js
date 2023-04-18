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
/*
	To predict the next expenses:
	- Use data of the previous year if there is any
	- Use the previous months to get the slope expected
	- Display it on the same graph ?

*/
	// Last months fetched
	let startDate = "2023-02-01";
    let endDate = "2023-04-31"; // if there are only 29 or 30 days it doesn"t matter, it'll still be correct since we'll be just checking a range with no values in the database

	let predictionLength = 3;
	// Months predicted
	let startPredicted = {month:parseInt(endDate.slice(5, 7)), year:parseInt(endDate.slice(0,4))};
	let endPredicted = {month:parseInt(endDate.slice(5, 7)) + predictionLength, year:parseInt(endDate.slice(0,4))};

	if(startPredicted.month > 12){
		startPredicted.year ++;
		startPredicted.month -= 12;
	}

	if(endPredicted.month > 12){
		endPredicted.year ++;
		endPredicted.month -= 12;
	}

	let predictionResult = new Array(12).fill(0);

	let responseMeanMonths = await getDataProfile("null", "null", 'allExpensesByMonthMean');

	let meanMonths = null;
	if(responseMeanMonths != "") {
		meanMonths = JSON.parse(responseMeanMonths);
	}

	let responseLastMonths = await getDataProfile(startDate, endDate, 'allExpensesByMonthInRange');
	if(responseLastMonths == "") {
		console.log("There is not enough data to make a prediction");
		return;
	}
	let lastMonths = JSON.parse(responseLastMonths);

	// Average by month to see if there are some habits (for instance during week-end or holidays those number might change)
	if(meanMonths != null){
		let ind = startPredicted.month;
		for(let i=0; i<predictionLength; i++){
			if(ind > 12){
				ind = 1;
			}
			predictionResult[ind] = meanMonths[ind];
			console.log(predictionResult[ind]);
			ind++;
		}
	}
	
	// Last months are used to get if it increased or decreased recently

	let avg = 0;
	let compare = 0;
	if(lastMonths != null){
		for(let i=0; i<lastMonths.length; i++){
			avg += lastMonths;
		}
		avg /= lastMonths.length;

		if(lastMonths[lastMonths.length-1] > lastMonths[lastMonths.length-2]){
			compare = 1;
		} else if(lastMonths[lastMonths.length-1] == lastMonths[lastMonths.length-2]){
			compare = 0;
		} else {
			compare = -1;
		}
	}

	ind = startPredicted.month;
	compare * 0.1;

	// avg of the 2 predicted value
	for(let i=0; i<predictionLength; i++){
		if(ind > 12){
			ind = 1;
		}
		predictionResult[ind] += compare * i * avg;
		predictionResult[ind] /= 2;
		ind++;
	}
	console.log(lastMonths);
	console.log(meanMonths);
	console.log(predictionResult);
}