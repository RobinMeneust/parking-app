const months=["null","janvier","février","mars","avril","mai","juin","juillet","août","septembre","octobre","novembre","décembre"];
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
	return applyFunctionOnDateRange(start, end, function(month, year, data) {return months[month]+" "+year});
}

function applyFunctionOnDateRange(start, end, f, data) {
	let result = [];
	if(start.year != end.year){
		for(let month = start.month; month<=12; month++){
			result.push(f(month,start.year, data));
		}
		for(let year = start.year+1; year < end.year; year++){
			for(let month = 1; month<12; month++){
				result.push(f(month,year,data));
			}
		}
		for(let month = 1; month<=end.month; month++){
			result.push(f(month,end.year,data));
		}
	} else {
		for(let month = start.month; month<=end.month; month++){
			result.push(f(month,start.year,data));
		}
	}
	return result;
}

function getExpensesFromJSONObject(month, year, data) {
	for(let i = 0; i<data.length; i++) {
		if(data[i].month == month && data[i].year == year) {
			return data[i].expenses;
		}
	}
	return 0;
}

function fixDate(date) {
	while(date.month>12) {
		date.year++;
		date.month -= 12;
	}
	while(date.month<=0) {
		date.year--;
		date.month += 12;
	}
}

function dateToString(year, month, day) {
	return year+"-"+month+"-"+day;
}

function getAverageOfArray(array) {
	let avg = 0;
	for(let i=0; i<array.length; i++) {
		avg += array[i];
	}
	return avg / array.length;
}


async function predict() {
	// Last months fetched
	let currentDate = new Date();

	let knownLength = 5;
	let predictionLength = 5;

	// Date ranges used for the prediction
	let endKnown = {month:currentDate.getMonth()+1, year:currentDate.getFullYear()}; // +1 because months are indexed from 0
	let startKnown = {month:endKnown.month-knownLength, year:endKnown.year};
	fixDate(startKnown);

	let startKnownStr = dateToString(startKnown.year, startKnown.month,1);
	let endKnownStr = dateToString(endKnown.year, endKnown.month,31);

	// Date ranges where is done the prediction
	let startPredicted = {month:endKnown.month+1, year:endKnown.year};
	fixDate(startPredicted);
	let endPredicted = {month:endKnown.month + predictionLength, year:endKnown.year};
	fixDate(endPredicted);

	let xAxis = getMonthsArrayFromDateRanges(startKnown, endPredicted);

	
	let responseMeanMonths = await getDataProfile("null", "null", 'allExpensesByMonthMean');
	
	let meanMonths = null;
	if(responseMeanMonths != "") {
		meanMonths = JSON.parse(responseMeanMonths);
	}
	
	let responseLastMonths = await getDataProfile(startKnownStr, endKnownStr, 'allExpensesByMonthInRange');
	if(responseLastMonths == "") {
		console.error("There is not enough data to make a prediction");
		return;
	}
	
	let lastMonths = JSON.parse(responseLastMonths);
	// Last months are used to get if it increased or decreased recently
	
	let avg = 0;
	let compare = 0;
	let yAxisKnown = [];
	
	if(lastMonths != null){
		yAxisKnown = applyFunctionOnDateRange(startKnown, endKnown, getExpensesFromJSONObject, lastMonths);
		avg = getAverageOfArray(yAxisKnown);
		
		// Used to estimate the slope
		if(yAxisKnown[yAxisKnown.length-1] > avg){
			compare = 1;
		} else if(yAxisKnown[yAxisKnown.length-1] == avg){
			compare = 0;
		} else {
			compare = -1;
		}
	}
	
	compare * 0.1;
	avg += compare; // to count the iteration with i=0
	let predictionResult = new Array();
	
	for(let i=0; i<predictionLength; i++){
		predictionResult[i] = compare * i + avg;
	}
	// Average by month to see if there are some habits (for instance during week-end or holidays those values might change)
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
	//console.log("mean=",meanMonths);
	//console.log("last=",lastMonths);
	//console.log("predict=",predictionResult);

	let yAxis = yAxisKnown.concat(predictionResult);
	//console.log(yAxis);

	//console.log(lastMonths.length );
	
	// The color of the predicted curve is set to red
	const color = (ctx) => ctx.p1.parsed.x >= yAxisKnown.length ? "red" : "blue";

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
					borderColor : ctx => color(ctx),
				}
            }]
        },
        options:{
			responsive: false,
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