const months=["janvier","février","mars","avril","mai","juin","juillet","août","septembre","octobre","novembre","décembre"];
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

function getMonthsArrayFromDateRanges(start, end){
	let result = [];

	if(start.year != end.year){
		for(let month = start.month; month<12; month++){
			result.push(months[month]+" "+start.year);
		}
		for(let year = start.year+1; year < end.year; year++){
			for(let month = 0; month<12; month++){
				result.push(months[month]+" "+year);
			}
		}
		for(let month = 0; month<=end.month; month++){
			result.push(months[month]+" "+end.year);
		}
	} else {
		for(let month = start.month; month<=end.month; month++){
			result.push(months[month]+" "+start.year);
		}
	}
	return result;
}

async function predict() {
/*
	To predict the next expenses:
	- Use data of the previous year if there is any
	- Use the previous months to get the slope expected
	- Display it on the same graph ?

*/
	// Last months fetched
	let startDate = "2023-01-01";
    let endDate = "2023-04-31"; // if there are only 29 or 30 days it doesn"t matter, it'll still be correct since we'll be just checking a range with no values in the database

	let predictionLength = 5;

	// Month values are between 0 and 11

	// Date ranges used for the prediction
	let startKnown = {month:parseInt(startDate.slice(5, 7))-1, year:parseInt(startDate.slice(0,4))};
	let endKnown = {month:parseInt(endDate.slice(5, 7))-1, year:parseInt(endDate.slice(0,4))};

	// Date ranges where is done the prediction
	let startPredicted = {month:endKnown.month+1, year:endKnown.year};
	let endPredicted = {month:endKnown.month + predictionLength, year:endKnown.year};
	
	let xAxis = getMonthsArrayFromDateRanges(startKnown, endPredicted);

	if(startPredicted.month > 11){
		startPredicted.year ++;
		startPredicted.month -= 12;
	}

	if(endPredicted.month > 11){
		endPredicted.year ++;
		endPredicted.month -= 12;
	}

	let predictionResult = new Array();

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
	
	// Last months are used to get if it increased or decreased recently

	let avg = 0;
	let compare = 0;
	let yAxisKnown = [];
	if(lastMonths != null){
		for(let i=0; i<lastMonths.length; i++){
			avg += lastMonths[i];
			yAxisKnown.push(lastMonths[i]);
		}
		avg /= lastMonths.length;

		if(lastMonths[lastMonths.length-1] > avg){
			compare = 1;
		} else if(lastMonths[lastMonths.length-1] == avg){
			compare = 0;
		} else {
			compare = -1;
		}
	}

	compare * 0.1;
	avg += compare; // to count the iteration with i=0
	
	for(let i=0; i<predictionLength; i++){
		predictionResult[i] = compare * i + avg;
	}
	// Average by month to see if there are some habits (for instance during week-end or holidays those number might change)
	let month = startPredicted.month;
	for(let i=0; i<predictionLength; i++){
		if(month >= 12){
			month = 0;
		}
		predictionResult[i] += meanMonths[month];
		// avg of the 2 predicted value
		predictionResult[i] /= 2;
		month++;
	}
	console.log("mean=",meanMonths);
	console.log("last=",lastMonths);
	console.log("predict=",predictionResult);
	
	let yAxis = yAxisKnown.concat(predictionResult);
	console.log(yAxis);

	console.log(lastMonths.length );
	
	// The color of the predicted curve is set to red
	const red = (ctx, value) => ctx.p1.parsed.x >= lastMonths.length ? value : undefined;

	new Chart("expensesPredict", {
        type: "line",
        data:{
            labels:xAxis,
            datasets: [{
                data: yAxis ,
                borderColor: "blue",
                backgroundColor: "black",
				label : 'Prediction',
                fill: false,
				cubicInterpolationMode: 'monotone',
				segment: {
					borderColor : ctx => red(ctx, "red"),
				}
            }]
        },
        options:{
			plugins: {
				legend: {
					display: false,
				},
				title: {
					display: true,
					text: "Prédiction des dépenses mensuelles"
				}
			}
        }
    });
}