let months=["janvier","février","mars","avril","mai","juin","juillet","août","septembre","octobre","novembre","décembre"];
let year = "2023";
let button = null;
const regex = new RegExp('^([0-9]{4})$'); // used to check if the given date (year) is correct

function initialize() {
    button = document.getElementById('refreshDate');
    button.addEventListener("click",function(){refreshDate();});

    getExpensesValues().then((result) =>{
        let expensesValues = result;
        createExpensesGraph(expensesValues);
    });
    
    getVisitsValues().then((result) =>{
        let visitsValues = result;
        console.log(visitsValues);
        createVisitsGraph(visitsValues);
    });
}

function createVisitsGraph(visitsValues){
    var visits = new Chart("visits", {
        type: "bar",
        data:{
            labels:months,
            datasets: [{
                data: visitsValues,
                backgroundColor:"blue"
            }]
        },
        options:{
            responsive: true,
            legend: {display: false},
            title: {
                display: true,
                text: 'Nombre de stationnements par mois'
            },
            scales: {
                yAxes: [{
                    display: true,
                    ticks: {
                        beginAtZero: true
    
                    }
                }]
            }
        }
    });
}

function createExpensesGraph(expensesValues){
    var expenses = new Chart("expenses", {
        type: "line",
        data:{
            labels:months,
            datasets: [{
                borderColor: 'blue',
                fill:false,
                data: expensesValues,
            }]
        },
        options:{
            responsive: true,
            legend: {display: false},
            title: {
                display: true,
                text: 'Dépenses par mois'
            },
            scales: {
                yAxes: [{
                    display: true,
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
}

async function sendQueryGraph(url){
    let result = [0,0,0,0,0,0,0,0,0,0,0,0];
    // example : [{"d":"4","n":"12.00"}]
    return fetch(url).then(function(response) {
        if(response.status >= 200 && response.status < 300) {
            return response.json();
        }
        throw new Error(response.statusText);
    })
    .then(function(response) {
        if(response == "0"){
            alert("Vous n'êtes pas connecté");
        } else{
            for(let i=0; i<response.length; i++){
                result[parseInt(response[i]["d"])] = parseInt(response[i]["n"]);
            }
            return result;
        }
    });
}

function getExpensesValues(){
    return sendQueryGraph('./PHP/queryMsqliGet.php?d=expenses&y='+year);
}

function getVisitsValues(){
    return sendQueryGraph('./PHP/queryMsqliGet.php?d=visits&y='+year);
}

function refreshDate(){
    let tempYear = document.getElementById('yearGraph').value;
    let tempYearInt = parseInt(tempYear)
    if(regex.test(tempYear) && tempYearInt >= 2000){
        year = tempYear;

        getExpensesValues().then((result) =>{
            let expensesValues = result;
            createExpensesGraph(expensesValues);
        });
        
        getVisitsValues().then((result) =>{
            let visitsValues = result;
            createVisitsGraph(visitsValues);
        });
    } else{
        document.getElementById('yearGraph').value = "2023";
    }
}

