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
        console.log("sdfs" + response.statusText);
        throw new Error(response.statusText);
    })
    .then(function(response) {
        if(response == ""){
            
        } else {
            return response;
        }
    }).catch((err)=>{
        console.error(err);
    });
}

// Convert a date to ther string format YYYY-MM-DD

function dateToString(year, month, day) {
	let m = month;
	let d = day;

	if(month<10) {
		m = "0"+month;
	}
	if(day<10) {
		d = "0"+day;
	}
	return year+"-"+m+"-"+d;
}

function searchData(startDate, endDate) {
    let expenses = document.getElementById('expensesProfileTable');
    let favorite = document.getElementById('favoriteParkingProfileTable');
    let visited = document.getElementById('visitedProfileTable');   
    
    getDataProfile(startDate, endDate, 'expensesProfile').then((response)=> {
        expenses.innerHTML = response ? response+" €" : "0 €";
    }).catch((err)=>{
        console.error(err);
    });
    getDataProfile(startDate, endDate, 'visitedProfile').then((response)=> {
        visited.innerHTML = response ? response : "0";
    }).catch((err)=>{
        console.error(err);
    });
    getDataProfile(startDate, endDate, 'favoriteProfile').then((response)=> {
        favorite.innerHTML = response ? response : "Aucun parking favori";
    }).catch((err)=>{
        console.error(err);
    });
}

function refreshDate() {
    const Start_input_Date = document.getElementById("Start_date");
    const End_input_Date = document.getElementById("End_date");
    const selected_Start_Date = new Date(Start_input_Date.value);
    const selected_End_Date = new Date(End_input_Date.value);
    const errorMessage = document.getElementById('date_error_message');
    
    if(isNaN(selected_Start_Date) || isNaN(selected_End_Date)) {
        return;
    }

    let startDate = dateToString(selected_Start_Date.getFullYear(), selected_Start_Date.getMonth()+1, selected_Start_Date.getDate());
    let endDate = dateToString(selected_End_Date.getFullYear(), selected_End_Date.getMonth()+1, selected_End_Date.getDate());

    const date_Limit = new Date(2010, 0, 1);

    if (selected_End_Date < selected_Start_Date) {
        errorMessage.style.display = 'block';
        return;
    } else if (selected_Start_Date < date_Limit) {
        errorMessage.style.display = 'block';
        return;
    } else {
        errorMessage.style.display = 'none';
    }

    // It's valid
    searchData(startDate, endDate);
}