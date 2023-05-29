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
    })
    .then(function(response) {
        if(response == ""){
            alert("Vous n'êtes pas connecté !");
        } else {
            return response;
        }
    });
}

function refreshDate() {
    let expenses = document.getElementById('expensesProfileTable');
    let favorite = document.getElementById('favoriteParkingProfileTable');
    let visited = document.getElementById('visitedProfileTable');
    let Start_input_Date = document.getElementById('Start_date');
    let End_input_Date = document.getElementById('End_date');

    let startDate = new Date(Start_input_Date.value).toLocaleDateString("fr-FR"); 
    let endDate = new Date(End_input_Date.value).toLocaleDateString("fr-FR");
    
    getDataProfile(startDate, endDate, 'expensesProfile').then((response)=> {
        expenses.innerHTML = response;
    });
    getDataProfile(startDate, endDate, 'visitedProfile').then((response)=> {
        favorite.innerHTML = response;
    });
    getDataProfile(startDate, endDate, 'favoriteProfile').then((response)=> {
        visited.innerHTML = response;
    });
}